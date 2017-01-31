<?php

/*
 * html_purifier_settings.settings.default array is passed directly in to HTMLPurifier_Config->loadArray()
 */

return array(
    'user_data_mapper_class' => \Railroad\Railforums\DataMappers\UserCloakDataMapper::class,

    'html_purifier_settings' => [
        'encoding' => 'UTF-8',
        'finalize' => true,
        'settings' => [
            'default' => [
                'HTML.Doctype' => 'XHTML 1.0 Strict',
                'HTML.Allowed' => 'div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
                'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
                'AutoFormat.AutoParagraph' => true,
                'AutoFormat.RemoveEmpty' => true,
            ],
        ],
    ]
);