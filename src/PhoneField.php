<?php

declare(strict_types=1);

namespace N3m3s7s\FilamentPhoneField;

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use N3m3s7s\FilamentPhoneField\Concerns\HasPhoneCountries;
use N3m3s7s\FilamentPhoneField\Enums\PhoneType;
use N3m3s7s\FilamentPhoneField\Rules\ValidPhoneNumber;
use N3m3s7s\FilamentPhoneField\Support\PhoneCountryRepository;
use N3m3s7s\FilamentPhoneField\Support\PhoneNumber;

final class PhoneField extends TextInput
{
    use HasPhoneCountries;

    protected string $view = 'filament-phone-field::components.phone-field';

    protected PhoneType | Closure $phoneType = PhoneType::Any;

    protected bool | Closure $autoDetectCountry = true;

    protected bool | Closure $formatStateUsingLibPhoneNumber = true;

    protected bool | Closure $saveAsE164 = true;

    protected bool | Closure $saveAsArray = false;

    protected ?string $countryStatePath = null;

    protected ?string $internalCountryStatePath = null;

    protected string|Closure|\Illuminate\Contracts\Support\Htmlable|null|\BackedEnum $suffixIcon = 'heroicon-m-phone';
/*


    protected ?string $suffixIcon = null;

    protected ?string $prefixIconColor = 'primary';

    protected ?string $suffixIconColor = 'primary';*/

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (PhoneField $component, mixed $state): void {
            if (is_array($state)) {
                $phone = $state['number'] ?? $state['phone'] ?? null;
                $country = $state['country'] ?? null;

                if (is_string($country) && $country !== '') {
                    $component->setSelectedCountry($country);
                }

                $state = $phone;
            }

            if (! is_scalar($state) || trim((string) $state) === '') {
                return;
            }

            $result = PhoneNumber::make()->parse(
                value: (string) $state,
                country: $component->getSelectedCountry(),
                allowedCountries: $component->getCountries(),
                type: $component->getPhoneType(),
            );

            if (! $result->valid) {
                return;
            }

            $component->state($result->national);
            $component->setSelectedCountry($result->country);
        });

        $this->afterStateUpdated(function (PhoneField $component, mixed $state): void {
            if (! is_scalar($state) || trim((string) $state) === '') {
                return;
            }

            $result = PhoneNumber::make()->parse(
                value: (string) $state,
                country: $component->getSelectedCountry(),
                allowedCountries: $component->getCountries(),
                type: $component->getPhoneType(),
            );

            if (! $result->valid) {
                return;
            }

            // Se abilitato, rileva il prefisso se l'utente lo incolla a mano
            if ($component->shouldAutoDetectCountry() && in_array($result->country, $component->getCountries(), true)) {
                $component->setSelectedCountry($result->country);
            }

            // FIX: Forza l'aggiornamento dell'input al formato nazionale.
            // Se l'utente incolla "+39 333 123456", Livewire lo trasforma in "333 123456"
            // e sposta il dropdown su IT.
            $component->state($result->national);
        });

        $this->dehydrateStateUsing(function (PhoneField $component, mixed $state): mixed {
            if (! is_scalar($state) || trim((string) $state) === '') {
                return $component->shouldSaveAsArray()
                    ? ['number' => null, 'country' => $component->getSelectedCountry()]
                    : null;
            }

            $result = PhoneNumber::make()->parse(
                value: (string) $state,
                country: $component->getSelectedCountry(),
                allowedCountries: $component->getCountries(),
                type: $component->getPhoneType(),
            );

            $number = $component->shouldSaveAsE164() && $result->valid
                ? $result->e164
                : $state;

            if ($component->shouldSaveAsArray()) {
                return [
                    'number' => $number,
                    'country' => $result->valid ? $result->country : $component->getSelectedCountry(),
                ];
            }

            return $number;
        });

        $this->rule(function (PhoneField $component): ValidPhoneNumber {
            return new ValidPhoneNumber(
                countries: $component->getCountries(),
                country: $component->getSelectedCountry(),
                type: $component->getPhoneType(),
                required: $component->isRequired(),
            );
        });
    }
/*
    public function prefixIcon(?string $icon): self
    {
        $this->prefixIcon = $icon;
        return $this;
    }

    public function getPrefixIcon(): ?string
    {
        return $this->prefixIcon;
    }

    public function prefixIconColor(?string $color): self
    {
        $this->prefixIconColor = $color;
        return $this;
    }

    public function getPrefixIconColor(): ?string
    {
        return $this->prefixIconColor;
    }

    public function suffixIcon(?string $icon): self
    {
        $this->suffixIcon = $icon;
        return $this;
    }

    public function getSuffixIcon(): ?string
    {
        return $this->suffixIcon;
    }

    public function suffixIconColor(?string $color): self
    {
        $this->suffixIconColor = $color;
        return $this;
    }

    public function getSuffixIconColor(): ?string
    {
        return $this->suffixIconColor;
    }*/

    public function mobile(): self
    {
        $this->phoneType = PhoneType::Mobile;

        $this->suffixIcon = 'heroicon-o-device-phone-mobile';

        return $this;
    }

    public function mobileOnly(): self
    {
        return $this->mobile();
    }

    public function landline(): self
    {
        $this->phoneType = PhoneType::Landline;

        return $this;
    }

    public function fixedLineOnly(): self
    {
        return $this->landline();
    }

    public function phoneType(PhoneType | string | Closure $type): self
    {
        $this->phoneType = $type instanceof PhoneType || $type instanceof Closure
            ? $type
            : PhoneType::from($type);

        return $this;
    }

    public function autoDetectCountry(bool | Closure $condition = true): self
    {
        $this->autoDetectCountry = $condition;

        return $this;
    }

    public function formatStateUsingLibPhoneNumber(bool | Closure $condition = true): self
    {
        $this->formatStateUsingLibPhoneNumber = $condition;

        return $this;
    }

    public function saveAsE164(bool | Closure $condition = true): self
    {
        $this->saveAsE164 = $condition;

        return $this;
    }

    public function saveAsArray(bool | Closure $condition = true): self
    {
        $this->saveAsArray = $condition;

        return $this;
    }

    public function countryStatePath(?string $path): self
    {
        $this->countryStatePath = $path;

        return $this;
    }

    public function getPhoneType(): PhoneType
    {
        $type = $this->evaluate($this->phoneType);

        if ($type instanceof PhoneType) {
            return $type;
        }

        return PhoneType::from((string) $type);
    }

    public function shouldAutoDetectCountry(): bool
    {
        return (bool) $this->evaluate($this->autoDetectCountry);
    }

    public function shouldFormatStateUsingLibPhoneNumber(): bool
    {
        return (bool) $this->evaluate($this->formatStateUsingLibPhoneNumber);
    }

    public function shouldSaveAsE164(): bool
    {
        return (bool) $this->evaluate($this->saveAsE164);
    }

    public function shouldSaveAsArray(): bool
    {
        return (bool) $this->evaluate($this->saveAsArray);
    }

    public function getCountryStatePath(): string
    {
        if ($this->countryStatePath !== null) {
            return $this->countryStatePath;
        }

        return $this->getInternalCountryStatePath();
    }

    public function hasExternalCountryStatePath(): bool
    {
        return $this->countryStatePath !== null;
    }

    public function getInternalCountryStatePath(): string
    {
        return $this->internalCountryStatePath ?? ($this->internalCountryStatePath = $this->getStatePath() . '_country');
    }

    public function getSelectedCountry(): ?string
    {
        $path = $this->getCountryStatePath();
        $value = data_get($this->getLivewire(), $path);

        if (is_string($value) && trim($value) !== '') {
            return strtoupper(trim($value));
        }

        return $this->getDefaultCountry();
    }

    public function setSelectedCountry(?string $country): void
    {
        $country = strtoupper(trim((string) $country));

        if ($country === '') {
            return;
        }

        $component = $this->getLivewire();
        data_set(
            target: $component,
            key: $this->getCountryStatePath(),
            value: $country,
        );
    }

    /**
     * @return array<string, array{iso: string, name: string, dial_code: int, flag_url: string, label: string}>
     */
    public function getCountryOptions(): array
    {
        return app(PhoneCountryRepository::class)
            ->all($this->getCountries())
            ->mapWithKeys(fn ($option): array => [
                $option->iso => $option->toArray(),
            ])
            ->all();
    }

    public function detectCountryFromState(): ?string
    {
        $state = $this->getState();

        if (! is_scalar($state) || trim((string) $state) === '') {
            return null;
        }

        $result = PhoneNumber::make()->parse(
            value: (string) $state,
            country: $this->getSelectedCountry(),
            allowedCountries: $this->getCountries(),
            type: $this->phoneType,
        );

        if (! $result->valid) {
            return null;
        }

        return $result->country;
    }
}
