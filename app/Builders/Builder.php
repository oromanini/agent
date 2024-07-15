<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Model;

interface Builder
{
    public function build(): Model;
}
