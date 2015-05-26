require 'erb'

namespace :webfront do

    task :default do
        config
        build
        symlink
    end

    desc "Build webfront app"
    task :build, :roles => :app, :except => { :no_release => true } do
        capifony_pretty_print "--> NPM build"
        flag = fetch(:npm_flag, '')
        run "cd #{latest_release} && npm install #{flag} && npm run build"
        capifony_puts_ok
    end

    desc "Symlink web folder"
    task :symlink, :roles => :app, :except => { :no_release => true } do
        run "cd #{latest_release} && ln -snf build web"
    end

    desc "Setup config"
    task :config, :roles => :app, :except => { :no_release => true } do
        capifony_pretty_print "--> Setup config.js"
        template = File.read(File.join('src', 'config.js.template'))

        api_url = fetch(:api_url, "#{host}.#{stage}.api.sellpoint.io")
        google_analytics_id = fetch(:google_analytics_id, '')

        result = ERB.new(template).result(binding)

        put result, "#{latest_release}/src/config.js", :mode => 0644
        capifony_puts_ok
    end

end

def capifony_pretty_print(msg)
    if logger.level == Capistrano::Logger::IMPORTANT
        pretty_errors

        msg = msg.slice(0, 57)
        msg << '.' * (60 - msg.size)
        print msg
    else
        puts msg.green
    end
end

def capifony_puts_ok
    if logger.level == Capistrano::Logger::IMPORTANT && !$error
        puts '✔'.green
    end

    $error = false
end

def pretty_errors
    if !$pretty_errors_defined
        $pretty_errors_defined = true

        class << $stderr
            @@firstLine = true
            alias _write write

            def write(s)
                if @@firstLine
                    s = '✘' << "\n" << s
                    @@firstLine = false
                end

                _write(s.red)
                $error = true
            end
        end
    end
end
