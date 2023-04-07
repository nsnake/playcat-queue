<?php

namespace Playcat\Queue\Protocols;

use Playcat\Queue\Model\Payload;

interface DriverInterface
{
    public function setIconicId(int $iconic_id = 0): void;

    public function subscribe(array $channels): bool;

    public function shift(): ?ConsumerDataInterface;

    public function push(ProducerDataInterface $payload): ?string;

    public function consumerFinished(): bool;
}

