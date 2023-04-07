<?php

namespace Playcat\Queue\Protocols;


/**
 * Interface Consumer
 */
interface ConsumerInterface
{
    public function consume(ConsumerDataInterface $data);
}

