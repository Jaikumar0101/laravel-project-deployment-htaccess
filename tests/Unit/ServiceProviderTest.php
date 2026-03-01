<?php

namespace Jaikumar0101\LaravelHtaccess\Tests\Unit;

use Jaikumar0101\LaravelHtaccess\Console\InstallHtaccessCommand;
use Jaikumar0101\LaravelHtaccess\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_the_install_command(): void
    {
        $commands = $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->all();

        $this->assertArrayHasKey('htaccess:install', $commands);
    }

    /** @test */
    public function the_registered_command_is_correct_class(): void
    {
        $commands = $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->all();

        $this->assertInstanceOf(InstallHtaccessCommand::class, $commands['htaccess:install']);
    }

    /** @test */
    public function command_has_correct_signature(): void
    {
        $command = $this->app->make(InstallHtaccessCommand::class);

        $this->assertSame('htaccess:install', $command->getName());
    }

    /** @test */
    public function command_has_a_description(): void
    {
        $command = $this->app->make(InstallHtaccessCommand::class);

        $this->assertNotEmpty($command->getDescription());
    }

    /** @test */
    public function command_has_force_option(): void
    {
        $command = $this->app->make(InstallHtaccessCommand::class);

        $this->assertTrue($command->getDefinition()->hasOption('force'));
    }

    /** @test */
    public function command_has_root_only_option(): void
    {
        $command = $this->app->make(InstallHtaccessCommand::class);

        $this->assertTrue($command->getDefinition()->hasOption('root-only'));
    }

    /** @test */
    public function command_has_public_only_option(): void
    {
        $command = $this->app->make(InstallHtaccessCommand::class);

        $this->assertTrue($command->getDefinition()->hasOption('public-only'));
    }
}
