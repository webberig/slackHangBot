server {
    listen  80;

    root {{ doc_root }}/web;
    index /app.php;

    server_name {{ servername }} {{ ansible_eth1.ipv4.address }};

    location / {
        try_files $uri @app;
    }

    location @app {
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME /vagrant/web/app.php;
        fastcgi_param SYMFONY_ENV 'dev';
        fastcgi_param SYMFONY_DEBUG 1;
    }

    location ~ /\. {
        access_log off;
        log_not_found off;
        deny all;
    }
}
