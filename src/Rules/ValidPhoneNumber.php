<?php

declare(strict_types=1);

namespace N3m3s7sFilamentPhoneFieldRules;

use Closure;
use IlluminateContractsValidationValidationRule;
use N3m3s7sFilamentPhoneFieldEnumsPhoneType;
use N3m3s7sFilamentPhoneFieldSupportPhoneNumber;

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
            'invalid_country' => $fail(__('The phone number country is not allowed.')),
            'invalid_type' => $fail($this->invalidTypeMessage()),
            default => $fail(__('The phone number is invalid.')),
        };
    }

    private function invalidTypeMessage(): string
    {
        return match ($this->type) {
            PhoneType::Mobile => __('The phone number must be a valid mobile number.'),
            PhoneType::Landline => __('The phone number must be a valid landline number.'),
            PhoneType::Any => __('The phone number is invalid.'),
        };
    }
}
