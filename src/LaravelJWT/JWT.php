<?php
/**
 * Laravel-JWT - A simple Laravel package to work with JWT
 * Author: Miguel Ángel Villagrá
 * Organization: OcioMercado
 */

namespace OcioMercado\LaravelJWT;

use Illuminate\Http\Request;
use Auth;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;
use OcioMercado\LaravelJWT\Exceptions\TokenNotFoundException;
use OcioMercado\LaravelJWT\Exceptions\InvalidTokenException;
use OcioMercado\LaravelJWT\Exceptions\InvalidTokenSignException;
use InvalidArgumentException;

class JWT
{
  private $request;
  private $config;
  private $builder;
  private $signer;
  private $key;
  private $publicKey;
  private $tokenString;
  public $token;

  public function __construct(Request $request) {
    $this->request = $request;
    $this->config = config('jwt');

    if (is_null($this->config['privateKeyPath'])) {
      $this->signer = new Sha256();
      $this->key = $this->config['key'];
    } else {
      $this->signer = new RsaSha256();
      $this->key = (new Keychain())->getPrivateKey($this->config['privateKeyPath']);

      if (!is_null($this->config['publicKeyPath'])) {
        $this->publicKey = (new Keychain())->getPublicKey($this->config['publicKeyPath']);
      }
    }
  }

  /**
   * Gets the JWT string from the request headers or from the GET parameter.
   *
   * @return string Returns the token string.
   *
   * @throws TokenNotFoundException When the token is not found.
   */
  public function getTokenString() {
    $headerToken = $this->request->header($this->config['headerKey']);
    $requestToken = $this->request->get($this->config['requestKey']);

    if (!isset($headerToken) && !isset($requestToken)) {
      throw new TokenNotFoundException();
    }

    $tokenString = isset($headerToken) ? $headerToken : $requestToken;
    $tokenString = explode('Bearer ', $tokenString)[1];
    return $tokenString;
  }

  /**
   * Parses the JWT string.
   *
   * @return Lcobucci\JWT\Token Returns the token.
   *
   * @throws TokenNotFoundException When the token is not found.
   * @throws InvalidTokenException When the token is not valid.
   */
  public function parseTokenString() {
    if (is_null($this->tokenString)) {
      try {
        $this->tokenString = self::getTokenString();
      } catch (TokenNotFoundException $e) {
        throw $e;
      }
    }

    try {
      $token = (new Parser())->parse((string)$this->tokenString);
    } catch (InvalidArgumentException $e) {
      throw new InvalidTokenException();
    }

    return $token;
  }

  /**
   * Creates and signs a new JWT.
   *
   * It signs the token with the configured type of key in the jwt.php file.
   *
   * @param string $jti A unique identifier for the token.
   * @param mixed[] $customClaims Optional data to append to the token.
   *
   * @return Lcobucci\JWT\Token
   */
  public function createToken($jti = null, $customClaims = null) {
    $this->builder = new Builder();

    if (!is_null($this->config['iss'])) {
      $this->builder->setIssuer($this->config['iss']);
    }

    if (!is_null($this->config['sub'])) {
      $this->builder->setSubject($this->config['sub']);
    }

    if (!is_null($this->config['aud'])) {
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

    if (!is_null($customClaims) && is_array($customClaims) && count($customClaims) > 0) {
      foreach ($customClaims as $k => $v) {
        $this->builder->set($k, $v);
      }
      $ccKeys = implode(',', array_keys($customClaims));
      $this->builder->set('customClaims', $ccKeys);
    }

    $this->token = $this->builder->sign($this->signer, $this->key)->getToken();

    return $this->token;
  }

  /**
   * Validates and verifies a JWT.
   *
   * It verfies the token with the configured type of key in the jwt.php file.
   *
   * @return Lcobucci\JWT\Token Returns the token.
   *
   * @throws TokenNotFoundException When the token is not found.
   * @throws InvalidTokenException When the token is not valid.
   * @throws InvalidTokenSignException When the token sign is not valid.
   */
  public function verifyToken() {
    $validator = new ValidationData();

    if (!is_null($this->config['iss'])) {
      $validator->setIssuer($this->config['iss']);
    }

    if (!is_null($this->config['sub'])) {
      $validator->setSubject($this->config['sub']);
    }

    if (!is_null($this->config['aud'])) {
      $validator->setAudience($this->config['aud']);
    }

    try {
      $this->token = self::parseTokenString();
    } catch (TokenNotFoundException $e) {
      throw $e;
    } catch (InvalidTokenException $e) {
      throw $e;
    }

    $validator->setId($this->token->getClaim('jti'));

    if (!$this->token->validate($validator)) {
      throw new InvalidTokenException();
    }

    if (is_null($this->publicKey)) {
      if (!$this->token->verify($this->signer, $this->key)) {
        throw new InvalidTokenSignException();
      }
    } else {
      if (!$this->token->verify($this->signer, $this->publicKey)) {
        throw new InvalidTokenSignException();
      }
    }

    return $this->token;
  }

  /**
   * Checks if the JWT has expired.
   *
   * @throws TokenNotFoundException When the token is not found.
   * @throws InvalidTokenException When the token is not valid.
   * @throws TokenExpiredException When the token has expired.
   */
  public function tokenExpired() {
    if (is_null($this->token)) {
      try {
        $this->token = self::parseTokenString();
      } catch (TokenNotFoundException $e) {
        throw $e;
      } catch (InvalidTokenException $e) {
        throw $e;
      }
    }
    $iat = $this->token->getClaim('iat');
    $time = time();

    if ($time > ($iat + $this->config['exp'])) {
      throw new TokenExpiredException();
    }
  }

  /**
   * Checks if the JWT can be refreshed.
   *
   * @return boolean Returns true is the token can be refreshed, otherwise it returns false.
   *
   * @throws TokenNotFoundException When the token is not found.
   * @throws InvalidTokenException When the token is not valid.
   */
  public function isRefreshableToken() {
    if (is_null($this->token)) {
      try {
        $this->token = self::parseTokenString();
      } catch (TokenNotFoundException $e) {
        throw $e;
      } catch (InvalidTokenException $e) {
        throw $e;
      }
    }
    $iat = $this->token->getClaim('iat');
    $time = time();

    if ($time > ($iat + $this->config['refreshTTL'])) {
      return false;
    }

    return true;
  }
}