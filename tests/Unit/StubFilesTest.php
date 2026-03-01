<?php

namespace Jaikumar0101\LaravelHtaccess\Tests\Unit;

use Jaikumar0101\LaravelHtaccess\Tests\TestCase;

class StubFilesTest extends TestCase
{
    private string $stubsPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stubsPath = dirname(__DIR__, 2) . '/stubs';
    }

    /** @test */
    public function root_htaccess_stub_exists(): void
    {
        $this->assertFileExists($this->stubsPath . '/htaccess-root');
    }

    /** @test */
    public function public_htaccess_stub_exists(): void
    {
        $this->assertFileExists($this->stubsPath . '/htaccess-public');
    }

    /** @test */
    public function root_stub_is_not_empty(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-root');

        $this->assertNotEmpty(trim($content));
    }

    /** @test */
    public function public_stub_is_not_empty(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-public');

        $this->assertNotEmpty(trim($content));
    }

    /** @test */
    public function root_stub_contains_mod_rewrite_block(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-root');

        $this->assertStringContainsString('mod_rewrite.c', $content);
        $this->assertStringContainsString('RewriteEngine On', $content);
    }

    /** @test */
    public function root_stub_redirects_to_public_directory(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-root');

        $this->assertStringContainsString('public/$1', $content);
        $this->assertStringContainsString('RewriteRule', $content);
    }

    /** @test */
    public function root_stub_denies_access_to_env_file(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-root');

        $this->assertStringContainsString('.env', $content);
        $this->assertStringContainsString('Deny from all', $content);
    }

    /** @test */
    public function root_stub_denies_access_to_composer_files(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-root');

        $this->assertStringContainsString('composer', $content);
    }

    /** @test */
    public function public_stub_contains_mod_rewrite_block(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-public');

        $this->assertStringContainsString('mod_rewrite.c', $content);
        $this->assertStringContainsString('RewriteEngine On', $content);
    }

    /** @test */
    public function public_stub_routes_to_index_php(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-public');

        $this->assertStringContainsString('index.php', $content);
    }

    /** @test */
    public function public_stub_handles_authorization_header(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-public');

        $this->assertStringContainsString('HTTP:Authorization', $content);
        $this->assertStringContainsString('HTTP_AUTHORIZATION', $content);
    }

    /** @test */
    public function public_stub_redirects_trailing_slashes(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-public');

        $this->assertStringContainsString('R=301', $content);
    }

    /** @test */
    public function public_stub_disables_directory_listing(): void
    {
        $content = file_get_contents($this->stubsPath . '/htaccess-public');

        $this->assertStringContainsString('-Indexes', $content);
    }
}
