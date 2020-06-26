import io from 'socket.io-client';
import store from './store';

// 将后端配置修改为 Swoole WebSocket 服务器
// 然后将路径设置为 /ws，以便连接到 Swoole WebSocket 服务器，
// 最后设置传输层协议为 websocket，取代默认的长轮询（polling）机制。
const api_token = store.state.userInfo.token;
// const url = 'http://lara-first.test?api_token=' + api_token;
const url = 'http://lara-first.test';
const socket = io.connect(url, {
    query: { api_token },
    path: '/ws',
    transports: ['websocket']
});
// const socket = io(url);

export default socket;
