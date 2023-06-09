<?php

namespace Playcat\Queue;


use Dotenv\Exception\ValidationException;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Protocols\ProducerData;
use Playcat\Queue\Protocols\TimerClientProtocols;

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
     * @throws ConnectFailExceptions
     */
    private function client()
    {
        if (!self::$client) {
            self::$client = stream_socket_client('tcp://' . $this->config ['timerserver'], $errno, $errstr);
            if (!self::$client) {
                throw new ConnectFailExceptions('Connect to playcat time server failed. ' . $errstr);
            }
        }

        return self::$client;
    }


    public function send(ProducerData $payload): string
    {
        try {
            $protocols = new TimerClientProtocols();
            $protocols->setCMD(TimerClientProtocols::CMD_PUSH);
            $protocols->setPayload($payload);
            fwrite($this->client(), serialize($protocols) . "\n");
            $result = fread($this->client(), 1024);
            $result = json_decode($result, true);
            return $result['code'] == 200 ? $result['data'] : '';
        } catch (ConnectFailExceptions $e) {
            return '';
        }
    }

}