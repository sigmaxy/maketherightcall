# Installation
  1. Checkout the lastest code into webroot folder 
  2. Copy sites/default to sites/local
  3. Modify sites/local/files permission 
  ```console
  chmod -R 777
  ```
  4. create database mtrc and import data
  ```console
  drop database if exists mtrc;
  create database mtrc;
  
  mysql -u root -p mtrc < config/mtrc.sql
  ```
  5. modify sites/local/setting.php
  database connection and other parameters settings
  6. modify sites/sites.php
  rewrite local hosts file to point local URL host
  7 clear cache and rebuild
  ```console
  vendor/bin/drupal --uri=local.mtrc.sigmaxu.com update:execute system
  vendor/bin/drupal --uri=local.mtrc.sigmaxu.com cr all
  ```
  

# Download Staging DB
  ```console
  mysqldump -h db-stag-mtrc.sigmaxu.com -u root -p mtrc > mtrc_stag.sql
  ```
  
# Download Production DB
  ```console
  mysqldump -h db-stag-mtrc.sigmaxu.com -u root -p mtrc > mtrc_prod.sql
  ```