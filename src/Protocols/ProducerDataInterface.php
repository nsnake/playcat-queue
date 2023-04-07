<?php

namespace Playcat\Queue\Protocols;

interface ProducerDataInterface
{

    /**
     * @param string $id
     * @return void
     */
    public function setID(string $id): void;

    /**
     * @return string
     */
    public function getID(): string;

    /**
     * @param string $channel
     * @return void
     */
    public function setChannel(string $channel = 'default'): void;

    /**
     * @return string
     */
    public function getChannel(): string;


    /**
     * @param int $count
     * @return void
     */
    public function setRetryCount(int $count = 0): void;

    /**
     * @return int
     */
    public function getRetryCount(): int;

    /**
     * @param array $data
     * @return void
     */
    public function setQueueData(array $data): void;

    /**
     * @return array
     */
    public function getQueueData(): ?array;

    /**
     * @param int $delay_time
     * @return mixed
     */
    public function setDelayTime(int $delay_time = 0);

    /**
     * @return int
     */
    public function getDelayTime(): int;

    /**
     *  Return an array data
     * @return array
     */
    public function getArray(): array;

    /**
     * Return JSON strings data
     * @return string
     */
    public function getJSON(): string;


}


