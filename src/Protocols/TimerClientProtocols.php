<?php

namespace Playcat\Queue\Protocols;

class TimerClientProtocols
{
    const CMD_PUSH = 'push';
    const CMD_DEL = 'del';

    protected $ver = '1.0';
    protected $cmd;

    protected $payload;

    public function setCMD(string $cmd): void
    {
        $this->cmd = $cmd;
    }

    public function getCMD(): string
    {
        return $this->cmd;
    }

    public function setPayload(ProducerDataInterface $producerdata): void
    {
        $this->payload = $producerdata;
    }

    public function getPayload(): ProducerDataInterface
    {
        return $this->payload;
    }

    public function getVer(): string
    {
        return $this->ver;
    }
}


