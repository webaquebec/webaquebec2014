set :application, 'qcnum'
set :repo_url, 'git@github.com:webaquebec/webaquebec2014.git'

set :scm, :git

set :linked_files, %w{wordpress/wp-config.php}
set :linked_dirs, %w{wordpress/wp-content/uploads}

set :keep_releases, 5

namespace :deploy do

  desc 'Restart application'
  task :restart do
    on roles(:app), in: :sequence, wait: 5 do
      # Your restart mechanism here, for example:
      # execute :touch, release_path.join('tmp/restart.txt')
    end
  end

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      # Here we can do anything such as:
      # within release_path do
      #   execute :rake, 'cache:clear'
      # end
    end
  end

  after :finishing, 'deploy:cleanup'

end
