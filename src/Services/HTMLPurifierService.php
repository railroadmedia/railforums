<?php

namespace Railroad\Railforums\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HTMLPurifierService
{
    private $purifier;

    public function __construct()
    {
        $this->purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());

        $this->purifier->config->set('Core.Encoding', config('railforums.html_purifier_settings.encoding'));

        if (!config('railforums.html_purifier_settings.finalize')) {
            $this->purifier->config->autoFinalize = false;
        }

        $this->purifier->config->loadArray(config('railforums.html_purifier_settings.settings.default'));
    }

    public function clean($string)
    {
        return $this->purifier->purify($string);
    }
}