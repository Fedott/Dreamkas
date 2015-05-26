set :default_stage, "staging"
set :stage_dir, "deploy/stages"

require 'capistrano/ext/multistage'

set :user, 'watchman'
set :deploy_to_base, '/var/www/'
set :deploy_via, :remote_cache_sub_folder

set :app_end, 'webfront'
set :deploy_subdir, 'webfront'

set :domain, 'sellpoint.io' unless exists?(:domain)

set :shared_children,   []

set :git_enable_pull_requests, true
set :repository, 'git@github.com:Fedott/Dreamkas.git'
set :scm, :git

set :npm_flag, '--production'

ssh_options[:forward_agent] = true

set :use_sudo, false
default_run_options[:pty] = true

set  :keep_releases,  5

logger.level = Logger::IMPORTANT

set :normalize_asset_timestamps, false

after 'deploy:restart' do
    puts "--> Webfront was successfully deployed to ".green + application_url.yellow
end

after 'deploy:finalize_update', 'webfront'

namespace :deploy do
    namespace :remove do
        task :default, :roles => :app, :except => { :no_release => true } do
            if force || Capistrano::CLI.ui.ask("Are you sure drop " + application_url.yellow + " (y/n)") == 'y'
                host
            end
        end
    end
end
