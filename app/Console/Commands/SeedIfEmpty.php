<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedIfEmpty extends Command
{
    protected $signature = 'db:seed-if-empty';

    protected $description = 'Seed database only if it appears to be empty (fresh deploy)';

    public function handle(): int
    {
        $count = DB::table('categories')->count();

        if ($count > 0) {
            $this->info('Database already has data (' . $count . ' categories). Skipping seed.');
            return Command::SUCCESS;
        }

        $this->info('Database appears empty. Running seeders...');
        $this->call('db:seed', ['--force' => true]);
        $this->info('Database seeded successfully!');

        return Command::SUCCESS;
    }
}
