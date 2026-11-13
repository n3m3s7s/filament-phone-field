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
     * URL template used to render each country's flag (`{country}` is the lowercase ISO code).
     * The default points to the flags bundled with the package: publish them with
     * `php artisan vendor:publish --tag=filament-phone-field-flags` (they land in `public/vendor/filament-phone-field/flags`).
     * Set an absolute URL here only if you really want to serve the flags from somewhere else.
     */
    'flag_url' => '/vendor/filament-phone-field/flags/{country}.svg',
];
