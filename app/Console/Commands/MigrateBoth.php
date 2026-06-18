<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MigrateBoth extends Command
{
    protected $signature = 'migrate:both {--fresh : Drop all tables and re-run migrations}';

    protected $description = 'Run migrations locally and optionally on remote server via SSH';

    public function handle(): int
    {
        $remoteHost = env('REMOTE_SSH_HOST');
        $remoteUser = env('REMOTE_SSH_USER');
        $remotePath = env('REMOTE_APP_PATH');
        $remotePhp = env('REMOTE_PHP_PATH', 'php');

        // 1. Run locally
        $this->info('Running migrations on LOCAL database...');
        $localCommand = $this->option('fresh') ? 'migrate:fresh' : 'migrate';
        $local = Process::fromShellCommandline('php artisan ' . $localCommand);
        $local->setWorkingDirectory(base_path());
        $local->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $local->isSuccessful()) {
            $this->error('Local migration failed!');
            return Command::FAILURE;
        }

        $this->info('Local migration completed successfully.');

        // 2. Run remotely (if configured)
        if (! $remoteHost || ! $remoteUser || ! $remotePath) {
            $this->warn('Remote SSH not configured in .env. Skipping remote migration.');
            $this->warn('Set REMOTE_SSH_HOST, REMOTE_SSH_USER, and REMOTE_APP_PATH to enable remote sync.');
            return Command::SUCCESS;
        }

        $this->info('Running migrations on REMOTE server (' . $remoteHost . ')...');

        $remoteCommand = $remotePhp . ' artisan ' . $localCommand;
        $sshCommand = sprintf(
            'ssh %s@%s "cd %s && %s"',
            $remoteUser,
            $remoteHost,
            $remotePath,
            $remoteCommand
        );

        $remote = Process::fromShellCommandline($sshCommand);
        $remote->setTimeout(120);
        $remote->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $remote->isSuccessful()) {
            $this->error('Remote migration failed!');
            return Command::FAILURE;
        }

        $this->info('Remote migration completed successfully.');
        $this->info('Both environments are now in sync!');

        return Command::SUCCESS;
    }
}
