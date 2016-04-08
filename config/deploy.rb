set :repo_url, 'git@bitbucket.org:steviebiddles/secret.git'
set :scm, :git
set :format, :pretty
set :log_level, :info
set :use_sudo, false
set :deploy_via, :remote_cache
set :composer_install_flags, "--no-dev --verbose --optimize-autoloader --prefer-dist --no-interaction"
set :composer_dump_autoload_flags, "--optimize"
set :interactive_mode, true
set :keep_releases, 3

set :symfony_env, "prod"

set :ssh_options, {
   keys: %w(~/.ssh/id_rsa),
   forward_agent: true,
}

set :linked_files, fetch(:linked_files, []).push('app/config/parameters.yml')
set :linked_dirs, fetch(:linked_dirs, []).push('vendor')

before "symfony:cache:warmup", "symfony:cache:clear"
after  "deploy:updated", "symfony:assets:install"
#before "deploy:finished", "deploy:restart_php"

# add task to create sitemap xml

namespace :deploy do

  before :starting, "composer:install_executable"

  desc "Restart PHP5-FPM (requires sudo access to /usr/sbin/service php5-fpm restart)"
  task :restart_php do
    on roles(:web) do
      execute "sudo service php5-fpm restart"
    end
  end

  desc "Check if agent forwarding is working"
  task :forwarding do
    on roles(:all) do |host|
      if test("env | grep SSH_AUTH_SOCK")
        info "Agent forwarding is up to #{host}"
      else
        error "Agent forwarding is NOT up to #{host}"
      end
    end
  end

  desc "Check that we can access everything"
  task :check_write_permissions do
    on roles(:web) do |host|
      if test("[ -w #{fetch(:deploy_to)} ]")
        info "#{fetch(:deploy_to)} is writable on #{host}"
      else
        error "#{fetch(:deploy_to)} is not writable on #{host}"
      end
    end
  end

  namespace :parameters do

    desc "Push parameters to a stage"
    task :push do
      on roles(:web) do
        upload! "app/config/parameters.yml.#{fetch(:stage)}", "#{shared_path}/app/config/parameters.yml"
      end
    end

  end

end