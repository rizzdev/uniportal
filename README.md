Uni Portal
=======================

Introduction
------------
This application is designed as a dyanmic interface which allows the creating, modifying, and removal of Uni Portals

Installation
------------

Initial Requirements (recommended)
----------------------------
    PHP-FPM
    NGINX
    MYSQL

Updating doctrine
--------------------
Running the following commands regenerates the database entities completely:

    php vendor/bin/doctrine-module orm:clear-cache:metadata
    php vendor/bin/doctrine-module orm:clear-cache:query
    php vendor/bin/doctrine-module orm:clear-cache:result
    php vendor/bin/doctrine-module orm:convert:mapping --namespace='Entity\' --force --from-database annotation /ssd/app/model
    php vendor/bin/doctrine-module orm:generate:entities  /ssd/app/model
    php vendor/bin/doctrine-module orm:generate:proxies

Web Server Setup
----------------

    server {
            listen 80;
            listen [::]:80 default ipv6only=on;

            server_name localhost;
            root /ssd/app/public;
            index index.php;
            try_files $uri $uri/ @notfile;

            location @notfile {
                    rewrite ^(.*) /index.php last;
            }

            location ~ \.php$ {
                    fastcgi_pass unix:/var/run/php5-fpm.sock;
                    fastcgi_index /index.php;
                    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                    include /etc/nginx/fastcgi_params;
            }
    }
