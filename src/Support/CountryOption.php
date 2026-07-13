<?php

declare(strict_types=1);

namespace N3m3s7sFilamentPhoneFieldSupport;

final readonly class CountryOption
{
    public function __construct(
        public string $iso,
        public string $name,
        public int $dialCode,
        public string $flagUrl,
    ) {
    }

    public function label(): string
    {
        return "{$this->name} +{$this->dialCode}";
    }

    public function toArray(): array
    {
        return [
            'iso' => $this->iso,
            'name' => $this->name,
            'dial_code' => $this->dialCode,
            'flag_url' => $this->flagUrl,
            'label' => $this->label(),
        ];
    }
}
