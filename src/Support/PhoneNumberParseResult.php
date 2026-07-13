<?php

declare(strict_types=1);

namespace N3m3s7s\FilamentPhoneField\Support;

use libphonenumber\PhoneNumberType;

final readonly class PhoneNumberParseResult
{
    private function __construct(
        public bool $valid,
        public bool $empty,
        public ?string $e164 = null,
        public ?string $international = null,
        public ?string $national = null,
        public ?string $country = null,
        public ?PhoneNumberType $type = null,
        public ?string $error = null,
    ) {
    }

    public static function empty(): self
    {
        return new self(valid: false, empty: true);
    }

    public static function invalid(): self
    {
        return new self(valid: false, empty: false, error: 'invalid');
    }

    public static function invalidCountry(string $country): self
    {
        return new self(valid: false, empty: false, country: $country, error: 'invalid_country');
    }

    public static function invalidType(string $country, PhoneNumberType $type): self
    {
        return new self(valid: false, empty: false, country: $country, type: $type, error: 'invalid_type');
    }

    public static function valid(
        string $e164,
        string $international,
        string $national,
        string $country,
        PhoneNumberType $type,
    ): self {
        return new self(
            valid: true,
            empty: false,
            e164: $e164,
            international: $international,
            national: $national,
            country: $country,
            type: $type,
        );
    }
}
