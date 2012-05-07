#!/usr/bin/env php
<?php

/**
 * @file
 *
 * Namecheap API SSL test script that operates on Sandbox API.
 *
 * Arguments required:
 *   - Namecheap API user
 *   - Namecheap API key
 *   - Domain to register (string 'random' for random domain)
 */

require_once('common.php');

// Check if the domain is already registered
if (!$namecheap->domainsCheck($domain)) {
  $time = $namecheap->getExecutionTime();
  echo "$domain is already registered.\t\t\t\t";
  echo "(t = " . $time['RealTime'] . "s)\n";
} else {
  $time = $namecheap->getExecutionTime();
  echo "$domain is available.\t\t\t\t";
  echo "(t = " . $time['RealTime'] . "s)\n";
  // Register domain
  if (!$namecheap->domainsCreate($domain, $domain_reg_data)) {
    file_put_contents('php://stderr', "$domain failed to register with error: " . $namecheap->Error . ".\n");
    exit(1);
  } else {
    $time = $namecheap->getExecutionTime();
    echo "$domain registered.\t\t\t\t";
    echo "(t = " . $time['RealTime'] . "s)\n";
  }
}

// Create SSL certificate
if ($ssl = $namecheap->sslCreate()) {
  $time = $namecheap->getExecutionTime();
  echo "SSL certificate ID #{$ssl['CertificateId']} created.\t\t\t\t";
  echo "(t = " . $time['RealTime'] . "s)\n";
} else {
  file_put_contents('php://stderr', "Failed to purchase SSL with error: " . $namecheap->Error . ".\n");
  exit(1);
}

// Generate CSR
$openssl_command = 'cd /tmp && openssl req -new -nodes -keyout myserver.key -newkey rsa:2048 -subj "/C=US/ST=NY/L=Somewhere/organizationName=MyOrg/OU=MyDept/CN=' . $domain . '" 2>1';
exec($openssl_command, $openssl_output);
$csr = implode("\n", $openssl_output);

// Get approver mail and use namecheap named one
$mails = $namecheap->sslGetApproverEmailList($domain);
$namecheap_mail = '';
foreach ($mails as $mail) {
  if (strpos($mail, 'namecheap@') !== FALSE) {
    $namecheap_mail = $mail;
    break;
  }
}

if ($namecheap_mail) {
  // Activate SSL certificate
  $ssl_act_data['csr'] = $csr;
  $ssl_act_data['CertificateID'] = $ssl['CertificateId'];
  $ssl_act_data['AdminEmailAddress'] = $ssl_act_data['ApproverEmail'] = $namecheap_mail;

  if ($ssl_act = $namecheap->sslActivate($ssl_act_data)) {
    $time = $namecheap->getExecutionTime();
    echo "SSL certificate ID #{$ssl['CertificateId']} activated.\t\t\t\t";
    echo "(t = " . $time['RealTime'] . "s)\n";
  } else {
    file_put_contents('php://stderr', "Failed to activate SSL certificate with error: " . $namecheap->Error . ".\n");
    exit(1);
  }
} else {
    file_put_contents('php://stderr', "Did not found namecheap@ email address.\n");
    exit(1);
}
