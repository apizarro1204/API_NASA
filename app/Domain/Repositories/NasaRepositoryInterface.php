<?php

namespace App\Domain\Repositories;

interface NasaRepositoryInterface
{
    public function getInstruments(string $startDate, string $endDate): array;
}
