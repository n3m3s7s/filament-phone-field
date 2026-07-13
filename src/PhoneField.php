<?php

declare(strict_types=1);

namespace N3m3s7s\FilamentPhoneField;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use N3m3s7s\FilamentPhoneField\Concerns\HasPhoneCountries;
use N3m3s7s\FilamentPhoneField\Enums\PhoneType;
use N3m3s7s\FilamentPhoneField\Rules\ValidPhoneNumber;
use N3m3s7s\FilamentPhoneField\Support\PhoneCountryRepository;
use N3m3s7s\FilamentPhoneField\Support\PhoneNumber;

final class PhoneField extends Field
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

    protected ?string $iconName = 'heroicon-m-phone';

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefixIcon($this->getIcon());

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
                type: PhoneType::Any,
            );

            if (! $result->valid) {
                return;
            }

            $component->state($result->international);
            $component->setSelectedCountry($result->country);
        });

        $this->afterStateUpdated(function (PhoneField $component, mixed $state): void {
            if (! $component->shouldAutoDetectCountry()) {
                return;
            }

            if (! is_scalar($state) || trim((string) $state) === '') {
                return;
            }

            $country = $component->detectCountryFromState();

            if ($country === null) {
                return;
            }

            if (! in_array($country, $component->getCountries(), true)) {
                return;
            }

            $component->setSelectedCountry($country);
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

    public function icon(?string $icon): static
    {
        $this->iconName = $icon;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->iconName;
    }

    public function mobile(): static
    {
        $this->phoneType = PhoneType::Mobile;

        return $this;
    }

    public function landline(): static
    {
        $this->phoneType = PhoneType::Landline;

        return $this;
    }

    public function phoneType(PhoneType | string | Closure $type): static
    {
        $this->phoneType = $type instanceof PhoneType || $type instanceof Closure
            ? $type
            : PhoneType::from($type);

        return $this;
    }

    public function autoDetectCountry(bool | Closure $condition = true): static
    {
        $this->autoDetectCountry = $condition;

        return $this;
    }

    public function formatStateUsingLibPhoneNumber(bool | Closure $condition = true): static
    {
        $this->formatStateUsingLibPhoneNumber = $condition;

        return $this;
    }

    public function saveAsE164(bool | Closure $condition = true): static
    {
        $this->saveAsE164 = $condition;

        return $this;
    }

    public function saveAsArray(bool | Closure $condition = true): static
    {
        $this->saveAsArray = $condition;

        return $this;
    }

    public function countryStatePath(?string $path): static
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
        if ($this->internalCountryStatePath !== null) {
            return $this->internalCountryStatePath;
        }

        return $this->internalCountryStatePath = $this->getStatePath() . '_country';
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

        data_set(
            target: $this->getLivewire(),
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
            type: PhoneType::Any,
        );

        if (! $result->valid) {
            return null;
        }

        return $result->country;
    }

    /**
     * @param array<int, string> $countries
     * @return array<int, Filament\Forms\Components\Component>
     */
    public static function makeWithCountrySelect(
        string $phoneName,
        string $countryName,
        array $countries,
        PhoneType $type = PhoneType::Any,
        ?string $defaultCountry = null,
    ): array {
        $repository = app(PhoneCountryRepository::class);

        $options = $repository
            ->all($countries)
            ->mapWithKeys(fn ($option): array => [
                $option->iso => "{$option->name} +{$option->dialCode}",
            ])
            ->all();

        return [
            Select::make($countryName)
                //->label(__('Country'))
                ->hideLabel()
                ->options($options)
                ->default($defaultCountry ?: config('filament-phone-field.default_country'))
                ->searchable()
                ->preload()
                ->native(false)
                ->required()
                ->live(),

            self::make($phoneName)
                ->countries($countries)
                ->countryStatePath($countryName)
                ->phoneType($type)
                ->defaultCountry($defaultCountry)
                ->saveAsE164()
                //->label($this->getLabel()),
        ];
    }
}
