<?php

namespace N3m3s7s\FilamentPhoneField\Tests\Unit\Rules;

use N3m3s7s\FilamentPhoneField\Rules\ValidPhoneNumber;
use N3m3s7s\FilamentPhoneField\Tests\TestCase;

class ValidPhoneNumberTest extends TestCase
{
    public function test_it_passes_on_empty_values(): void
    {
        $rule = new ValidPhoneNumber();
        $failCalled = false;

        $rule->validate('phone', '', function () use (&$failCalled) {
            $failCalled = true;
        });

        $this->assertFalse($failCalled);
    }

    public function test_it_validates_correct_mobile_number(): void
    {
        $rule = new ValidPhoneNumber();
        $failCalled = false;

        $rule->validate('phone', '+393491234567', function () use (&$failCalled) {
            $failCalled = true;
        });

        $this->assertFalse($failCalled);
    }

    public function test_it_validates_correct_landline_number(): void
    {
        $rule = new ValidPhoneNumber();
        $failCalled = false;

        $rule->validate('phone', '+390612345678', function () use (&$failCalled) {
            $failCalled = true;
        });

        $this->assertFalse($failCalled);
    }

    public function test_it_fails_on_invalid_number_format(): void
    {
        $rule = new ValidPhoneNumber();
        $failMessage = null;

        $rule->validate('phone', '+39123', function ($message) use (&$failMessage) {
            $failMessage = $message;
        });

        $this->assertNotNull($failMessage);
        $this->assertEquals(__('filament-phone-field::validation.invalid_number'), $failMessage);
    }

    public function test_it_fails_when_string_cannot_be_parsed(): void
    {
        $rule = new ValidPhoneNumber();
        $failMessage = null;

        $rule->validate('phone', 'not-a-phone-number', function ($message) use (&$failMessage) {
            $failMessage = $message;
        });

        $this->assertNotNull($failMessage);
        $this->assertEquals(__('filament-phone-field::validation.invalid_format'), $failMessage);
    }

    public function test_it_respects_allowed_countries_restrictions(): void
    {
        $rule = new ValidPhoneNumber(['US']);
        $failMessage = null;

        $rule->validate('phone', '+393491234567', function ($message) use (&$failMessage) {
            $failMessage = $message;
        });

        $this->assertNotNull($failMessage);
        $this->assertEquals(__('filament-phone-field::validation.invalid_country'), $failMessage);
    }
}