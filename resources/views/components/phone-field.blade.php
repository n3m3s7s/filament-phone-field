@php
    $id = $getId();
    $statePath = $getStatePath();
    $countryStatePath = $getCountryStatePath();
    $countryOptions = $getCountryOptions();
    // Recuperiamo il valore di default impostato dal tuo componente PHP
    $defaultCountry = $getSelectedCountry();
    // Otteniamo la nazione pre-calcolata e idratata dal metodo afterStateHydrated in PHP
    $hydratedCountry = $getSelectedCountry();
    $labelFilter = __('filament-phone-field::validation.search_country');
    $labelNoResults = __('filament-phone-field::validation.search_no_results');
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-phone-field'])
        }}
    >
        <div class="flex items-start gap-2">

            <!-- Custom Dropdown Wrapper -->
            <x-filament::input.wrapper class="relative w-24 shrink-0">
                <div
                    x-data="{
                        state: $wire.$entangle('{{ $countryStatePath }}').live,
                        serverCountry: @js($hydratedCountry),
                        defaultCountry: @js($defaultCountry),
                        open: false,
                        search: '',
                        options: @js($countryOptions),
                        popoverUp: false,

                        init() {
                            if (this.serverCountry) {
                                this.state = this.serverCountry;
                                return;
                            }
                            // Se il form è vuoto/nuovo, imposta la country di default e sincronizza con Livewire
                            if (!this.state && this.defaultCountry) {
                                this.state = this.defaultCountry;
                            }
                        },

                        get selectedCountry() {
                            return this.options[this.state] || null;
                        },

                        get filteredOptions() {
                            if (this.search === '') {
                                return Object.entries(this.options);
                            }
                            const q = this.search.toLowerCase();
                            return Object.entries(this.options).filter(([key, country]) => {
                                return (country.name && country.name.toLowerCase().includes(q)) ||
                                       (country.dial_code && String(country.dial_code).includes(q)) ||
                                       (key.toLowerCase().includes(q));
                            });
                        },

                        toggle() {
                            if (this.open) {
                                this.close();
                            } else {
                                this.open = true;
                                this.search = '';
                                this.$nextTick(() => {
                                    this.calculatePosition();
                                    this.$refs.searchInput.focus();
                                });
                            }
                        },

                        close() {
                            this.open = false;
                        },

                        calculatePosition() {
                            const buttonRect = this.$refs.button.getBoundingClientRect();
                            const spaceBelow = window.innerHeight - buttonRect.bottom;
                            const dropdownHeight = 280;
                            this.popoverUp = spaceBelow < dropdownHeight && buttonRect.top > dropdownHeight;
                        }
                    }"
                    @click.outside="close()"
                    class="relative w-full"
                >
                    <!-- Trigger Button (Stato Chiuso) -->
                    <div
                        x-ref="button"
                        @click="toggle()"
                        class="flex w-full cursor-pointer items-center justify-between px-3 py-2 text-sm text-gray-950 dark:text-white"
                    >
                        <template x-if="selectedCountry">
                            <span class="flex items-center gap-2">
                                <img :src="selectedCountry.flag_url || selectedCountry.flagUrl" class="h-4 w-4 shrink-0 rounded-full object-cover shadow-sm" loading="lazy" />
                                <span class="font-medium" x-text="'+' + (selectedCountry.dial_code || selectedCountry.dialCode)"></span>
                            </span>
                        </template>
                        <template x-if="!selectedCountry">
                            <span class="text-gray-500 dark:text-gray-400">---</span>
                        </template>

                        <x-filament::icon
                            icon="heroicon-m-chevron-down"
                            class="h-4 w-4 text-gray-500 transition-transform dark:text-gray-400"
                            x-bind:class="open && popoverUp ? 'rotate-180' : (open ? '-rotate-180' : '')"
                        />
                    </div>

                    <!-- Menu a tendina (Stato Aperto) -->
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        x-cloak
                        :class="popoverUp ? 'bottom-full mb-1' : 'top-full mt-1'"
                        class="absolute left-0 z-[9999] flex w-72 flex-col overflow-hidden rounded-lg bg-white shadow-2xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                    >
                        <!-- Campo di Ricerca -->
                        <div class="border-b border-gray-100 bg-gray-50 p-2 dark:border-white/5 dark:bg-gray-800">
                            <input
                                x-ref="searchInput"
                                x-model="search"
                                type="search"
                                placeholder="{!! $labelFilter !!}"
                                class="w-full rounded-md border-0 px-3 py-1.5 text-sm text-gray-950 bg-white shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 dark:bg-gray-900 dark:text-white dark:ring-white/20"
                            />
                        </div>

                        <!-- Lista Risultati (Fondo solido garantito) -->
                        <ul class="max-h-60 overflow-y-auto bg-white p-1 dark:bg-gray-900">
                            <template x-for="[key, country] in filteredOptions" :key="key">
                                <li
                                    @click="state = key; close()"
                                    class="flex cursor-pointer items-center justify-between rounded-md px-3 py-2 text-sm transition-colors hover:bg-gray-100 dark:hover:bg-white/10"
                                    :class="state === key ? 'bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400 font-semibold' : 'text-gray-700 dark:text-gray-200'"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        <img :src="country.flag_url || country.flagUrl" class="h-4 w-4 shrink-0 rounded-full object-cover shadow-sm" loading="lazy" />
                                        <span class="truncate" x-text="country.name"></span>
                                    </div>
                                    <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400" x-text="'+' + (country.dial_code || country.dialCode)"></span>
                                </li>
                            </template>

                            <li x-show="filteredOptions.length === 0" class="p-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                {!! $labelNoResults !!}
                            </li>
                        </ul>
                    </div>
                </div>
            </x-filament::input.wrapper>

            <!-- Input del Numero (Destra) -->
            <x-filament::input.wrapper
                class="flex-1"
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
    </div>
</x-dynamic-component>
