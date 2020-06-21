<?php

namespace App\Services\WebSocket;

/**
 * 通信数据发送
 */
class Pusher
{
    /**
     * @var \Swoole\WebSocket\Server
     */
    protected $server;

    /**
     * @var int
     */
    protected $opcode;

    /**
     * @var int
     */
    protected $sender;

    /**
     * @var array
     */
    protected $descriptors;

    /**
     * @var bool
     */
    protected $broadcast;

    /**
     * @var bool
     */
    protected $assigned;

    /**
     * @var string
     */
    protected $event;

    /**
     * @var mixed|null
     */
    protected $message;

    /**
     * Push constructor
     *
     * @param integer $opcode
     * @param integer $sender
     * @param array $descriptors
     * @param boolean $broadcast
     * @param boolean $assigned
     * @param string $event
     * @param mixed|null $message
     * @param \Swoole\WebSocket\Server $server
     */
    protected function __construct(
        int $opcode,
        int $sender,
        array $descriptors,
        bool $broadcast,
        bool $assigned,
        string $event,
        $message = null,
        $server
    )
    {
        $this->opcode = $opcode;
        $this->sender = $sender;
        $this->descriptors = $descriptors;
        $this->broadcast = $broadcast;
        $this->assigned = $assigned;
        $this->event = $event;
        $this->message = $message;
        $this->sever = $server;
    }

    /**
     * Static constructor
     *
     * @param array $data
     * @param \Swoole\WebSocket\Server $server
     * @return Pusher
     */
    public static function make(array $data, $server)
    {
        return new static(
            $data['opcode'] ?? 1,
            $data['sender'] ?? 0,
            $data['fds'] ?? [],
            $data['broadcast'] ?? false,
            $data['assigned'] ?? false,
            $data['event'] ?? null,
            $data['message'] ?? null,
            $server
        );
    }

    /**
     * @return integer
     */
    public function getOpcode() : int
    {
        return $this->opcode;
    }

    /**
     * @return integer
     */
    public function getSender() : int
    {
        return $this->sender;
    }

    /**
     * @return integer
     */
    public function getDescriptors() : array
    {
        return $this->descriptors;
    }

    /**
     * @param int $descriptor
     * @return self
     */
    public function addDescriptor($descriptor) : self
    {
        return $this->addDescriptors([$descriptor]);
    }

    /**
     * @param array $descriptors
     * @return self
     */
    public function addDescriptors(array $descriptors) : self
    {
        $this->descriptors = array_values(array_unique(
            array_merge($this->descriptors, $descriptors)
        ));
        return $this;
    }

    /**
     * @param integer $descriptor
     * @return boolean
     */
    public function hasDescriptor(int $descriptor) : bool
    {
        return in_array($descriptor, $this->descriptors);
    }

    /**
     * @return boolean
     */
    public function isBroadcast() : bool
    {
        return $this->broadcast;
    }
}
