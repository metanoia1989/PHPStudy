<?php

use Swoole\Process;

class TcpServer
{
    // 系统支持的最大子进程数
    const MAX_PROCESS = 3;
    // 子进程pid数组
    private $pids = [];
    // 网络套接字
    private $socket;
    // 主进程ID
    private $mpid;

    /**
     * 服务器主进程业务逻辑
     *
     * @return void
     */
    public function run()
    {
        // 主进程
        $process = new Process(function () {
            // 获取当前进程ID作为主进程ID
            $this->mpid = posix_getpid();
            echo time(). " Master process, pid {$this->mpid}\n";

            // 创建 TCP 服务器并获取套接字
            $this->socket = stream_socket_server('tcp://localhost:9503', $errno, $errstr);
            if (!$this->socket) {
                exit("Server start error: $errstr --- $errno");
            }

            // 启动子进程处理请求
            for ($i = 0; $i < self::MAX_PROCESS; $i++) {
                $this->startWorkerProcess();
            }
            echo "Waiting client start...\n";

            // 主进程等待子进程退出，必须是死循环
            while (true) {
                foreach ($this->pids as $k => $pid) {
                    if ($pid) {
                        // 回收结束运行的子进程，以避免僵尸进程出现
                        $ret = Process::wait(false);
                        if ($ret) {
                            echo time(). " Worker process $pid exit, will start new...\n";
                            // 子进程退出后重新启动一个新的子进程
                            $this->startWorkerProcess();
                            unset($this->pids[$k]);
                        }
                    }
                }
                sleep(1); // 让出1s时间给CPU
            }
        }, false, false); // 不启用管道通信

        // 让当前进程变成一个守护进程
        Process::daemon();
        // 执行fork系统调用，启动进程
        // 注意：start之后的变量子进程里面是获取不到的
        $process->start();
    }

    /**
     * 创建Worker子进程，接收客户端连接并处理
     *
     * @return void
     */
    private function startWorkerProcess()
    {
        // 子进程
        $process = new Process(function (Process $worker) {
            // 子进程业务逻辑
            $this->acceptClient($worker);
        }, false, false);
        // 启动子进程并获取子进程ID
        $pid = $process->start();
        $this->pids[] = $pid;
    }

    /**
     * 等待客户端连接并处理
     *
     * @param Process $worker
     * @return void
     */
    private function acceptClient(&$worker)
    {
        // 子进程一直等待客户端连接，不能退出
        while (true) {
            // 从主进程创建的网络套接字上获取连接
            $conn = stream_socket_accept($this->socket, -1);
            // 如果定义了连接建立回调函数，则在连接上执行该回调
            if ($this->onConnect) {
                call_user_func($this->onConnect, $conn);
            }

            // 开始循环读取客户端请求消息
            $recv = ''; // 实际收到的消息
            $buffer = ''; // 缓冲消息
            while (true) {
                // 检查主进程是否正常，不正常则退出子进程
                $this->checkMpid($worker);
                // 读取客户端请求消息
                $buffer = fread($conn, 20);

                // 没有收到正常消息
                if ($buffer === false || $buffer === '') {
                    // 如果服务器设置了连接关闭回调函数看，则在当前连接上执行该回调
                    if ($this->onClose) {
                        call_user_func($this->onClose, $conn);
                    }
                    // 结束读取消息，退出当前循环，等待下一个客户端连接
                    break;
                }

                // 消息结束符的位置
                $pos = strpos($buffer, "\n");
                if ($pos === false) { // 没有读取完，继续读取
                    $recv .= $buffer;
                } else { // 读取完毕，开始处理请求信息
                    // 处理收到的信息
                    $recv .= trim(substr($buffer, 0, $pos + 1));

                    // 如果服务器定义了消息处理回调函数，则在当前连接上将消息传入回调函数并执行该回调
                    if ($this->onMessage) {
                        call_user_func($this->onMessage, $conn, $recv);
                    }
                    // 如果接收到 quit 消息，表示关闭此连接，等待下一个客户端连接
                    if ($recv == "quit") {
                        echo "Client close connection\n";
                        fclose($conn);
                        break;
                    }

                    $recv = ''; // 清空消息，准备下一次接收
                }
            }
        }
    }

    /**
     * 如果主进程已退出，则子进程也退出，避免孤儿进程出现
     *
     * @param Process $worker
     * @return void
     */
    public function checkMpid(&$worker)
    {
        // 检测主进程是否存在，如果不存在，则退出子进程
        if (!Process::kill($this->mpid, 0)) {
            $worker->exit();
            // 进程已退出，写入日志
            echo "Master process exited, I [{$worker['pid']}] also quit\n";
        }
    }
}

function main() {
    $server = new TcpServer();

    // 定义连接建立回调函数
    $server->onConnect = function ($conn) {
        echo "onConnect -- accepted " . stream_socket_get_name($conn, true) . "\n";
    };

    // 定义收到消息回调函数
    $server->onMessage = function ($conn, $msg) {
        echo "onMessage --". $msg. "\n";
        fwrite($conn, "received ". $msg. "\n");
    };

    // 定义连接关闭回调函数
    $server->onClose = function ($conn) {
        echo "onClose --" . stream_socket_get_name($conn, true) . "\n";
    };

    // 启动服务器主进程
    $server->run();
}

main();
