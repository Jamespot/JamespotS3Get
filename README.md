# JamespotS3Get
Jamespot PHP Script to get files from S3 backup


# Install Application

```bash
composer install
```

# Configure with your values

```bash
cp config.sample.php config.php

```
Edit config.php according to your production values



# Use the application

```bash
php getFileData.php xx  > /tmp/xx.binary

```
This will pull the data of file 'xx' out of your S3 repo and store it in /tmp/xx


## Notice
openstack requires php >=7.3 
