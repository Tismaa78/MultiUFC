<?php

namespace App\Service;

use ReCaptcha\ReCaptcha;

class RecaptchaService
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function verify(string $response, ?string $ip = null): bool
    {
        if (empty($response)) {
            return false;
        }

        $recaptcha = new ReCaptcha($this->secretKey);

        $result = $recaptcha->verify($response, $ip);

        return $result->isSuccess();
    }
} 