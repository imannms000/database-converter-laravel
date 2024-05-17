<?php

declare(strict_types=1);

namespace RichanFongdasen\DatabaseConverter;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TableConverter
{
    public function __construct(
        protected DatabaseConverterManager $manager,
        protected string $tableName
    ) {
    }

    public static function make(
        DatabaseConverterManager $manager,
        string $tableName
    ): self {
        return new TableConverter($manager, $tableName);
    }

    public function run(): void
    {
        $recordCount = $this->manager->source()
            ->table($this->tableName)
            ->count();

        $generatedColumns = collect(
            $this->manager->target()
                ->getSchemaBuilder()
                ->getColumns($this->tableName)
        )->keyBy('name')
            ->whereNotNull('generation')
            ->keys()
            ->toArray();

        $this->manager->command()->info(sprintf(
            'Converting %s records from table `%s`',
            number_format($recordCount),
            $this->tableName
        ));

        $progressBar = $this->manager->command()
            ->getOutput()
            ->createProgressBar($recordCount);

        $firstColumn = (string) collect(
            $this->manager->source()
                ->getSchemaBuilder()
                ->getColumns($this->tableName)
        )->keyBy('name')->keys()->first();

        $this->manager->source()
            ->table($this->tableName)
            ->orderBy($firstColumn, 'asc')
            ->chunk((int) config('database-converter-laravel.chunk_size', 700), function (Collection $rows) use ($progressBar, $generatedColumns) {
                $data = $rows->map(function ($row) use ($generatedColumns) {
                    $record = (array) $row;

                    Arr::forget($record, $generatedColumns);

                    return $record;
                })->toArray();

                $this->manager->target()
                    ->table($this->tableName)
                    ->insert($data);

                $progressBar->advance($rows->count());

                unset($rows, $data);
            });

        $progressBar->finish();

        $this->manager->command()->newLine();
        $this->manager->command()->info(sprintf(
            'All %s records from table %s has been converted successfully.',
            number_format($recordCount),
            $this->tableName
        ));
        $this->manager->command()->newLine();
    }
}
