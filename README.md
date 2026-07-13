# Filament Phone Field

A Filament 5 multi-country phone field powered by `giggsey/libphonenumber-for-php`.

## Installation

```bash
composer require n3m3s7s/filament-phone-field
```

Publish config:

```bash
php artisan vendor:publish --tag=filament-phone-field-config
```

## Basic usage

By default, the field saves only the phone number as E.164. It does not require a separate database column for the country.

```php
use N3m3s7s\FilamentPhoneField\PhoneField;
use N3m3s7s\FilamentPhoneField\Enums\PhoneType;

PhoneField::make('phone')
    ->required()
    ->countries(['IT', 'FR', 'DE', 'ES', 'GB', 'US'])
    ->defaultCountry('IT')
    ->phoneType(PhoneType::Any)
    ->saveAsE164();
```

## Mobile only

```php
PhoneField::make('mobile_phone')
    ->required()
    ->countries(['IT', 'FR', 'DE'])
    ->defaultCountry('IT')
    ->mobile();
```

## Landline only

```php
PhoneField::make('landline_phone')
    ->required()
    ->countries(['IT', 'FR', 'DE'])
    ->defaultCountry('IT')
    ->landline();
```

## Optional external country path

Use this only when you explicitly want to persist the country in a separate database column.

```php
PhoneField::make('phone')
    ->countries(['IT', 'FR', 'DE'])
    ->defaultCountry('IT')
    ->countryStatePath('phone_country')
    ->saveAsE164();
```

## Optional array state

Use this when the phone number is a JSON metadata value and you want one attribute to contain both phone number and country.

```php
PhoneField::make('metadata.phone')
    ->countries(['IT', 'FR', 'DE'])
    ->defaultCountry('IT')
    ->saveAsArray();
```

The dehydrated state becomes:

```php
[
    'number' => '+393331234567',
    'country' => 'IT',
]
```

## Searchable country select

For a native Filament searchable country combobox, use the helper that returns a Filament Select plus PhoneField.

```php
...PhoneField::makeWithCountrySelect(
    phoneName: 'phone',
    countryName: 'phone_country',
    countries: ['IT', 'FR', 'DE', 'ES', 'GB', 'US'],
    type: PhoneType::Any,
    defaultCountry: 'IT',
)
```

## Stored format

Default stored value:

```text
+393331234567
```
