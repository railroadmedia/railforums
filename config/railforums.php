<?php

/*
 * html_purifier_settings.settings.default array is passed directly in to HTMLPurifier_Config->loadArray()
 */

return array(
    'author_table_name' => 'users',
    'author_table_id_column_name' => 'id',
    'author_table_display_name_column_name' => 'display_name',

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