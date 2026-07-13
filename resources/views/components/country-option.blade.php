<span class="flex items-center gap-2">
    <img
        src="{{ $country->flagUrl }}"
        alt="{{ $country->iso }}"
        class="h-5 w-5 rounded-full"
        loading="lazy"
    />

    <span>
        {{ $country->name }} +{{ $country->dialCode }}
    </span>
</span>
