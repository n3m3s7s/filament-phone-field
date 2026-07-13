@php
    $id = $getId();
    $statePath = $getStatePath();
    $countryStatePath = $getCountryStatePath();
    $countryOptions = $getCountryOptions();
    $selectedCountry = $getSelectedCountry();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-phone-field grid gap-2',
                ])
        }}
    >
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-[minmax(14rem,18rem)_1fr]">
            <x-filament::input.wrapper>
                <x-filament::input.select
                    :id="$id . '-country'"
                    :disabled="$isDisabled()"
                    wire:model.live="{{ $countryStatePath }}"
                >
                    @foreach ($countryOptions as $key => $country)
                        <option value="{{ $key }}">
                            {{ $country['name'] }} +{{ $country['dial_code'] }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <x-filament::input.wrapper
                :prefix-icon="$getPrefixIcon()"
                :prefix-icon-color="$getPrefixIconColor()"
                :suffix-icon="$getSuffixIcon()"
                :suffix-icon-color="$getSuffixIconColor()"
            >
                <x-filament::input
                    :id="$id"
                    :disabled="$isDisabled()"
                    :placeholder="$getPlaceholder()"
                    type="tel"
                    wire:model.live.debounce.500ms="{{ $statePath }}"
                />
            </x-filament::input.wrapper>
        </div>

        @if ($selectedCountry && isset($countryOptions[$selectedCountry]))
            @php
                $country = $countryOptions[$selectedCountry];
            @endphp

            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <img
                    src="{{ $country['flag_url'] }}"
                    alt="{{ $country['iso'] }}"
                    class="h-5 w-5 rounded-full"
                    loading="lazy"
                />

                <span>
                    {{ $country['name'] }} · +{{ $country['dial_code'] }}
                </span>
            </div>
        @endif
    </div>
</x-dynamic-component>
