set :application, "page_navigation"
set :repository, "file://#{File.expand_path('.')}"

set :domain, 'localhost'
server "#{domain}", :app, :web, :db, :primary => true

set :copy_exclude, [".rvmrc", ".gitignore", ".git", ".DS_Store", "Capfile", "Gemfile", "Gemfile.lock"]
set :branch, "master"
set :git_shallow_clone, 1

set :scm, :git
set :spinner, false
set :use_sudo, false
set :deploy_via, :copy
set :copy_strategy, :export
set :deploy_to, "/opt/local/apache2/htdocs/wordpress/wp-content/plugins/cap/#{application}"

#ssh_options[:verbose] = :debug
#set :user, "www"

after "deploy", "deploy:cleanup"

namespace :deploy do

  desc "Show environment"
  task :debug_env do
    run "env"
  end
  #
  # Neutralize cap tasks that make no sense
  # See:
  #  http://wiki.capify.org/article/Neutralise_Capistrano_Default_Methods
  #
  [:restart, :start, :stop].each do |default_task|
    task default_task do
      # Do nothing
      puts "Blanked the '#{default_task.to_s}' task"
    end
  end

  #
  # Stolen from MMX
  #
  desc "Deploy all files to app server"
  task :deploy, :except => { :no_release => true } do
    update_code
    symlink
    cleanup
  end

  # stolen from http://www.paperplanes.de/archives/2007/5/26/deploying_in_a_chroot_environment/
  # I, for one, welcome MMX as the new Cap Overlord
  #
  desc "Overwriting symlink task to replace absolute links with relative ones"
  task :symlink, :except => { :no_release => true } do
    on_rollback {
      run "cd #{deploy_to} && ln -nfs releases/#{File.basename previous_release} current"
    }

    run "cd #{deploy_to} && ln -nfs releases/#{File.basename current_release} current"

    run <<-CMD
      cd #{deploy_to}/current &&
      rm -rf tmp log public config
    CMD
  end
end
