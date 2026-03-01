<?php

namespace Jaikumar0101\LaravelHtaccess\Console;

use Illuminate\Console\Command;

class InstallHtaccessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'htaccess:install
                            {--force : Overwrite existing .htaccess files}
                            {--root-only : Only place the root .htaccess}
                            {--public-only : Only place the public .htaccess}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Place .htaccess files in the Laravel root and/or public directory for Apache shared hosting deployment';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rootOnly   = $this->option('root-only');
        $publicOnly = $this->option('public-only');
        $force      = $this->option('force');

        $this->info('Laravel .htaccess Installer');
        $this->line('─────────────────────────────');

        if (! $publicOnly) {
            $this->installRootHtaccess($force);
        }

        if (! $rootOnly) {
            $this->installPublicHtaccess($force);
        }

        $this->newLine();
        $this->info('Done! Your .htaccess file(s) are ready for Apache deployment.');

        return self::SUCCESS;
    }

    /**
     * Install the root .htaccess file.
     */
    protected function installRootHtaccess(bool $force): void
    {
        $destination = base_path('.htaccess');
        $stub        = $this->stubPath('htaccess-root');

        $this->copyFile($stub, $destination, 'Root .htaccess', $force);
    }

    /**
     * Install the public .htaccess file.
     */
    protected function installPublicHtaccess(bool $force): void
    {
        $destination = public_path('.htaccess');
        $stub        = $this->stubPath('htaccess-public');

        $this->copyFile($stub, $destination, 'Public .htaccess', $force);
    }

    /**
     * Copy a stub file to a destination.
     */
    protected function copyFile(string $stub, string $destination, string $label, bool $force): void
    {
        if (file_exists($destination) && ! $force) {
            $this->warn("  ⚠  {$label} already exists at: {$destination}");
            $this->line('     Use --force to overwrite it.');

            return;
        }

        copy($stub, $destination);

        $this->line("  ✔  {$label} placed at: {$destination}");
    }

    /**
     * Get the path to a stub file.
     */
    protected function stubPath(string $stub): string
    {
        return __DIR__ . '/../../stubs/' . $stub;
    }
}
