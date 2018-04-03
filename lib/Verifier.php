<?php
/**
 * Created by PhpStorm.
 * User: pablo-i
 * Date: 02/04/18
 * Time: 13:01
 */

namespace Manifold\Verifier;


use Manifold\Verifier\Exceptions\InvalidException;
use Manifold\Verifier\Exceptions\ValidationException;

class Verifier
{

	/**
	 * Verifier constructor.
	 * @param null $masterKey
	 * @throws InvalidException
	 */
	function __construct($masterKey = NULL) {
		if (empty($masterKey) && empty($this->masterKey))
			throw new InvalidException("Invalid master key");
		$this->masterKey = $masterKey;
	}

	/**
	 * @param $req
	 * @return bool
	 * @throws InvalidException
	 * @throws ValidationException
	 */
	function test($req) {
		if (empty($this->masterKey)) {
			throw new ValidationException('Missing master key');
		}
		$Signature = new Signature($req);
		return $Signature->test($this->masterKey);
	}

	function __base64UrlDecode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}
}