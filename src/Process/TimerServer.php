<?php

namespace Playcat\Queue\Process;

use ErrorException;
use Playcat\Queue\Model\Payload;
use Workerman\Timer;
use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Playcat\Queue\Manager;

class TimerServer
{
    private $manager;

    /**
     * onWorkerStart.
     */
    public function onWorkerStart(Worker $worker)
    {
        $this->manager = Manager::getInstance();
    }

    public function onConnect(TcpConnection $connection)
    {

    }

    public function onMessage(TcpConnection $connection, $data)
    {
        try {
            $payload = json_decode($data, true);
            if ($payload && is_array($payload)) {
                $payload = new Payload($payload);
                Timer::add(floatval($payload->getDelayTime()), function (Payload $payload) {
                    $payload->setDelayTime();
                    $this->manager->push($payload);
                }, [$payload], false);
            }
            $connection->send(json_encode(['code' => 200, 'msg' => 'ok', 'data' => '']));
        } catch (ErrorException $e) {

        }
    }

    public function onClose(TcpConnection $connection)
    {

    }
}
