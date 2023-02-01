<?php

namespace App\Services;

interface BaseService
{
    public function store($data):array;
    public function update($id, $data):array;
    public function delete($id):array;
    public function fillObject(array $data, ?object $incidence = null):object;
}
