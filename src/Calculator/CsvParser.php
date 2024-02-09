<?php

namespace App\Calculator;

use App\Calculator\Interfaces\DataProcessorInterface;

class CsvParser implements DataProcessorInterface
{
    public function __construct(private string $csvFile)
    {
    }

    /**
     * Retrieves all data from the CSV file.
     *
     * @return array An array containing all the data from the CSV file.
     */
    public function getAll(): array
    {
        $data = [];
        $file = fopen($this->csvFile, 'r');
        while (($line = fgetcsv($file, 0, ',') ) !== false) {
            $data[] = $line;
        }
        fclose($file);
        return $data;
    }
}
