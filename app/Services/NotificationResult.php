<?php

namespace App\Services;

class NotificationResult
{
    public function __construct(
        public readonly int $sent = 0,
        public readonly int $failed = 0,
    ) {}

    /**
     * Get the total number of notification attempts.
     */
    public function total(): int
    {
        return $this->sent + $this->failed;
    }
}
