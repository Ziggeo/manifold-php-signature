<?php
require "vendor/autoload.php";

use Manifold\Verifier\Verifier;

try {
	$Verifier = new Verifier("KaCIgsOVQmLQ/NOdp3oPAlYxYuHtA9QXHaCitXuwDrE=");
	$resp = $Verifier->test($_SERVER);
} catch (Exception $exception) {
	var_dump($exception->getMessage());
	var_dump($exception->getData());
}

var_dump($resp);