<?php

declare(strict_types=1);

namespace N3m3s7sFilamentPhoneFieldSupport;

use N3m3s7sFilamentPhoneFieldEnumsPhoneType;
use libphonenumberNumberParseException;
use libphonenumberPhoneNumberFormat;
use libphonenumberPhoneNumberType;
use libphonenumberPhoneNumberUtil;

final readonly class PhoneNumber
{
    public function __construct(
        private PhoneNumberUtil $util,
    ) {
    }

    public static function make(): self
    {
        return new self(PhoneNumberUtil::getInstance());
    }

    public function parse(
        ?string $value,
        ?string $country = null,
        array $allowedCountries = [],
        PhoneType $type = PhoneType::Any,
    ): PhoneNumberParseResult {
        $value = trim((string) $value);
        $country = $country !== null ? strtoupper(trim($country)) : null;

        if ($value === '') {
            return PhoneNumberParseResult::empty();
        }

        try {
            $number = $this->util->parse($value, $country ?: null);
        } catch (NumberParseException) {
            return PhoneNumberParseResult::invalid();
        }

        if (! $this->util->isValidNumber($number)) {
            return PhoneNumberParseResult::invalid();
        }

        $detectedCountry = $this->util->getRegionCodeForNumber($number);

        if (! is_string($detectedCountry) || $detectedCountry === '') {
            return PhoneNumberParseResult::invalid();
        }

        $detectedCountry = strtoupper($detectedCountry);

        $allowedCountries = collect($allowedCountries)
            ->map(fn (string $country): string => strtoupper(trim($country)))
            ->filter()
            ->values()
            ->all();

        if ($allowedCountries !== [] && ! in_array($detectedCountry, $allowedCountries, true)) {
            return PhoneNumberParseResult::invalidCountry($detectedCountry);
        }

        $numberType = $this->util->getNumberType($number);

        if (! $this->acceptsType($numberType, $type)) {
            return PhoneNumberParseResult::invalidType($detectedCountry, $numberType);
        }

        return PhoneNumberParseResult::valid(
            e164: $this->util->format($number, PhoneNumberFormat::E164),
            international: $this->util->format($number, PhoneNumberFormat::INTERNATIONAL),
            national: $this->util->format($number, PhoneNumberFormat::NATIONAL),
            country: $detectedCountry,
            type: $numberType,
        );
    }

    private function acceptsType(PhoneNumberType $actual, PhoneType $expected): bool
    {
        return $expected->accepts($actual);
    }
}
