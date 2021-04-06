<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class GetCodeCsv implements ToCollection, WithChunkReading, WithStartRow
{
    public function collection(Collection $rows)
    {
        \App\Jobs\SaveItemsToDb::dispatch($rows);
    }

    public function chunkSize(): int
    {
        return 30000;
    }

    public function startRow(): int
    {
        return 2;
    }
}
