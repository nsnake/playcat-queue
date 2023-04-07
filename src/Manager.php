<?php

namespace Playcat\Queue;

use Playcat\Queue\Model\Payload;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\DriverInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;

class Manager implements DriverInterface
{
    protected static $instance;
    protected $driver;
    private $timer_client;

    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $config = config('plugin.playcat.queue.manager', []);
        $this->driver = new $config['driver'];
        $this->timer_client = TimerClient::getInstance([
            'timerserver' => $config['timerserver']]);
    }


    public function setIconicId(int $iconic_id = 0): void
    {
        $this->driver->setIconicId($iconic_id);
    }

    public function subscribe(array $channels): bool
    {
        return $this->driver->subscribe($channels);
    }


    public function shift(): ?ConsumerDataInterface
    {
        return $this->driver->shift();
    }

    public function push(ProducerDataInterface $payload): ?string
    {
        if ($payload->getDelayTime() >= 3) {
            $this->timer_client->send($payload);
            return '';
        } else {
            return $this->driver->push($payload);
        }
    }

    public function consumerFinished(): bool
    {
        return $this->driver->consumerFinished();
    }

}