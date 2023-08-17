<?php

namespace App\Packages\EdeltecApiPackage\Enums;

enum Category: string
{
    case ONGRID = "GERADOR FOTOVOLTAICO";
    case HYBRID = "GERADOR HIBRIDO";
    case MICROINVERTER = "GERADOR MICROINVERSOR";
}
