<?php

declare(strict_types=1);

namespace N3m3s7sFilamentPhoneFieldSupport;

use IlluminateSupportCollection;
use libphonenumberPhoneNumberUtil;

final class PhoneCountryRepository
{
    /**
     * @var array<string, CountryOption>
     */
    private static array $cache = [];

    public function __construct(
        private readonly ?PhoneNumberUtil $phoneNumberUtil = null,
    ) {
    }

    public function all(?array $onlyCountries = null): Collection
    {
        $countries = $this->normalizeCountries(
            $onlyCountries ?: config('filament-phone-field.countries', []),
        );

        return collect($countries)
            ->map(fn (string $country): ?CountryOption => $this->find($country))
            ->filter()
            ->sortBy(fn (CountryOption $option): string => $option->name)
            ->values();
    }

    public function find(string $country): ?CountryOption
    {
        $country = $this->normalizeCountry($country);

        if ($country === '') {
            return null;
        }

        if (isset(self::$cache[$country])) {
            return self::$cache[$country];
        }

        $util = $this->phoneNumberUtil ?? PhoneNumberUtil::getInstance();

        if (! in_array($country, $util->getSupportedRegions(), true)) {
            return null;
        }

        $dialCode = $util->getCountryCodeForRegion($country);

        if ($dialCode <= 0) {
            return null;
        }

        $flagUrl = str_replace(
            '{country}',
            strtolower($country),
            (string) config(
                'filament-phone-field.flag_cdn',
                'https://cdn.jsdelivr.net/gh/HatScripts/circle-flags@latest/flags/{country}.svg',
            ),
        );

        return self::$cache[$country] = new CountryOption(
            iso: $country,
            name: $this->countryName($country),
            dialCode: $dialCode,
            flagUrl: $flagUrl,
        );
    }

    /**
     * @return array<int, string>
     */
    public function supportedCountries(): array
    {
        return PhoneNumberUtil::getInstance()->getSupportedRegions();
    }

    /**
     * @param array<int, string> $countries
     * @return array<int, string>
     */
    public function normalizeCountries(array $countries): array
    {
        return collect($countries)
            ->filter(fn (mixed $country): bool => is_string($country))
            ->map(fn (string $country): string => $this->normalizeCountry($country))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function normalizeCountry(?string $country): string
    {
        return strtoupper(trim((string) $country));
    }

    private function countryName(string $country): string
    {
        $locale = app()->getLocale();

        if (class_exists(Locale::class)) {
            $name = Locale::getDisplayRegion("-{$country}", $locale);

            if (is_string($name) && $name !== '') {
                return $name;
            }
        }

        return $country;
    }
}
