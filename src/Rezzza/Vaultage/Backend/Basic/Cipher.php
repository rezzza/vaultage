<?php

namespace Rezzza\Vaultage\Backend\Basic;

use Rezzza\Vaultage\Exception\BadCredentialsException;

/**
 * Cipher
 *
 * @inspired from https://gist.github.com/meglio/3965357
 *
 * @author Anton Andriyevskyy
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Cipher
{
    /**
     * @var string
     */
    private $cipher;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $iv;

    public function __construct()
    {
        $this->cipher  = MCRYPT_RIJNDAEL_256;
        $this->mode    = MCRYPT_MODE_ECB;
        $this->iv      = mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_RAND);
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($str, Metadata $metadata)
    {
        $token = $this->extractTokenFromMetadata($metadata);
        $str   = substr(md5($str), 0, 4).$str;

        return base64_encode(mcrypt_encrypt($this->cipher, $token, $str, $this->mode, $this->iv));
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($str, Metadata $metadata)
    {
        $token = $this->extractTokenFromMetadata($metadata);

        $str = base64_decode($str);
        if ($str === false || strlen($str) < 32) {
            throw new BadCredentialsException('Code has not expected length');
        }

        $decrypted = rtrim(mcrypt_decrypt($this->cipher, $token, $str, $this->mode, $this->iv), "\0");

        if ($decrypted === false || is_null($decrypted) || strlen($decrypted) < 4) {
            throw new BadCredentialsException('Bad credentials');
        }

        $dataHash = substr($decrypted, 0, 4);
        $data     = substr($decrypted, 4);

        if (substr(md5($data), 0, 4) !== $dataHash) {
            throw new BadCredentialsException('Bad credentials');
        }

        return $data;
    }

    /**
     * @param Metadata $metadata metadata
     *
     * @return string
     */
    private function extractTokenFromMetadata(Metadata $metadata)
    {
        $phrase = $metadata->key;
        if ($metadata->passphrase) {
            $phrase .= ':'.$metadata->passphrase;
        }

        return mhash(MHASH_SHA1, $phrase);
    }
}
