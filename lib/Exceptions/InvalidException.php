<?php

namespace Manifold\Verifier\Exceptions;
use Exception;

Class InvalidException extends Exception {

	private $data;

	function __construct($data = "") {
		parent::__construct("Invalid Request Signature - $data", 401);
		$this->data = $data;
	}

	function getData() {
		return $this->data;
	}

}