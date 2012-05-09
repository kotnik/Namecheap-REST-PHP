#!/usr/bin/env php
<?php

/**
 * @file
 *
 * Namecheap API Users test script that operates on Sandbox API.
 *
 * Arguments required:
 *   - Namecheap API user
 *   - Namecheap API key
 *   - Ammount treshold
 */

define('NO_DOMAIN', TRUE);
require_once('common.php');

$ammount = (int) $argv[2];

// Check remaining funds
if ($balances = $namecheap->usersGetBalances()) {
  $time = $namecheap->getExecutionTime();
  echo "Remaining balance: {$balances['Currency']} {$balances['AvailableBalance']}.\t\t\t\t";
  echo "(t = " . $time['RealTime'] . "s)\n";
  if ($ammount > $balances['AvailableBalance']) {
    file_put_contents('php://stderr', "Balances below the treshold of $ammount.\n");
    exit(1);
  } else {
    echo "Treshhold of $ammount not reached.\n";
  }
} else {
  file_put_contents('php://stderr', "Failed to get user balance with error: " . $namecheap->Error . ".\n");
  exit(1);
}
