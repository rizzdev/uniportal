<?php

namespace Client\Service;


class CryptService
{
    protected $key = 'woa498ghna0we948tjaw49g9erjfkewr89gtfjaewrgklpsafdjgae09tgjhaw4gtj98t43w0g98jaewg';
    protected $iv;

    public function __construct()
    {
        //$this->iv = \mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
    }

    public function lock($string)
    {
        $encrypted_string = \mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $string, MCRYPT_MODE_CBC, $this->iv);
        return $encrypted_string;
    }

    public function unlock($string)
    {
        $decrypted_string = \mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $string, MCRYPT_MODE_CBC, $this->iv);
        return $decrypted_string;
    }

}