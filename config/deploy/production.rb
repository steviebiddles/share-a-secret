set :stage, :production
set :application, "secret.octaldynamics.com"
set :host, "secret.octaldynamics.com"
set :deploy_to, -> { "/var/www/#{fetch(:application)}/web" }
set :tmp_dir, "/var/www/#{fetch(:application)}/tmp"
set :keep_releases, 2
set :branch, :master

SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"

# Simple Role Syntax
# ==================
role :web, %w{smcauley_od_secret_ssh@webserver1.octaldynamics.com}

# Extended Server Syntax
# ======================
server 'webserver1.octaldynamics.com', user: 'smcauley_od_secret_ssh', roles: %w{web}, primary: true
