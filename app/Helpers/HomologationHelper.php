<?php

namespace App\Helpers;

use App\Services\HomologationService;

class HomologationHelper
{
    public const MODEL_NAME = 'Homologation';
    public const STATUS_TRT_PAY_ORDER = 18;
    public const STATUS_PROOF_OF_BILL_PAYMENT = 15;
    public const STATUS_ACCESS_OPINION_FORM = 16;
    public const STATUS_SIGNED_ACCESS_OPINION_FORM = 17;
    public const STATUS_AWAITING = 13;

    public const FIELD_TRT_PAY_ORDER = 'trt_pay_order';
    public const FIELD_PROOF_OF_BILL_PAYMENT = 'proof_of_bill_payment';
    public const FIELD_ACCESS_OPINION_FORM = 'access_opinion_form';
    public const FIELD_SIGNED_ACCESS_OPINION_FORM = 'signed_access_opinion_form';



    public static function matchStatus(string $key): int
    {
        return match ($key) {
            self::FIELD_TRT_PAY_ORDER => self::STATUS_TRT_PAY_ORDER,
            self::FIELD_PROOF_OF_BILL_PAYMENT => self::STATUS_PROOF_OF_BILL_PAYMENT,
            self::FIELD_ACCESS_OPINION_FORM => self::STATUS_ACCESS_OPINION_FORM,
            self::FIELD_SIGNED_ACCESS_OPINION_FORM => self::STATUS_SIGNED_ACCESS_OPINION_FORM,
            default => self::STATUS_AWAITING
        };
    }

    public static function translateItem(string $item): string
    {
        $homologationService = new HomologationService();

        return match ($item) {
            self::FIELD_TRT_PAY_ORDER => $homologationService::CHECKLIST_ITEM_DUPLICATE_EMITTED,
            self::FIELD_PROOF_OF_BILL_PAYMENT => $homologationService::CHECKLIST_ITEM_DUPLICATE_PAYED,
            self::FIELD_ACCESS_OPINION_FORM => $homologationService::CHECKLIST_ITEM_ACCESS_FORM_EMITTED,
            self::FIELD_SIGNED_ACCESS_OPINION_FORM => $homologationService::CHECKLIST_ITEM_ACCESS_FORM_SIGNED,
            default => ''
        };
    }

    public static function setListenedFields(): array
    {
        return [
            'trt_pay_order',
            'proof_of_bill_payment',
            'access_opinion_form',
            'signed_access_opinion_form',
        ];
    }
}
