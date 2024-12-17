<?php

namespace Tests\Unit\Pricingv2;

use App\Models\Pricing\Enums\CardTaxEnum;
use Tests\TestCase;

class CardTaxEnumTest extends TestCase
{
    public function testCalculateInstallmentsMethod_WithFinalValue_ShouldReturnCorrectlyInstallments(): void
    {
        $installments = CardTaxEnum::calculateInstallments(10000.00);

        $expected = [
              1 => 10307.15,
              2 => 5192.11,
              3 => 3481.29,
              4 => 2625.77,
              5 => 2112.82,
              6 => 1770.6,
              7 => 1524.13,
              8 => 1341.2,
              9 => 1198.87,
              10 => 1085.07,
              11 => 992.02,
              12 => 914.54,
              13 => 848.85,
              14 => 792.68,
              15 => 743.96,
              16 => 701.3,
              17 => 663.77,
              18 => 630.38,
        ];

        $this->assertEquals($expected, $installments);
    }
}
