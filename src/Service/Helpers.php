<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class Helpers
{
    private $langue;
    public function __construct(private LoggerInterface $logger) {
    }
    public function sayCc(): string {
        $this->logger->info('Je dis cc');
        return 'cc';
    }
}