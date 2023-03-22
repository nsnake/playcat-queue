<?php

namespace Playcat\Queue\Protocols;

use Playcat\Queue\Model\Payload;

interface Driver
{
    public function setIconicId(int $iconic_id = 0): void;

    public function subscribe(string $channle): bool;

    public function shift(): ?Payload;

    public function push(Payload $payload): ?string;

    public function consumerFinished(): bool;
}

