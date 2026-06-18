<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncRemoteData extends Command
{
    protected $signature = 'sync:remote
                            {--tables=all : Comma-separated table list or "all"}
                            {--truncate : Clear local tables before sync}';

    protected $description = 'Sync data from remote PostgreSQL database to local MySQL';

    private array $tables = [
        'categories',
        'products',
        'banners',
        'users',
        'orders',
        'order_items',
        'carts',
        'reviews',
        'wishlists',
        'coupons',
        'settings',
        'personal_access_tokens',
    ];

    public function handle(): int
    {
        $remoteConfig = [
            'driver' => env('REMOTE_DB_CONNECTION', 'pgsql'),
            'host' => env('REMOTE_DB_HOST'),
            'port' => env('REMOTE_DB_PORT', 5432),
            'database' => env('REMOTE_DB_DATABASE'),
            'username' => env('REMOTE_DB_USERNAME'),
            'password' => env('REMOTE_DB_PASSWORD'),
            'charset' => 'utf8',
        ];

        if (! $remoteConfig['host'] || ! $remoteConfig['database']) {
            $this->error('Remote DB not configured. Add these to your .env:');
            $this->line('REMOTE_DB_HOST=your-server-ip');
            $this->line('REMOTE_DB_PORT=5432');
            $this->line('REMOTE_DB_DATABASE=your-db-name');
            $this->line('REMOTE_DB_USERNAME=your-db-user');
            $this->line('REMOTE_DB_PASSWORD=your-db-password');
            return Command::FAILURE;
        }

        config(['database.connections.remote' => $remoteConfig]);
        DB::purge('remote');

        // Test connection
        try {
            DB::connection('remote')->getPdo();
            $this->info('Connected to remote database.');
        } catch (\Exception $e) {
            $this->error('Cannot connect to remote DB: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $tables = $this->option('tables') === 'all'
            ? $this->tables
            : explode(',', $this->option('tables'));

        foreach ($tables as $table) {
            $this->syncTable($table);
        }

        $this->info('Sync completed!');
        return Command::SUCCESS;
    }

    private function syncTable(string $table): void
    {
        $this->info("Syncing table: {$table}");

        if (! Schema::hasTable($table)) {
            $this->warn("Table {$table} does not exist locally. Skipping.");
            return;
        }

        $remoteCount = DB::connection('remote')->table($table)->count();
        $this->line("Remote rows: {$remoteCount}");

        if ($remoteCount === 0) {
            $this->warn("No data in remote {$table}. Skipping.");
            return;
        }

        if ($this->option('truncate')) {
            DB::table($table)->delete();
            $this->line("Local table {$table} cleared.");
        }

        $localCount = DB::table($table)->count();
        if ($localCount > 0 && ! $this->option('truncate')) {
            $this->warn("Local {$table} has {$localCount} rows. Use --truncate to replace.");
            return;
        }

        $batchSize = 500;
        $inserted = 0;

        DB::connection('remote')->table($table)->orderBy('id')->chunk($batchSize, function ($rows) use ($table, &$inserted) {
            $data = [];
            foreach ($rows as $row) {
                $item = (array) $row;
                // PostgreSQL timestamps may have timezone (+00), MySQL doesn't accept it
                foreach (['created_at', 'updated_at', 'starts_at', 'expires_at', 'shipped_at', 'delivered_at', 'start_date', 'end_date'] as $col) {
                    if (isset($item[$col]) && is_string($item[$col])) {
                        $item[$col] = preg_replace('/\.\d+([+-]\d{2})?$/', '', $item[$col]);
                        $item[$col] = preg_replace('/[+-]\d{2}$/', '', $item[$col]);
                    }
                }
                $data[] = $item;
            }

            if (! empty($data)) {
                DB::table($table)->insert($data);
                $inserted += count($data);
                $this->line("Inserted {$inserted} rows...");
            }
        });

        $this->info("{$table}: {$inserted} rows synced.");
    }
}
