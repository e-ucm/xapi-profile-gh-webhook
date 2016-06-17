# GitHub webhook handler to process xAPI profiles

If you host your xAPI profile in a GitHub repository, this project helps you with the publication of the JSON-LD file and the generation of an alternative and human redable HTML version.

## Requirements

* PHP >= 5.3
* [Composer](http://getcomposer.org)

## Installation

1. Clone the repository in ```$INSTALL_DIR```
2. Create a configuration file (```$INSTALL_DIR/config.php```). Use file ```$INSTALL_DIR/config-sample.php``` as a template.
3. Configure your web server to serve ```$INSTALL_DIR/public_html```
4. Setup a [webhook handler](https://developer.github.com/webhooks/creating/) for your repository, and make sure that you put the value of ```GITHUB_WEBHOOK_SECRET``` configuration parameter in the *Secret* parameter of the new webhook.

### CRON

The GitHub webhook handler just creates a file to represent a future task to do. Although nowadays the profile generation is quite lightweight, the webhook handler its designed to deal with the 30s execution time limitation of GitHub webhooks. So you have to configure a cron to actually do the publication. The script ```$INSTALL_DIR/scripts/cron.sh``` is prepared to be run as a cron job.

## Ubuntu 12.04 + Apache 2.2.X + PHP 5.3 Installation instructions

1. Clone the repository in ```/opt/xapi-profile-gh-webhook```
```
user@example:~# cd /opt
user@example:/opt# git clone https://github.com/e-ucm/xapi-profile-gh-webhook.git
```
2. Create a configuration file 
```
user@example:/opt# cd xapi-profile-gh-webhook
user@example:/opt/xapi-profile-gh-webhook# cp config-sample.php config.php
# Edit config.php and give proper values to the configuration parameters
```
3. Add the following directives to your apache config:
``` 
Alias /github "/opt/xapi-profile-gh-webhook/public_html"
ScriptAlias /github/webhook "/opt/xapi-profile-gh-webhook/public_html/index.php"
<Directory "/opt/xapi-profile-gh-webhook/public_html">
    AllowOverride FileInfo Options
    Order allow,deny
    Allow from all
</Directory>
```
4. Configure a cron job to run every 10 minutes
```
user@example:/opt/xapi-profile-gh-webhook# echo "*/10 * * * *     root    /opt/xapi-profile-gh-webhook/scripts/cron.sh" > /etc/cron.d/xapi-profile-publication
```
