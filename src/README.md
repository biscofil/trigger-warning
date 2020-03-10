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
