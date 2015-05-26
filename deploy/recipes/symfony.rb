namespace :symfony do

    def console_command(command)
        check_app_deployed
        "sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} #{command} --env=#{symfony_env_prod}'"
    end

    desc "Run custom command. Add '-s command=<command goes here>' option"
    task :console do
        prompt_with_default(:command, "list") unless exists?(:command)
        stream console_command(command)
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

        desc "Load data fixtures"
        task :load_fixtures, :roles => :app, :except => { :no_release => true } do
            puts capture console_command("doctrine:mongodb:fixtures:load #{doctrine_em_flag} --no-interaction"), :once => true
        end
    end

    namespace :logs do

        desc "Tail symfony log according to environment, optional -S log_name=<log name without .log>"
        task :default, :roles => :app, :except => { :no_release => true } do
            set :lines, '50' unless exists?(:lines)
            set :log_name, stage unless exists?(:log_name)
            log = "#{log_name}.log"
            run "#{try_sudo} tail -n #{lines} -f #{shared_path}/#{log_path}/#{log}" do |channel, stream, data|
              trap("INT") { puts 'Interrupted'; exit 0; }
              puts
              puts "#{channel[:host]}: #{data}"
              break if stream == :err
            end
        end

    end

    namespace :parameters do

        after "deploy:setup", "symfony:parameters:setup"

        set(:parameters_file) {shared_path + "/app/config/parameters.yml"}

        desc "Setup parameters: upload parameters.yml and rename database"
        task :setup, :roles => :app, :except => { :no_release => true } do
            upload
            rename_database_name
            rename_web_host
        end

        desc "Upload current parameters.yml to shared folder"
        task :upload, :roles => :app, :except => { :no_release => true } do
            origin_file = "app/config/parameters.yml"

            try_sudo "mkdir -p #{File.dirname(parameters_file)}"
            top.upload(origin_file, parameters_file)
        end

        desc "Rename database_name in app/config/parameters.yml. Application name will be used (%branch.stage.env%) unless -S database_name=%database_name% argument is provided"
        task :rename_database_name, :roles => :app, :except => { :no_release => true } do
          unless exists?(:database_name)
            database_name = "#{host}_#{stage[0..1]}".gsub(/\./, '_')
            set :database_name, database_name
          end
            puts "--> Database name in ".yellow + "parameters.yml".bold.yellow + " will be set to ".yellow + "#{database_name}".red
            run "sed -r -i 's/^(\\s+database_name:\\s+).+$/\\1#{database_name}/g' #{parameters_file}"
        end

        desc "Rename web_host in app/config/parameters.yml"
        task :rename_web_host, :roles => :app, :except => { :no_release => true } do
            set :web_host, application_url.gsub('/', '\/')
            puts "--> Web host in ".yellow + "parameters.yml".bold.yellow + " will be set to ".yellow + "#{application_url}".red
            run "sed -r -i 's/^(\\s+web_host:\\s+).+$/\\1#{web_host}/g' #{parameters_file}"
        end
    end

    namespace :auth do
        namespace :client do

            def create_auth_client(secret, public_id)
                capture console_command("lighthouse:auth:client:create #{secret} #{public_id}")
            end

            desc "Create API client, required: -S public_id=<..> -S secret=<..>"
            task :create, :roles => :app, :except => { :no_release => true } do
                puts "--> Creating client"
                raise "secret should be provided by -S secret=.." unless exists?(:secret)
                raise "public_id should be provided by -S public_id=.." unless exists?(:public_id)
                puts create_auth_client(secret, public_id)
            end

            desc "Create default API clients provided in :api_clients variable"
            task :create_default, :roles => :app, :except => { :no_release => true } do
                puts "--> Creating default clients"
                api_clients.each do |api_client|
                    puts "--> Creating client " + api_client[:public_id].green
                    puts create_auth_client(api_client[:secret], api_client[:public_id])
                end
            end

            desc "List API clients"
            task :list, :roles => :app, :except => { :no_release => true } do
                puts "--> List auth clients"
                puts capture console_command("lighthouse:auth:client:list")
            end
        end
    end

    namespace :user do

        def create_api_user(email, password, role, custom_project_name)
            capture console_command("lighthouse:user:create #{email} #{password} #{role} --customProjectName=#{custom_project_name}")
        end

        desc "Create user, required: -S email=<..> -S userpass=<..>, optional: -S userrole=<..> (administrator by default) -S customProjectName=<..>"
        task :create, :roles => :app, :except => { :no_release => true } do
            puts "--> Creating user"
            raise "email should be provided by -S email=.." unless exists?(:email)
            raise "userpass should be provided by -S userpass=.." unless exists?(:userpass)
            set :userrole, "" unless exists?(:userrole)
            set :customProjectName, "" unless exists?(:customProjectName)
            puts create_api_user(email, userpass, userrole, custom_project_name)
            capifony_puts_ok
        end

        desc "Create default users"
        task :create_default, :roles => :app, :except => { :no_release => true } do
            transaction do
              on_rollback do
                  puts "--> Failed to create user".red
              end
              api_users.each do |api_user|
                  puts "--> Creating user " + api_user[:email].green
                  puts create_api_user(api_user[:email], api_user[:userpass], api_user[:userrole], "")
              end
            end
        end
    end

    namespace :env do
        desc "Recreate db, create default clients and users"
        task :init, :roles => :app, :except => { :no_release => true } do
            doctrine.mongodb.schema.recreate
            auth.client.create_default
            user.create_default
        end
    end

    namespace :import do
        desc "Import products catalog from file, required: -S file=<..>, -S projectId=<..>"
        task :products do
            raise "Path to xml file should be provided by -S file=.." unless exists?(:file)
            raise "project should be provided by -S projectId=.." unless exists?(:projectId)

            set :xml_file_path, file
            set :remote_temp_file_path, "/tmp/#{host}_#{stage}_xml_import.xml"

            run "#{try_sudo} rm #{remote_temp_file_path}" if File.exists?(remote_temp_file_path)

            puts "--> Uploading xml file".yellow
            top.upload(xml_file_path, remote_temp_file_path)

            puts "--> Import products".yellow
            stream console_command("lighthouse:import:products #{remote_temp_file_path} --project=#{projectId}")
            capifony_puts_ok
        end

        namespace :sales do
            desc "Upload and import sales xml, required: -S projectId=<..>"
            task :local, :roles => :app, :except => { :no_release => true } do
                raise "Path to xml file should be provided by -S file=.." unless exists?(:file)
                raise "project should be provided by -S projectId=.." unless exists?(:projectId)

                set :xml_file_path, file
                set :remote_temp_file_path, "/tmp/#{host}_#{stage}_xml_import_sales.xml"

                run "#{try_sudo} rm #{remote_temp_file_path}" if File.exists?(remote_temp_file_path)

                puts "--> Uploading xml file".yellow
                top.upload(xml_file_path, remote_temp_file_path)

                puts "--> Import products".yellow
                stream console_command("lighthouse:import:sales:local #{remote_temp_file_path} --project=#{projectId}")
                capifony_puts_ok
            end
        end

        namespace :kesko do
            desc "Import Kesko products"
            task :default, :roles => :app, :except => { :no_release => true } do
                set :fixtures, "src/Lighthouse/CoreBundle/DataFixtures/Kesko"
                puts "--> Create users and stores".yellow
                stream console_command("doctrine:mongodb:fixtures:load --fixtures=#{fixtures}"), :once => true
                capifony_puts_ok
                puts "--> Import products".yellow
                stream console_command("lighthouse:import:products fixtures/kesko-goods.xml"), :once => true
                capifony_puts_ok
            end
        end

        task :amn, :roles => :app, :except => { :no_release => true } do
            check_app_deployed
            puts "--> Import AMN".yellow
            stream "sh -c 'cd #{latest_release} && ./import_amn.sh #{symfony_env_prod}'"
            capifony_puts_ok
        end
    end

    task :import_xml do
        puts "--> This task is deprecated. Use symfony:import:products instead"
        symfony.import.products
    end

    namespace :products do
        desc "Recalculate products metrics"
        task :recalculate_metrics, :roles => :app, :except => { :no_release => true } do
            stream console_command("lighthouse:products:recalculate_metrics"), :once => true
        end
    end

    namespace :reports do
        desc "Recalculate reports data"
        task :recalculate, :roles => :app, :except => { :no_release => true } do
            stream console_command("lighthouse:reports:recalculate"), :once => true
        end
    end

    namespace :openstack do
        namespace :container do

            after "deploy:update", "symfony:openstack:container:create"

            before "deploy:remove:go" do
                begin
                    symfony.openstack.container.delete
                rescue
                    puts "✘\n".red
                end
            end

            desc "Create OpenStack Storage container"
            task :create, :roles => :app, :except => { :no_release => true } do
                puts "--> Create storage container"
                stream console_command("openstack:container:create"), :once => true
                capifony_puts_ok
            end

            desc "Delete OpenStack Storage container"
            task :delete, :roles => :app, :except => { :no_release => true } do
                puts "--> Delete storage container".yellow
                stream console_command("openstack:container:delete"), :once => true
                capifony_puts_ok
            end
        end
    end

    namespace :apc do

        desc "Clear apc cache"
        task :clear do
            capifony_pretty_print "--> Clear apc cache"
            run console_command("apc:clear"), :once => true
            capifony_puts_ok
        end
    end

end