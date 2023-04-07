<?php

namespace Playcat\Queue\Protocols;

use Playcat\Queue\Exceptions\ParamsError;

class ConsumerData implements ConsumerDataInterface
{
    protected $channel = 'default';
    protected $creat_time = 0;
    protected $retry_count = 0;
    protected $queue_data = '[]';
    protected $delay_time = 0;
    protected $id = '';

    /**
     * @param array $payload
     * @throws ParamsError
     */
    public function __construct($payload = '')
    {
        if (!empty($payload)) {
            if (is_string($payload)) {
                $payload = json_decode($payload, true);
            }
            foreach (['creattime', 'channel', 'retrycount', 'queuedata', 'delaytime'] as $value) {
                if (!isset($payload[$value])) {
                    throw new ParamsError('Error payload data. ignore it!');
                }
            }
            $this->creat_time = $payload['creattime'];
            $this->channel = $payload['channel'];
            $this->retry_count = $payload['retrycount'];
            $this->queue_data = $payload['queuedata'];
            $this->delay_time = $payload['delaytime'];
        }
    }

    /**
     * @param string $id
     * @return void
     */
    public function setID(string $id): void
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return int
     */
    public function getRetryCount(): int
    {
        return $this->retry_count;
    }

    /**
     * @return array
     */
    public function getQueueData(): ?array
    {
        return $this->queue_data;
    }


}


