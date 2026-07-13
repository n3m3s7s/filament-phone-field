<?php

declare(strict_types=1);

namespace N3m3s7s\FilamentPhoneField\Enums;

use libphonenumber\PhoneNumberType as LibPhoneNumberType;

enum PhoneType: string
{
    case Mobile = 'mobile';
    case Landline = 'landline';
    case Any = 'any';

    public function accepts(LibPhoneNumberType $type): bool
    {
        return match ($this) {
            self::Any => in_array($type, [
                LibPhoneNumberType::MOBILE,
                LibPhoneNumberType::FIXED_LINE,
                LibPhoneNumberType::FIXED_LINE_OR_MOBILE,
            ], true),

            self::Mobile => in_array($type, [
                LibPhoneNumberType::MOBILE,
                LibPhoneNumberType::FIXED_LINE_OR_MOBILE,
            ], true),

            self::Landline => in_array($type, [
                LibPhoneNumberType::FIXED_LINE,
                LibPhoneNumberType::FIXED_LINE_OR_MOBILE,
            ], true),
        };
    }
}
