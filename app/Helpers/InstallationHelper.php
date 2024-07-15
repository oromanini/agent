<?php

namespace App\Helpers;

use App\Services\InstallationService;

class InstallationHelper
{
    public const MODEL_NAME = 'Instalação';
    public const STATUS_APPOINTMENT_MADE = 25;
    public const STATUS_CA_PURCHASED = 26;
    public const STATUS_INSTALLATION_DONE = 27;
    public const STATUS_AWAITING = 13;

    public const FIELD_INSTALLATION_FORECAST = 'installation_forecast';
    public const FIELD_CA_PURCHASED = 'ca_invoice';
    public const FIELD_INSTALLATION_DONE = 'installation_date';



    public static function matchStatus(string $key): int
    {
        return match ($key) {
            self::FIELD_INSTALLATION_FORECAST => self::STATUS_APPOINTMENT_MADE,
            self::FIELD_CA_PURCHASED => self::STATUS_CA_PURCHASED,
            self::FIELD_INSTALLATION_DONE => self::STATUS_INSTALLATION_DONE,
            default => self::STATUS_AWAITING
        };
    }

    public static function translateItem(string $item): string
    {
        $installationService = new InstallationService();

        return match ($item) {
            self::FIELD_INSTALLATION_FORECAST => $installationService::CHECKLIST_ITEM_APPOINTMENT_MADE,
            self::FIELD_CA_PURCHASED => $installationService::CHECKLIST_ITEM_CA_PURCHASED,
            self::FIELD_INSTALLATION_DONE => $installationService::CHECKLIST_ITEM_INSTALLATION_DONE,
            default => ''
        };
    }

    public static function setListenedFields(): array
    {
        return [
            'installation_forecast',
            'ca_invoice',
            'installation_date',
        ];
    }
}
