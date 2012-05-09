<?php

/**
 * @file
 *
 * Common data and functionality for the tests.
 */

require_once('../namecheap.php');

array_shift($argv);
if (count($argv) < 3) {
  if (!defined('NO_DOMAIN')) {
    file_put_contents('php://stderr', "Need ApiUser, ApiKey and DomainName parameters provided, respectively.\n");
  } else {
    file_put_contents('php://stderr', "Need ApiUser, ApiKey and FundsTreshold parameters provided, respectively.\n");
  }
  exit(1);
}

// Get API class
$namecheap_params = array(
  'api_user' => $argv[0],
  'api_key' => $argv[1]
);
$namecheap = Namecheap::init($namecheap_params, TRUE);

$domain = $argv[2];
if (!defined('NO_DOMAIN')) {
  // Generate random domain if needed
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
}

// Domain registration data
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

// SSL activation data except CSR and CertificateID
$ssl_act_data = $domain_reg_data;
$ssl_act_data['ApproverEmail'] = 'admin@' . $domain;
$ssl_act_data['WebServerType'] = 'apacheopenssl';
$ssl_act_data['AdminJobTitle'] = 'Administratos';
$ssl_act_data['TechJobTitle'] = 'Technician';
$ssl_act_data['BillingJobTitle'] = 'Biller';
$ssl_act_data['BillingFirstName'] = 'John';
$ssl_act_data['BillingLastName'] = 'Smith';
$ssl_act_data['BillingAddress1'] = '8939 S.cross Blvd';
$ssl_act_data['BillingStateProvince'] = 'CA';
$ssl_act_data['BillingPostalCode'] = '90045';
$ssl_act_data['BillingCountry'] = 'US';
$ssl_act_data['BillingPhone'] = '+1.6613102107';
$ssl_act_data['BillingEmailAddress'] = 'namecheap@example.com';
$ssl_act_data['BillingOrganizationName'] = 'NC';
$ssl_act_data['BillingCity'] = 'CA';
