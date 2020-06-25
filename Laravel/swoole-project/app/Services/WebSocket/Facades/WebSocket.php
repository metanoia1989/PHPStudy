<?php

namespace App\Services\Websocket\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * WebSocekt 的门面代理类
 *
 * @method static self broadcast()
 * @method static self to($values)
 * @method static self join($rooms)
 * @method static self leave($rooms)
 * @method static boolean emit($event, $data)
 * @method static self in($room)
 * @method static self on($event, $callback)
 * @method static boolean eventExistsn($event)
 * @method static mixed call($event, $data)
 * @method static boolean close($fd)
 * @method static self setSender($fd)
 * @method static int getSender()
 * @method static boolean getIsBroadcast()
 * @method static array getTo()
 * @method static self reset()
 * @method static self middleware($middleware)
 * @method static self setContainer($container)
 * @method static self setPipeline($pipeline)
 * @method static \Illuminate\Contracts\Pipeline\Pipeline getPipeline()
 * @method static mixed loginUsing($user)
 * @method static self loginUsingId($userId)
 * @method static self logout()
 * @method static self toUser($users)
 * @method static self toUserId($userIds)
 * @method static string getUserId()
 * @method static boolean isUserIdOnline($userId)
 *
 * @see \App\Services\WebSocket\WebSocket;
 */
class WebSocket extends Facade
{
    /**
     * Get the registered name of the compoennt
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.websocket';
    }
}
