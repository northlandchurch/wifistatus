<?php
include 'meekrodb.2.3.class.php';
include 'db-config.php';

// check responsetime for a webbserver
function pingDomain($domain){
  $starttime = microtime(true);
  // supress error messages with @
  $file      = @fsockopen($domain, 80, $errno, $errstr, 10);
  $stoptime  = microtime(true);
  $status    = 0;

  if (!$file){
    $status = -1;  // Site is down
  }
  else{
    fclose($file);
    $status = ($stoptime - $starttime) * 1000;
    $status = floor($status);
  }
  return $status;
}

$clientprobe = gethostname();
$coreswitch = pingDomain('10.254.0.1');
$google = pingDomain('google.com');
$pco = pingDomain('planningcenteronline.com');
$slack = pingDomain('northlandchurch.slack.com');
$northland = pingDomain('northlandchurch.net');

DB::insert('wifidata', array(
  'clientprobe' => $clientprobe,
  'coreswitch' => $coreswitch,
  'google' => $google,
  'label' => 'Green Room - Team',
  'pco' => $pco,
  'slack' => $slack,
  'northland' => $northland
));
