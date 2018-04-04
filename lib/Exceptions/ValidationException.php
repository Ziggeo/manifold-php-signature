<?php

namespace Manifold\Verifier\Exceptions;
use Exception;


Class ValidationException extends Exception {

	private $data;

	function __construct($data = "") {
		parent::__construct("Request Validation Failed - $data", 400);
		$this->data = $data;
	}

	function getData() {
		return $this->data;
	}

}