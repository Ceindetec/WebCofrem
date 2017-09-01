<?php
/**
 * Created by PhpStorm.
 * User: ceindetec151
 * Date: 01/09/2017
 * Time: 14:20
 */

namespace creditocofrem;


class Encript
{
    private $METHOD = 'AES-256-CBC';
    private $SECRET_KEY = '$CREDICOFREM@2017';
    private $SECRET_IV = '142563';

    public function encryption($string){
        $output=FALSE;
        $key=hash('sha256', $this->SECRET_KEY);
        $iv=substr(hash('sha256', $this->SECRET_IV), 0, 16);
        $output=openssl_encrypt($string, $this->METHOD, $key, 0, $iv);
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