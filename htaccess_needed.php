# prevent directory listings
Options -Indexes
# follow symbolic links
Options FollowSymlinks
RewriteEngine on

RewriteCond %{REQUEST_URI} ^/admin/$
RewriteRule ^(admin)/$ /$1 [R=301,L]
RewriteCond %{REQUEST_URI} ^/admin
RewriteRule ^admin(/.+)?$ /backend/web/$1 [L,PT]

RewriteCond %{REQUEST_URI} ^.*$
RewriteRule ^(.*)$ /frontend/web/$1
