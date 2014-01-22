# Web à Québec 2014

## Quick start

    mysql -u root -e "drop database waq2014;create database waq2014;"
    mysql -u root waq2014 < dump.sql
    sudo php -S localhost:80 -t wordpress
    open http://waq2014.lvh.me

User `admin` password is `qwe123` in development.

Please change it when deploying :-)

## Deployment

First setup database from a dump ex. :

    replace "http://waq2014.lvh.me" "http://staging.webaquebec.org" -- dump.sql
    mysql -u waq_staging --password=X waq_staging < dump.sql

Then deploy updates with capistrano

    bundle install
    cap staging deploy

## Credits

Thanks to [Libéo](http://libeo.com) team for sponsoring 2014 website. 

Thanks [@j15e](http://j15e.com) for writing a README and managing deployment.

See all [contributors](https://github.com/webaquebec/webaquebec2014/graphs/contributors)
