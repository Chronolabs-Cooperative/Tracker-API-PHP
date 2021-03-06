## Chronolabs Cooperative presents

# Torrent Tracker API ~ http://tracker.snails.email

### Author: Simon Antony Roberts <simon@snails.email>

This is a torrent tracker for Apache2 or any PHP complaint services; you can often run this tracker which will allow for automounting for torrents, we hope to take this out to a point where it will download the torrent and mount a seed for them as well!

## Mod Rewrite Short URL

You will need to have this in API_ROOT_PATH/.htaccess

    php_value memory_limit 288M
    php_value upload_max_filesize 1456M
    php_value post_max_size 1456M
    php_value error_reporting 1
    php_value display_errors 1

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^announce?(.*?)$ announce.php?$1 [L,NC,QSA]				
    RewriteRule ^scrape?(.*?)$ scrape.php?$1 [L,NC,QSA]	
    RewriteRule ^announce$ announce.php [L,NC,QSA]				
    RewriteRule ^scrape$ scrape.php [L,NC,QSA]	
    RewriteRule ^([a-z0-9]{2})/(.*?)/callback.api$ callback.php?version=$1&mode=$2 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(peers|seeds|files|trackers|network)/(.*?)/(raw|benc|json|serial|xml).api?(.*?)$ index.php?version=$1&mode=$2&clause=$3&state=&output=$4&$5 [L]	
    RewriteRule ^([a-z0-9]{2})/(peers|seeds|files|trackers|network)/(.*?)/(raw|benc|json|serial|xml).api$ index.php?version=$1&mode=$2&clause=$3&state=&output=$4 [L]	
    RewriteRule ^([a-z0-9]{2})/(torrents)/(raw|benc|json|serial|xml).api?(.*?)$ index.php?version=$1&mode=$2&clause=&state=&output=$3&$4 [L]
    RewriteRule ^([a-z0-9]{2})/(torrents)/(raw|benc|json|serial|xml).api$ index.php?version=$1&mode=$2&clause=&state=&output=$3& [L]
    RewriteRule ^([a-z0-9]{2})/(download)/(.*?).(torrent)$ download.php?version=$1&mode=$2&clause=&state=&output=$3& [L]
    
## Installation

Copy the PHP file to your hosting spot, then run the install with your browser!