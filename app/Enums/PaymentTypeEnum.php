<?php

namespace App\Enums;

enum PaymentTypeEnum
{
    const CASH_PAYMENT = 1;
    const FINANCING = 2;
    const CREDIT_CARD = 3;
    const CUSTOM = 4;
}
