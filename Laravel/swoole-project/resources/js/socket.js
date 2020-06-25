import io from 'socket.io-client';

// 将后端配置修改为 Swoole WebSocket 服务器
// 然后将路径设置为 /ws，以便连接到 Swoole WebSocket 服务器，
// 最后设置传输层协议为 websocket，取代默认的长轮询（polling）机制。
const url = 'http://lara-first.test/';
// const socket = io.connect(url, {
//     path: '/ws',
//     transports: ['websocket']
// });
const socket = io(url);

export default socket;
