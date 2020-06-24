
<?php

namespace App\Services\Websocket\Rooms;

/**
 * 房间的接口
 */
interface RoomContract
{
    /**
     * Rooms key
     *
     * @const string
     */
    public const ROOMS_KEY = 'rooms';

    /**
     * Descriptors key
     *
     * @const string
     */
    public const DESCRIPTORS_KEY = 'fds';

    /**
     * Do some init stuffs before workers started.
     *
     * @return RoomContract
     */
    public function prepare() : RoomContract;

    /**
     * Add multiple socket fds to a room
     *
     * @param integer $fd
     * @param array|string $rooms
     * @return void
     */
    public function add(int $fd, $rooms);

    /**
     * Delete multiple socket fds from a room
     *
     * @param integer $fd
     * @param array|string $rooms
     * @return void
     */
    public function delete(int $fd, $rooms);

    /**
     * Get all sockets by a room key.
     *
     * @param string $room
     * @return array
     */
    public function getClients(string $room);

    /**
     * Get all rooms by a fd.
     *
     * @param integer $fd
     * @return array
     */
    public function getRooms(int $fd);
}
