<?php

declare(strict_types=1);

namespace RichanFongdasen\DatabaseConverter\Commands;

use Illuminate\Console\Command;
use RichanFongdasen\DatabaseConverter\DatabaseConverterManager;

class DatabaseConverterCommand extends Command
{
    public $signature = 'db:convert {sourceConnection} {targetConnection}';

    public $description = 'Convert database from one connection to another.';

    public function handle(): int
    {
        DatabaseConverterManager::make(
            $this,
            (string) $this->argument('sourceConnection'),
            (string) $this->argument('targetConnection')
        )->run();

        return self::SUCCESS;
    }
}
