<?php

namespace App\Services\Websocket\Rooms;

use InvalidArgumentException;
use Swoole\Table;

/**
 * 基于 Swoole Table 作为存储媒介的房间类
 */
class TableRoom implements RoomContract
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Table
     */
    protected $rooms;

    /**
     * @var Table
     */
    protected $fds;

    /**
     * TableRoom constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Do some init stuffs before workers started.
     *
     * @return RoomContract
     */
    public function prepare() : RoomContract
    {
        $this->initRoomsTable();
        $this->initFdsTable();

        return $this;
    }

    /**
     * Add a socket fd to multiple rooms
     *
     * @param integer $fd
     * @param array|string $roomNames
     * @return void
     */
    public function add(int $fd, $roomNames)
    {
        $rooms = $this->getRooms($fd);
        $roomNames = is_array($roomNames) ? $roomNames : [$roomNames];

        foreach ($roomNames as $room) {
            $fds = $this->getClients($room);

            if (in_array($fd, $fds)) {
                continue;
            }

            $fds[] = $fd;
            $rooms[] = $room;

            $this->setClients($room, $fds);
        }

        $this->setRooms($fd, $rooms);
    }

    public function delete(int $fd, $roomNames = [])
    {
        $allRooms = $this->getRooms($fd);
        $roomNames = is_array($roomNames) ? $roomNames : [$roomNames];
        $rooms = count($roomNames) ? $roomNames : $allRooms;

        $removeRooms = [];
        foreach ($rooms as $room) {
            $fds = $this->getClients($room);

            if (!in_array($fd, $fds)) {
                continue;
            }

            $this->setClients($room, array_values(array_diff($fds, [$fd])));
            $removeRooms[] = $room;
        }

        $this->setRooms($fd, array_values(array_diff($allRooms, $removeRooms)));
    }

    /**
     * Get all sockets by a room key
     *
     * @param string $room
     * @return array
     */
    public function getClients(string $room)
    {
        return $this->getValue($room, RoomContract::ROOMS_KEY);
    }

    /**
     * Get all rooms by a fd
     *
     * @param integer $fd
     * @return array
     */
    public function getRooms(int $fd)
    {
        return $this->getValue($fd, RoomContract::DESCRIPTORS_KEY);
    }

    /**
     * @param string $room
     * @param array $fds
     * @return TableRoom
     */
    protected function setClients(string $room, array $fds) : TableRoom
    {
        return $this->setValue($room, $fds, RoomContract::ROOMS_KEY);
    }

    /**
     * @param integer $fd
     * @param array $rooms
     * @return TableRoom
     */
    protected function setRooms(int $fd, array $rooms) : TableRoom
    {
        return $this->setValue($fd, $rooms, RoomContract::DESCRIPTORS_KEY);
    }

    /**
     * Init rooms table
     *
     * @return void
     */
    protected function initRoomsTable() : void
    {
        $this->rooms = new Table($this->config['room_rows']);
        $this->rooms->column('value', Table::TYPE_STRING, $this->config['room_size']);
        $this->rooms->create();
    }

    /**
     * Init descriptors table
     *
     * @return void
     */
    protected function initFdsTable()
    {
        $this->fds = new Table($this->config['client_rows']);
        $this->fds->column('value', Table::TYPE_STRING, $this->config['client_size']);
        $this->fds->create();
    }

    /**
     * Set value to table
     *
     * @param string $key
     * @param array $value
     * @param string $table
     * @return $this;
     */
    public function setValue($key, array $value, string $table)
    {
        $this->checkTable($table);
        $this->$table->set($key, ['value' => json_encode($value)]);
        return $this;
    }

    /**
     * Get value from table
     *
     * @param string $key
     * @param string $table
     * @return array|mixed
     */
    public function getValue(string $key, string $table)
    {
        $this->checkTable($table);
        $value = $this->$table->get($key);
        return $value ? json_decode($value['value'], true) : [];
    }

    /**
     * Check table for exists
     *
     * @param string $table
     * @return void
     */
    protected function checkTable(string $table)
    {
        if (!property_exists($this, $table) || !$this->$table instanceof Table) {
            throw new InvalidArgumentException("Invalid table name: `{$table}`.");
        }
    }
}
