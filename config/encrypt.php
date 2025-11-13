<?php
class Encryption {
    private static $key = "MCCPrinter@#"; // Clave de encriptación - cámbiala por una segura

    public static function encrypt($data) {
        $encrypted = base64_encode(openssl_encrypt(
            $data,
            'AES-256-CBC',
            hash('sha256', self::$key, true),
            0,
            substr(hash('sha256', self::$key, true), 0, 16)
        ));
        return urlencode(strtr($encrypted, '+/', '-_'));
    }

    public static function decrypt($data) {
        $data = strtr(urldecode($data), '-_', '+/');
        return openssl_decrypt(
            base64_decode($data),
            'AES-256-CBC',
            hash('sha256', self::$key, true),
            0,
            substr(hash('sha256', self::$key, true), 0, 16)
        );
    }
}
?>