<?php

class JWTService
{
    private static $secret_key = "YOUR_SUPER_SECRET_KEY_UKM_ASSET_MANAGER"; // In production, use environment variable
    private static $algorithm = "HS256";

    public static function generate($payload)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);

        // Add expiration
        $payload['exp'] = time() + (60 * 60 * 24); // 24 hours
        $payload_json = json_encode($payload);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload_json);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function validate($token)
    {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            return false;
        }

        $header = $parts[0];
        $payload = $parts[1];
        $signature_provided = $parts[2];

        $signature = hash_hmac('sha256', $header . "." . $payload, self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        if (!hash_equals($base64UrlSignature, $signature_provided)) {
            return false;
        }

        $payloadData = json_decode(self::base64UrlDecode($payload), true);

        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }

    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
