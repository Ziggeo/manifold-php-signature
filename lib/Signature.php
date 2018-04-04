<?php


namespace Manifold\Verifier;

use Manifold\Verifier\Exceptions\InvalidException;
use Manifold\Verifier\Exceptions\ValidationException;

class Signature
{
	const PERMITTED_SKEW_IN_MS = 5 * 60 * 1000; // 5 minutes

	function __construct($req) {
		$this->req = $req;
		if (empty($req["body"]))
			$this->req["body"] = file_get_contents("php://input");
		$this->message = "";

		$this->xSignature = preg_split('/ /', $req["HTTP_X_SIGNATURE"]);
		$this->signature = $this->__base64UrlDecode($this->xSignature[0]);
		$this->publicKey = $this->__base64UrlDecode($this->xSignature[1]);
		$this->endorsement = $this->__base64UrlDecode($this->xSignature[2]);

		$this->date = strtotime($req["HTTP_DATE"]);

	}

	/**
	 * @param $masterKey
	 * @return bool
	 * @throws InvalidException
	 * @throws ValidationException
	 */
	function test($masterKey) {
		if (count($this->xSignature) < 3) {
			throw new ValidationException("Invalid x-signature length");
		}
		if (!$this->__validTime()) {
			throw new ValidationException("Clock Skew");
		}

		$this->message = $this->__canonize($this->req);

		return $this->goodSignature($masterKey);
	}


	/**
	 * @param $masterKey
	 * @return bool
	 * @throws InvalidException
	 * @throws ValidationException
	 */
	function goodSignature($masterKey) {
		$mk = $this->__base64UrlDecode($masterKey);
		$message = $this->message;

		if (!$this->__validBuffers($mk)) {
			throw new InvalidException('Invalid signature size');
		}

		if (!\Sodium\crypto_sign_verify_detached($this->endorsement, $this->publicKey, $mk)) {
			throw new InvalidException('Failed to verify endorsement');
		}

		if (!\Sodium\crypto_sign_verify_detached($this->signature, $message, $this->publicKey)) {
			throw new InvalidException('Failed to verify signature');
		}


		return TRUE;
	}


	function __canonize($req) {
		$url_data = parse_url($req["REQUEST_URI"]);
		$msg = strtolower($req["REQUEST_METHOD"]) . " " . $url_data["path"];
		if (isset($url_data["query"]) && count($url_data["query"]) > 0) {
			$qs = preg_split("/&/", $url_data["query"]);
			sort($qs);
			$qs = join("&", $qs);
			$msg .= "?" . $qs;
		}
		$msg .= "\n";
		$signed_headers_group = $req["HTTP_X_SIGNED_HEADERS"];
		$signed_headers = preg_split("/ /", $signed_headers_group);
		$signed_headers[] = "x-signed-headers";
		foreach ($signed_headers as $id_s => $header) {
			$name = $header;
			$key = preg_replace("/-/", "_", strtoupper($name));
			if (isset($req["$key"]) || isset($req["HTTP_$key"])) {
				$value = isset($req["$key"]) ? trim($req["$key"]) : trim($req["HTTP_$key"]);
				$msg .= "$name: $value\n";
			}
		}
		if (isset($req["body"]))
			$msg .= $req["body"];
		return $msg;
	}

	function __validTime() {
		$skew = abs($this->date - time());
		return $skew < self::PERMITTED_SKEW_IN_MS;
	}

	function __validBuffers($masterKey = NULL) {
		$invalidEndorsement = empty($this->endorsement) || strlen($this->endorsement) !== 64;
		$invalidMk = empty($masterKey) || strlen($masterKey) !== 32;
		$invalidSig = empty($this->signature) || strlen($this->signature) !== 64;
		$invalidPk = !$this->publicKey || strlen($this->publicKey) !== 32;
		if ($invalidEndorsement || $invalidMk || $invalidSig || $invalidPk) {
			return FALSE;
		}

		return TRUE;
	}

	function __base64UrlEncode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	function __base64UrlDecode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
}