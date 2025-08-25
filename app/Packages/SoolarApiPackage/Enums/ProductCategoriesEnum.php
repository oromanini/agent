<?php

namespace App\Packages\SoolarApiPackage\Enums;

enum ProductCategoriesEnum: string
{
    case MODULO = 'Modulo';
    case INVERSOR = 'Inversores';
    case ESTRUTURA = 'Estruturas-Inox';
    case CABO = 'Cabos';
    case CONECTOR = 'Conectores';
}
