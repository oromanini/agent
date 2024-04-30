<?php

namespace App\Services;

interface BaseService
{
    public function store(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): array;
    public function fillObject(array $data, ?object $incidence = null):object;
}
