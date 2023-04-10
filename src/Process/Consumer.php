<?php

namespace Playcat\Queue\Process;

use Exception;
use Playcat\Queue\Exceptions\DontRetry;
use Playcat\Queue\Manager;
use Playcat\Queue\Protocols\ConsumerData;
use Playcat\Queue\Protocols\ProducerData;
use support\Container;
use Workerman\Worker;
use Workerman\Timer;


/**
 * Class Consumer
 * @package process
 */
class Consumer
{

    /**
     * @var array
     */
    private $config = [];

    private $pull_timing;

    /**
     * @param string $consumer_dir
     * @param int $max_attempts
     * @param int $retry_seconds
     */
    public function __construct(string $consumer_dir = '', int $max_attempts = 0, int $retry_seconds = 5)
    {
        $this->config['consumer_dir'] = $consumer_dir;
        $this->config['max_attempts'] = $max_attempts;
        $this->config['retry_seconds'] = $retry_seconds;
    }


    /**
     * onWorkerStart.
     */
    public function onWorkerStart(Worker $worker)
    {
        if (!is_dir($this->config['consumer_dir'])) {
            echo "Consumer directory $this->consumer_dir not exists\r\n";
            return;
        }
        $manager = Manager::getInstance();
        $manager->setIconicId($worker->id);

        try {
            $consumers = $this->loadWorkTask($this->config['consumer_dir']);
        } catch (Exception $e) {
            echo 'Error while loading consumers: ' . $e->getMessage() . "\r\n";
            return;
        }
        $manager->subscribe(array_keys($consumers));

        $this->pull_timing = Timer::add(0.1, function ($config) use ($manager, $consumers) {
            $payload = $manager->shift();
            if (($payload instanceof ConsumerData)) {
                if (!empty($consumers[$payload->getChannel()])) {
                    try {
                        call_user_func([$consumers[$payload->getChannel()], 'consume'], $payload);
                    } catch (DontRetry $e) {

                    } catch (Exception $e) {
                        if (isset($config['max_attempts'])
                            && $config['max_attempts'] > 0
                            && $config['max_attempts'] > $payload->getRetryCount()) {
                            $producer_data = new ProducerData();
                            $producer_data->setChannel($payload->getChannel());
                            $producer_data->setQueueData($payload->getQueueData());
                            $producer_data->setRetryCount($payload->getRetryCount() + 1);
                            $producer_data->setDelayTime(
                                pow($config['retry_seconds'], $producer_data->getRetryCount())
                            );
                            $manager->push($producer_data);
                        }
                    } finally {
                        $manager->consumerFinished();
                    }
                }
            }

        }, [$this->config]);
    }

    /**
     * @param Worker $worker
     * @return void
     */
    public function onWorkerStop(Worker $worker): void
    {
        if ($this->pull_timing) {
            Timer::del($this->pull_timing);
        }
    }

    /**
     * @param string $dir
     * @return array
     */
    protected function loadWorkTask(string $dir = ''): array
    {
        $consumers = [];
        foreach (glob($dir . '/*.php') as $file) {
            $class = str_replace('/', "\\", substr(substr($file, strlen(base_path())), 0, -4));
            if (is_a($class, 'Playcat\Queue\Protocols\ConsumerInterface', true)) {
                $consumer = Container::get($class);
                $channel = $consumer->queue;
                $consumers[$channel] = $consumer;
            }
        }
        return $consumers;
    }

}
