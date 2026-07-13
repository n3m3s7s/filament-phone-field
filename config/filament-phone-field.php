<?php

declare(strict_types=1);

return [
    /*
    * Default available countries showed/handled by the package;
    * You can specify your own since all prefix are resolved via PhoneNumberUtils
    */
    'countries' => [
        'IT',
        'FR',
        'DE',
        'ES',
        'GB',
        'US',
    ],

    /**
     * Default country to initiate the country combobox
     */
    'default_country' => 'IT',

    /**
     * URL that will be injected in the country combobox to render each country's flag
     */
    'flag_cdn' => 'https://cdn.jsdelivr.net/gh/HatScripts/circle-flags@latest/flags/{country}.svg',
];
