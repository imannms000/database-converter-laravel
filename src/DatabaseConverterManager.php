<?php

declare(strict_types=1);

namespace RichanFongdasen\DatabaseConverter;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use RichanFongdasen\DatabaseConverter\Commands\DatabaseConverterCommand;

class DatabaseConverterManager
{
    public function __construct(
        protected DatabaseConverterCommand $command,
        protected string $sourceConnection,
        protected string $targetConnection
    ) {}

    public static function make(
        DatabaseConverterCommand $command,
        string $sourceConnection,
        string $targetConnection
    ): self {
        return new DatabaseConverterManager($command, $sourceConnection, $targetConnection);
    }

    public function command(): DatabaseConverterCommand
    {
        return $this->command;
    }

    /**
     * Get the source database connection.
     *
     * @return Connection
     */
    public function source()
    {
        return DB::connection($this->sourceConnection);
    }

    /**
     * Get the target database connection.
     *
     * @return Connection
     */
    public function target()
    {
        return DB::connection($this->targetConnection);
    }

    public function run(): void
    {
        $tables = collect(
            DB::connection($this->sourceConnection)
                ->getSchemaBuilder()
                ->getTableListing()
        );

        $this->target()
            ->getSchemaBuilder()
            ->disableForeignKeyConstraints();

        $tables->each(function (string $table) {
            if ($table === 'migrations') {
                return;
            }

            $ignoreTables = (array) config('database-converter-laravel.ignore_tables', []);
            if (in_array($table, $ignoreTables, true)) {
                return;
            }

            TableConverter::make($this, $table)
                ->run();
        });
    }
}
