<?php

namespace N3m3s7s\FilamentPhoneField\Tests\Unit;

use N3m3s7s\FilamentPhoneField\PhoneField;
use N3m3s7s\FilamentPhoneField\Tests\TestCase;

class PhoneFieldTest extends TestCase
{
    public function test_it_sets_default_values_correctly(): void
    {
        $field = PhoneField::make('phone');

        $this->assertFalse($field->hasCountryPath());
        $this->assertEmpty($field->getAllowedCountries());
    }

    public function test_it_can_configure_allowed_countries(): void
    {
        $field = PhoneField::make('phone')
            ->allowedCountries(['IT', 'US', 'GB']);

        $this->assertEquals(['IT', 'US', 'GB'], $field->getAllowedCountries());
    }

    public function test_it_can_enable_country_path(): void
    {
        $field = PhoneField::make('phone')
            ->enableCountryPath();

        $this->assertTrue($field->hasCountryPath());
    }

    public function test_it_can_customize_or_hide_the_icon(): void
    {
        $field = PhoneField::make('phone')
            ->suffixIcon('heroicon-o-device-phone-mobile');

        $this->assertEquals('heroicon-o-device-phone-mobile', $field->getSuffixIcon());

        $fieldHide = PhoneField::make('phone')->suffixIcon(null);
        $this->assertNull($fieldHide->getSuffixIcon());
    }
}
