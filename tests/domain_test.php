#!/usr/bin/env php
<?php

/**
 * @file
 *
 * Namecheap API test script that operates on Sandbox API.
 *
 * Arguments required:
 *   - Namecheap API user
 *   - Namecheap API key
 *   - Domain to register (string 'random' for random domain)
 */

require_once('../namecheap.php');

array_shift($argv);
if (count($argv) != 3) {
  file_put_contents('php://stderr', "Need ApiUser, ApiKey and DomainName parameters provided, respectively.\n");
  exit(1);
}

// Get API class
$namecheap_params = array(
  'api_user' => $argv[0],
  'api_key' => $argv[1]
);
$namecheap = Namecheap::init($namecheap_params, TRUE);

// Generate random domain if needed
$domain = $argv[2];
if ($domain == 'random') {
  $base = 'abcdefghjkmnpqrstwxyz';
  $max = strlen($base) - 1;
  $domain = '';
  mt_srand(microtime(TRUE) * 1000000);
  while (strlen($domain) < 17) {
    $domain .= $base{mt_rand(0, $max)};
  }
  $domain .= '.com';
}
echo "Working with $domain.\t\t\t\t(t = 0.000s)\n";

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

// Register domain
$domain_reg_data = array(
  'Years' => '1',
  'AuxBillingFirstName' => 'John',
  'AuxBillingLastName' => 'Smith',
  'AuxBillingAddress1' => '8939%20S.cross%20Blvd',
  'AuxBillingStateProvince' => 'CA',
  'AuxBillingPostalCode' => '90045',
  'AuxBillingCountry' => 'US',
  'AuxBillingPhone' => '+1.6613102107',
  'AuxBillingEmailAddress' => 'namecheap@example.com',
  'AuxBillingOrganizationName' => 'NC',
  'AuxBillingCity' => 'CA',
  'TechFirstName' => 'John',
  'TechLastName' => 'Smith',
  'TechAddress1' => '8939%20S.cross%20Blvd',
  'TechStateProvince' => 'CA',
  'TechPostalCode' => '90045',
  'TechCountry' => 'US',
  'TechPhone' => '+1.6613102107',
  'TechEmailAddress' => 'namecheap@example.com',
  'TechOrganizationName' => 'NC',
  'TechCity' => 'CA',
  'AdminFirstName' => 'John',
  'AdminLastName' => 'Smith',
  'AdminAddress1' => '8939%20S.cross%20Blvd',
  'AdminStateProvince' => 'CA',
  'AdminPostalCode' => '90045',
  'AdminCountry' => 'US',
  'AdminPhone' => '+1.6613102107',
  'AdminEmailAddress' => 'namecheap@example.com',
  'AdminOrganizationName' => 'NC',
  'AdminCity' => 'CA',
  'RegistrantFirstName' => 'John',
  'RegistrantLastName' => 'Smith',
  'RegistrantAddress1' => '8939%20S.cross%20Blvd',
  'RegistrantStateProvince' => 'CS',
  'RegistrantPostalCode' => '90045',
  'RegistrantCountry' => 'US',
  'RegistrantPhone' => '+1.6613102107',
  'RegistrantEmailAddress' => 'namecheap@example.com',
  'RegistrantOrganizationName' => 'NC',
  'RegistrantCity' => 'CA',
);

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
