<?php
namespace creditocofrem;


class AESCrypt
{
    private $METHOD = 'AES-256-CBC';
    private $SECRET_KEY = '92AE31A79FEEB2A3';
    private $SECRET_IV = '0123456789ABCDEF';

    public function encryption($string){
        $output=FALSE;
        $key=hash('sha256', $this->SECRET_KEY);
        $iv=substr(hash('sha256', $this->SECRET_IV), 0, 16);
        $output=openssl_encrypt($string, $this->METHOD, $this->SECRET_KEY, 0, $this->SECRET_IV);
        $output=base64_encode($output);
        return $output;
    }

    public function decryption($string){
        $key=hash('sha256', $this->SECRET_KEY);
        $iv=substr(hash('sha256', $this->SECRET_IV), 0, 16);
        $output=openssl_decrypt(base64_decode($string), $this->METHOD, $key, 0, $iv);
        return $output;
    }
}