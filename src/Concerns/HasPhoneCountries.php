<?php

declare(strict_types=1);

namespace N3m3s7sFilamentPhoneFieldConcerns;

use Closure;

trait HasPhoneCountries
{
    protected array | Closure | null $countries = null;

    protected string | Closure | null $defaultCountry = null;

    /**
     * @param array<int, string>|Closure|null $countries
     */
    public function countries(array | Closure | null $countries): static
    {
        $this->countries = $countries;

        return $this;
    }

    public function defaultCountry(string | Closure | null $country): static
    {
        $this->defaultCountry = $country;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getCountries(): array
    {
        $countries = $this->evaluate($this->countries);

        if ($countries === null) {
            $countries = config('filament-phone-field.countries', []);
        }

        return collect($countries)
            ->filter(fn (mixed $country): bool => is_string($country))
            ->map(fn (string $country): string => strtoupper(trim($country)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function getDefaultCountry(): ?string
    {
        $country = $this->evaluate($this->defaultCountry);

        if ($country === null) {
            $country = config('filament-phone-field.default_country');
        }

        $country = strtoupper(trim((string) $country));

        return $country !== '' ? $country : null;
    }
}
