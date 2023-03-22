<?php

namespace Playcat\Queue\Protocols;


use Playcat\Queue\Model\Payload;

/**
 * Interface Consumer
 */
interface Consumer
{
    public function consume(Payload $data);
}

