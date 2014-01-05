set :stage, :staging
set :deploy_to, '/home/waq/web/staging/'

server '33.33.33.10', user: 'waq', roles: %w{web app db}