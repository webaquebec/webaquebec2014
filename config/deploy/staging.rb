set :stage, :staging
set :deploy_to, '/home/waq/web/staging/'

server '192.241.243.186', user: 'waq', roles: %w{web app db}