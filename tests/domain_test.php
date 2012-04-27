#!/usr/bin/env php
<?php

/**
 * @file
 *
 * Namecheap API domain test script that operates on Sandbox API.
 *
 * Arguments required:
 *   - Namecheap API user
 *   - Namecheap API key
 *   - Domain to attach to SSL certificate to (string 'random' for random domain)
 */

require_once('common.php');

// Check if the domain is already registered
if (!$namecheap->domainsCheck($domain)) {
  if ($namecheap->errorCode == -1) {
    file_put_contents('php://stderr', "Namecheap API down.\n");
  } else if ($namecheap->errorCode == -2) {
    file_put_contents('php://stderr', "Error: " . $namecheap->Error . "\n");
  } else {
    file_put_contents('php://stderr', "$domain is already taken.\n");
  }
  exit(1);
} else {
  $time = $namecheap->getExecutionTime();
  echo "$domain is available.\t\t\t\t";
  echo "(t = " . $time['RealTime'] . "s)\n";
}

if (!$namecheap->domainsCreate($domain, $domain_reg_data)) {
  file_put_contents('php://stderr', "$domain failed to register with error: " . $namecheap->Error . ".\n");
  exit(1);
} else {
  $time = $namecheap->getExecutionTime();
  echo "$domain registered.\t\t\t\t";
  echo "(t = " . $time['RealTime'] . "s)\n";
}

// Get domain info
if ($domain_info = $namecheap->domainsGetInfo($domain)) {
  $time = $namecheap->getExecutionTime();
  echo "$domain " . ($argv[0] == $domain_info['User'] ? 'is' : 'is *not*') . " owned by us.\t\t\t\t";
  echo "(t = " . $time['RealTime'] . "s)\n";
} else {
  file_put_contents('php://stderr', "$domain info query failed.\n");
  exit(1);
}

// Renew domain if needed
$days_renew = 10;
$expiry_date = strptime($domain_info['Expires'], '%m/%d/%Y');

$expiry_timestamp = mktime(
  $expiry_date['tm_hour'],
  $expiry_date['tm_min'],
  $expiry_date['tm_sec'],
  1,
  $expiry_date['tm_yday'] + 1,
  $expiry_date['tm_year'] + 1900
);

if ($expiry_timestamp > time()) {
  $days_left = floor(($expiry_timestamp - time()) / (60 * 60 * 24));
  if ($days_left < $days_renew) {
    if ($namecheap->domainsRenew($domain)) {
      echo "$domain renewed.\t\t\t\t";
      echo "(t = " . $time['RealTime'] . "s)\n";
    } else {
      file_put_contents('php://stderr', "$domain renewal failed.\n");
      exit(1);
    }
  } else {
    echo "$domain expires in $days_left days.\n";
  }
}
