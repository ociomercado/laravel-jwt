<?php
namespace LaravelJWT;

use Illuminate\Http\Request;
use Auth;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;
use Exception;

class JWT
{
  private $config;
  private $builder;
  private $signer;
  private $key;
  private $publicKey;

  public function __construct($config) {
    $this->config = $config;

    if (is_null($this->config['privateKeyPath'])) {
      $this->signer = new Sha256();
      $this->key = $this->config['key'];
    } else {
      $this->signer = new RsaSha256();
      $this->key = (new Keychain())->getPrivateKey($this->config['privateKeyPath']);
      $this->publicKey = (new Keychain())->getPublicKey($this->config['publicKeyPath']);
    }
  }

  public function createToken($jti = null, $customClaims = null) {
    $this->builder = new Builder();

    if (!is_null($this->config['iss'])) {
      $this->builder->setIssuer($this->config['iss']);
    }

    if (!is_null($this-config['sub'])) {
      $this->builder->setSubject($this->config['sub']);
    }

    if (!is_null($this-config['aud'])) {
      $this->builder->setAudience($this->config['aud']);
    }

    $time = time();
    $this->builder->setIssuedAt($time);
    
    if (!is_null($jti)) {
      $this->builder->setId($jti);
    }

    if (!is_null($this->config['nbf'])) {
      $this->builder->setNotBefore($time + $this->config['nbf']);
    }

    if (!is_null($this->config['exp'])) {
      $this->builder->setExpiration($time + $this->config['exp']);
    }

    if (!is_null($customClaims) && is_array($customClaims)) {
      foreach ($customClaims as $k => $v) {
        $this->builder->set($k, $v);
      }
    }

    $token = $this->builder->sign($this->signer, $this->key)->getToken();

    return $token;
  }

  public function verifyToken($token) {
    $validator = new ValidationData();

    if (!is_null($this->config['iss'])) {
      $validator->setIssuer($this->config['iss']);
    }

    if (!is_null($this-config['sub'])) {
      $validator->setSubject($this->config['sub']);
    }

    if (!is_null($this-config['aud'])) {
      $validator->setAudience($this->config['aud']);
    }

    try {
      $token = (new Parser())->parse((string)$token);
      $validator->setId($token->getClaim('jti'));

      if (!$token->validate($data)) {
        return ['success' => false, 'error' => 'Unauthorized data.', 'code' => 401];
      }

      if (!$token->verify($this->signer, $this->publicKey)) {
        return ['success' => false, 'error' => 'Unauthorized sign.', 'code' => 401];;
      }
    } catch (Exception $e) {
      return ['success' => false, 'error' => 'Unauthorized.', 'code' => 403];
    }

    return ['success' => true, 'token' => $token];
  }
}