namespace :symfony do

    desc "Run custom command. Add '-s command=<command goes here>' option"
    task :console do
        prompt_with_default(:command, "list") unless exists?(:command)
        stream "sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} #{command} --env=#{symfony_env_prod}'"
    end

    namespace :doctrine do
        namespace :mongodb do
            namespace :schema do

                desc "Drop and create schema"
                task :recreate do
                    drop
                    create
                end

            end
        end
    end

    namespace :logs do

        desc "Tail symfony log according to environment"
        task :default, :roles => :app, :except => { :no_release => true } do
            set :lines, '50' unless exists?(:lines)
            log = "#{stage}.log"
            run "#{try_sudo} tail -n #{lines} -f #{shared_path}/#{log_path}/#{log}" do |channel, stream, data|
              trap("INT") { puts 'Interrupted'; exit 0; }
              puts
              puts "#{channel[:host]}: #{data}"
              break if stream == :err
            end
        end

    end

    namespace :parameters do

        desc "Setup parameters: upload parameters.yml and rename database"
        task :setup, :roles => :app, :except => { :no_release => true } do
            upload
            rename_database_name
        end

        desc "Upload current parameters.yml to shared folder"
        task :upload, :roles => :app, :except => { :no_release => true } do
            origin_file = "app/config/parameters.yml"
            destination_file = shared_path + "/app/config/parameters.yml"

            try_sudo "mkdir -p #{File.dirname(destination_file)}"
            top.upload(origin_file, destination_file)
        end

        desc "Rename database_name in app/config/parameters.yml. Application name will be used (%branch.stage.env%) unless -S database_name=%database_name% argument is provided"
        task :rename_database_name, :roles => :app, :except => { :no_release => true } do
            set :database_name, application.gsub(/\./, '_') unless exists?(:database_name)
            puts "--> Database name in ".yellow + "parameters.yml".bold.yellow + " will be set to ".yellow + "#{database_name}".red
            destination_file = shared_path + "/app/config/parameters.yml"
            run "sed -r -i 's/^(\\s+database_name:\\s+).+$/\\1#{database_name}/g' #{destination_file}"
        end
    end

    namespace :auth do
        namespace :client do

            def create_auth_client(secret, public_id)
                run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} lighthouse:auth:client:create #{secret} #{public_id} --env=#{symfony_env_prod}'", :once => true
            end

            task :create, :roles => :app, :except => { :no_release => true } do
                capifony_pretty_print "--> Creating client"
                create_auth_client(secret, public_id)
                capifony_puts_ok
            end
        end
    end
end