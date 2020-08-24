Trigger Warning
===============

[![pipeline status](https://gitlab.com/biscofil/trigger_warning/badges/master/pipeline.svg)](https://gitlab.com/biscofil/trigger_warning/-/commits/master)

[![coverage report](https://gitlab.com/biscofil/trigger_warning/badges/master/coverage.svg)](https://gitlab.com/biscofil/trigger_warning/-/commits/master)


```
<VirtualHost *:80>

        ServerName triggerwarning.local
        ServerAlias www.triggerwarning.local

        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/triggerwarning_server/src/public

        ErrorLog /var/www/triggerwarning_server/error.log
        CustomLog /var/www/triggerwarning_server/access.log combined

        <Directory /var/www/triggerwarning_server/src/public>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
                Include /var/www/triggerwarning_server/src/public/.htaccess
                Order allow,deny
                allow from all
        </Directory>

</VirtualHost>
```

Docker configuration change (Dockerfile)
```
docker build -t registry.gitlab.com/biscofil/trigger_warning .
docker push registry.gitlab.com/biscofil/trigger_warning
```
