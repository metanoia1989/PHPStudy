import io from 'socket.io-client';

// 将后端配置修改为 Swoole WebSocket 服务器
const url = 'http://lara-first.test/ws/';
const socket = io.connect(url);

export default socket;
