<?php

namespace Tests;

use Dotenv\Dotenv;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
    }
}