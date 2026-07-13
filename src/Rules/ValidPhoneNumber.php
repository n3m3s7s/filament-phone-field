<?php

declare(strict_types=1);

namespace N3m3s7s\FilamentPhoneField\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use N3m3s7s\FilamentPhoneField\Enums\PhoneType;
use N3m3s7s\FilamentPhoneField\Support\PhoneNumber;

final readonly class ValidPhoneNumber implements ValidationRule
{
    /**
     * @param array<int, string> $countries
     */
    public function __construct(
        private array $countries = [],
        private ?string $country = null,
        private PhoneType $type = PhoneType::Any,
        private bool $required = false,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = is_scalar($value) ? trim((string) $value) : '';

        if ($value === '') {
            if ($this->required) {
                $fail(__('validation.required', ['attribute' => $attribute]));
            }

            return;
        }

        $result = PhoneNumber::make()->parse(
            value: $value,
            country: $this->country,
            allowedCountries: $this->countries,
            type: $this->type,
        );

        if ($result->valid) {
            return;
        }

        match ($result->error) {
            'invalid_country' => $fail(__('filament-phone-field::validation.invalid_country')),
            'invalid_type' => $fail($this->invalidTypeMessage()),
            default => $fail(__('filament-phone-field::validation.invalid_number')),
        };
    }

    private function invalidTypeMessage(): string
    {
        return match ($this->type) {
            PhoneType::Mobile => __('filament-phone-field::validation.invalid_mobile_number'),
            PhoneType::Landline => __('filament-phone-field::validation.invalid_landline_number'),
            PhoneType::Any => __('filament-phone-field::validation.invalid_number'),
        };
    }
}
