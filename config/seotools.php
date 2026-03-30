<?php
/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),
    'meta' => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'title'        => false, // controllers always set titles explicitly
            'titleBefore'  => false,
            'description'  => 'বাংলাদেশের সেরা ডাক্তার, হাসপাতাল ও স্বাস্থ্যসেবার ডিরেক্টরি।',
            'separator'    => ' | ',
            'keywords'     => ['doctor bangladesh', 'hospital bangladesh', 'health directory bangladesh'],
            'canonical'    => false,
            'robots'       => false,
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title'       => 'DoctorBD24 — বাংলাদেশের স্বাস্থ্যসেবা ডিরেক্টরি',
            'description' => 'বাংলাদেশের সেরা ডাক্তার, হাসপাতাল ও স্বাস্থ্যসেবার ডিরেক্টরি।',
            'url'         => null,  // use current URL
            'type'        => 'website',
            'site_name'   => 'DoctorBD24',
            'images'      => [],
        ],
    ],
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            //'card'        => 'summary',
            //'site'        => '@LuizVinicius73',
        ],
    ],
    'json-ld' => [
        /*
         * The default configurations to be used by the json-ld generator.
         */
        'defaults' => [
            'title'       => false,
            'description' => false,
            'url'         => false,
            'type'        => 'WebPage',
            'images'      => [],
        ],
    ],
];
