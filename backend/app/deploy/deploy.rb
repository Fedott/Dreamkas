set :stage_dir, "app/deploy/stages"
set :default_stage, "staging"

require 'capistrano/ext/multistage'

set :user, "watchman"
set :deploy_to_base,   "/var/www/"
set :deploy_via,  :remote_cache_sub_folder

set :app_end, "api"
set :application, "api"

set :domain, 'sellpoint.io' unless exists?(:domain)

set :deploy_subdir, "backend"
set :app_path,    "app"
set :web_path,    "web"

set :shared_files, [app_path + "/config/parameters.yml"]

set :git_enable_pull_requests, true
set :repository,  "git@github.com:Fedott/Dreamkas.git"
set :scm,         :git

ssh_options[:forward_agent] = true

set :use_sudo, false
default_run_options[:pty] = true

set :use_composer, true
set :update_vendors, false
set :composer_bin, "composer"

set :use_set_permissions, false

set :api_clients, [
    {:public_id => "autotests", :secret => "secret"},
    {:public_id => "webfront", :secret => "secret"},
    {:public_id => "android", :secret => "secret"},
    {:public_id => "ios", :secret => "secret"}
]

set :api_users, [
    {:email => "watchman@lighthouse.pro",  :userpass => "lighthouse", :userrole => "ROLE_ADMINISTRATOR"},
    {:email => "owner@lighthouse.pro",     :userpass => "lighthouse"},
]

set  :keep_releases,  5

logger.level = Logger::IMPORTANT

after "deploy:restart" do
    puts "--> API was successfully deployed to ".green + "#{application_url}".yellow
end
