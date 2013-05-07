require 'time_diff'

namespace :deploy do

    desc "Check & setup environment if needed"
    task :setup_if_needed, :roles => :app, :except => { :no_release => true } do
        begin
            check
        rescue Exception => error
            setup
        end
    end

    desc "Reload PHP-FPM"
    task :reload_php, :roles => :app, :except => { :no_release => true } do
        run "#{sudo} service php5-fpm reload"
        capifony_pretty_print "--> Reloading PHP-FPM"
        capifony_puts_ok
    end

    desc "Init deploy configuration using current git branch"
    task :init do

        set :app_end, 'api' unless exists?(:app_end)
        set :branch, `git rev-parse --abbrev-ref HEAD`.delete("\n") || "master" unless exists?(:branch)
        set :host, branch unless exists?(:host)

        set :application, "#{host}.#{stage}.#{app_end}"
        set :deploy_to,   "/var/www/#{application}"
        set :symfony_env_prod, exists?(:symfony_env) ? symfony_env : stage
        set :application_url, "http://#{application}.lighthouse.cs"

        puts "--> Branch ".yellow + "#{branch}".red + " will be used for deploy".yellow
        puts "--> Application will be deployed to ".yellow + application_url.red
        puts "--> Symfony env: ".yellow + symfony_env_prod.to_s.red
    end

    desc "List all deployed hosts"
    task :list do
        hosts = capture("ls -x #{deploy_to_base}", :except => { :no_release => true }).split.select { |v| v =~ /\.#{app_end}$/ }.sort
        revisions = hosts.map do |host|
            host_current_path = File.join(deploy_to_base, host, current_dir)
            host_releases_path = File.join(deploy_to_base, host, version_dir)
            releases = capture("if [ -d #{host_releases_path} ]; then ls -x #{host_releases_path}; fi", :except => { :no_release => true }).split.sort
            last_release = releases.length > 0 ? Time.parse(releases.last.sub(/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '\1-\2-\3 \4:\5:\6 UTC')).getlocal : nil;
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
            if (x[:last_release].nil?)
                -1
            elsif (y[:last_release].nil?)
                1
            else
                x[:last_release] <=> y[:last_release]
            end
        end .each do |revision|
            print "Host: " + revision[:host].yellow
            print_shift(30, revision[:host].length)
            print " *" + revision[:releases].to_s.green
            print_shift(4, revision[:releases].to_s.length)

            unless (revision[:last_release].nil?)
                time_diff = Time.diff(revision[:last_release], Time.new);
                print " " + revision[:last_release].to_s.yellow + " (" + time_diff[:diff].green + ")"
                print_shift(30, time_diff[:diff].length)
            else
                print " never".red
                print_shift(58, 0)
            end

            host = revision[:host].sub(/^(.+)\..+?\.#{app_end}$/, '\1')
            stage = revision[:host].sub(/^.+\.(.+)?\.#{app_end}$/, '\1')
            puts "Cleanup command: " + "cap #{stage} deploy:cleanup -S host=#{host}".red

        end
    end

    desc "Delete host and drop mongo db"
    namespace :remove do

        task :default, :roles => :app, :except => { :no_release => true } do
            if Capistrano::CLI.ui.ask("Are you sure drop " + application_url.yellow + " (y/n)") == 'y'
                begin
                    mongodb
                rescue Exception => error
                    puts "✘\n#{error}".red
                end
                host
            end
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

after "deploy:update", "deploy:cleanup"