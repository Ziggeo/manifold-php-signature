# Manifold PHP Signature Verifier
Verify signed HTTP requests from Manifold.
## Install
```bash
composer require ziggeo/manifold-php-verifier:dev-master
```
## Usage
```php
<?php
require "vendor/autoload.php";

use Manifold\Verifier\Verifier;

$master_key = "YOUR_MASTER_KEY";
$request_data = $_SERVER;

try {
     $Verifier = new Verifier($master_key);
     $resp = $Verifier->test($request_data); 
} catch (Exception $e) {
     echo $exception->getMessage();
}
```


