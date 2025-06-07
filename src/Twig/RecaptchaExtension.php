<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class RecaptchaExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private string $siteKey
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'google_recaptcha_site_key' => $this->siteKey,
        ];
    }
} 