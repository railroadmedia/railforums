<?php

namespace Railroad\Railforums\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HTMLPurifierService
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.SafeIframe', true);
        $config->set(
            'URI.SafeIframeRegexp',
            '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/)%'
        );
        $config->set('Core.Encoding', config('railforums.html_purifier_settings.encoding'));
        $config->set('Cache.DefinitionImpl', null);
        $config->set('Cache.SerializerPath', null);
        $config->set('Cache.SerializerPermissions', null);

        if (!config('railforums.html_purifier_settings.finalize')) {
            $config->autoFinalize = false;
        }

        foreach (config('railforums.html_purifier_settings.settings.default') as $key => $value) {
            $config->set($key, $value);
        }

        $this->purifier = new HTMLPurifier($config);
    }

    public function clean($string)
    : string {
        //        dd($this->purifier->config);
        return $this->purifier->purify($string);
    }
}