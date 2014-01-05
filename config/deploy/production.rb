set :stage, :production
set :deploy_to, '/home/waq/web/production/'

server '192.241.243.186', user: 'waq', roles: %w{web app db}