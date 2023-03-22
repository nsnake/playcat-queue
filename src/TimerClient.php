<?php

namespace Playcat\Queue;


use Dotenv\Exception\ValidationException;
use Playcat\Queue\Exceptions\ConnectTimerServerFail;
use Playcat\Queue\Model\Payload;

class TimerClient
{
    protected static $instance;
    protected static $client;
    private $config;

    public static function getInstance($config = []): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return false|resource
     * @throws ConnectTimerServerFail
     */
    private function client()
    {
        if (!self::$client) {
            self::$client = stream_socket_client('tcp://' . $this->config ['timerserver'], $errno, $errstr);
            if (!self::$client) {
                throw new ConnectTimerServerFail('Connect to playcat time server failed. ' . $errstr);
            }
        }

        return self::$client;
    }

    /**
     * @param Payload $payload
     * @return string
     */
    public function send(Payload $payload): string
    {
        try {
            fwrite($this->client(), json_encode($payload->getPayload()) . "\n");
            $result = fread($this->client(), 1024);
            return $result;
        } catch (ConnectTimerServerFail $e) {
            return '';
        }
    }

}