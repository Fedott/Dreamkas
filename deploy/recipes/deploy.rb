require 'time_diff'
require 'i18n'
I18n.config.enforce_available_locales = true

def remote_folder_exists?(path)
    'true' ==  capture("if [ -d #{path} ]; then echo 'true'; fi").strip
end

def check_app_deployed
    unless remote_folder_exists?(deploy_to)
        capifony_pretty_print "--> You have not deployed your app yet".red
        raise "You have not deployed your app yet"
    end
end

namespace :deploy do

    desc "Check & setup environment if needed"
    task :setup_if_needed, :roles => :app, :except => { :no_release => true } do
        unless remote_folder_exists?(deploy_to)
            puts "--> Setup host ".yellow + application.to_s.red + " because it does not exist".yellow
            setup
        end
    end

    task :cleanup_if_needed, :roles => :app, :except => { :no_release => true } do
        count = fetch(:keep_releases, 5).to_i
        local_releases = capture("ls -xt #{releases_path}").split.reverse
        if local_releases.length > count
            deploy.cleanup
            puts "--> Cleanup old releases"
        end
    end

    namespace :php do
        {
            :start  => "Start",
            :stop   => "Stop",
            :reload => "Reload"
        }.each do |action, description|
            desc "PHP-FPM #{description}"
            task action, :roles => :app, :except => { :no_release => true } do
                run "#{sudo} service php5-fpm #{action.to_s}"
                capifony_pretty_print "--> PHP-FPM #{description}"
                capifony_puts_ok
            end
        end
    end

    namespace :supervisor do
        [:start, :stop].each do |action|
            desc "#{action.to_s.capitalize} supervisor"
            task action, :roles => :app do
                run "#{sudo} service supervisor #{action.to_s}"
                capifony_pretty_print "--> Supervisor #{action}"
                capifony_puts_ok
            end
        end

        task :restart, :roles => :app do
            stop
            start
        end
    end

    namespace :vpn do
        desc "Check VPN"
        task :check, :roles => :app, :except => { :no_release => true } do
            capifony_pretty_print "--> Checking VPN is up"
            interfaces = capture("ifconfig", :except => { :no_release => true })
            raise "VPN is down" unless /^ppp0/.match(interfaces)
            capifony_puts_ok
        end

        task :start,  :roles => :app, :except => { :no_release => true } do
            run "#{sudo} pon crystal"
            capifony_pretty_print "--> Starting VPN"
            capifony_puts_ok
        end

        task :stop,  :roles => :app, :except => { :no_release => true } do
            run "#{sudo} poff crystal"
            capifony_pretty_print "--> Stopping VPN"
            capifony_puts_ok
        end

        task :default do
            begin
                check
            rescue Exception => error
                puts "✘".red
                start
            end
        end
    end

    desc "Init deploy configuration using current git branch"
    task :init do

        set :app_end, 'api' unless exists?(:app_end)
        set :branch, `git rev-parse --abbrev-ref HEAD`.delete("\n") unless exists?(:branch)
        set :host, branch unless exists?(:host)
        set :host, host.gsub('/', '-')

        set :force, exists?(:force) ? true : false

        set :application, exists?(:application_folder) ? "#{application_folder}" : "#{host}.#{stage}.#{app_end}";

        set :deploy_to,   "#{deploy_to_base}#{application}"
        set :symfony_env_prod, exists?(:symfony_env) ? symfony_env : stage
        set :application_url, "http://#{application}.sellpoint.io" unless exists?(:application_url)

        puts "--> Branch ".yellow + "#{branch}".red + " will be used for deploy".yellow
        puts "--> Application will be deployed to ".yellow + application_url.red
        puts "--> Symfony env: ".yellow + symfony_env_prod.to_s.red
    end

    desc "List all deployed hosts"
    task :list do
        hosts = capture("ls -x #{deploy_to_base}", :except => { :no_release => true }).split.select { |v| v =~ /\.#{app_end}$/ }.sort
        revisions = hosts.map do |host|
            host_releases_path = File.join(deploy_to_base, host, version_dir)
            releases = capture("if [ -d #{host_releases_path} ]; then ls -x #{host_releases_path}; fi", :except => { :no_release => true }).split.sort
            last_release_date = releases.last.sub(/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '\1-\2-\3 \4:\5:\6 UTC') unless releases.empty?
            last_release = releases.length > 0 ? Time.parse(last_release_date).getlocal : nil
            {
                :host => host,
                :last_release => last_release,
                :releases => releases.length
            }
        end

        def print_shift(repeat, length)
            shift = (repeat - length)
            print " " * (shift > 0 ? shift : 1)
        end

        revisions.sort do |x,y|
            if x[:last_release].nil?
                -1
            elsif y[:last_release].nil?
                1
            else
                x[:last_release] <=> y[:last_release]
            end
        end .each do |revision|
            host = revision[:host].sub(/^(.+)\..+?\.#{app_end}$/, '\1')
            stage = revision[:host].sub(/^.+\.(.+)?\.#{app_end}$/, '\1')
            if force
              puts "cap #{stage} deploy:remove -S host=#{host} -S force"
            else
              print "Host: " + revision[:host].yellow
              print_shift(30, revision[:host].length)
              print " *" + revision[:releases].to_s.green
              print_shift(4, revision[:releases].to_s.length)

              if revision[:last_release].nil?
                print " never".red
                print_shift(58, 0)
              else
                time_diff = Time.diff(revision[:last_release], Time.new)
                print " " + revision[:last_release].to_s.yellow + " (" + time_diff[:diff].green + ")"
                print_shift(30, time_diff[:diff].length)
              end

              puts "Remove command: " + "cap #{stage} deploy:remove -S host=#{host}".red
            end
        end
    end

    desc "Delete host and drop mongo db"
    namespace :remove do

        task :default, :roles => :app, :except => { :no_release => true } do
            if force || Capistrano::CLI.ui.ask("Are you sure drop " + application_url.yellow + " (y/n)") == 'y'
                go
            end
        end

        task :go do
            begin
                mongodb
            rescue Exception => error
                puts "✘\n#{error}".red
            end
            host
        end

        desc "Drop mongodb database"
        task :mongodb do
            symfony.doctrine.mongodb.schema.drop
        end

        desc "Remove :deploy_to directory"
        task :host do
            capifony_pretty_print "--> Removing " + deploy_to.yellow + " directory"
            run "#{try_sudo} rm -rf #{deploy_to}"
            capifony_puts_ok
        end
    end

end

after "multistage:ensure", "deploy:init"

before "deploy", "deploy:setup_if_needed"

after "deploy:update", "deploy:cleanup_if_needed"