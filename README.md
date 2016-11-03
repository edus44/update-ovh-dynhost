PHP cron task to sync OVH ddns (DynHost)
======

##Clone this repo
```
cd
git clone https://github.com/edus44/update-ovh-dynhost.git ddns
```


##Configure dns
 - Reset your `DNS zone` and left NS records only
 - In `DynHost` tab add the domain to manage (left empty for root and subdomains, in case it doesn't allow empty, create with dummy subdomain, then edit and remove it)
 - Go to `Manage Access` and create and Identifier to manage previous domains

##Configure sync task
 - Edit the `$domain` variable in `update.php` file
 - You will need PHP 5 and php-curl extension (debian based: `sudo apt-get install php5-cli php5-curl`)
 - Edit your crontab (`crontab -e`) and add this line (change the path to file):  
    `*/5 * * * * /home/pi/ddns/update.php` 


