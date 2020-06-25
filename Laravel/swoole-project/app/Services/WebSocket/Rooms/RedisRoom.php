<?php

namespace App\Services\Websocket\Rooms;

use Illuminate\Support\Arr;
use Predis\Client as RedisClient;

/**
 * Redis 作为存储媒介
 */
class RedisRoom implements RoomContract
{
    /**
     * @var \Predis\Client
     */
    protected $redis;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $prefix = 'swoole:';

    /**
     * RedisRoom constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function prepare(RedisClient $redis = null): RoomContract
    {
        $this->setRedis($redis);
        $this->setPrefix();
        $this->cleanRooms();

        return $this;
    }

    /**
     * Set redis client.
     *
     * @param RedisClient|null $redis
     * @return void
     */
    public function setRedis(?RedisClient $redis = null)
    {
        if (!$redis) {
            $server = Arr::get($this->config, 'server', []);
            $options = Arr::get($this->config, 'options', []);

            // forbid setting prefix from options
            if (Arr::has($options, 'prefix')) {
                $options = Arr::except($options, 'prefix');
            }
            $redis = new RedisClient($server, $options);
        }

        $this->redis = $redis;
    }


}
