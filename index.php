<?php
require "vendor/autoload.php";

use Manifold\Verifier\Verifier;

$master_key = "YOUR MASTER KEY";

try {
	$Verifier = new Verifier($master_key);
	$resp = $Verifier->test($_SERVER);
} catch (Exception $exception) {
	var_dump($exception->getMessage());
}