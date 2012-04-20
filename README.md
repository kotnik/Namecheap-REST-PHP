Simple PHP library for using the Namecheap REST API.

API documentation: http://developer.namecheap.com/docs/doku.php?id=api-reference:index.

### About

This is a simple single-file class for accessing the Namecheap REST API.

Methods are loosely named after the corresponding API endpoint.

  namecheap.domains.check         == domainsCheck()
  namecheap.domains.create        == domainsCreate()
  namecheap.domains.ns.create     == nsCreate()
  namecheap.domains.dns.setCustom == dnsSetCustom()

The class fully supports the Namecheap Sandbox (https://www.sandbox.namecheap.com) during object construction.

The raw response from the Namecheap API is available as a public variable "$Raw". Any call the results in an error stores the full error in a public variable "$Error".

### Usage

```php
include_once('namecheap.php');
$nc_api = array(
  'api_user' => 'username',
  'api_key' => 'some_key',
  'api_ip' => 'detect'
);

$sandbox = TRUE; // Use the Namecheap sandbox to test

$nc = Namecheap::get($nc_api, $sandbox);

if ($nc) {
  if ($nc->domainsCheck('example.com')) {
    echo "<p>example.com is available!</p>";
  }

  $domains = array('example.net', 'example.org', 'example.info');
  $results = $nc->domainsCheck($domains);
  echo "<ul>";
  foreach ($results as $domain => $available) {
    $status = ($available) ? 'available' : 'not available';
    echo "<li>$domain: $status</li>";
  }
  echo "</ul>";

  // registering a domain requires an awful lof of mandatory data:
  // http://developer.namecheap.com/docs/doku.php?id=api-reference:domains:create
  // stick all that in an associative array
  if (!$nc->domainCreate('example.com', $registration_data)) {
    print_r($nc->Error);
  }
}
```

### License

Copyright 2011 Scott Merrill

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
