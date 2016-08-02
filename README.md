# wifistatus

Using [raspberry pi](https://www.raspberrypi.org/)'s as probes we collect response times from various websites that are critical to us. Originally we were testing only for wifi performance, but this works just as well as wired probes

## The Probes
The probes consist of a two raspberry pi units (each on different wifi networks). They use the `collectwifidata.php` script to store the data in a database (which can be setup using the SQL script). Using `cron` the script runs every minute.

## Config
You will need to configure a `db-config.php` file that contains the following:
```
<?php
DB::$host = '';
DB::$user = '';
DB::$password = '';
DB::$dbName = '';
```
Make sure to fill in the appropriate values.
