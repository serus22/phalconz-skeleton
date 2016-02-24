# phalconz-skeleton
Skeleton application for phalconz (like ZF2)

requirements: [mongodb, phalconphp, php-mongodb, composer, livereload]

git clone https://github.com/serus22/phalconz-skeleton
composer install

cd front & npm install & gulp 


# example working nginx config

```
server {
    listen 80;
    root /<yourPath>;
    index index.php index.html index.htm;

    server_name <domain>;

    location / {
         try_files $uri $uri/ /index.php?_url=$uri&$args;
    }

    location ~ \.php {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```