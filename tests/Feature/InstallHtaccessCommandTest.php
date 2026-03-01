<?php

namespace Jaikumar0101\LaravelHtaccess\Tests\Feature;

use Jaikumar0101\LaravelHtaccess\Tests\TestCase;

class InstallHtaccessCommandTest extends TestCase
{
    /**
     * Clean up any .htaccess files created during tests.
     */
    protected function tearDown(): void
    {
        @unlink(base_path('.htaccess'));
        @unlink(public_path('.htaccess'));

        parent::tearDown();
    }

    /** @test */
    public function it_places_both_htaccess_files_by_default(): void
    {
        $this->artisan('htaccess:install')
            ->assertSuccessful();

        $this->assertFileExists(base_path('.htaccess'));
        $this->assertFileExists(public_path('.htaccess'));
    }

    /** @test */
    public function it_places_only_root_htaccess_with_root_only_flag(): void
    {
        $this->artisan('htaccess:install', ['--root-only' => true])
            ->assertSuccessful();

        $this->assertFileExists(base_path('.htaccess'));
        $this->assertFileDoesNotExist(public_path('.htaccess'));
    }

    /** @test */
    public function it_places_only_public_htaccess_with_public_only_flag(): void
    {
        $this->artisan('htaccess:install', ['--public-only' => true])
            ->assertSuccessful();

        $this->assertFileDoesNotExist(base_path('.htaccess'));
        $this->assertFileExists(public_path('.htaccess'));
    }

    /** @test */
    public function it_does_not_overwrite_existing_files_without_force(): void
    {
        // Pre-create files with custom content
        file_put_contents(base_path('.htaccess'), 'custom root content');
        file_put_contents(public_path('.htaccess'), 'custom public content');

        $this->artisan('htaccess:install')
            ->assertSuccessful();

        // Content should remain unchanged
        $this->assertStringContainsString('custom root content', file_get_contents(base_path('.htaccess')));
        $this->assertStringContainsString('custom public content', file_get_contents(public_path('.htaccess')));
    }

    /** @test */
    public function it_overwrites_existing_files_with_force_flag(): void
    {
        // Pre-create files with custom content
        file_put_contents(base_path('.htaccess'), 'custom root content');
        file_put_contents(public_path('.htaccess'), 'custom public content');

        $this->artisan('htaccess:install', ['--force' => true])
            ->assertSuccessful();

        // Content should now be replaced with stub content
        $this->assertStringNotContainsString('custom root content', file_get_contents(base_path('.htaccess')));
        $this->assertStringNotContainsString('custom public content', file_get_contents(public_path('.htaccess')));
    }

    /** @test */
    public function root_htaccess_contains_rewrite_rule_to_public(): void
    {
        $this->artisan('htaccess:install', ['--root-only' => true])
            ->assertSuccessful();

        $content = file_get_contents(base_path('.htaccess'));

        $this->assertStringContainsString('RewriteEngine On', $content);
        $this->assertStringContainsString('public/$1', $content);
    }

    /** @test */
    public function public_htaccess_contains_index_php_rewrite_rule(): void
    {
        $this->artisan('htaccess:install', ['--public-only' => true])
            ->assertSuccessful();

        $content = file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('RewriteEngine On', $content);
        $this->assertStringContainsString('index.php', $content);
    }

    /** @test */
    public function root_htaccess_blocks_env_file_access(): void
    {
        $this->artisan('htaccess:install', ['--root-only' => true])
            ->assertSuccessful();

        $content = file_get_contents(base_path('.htaccess'));

        $this->assertStringContainsString('.env', $content);
        $this->assertStringContainsString('Deny from all', $content);
    }

    /** @test */
    public function command_is_registered_and_accessible(): void
    {
        $commands = $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->all();

        $this->assertArrayHasKey('htaccess:install', $commands);
    }
}
