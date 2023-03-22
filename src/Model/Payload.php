<?php

namespace Playcat\Queue\Model;

use Playcat\Queue\Exceptions\ParamsError;

class Payload
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
    public function __construct(array $payload = [])
    {
        if (!empty($payload)) {
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
        } else {
            $this->creat_time = time();
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
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @param string $channel
     * @return void
     */
    public function setChannel(string $channel = 'default'): void
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }


    /**
     * @param int $count
     * @return void
     */
    public function setRetryCount(int $count = 0): void
    {
        $this->retry_count = $count;
    }

    /**
     * @return int
     */
    public function getRetryCount(): int
    {
        return $this->retry_count;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setQueueData(array $data): void
    {
        $this->queue_data = json_encode($data);
    }

    /**
     * @return array
     */
    public function getQueueData(): ?array
    {
        return json_decode($this->queue_data);
    }

    public function setDelayTime(int $delay_time = 0)
    {
        $this->delay_time = $delay_time;
    }

    public function getDelayTime(): int
    {
        return $this->delay_time;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return [
            'channel' => $this->channel,
            'creattime' => $this->creat_time,
            'retrycount' => $this->retry_count,
            'queuedata' => $this->queue_data,
            'delaytime' => $this->delay_time
        ];
    }

}