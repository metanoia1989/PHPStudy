<?php

namespace App\Services\WebSocket;

/**
 * WebSocket 服务类
 * 比如房间的加入和退出、用户的认证和获取、数据的发送和广播等
 * 发送数据通过调用 Pusher 实现
 */
class WebSocket
{
    const PUSH_ACTION = 'push';
    const EVENT_CONNECT = 'connect';
    const USER_PREFIX = 'uid_';

    /**
     * Determine if to broadcast.
     *
     * @var boolean
     */
    protected $isBroadcast = false;

    /**
     * Socket sender's fd.
     *
     * @var integer
     */
    protected $sender;

    /**
     * Recepient's fd or room name.
     *
     * @var array
     */
    protected $to = [];

    /**
     * WebScoekt event callbacks
     *
     * @var array
     */
    protected $callbacks = [];
}
