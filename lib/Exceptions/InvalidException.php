<?php

namespace Manifold\Verifier\Exceptions;
use Exception;

Class InvalidException extends Exception {

	private $data;

	function __construct($data = array()) {
		parent::__construct("Invalid Request Signature", 401);
		$this->data = $data;
	}

	function getData() {
		return $this->data;
	}

}