<?php

namespace Manifold\Verifier\Exceptions;
use Exception;


Class ValidationException extends Exception {

	private $data;

	function __construct($data = array()) {
		parent::__construct("Request Validation Failed", 400);
		$this->data = $data;
	}

	function getData() {
		return $this->data;
	}

}