<?php

namespace Playcat\Queue\Driver;

use Playcat\Queue\Exceptions\ParamsError;
use Playcat\Queue\Model\Payload;
use Playcat\Queue\Protocols\Driver;
use RedisException;
use RuntimeException;

class Redis extends Base implements Driver
{

    public const CONSUMERGROUPNAME = 'PLAYCATCONSUMERGROUP';
    protected $channels = [];
    private $redis;
    private $current_id = 0;
    private $current_channel = 0;
    private $config;

    public function __construct(string $config_name = 'default')
    {
        if (!extension_loaded('redis')) {
            throw new RuntimeException('Please make sure the PHP Redis extension is installed and enabled.');
        }
        $configs = config('redis_queue', config('plugin.playcat.queue.redis', []));
        $this->config = $configs[$config_name];
        $this->connectRedis();
    }


    /**
     * @return void
     */
    private function connectRedis(): void
    {
        $this->redis = new \Redis();
        $address = $this->config['host'];
        $this->redis->connect(parse_url($address, PHP_URL_HOST), parse_url($address, PHP_URL_PORT), $this->config['options']['timeout'] ?? 3);
        if ($this->config['options']['auth']) {
            $this->redis->auth($this->config['options']['auth']);
        }
        $this->redis->select($this->config['options']['db'] ?? 0);
    }

    /**
     * @return \Redis
     */
    private function getRedis(): \Redis
    {
        try {
            if (!$this->redis || !$this->redis->ping()) {
                $this->connectRedis();
            }
        } catch (RedisException $e) {

        }
        return $this->redis;
    }

    /**
     * @param array $channels
     * @return bool
     */
    public function subscribe(array $channels): bool
    {
        $result = true;
        foreach ($channels as $channel) {
            $this->channels[$channel] = '>';
            if (!$this->getRedis()
                ->xGroup('CREATE', $channel, self::CONSUMERGROUPNAME, '0', true)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @return Payload|null
     * @throws ParamsError
     */
    public function shift(): ?Payload
    {
        $result = $this->getRedis()
            ->xReadGroup(self::CONSUMERGROUPNAME, "consumer_" . $this->iconic_id, $this->channels, 1);
        if ($result) {
            $this->current_channel = key($result);
            $this->current_id = key($result[$this->current_channel]);
            $result = new Payload($result[$this->current_channel][$this->current_id]);
            $result->setID($this->current_id);
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Remove it when done,
     * @return bool
     */
    public function consumerFinished(): bool
    {
        return ($this->getRedis()->xAck($this->current_channel, self::CONSUMERGROUPNAME, [$this->current_id])
            && $this->getRedis()->xDel($this->current_channel, [$this->current_id]));
    }

    /**
     * @param Payload $payload
     * @return string|null  Unique id
     */
    public function push(Payload $payload): ?string
    {
        return $this->getRedis()->xadd($payload->getChannel(), '*', $payload->getPayload());
    }

}
