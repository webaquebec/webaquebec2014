set :stage, :production
set :deploy_to, '/home3/webaqueb/public_html/production/'
set :tmp_dir, '/home3/webaqueb/tmp'

server '66.147.244.160', user: 'webaqueb', roles: %w{web app db}