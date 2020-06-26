# Swoole 从入门到实战
教程地址 https://xueyuanjun.com/books/swoole-tutorial           


## 安装配置 LaravelS
LaravelS 扩展文档 https://github.com/hhxsv5/laravel-s

LaravelS 基于这个扩展包可以快速入门，免去很多不必要的配置和整合操作，你可以将其看作套在 Swoole 之上的一层壳，或者一个代理，真正提供服务的还是底层的 Swoole。        

在 Laravel 应用中使用 Swoole 之前，先通过 Composer 安装 LaravelS 扩展包：
```sh
$ composer require hhxsv5/laravel-s
$ php artisan laravels publish
$ php bin/laravels start
```

添加 nginx 反向代理：
```conf
map $http_upgrade $connection_upgrade {
    default upgrade;
    ''      close;
}

upstream laravels {
    # Connect IP:Port
    server localhost:5200 weight=5 max_fails=3 fail_timeout=30s;
    keepalive 16;
}

server {
    listen 80;
    
    server_name lara-first.test;
    root "E:/WorkSpace/PHPStudy/Laravel/first-project/public";
    index index.php index.html error/index.html;
    
    # Nginx 处理静态资源，LaravelS 处理动态资源
    location / {
        try_files $uri @laravels;
    }

    # Http and WebSocket are concomitant, Nginx identifies them by "location"
    # !!! The location of WebSocket is "/ws"
    # Javascript: var ws = new WebSocket("ws://todo-s.test/ws");
    # 处理 WebSocket 通信
    location =/ws {
        # proxy_connect_timeout 60s;
        # proxy_send_timeout 60s;
        # proxy_read_timeout: Nginx will close the connection if the proxied server does not send data to Nginx in 60 seconds; At the same time, this close behavior is also affected by heartbeat setting of Swoole.
        # proxy_read_timeout 60s;
        proxy_http_version 1.1;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        proxy_pass http://localhost:5200;
    }
    
    location @laravels {
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_pass http://localhost:5200;
    }
}
```

=_= 然后启动 laravel-s 发现没有 websocket，原来是配置的时候只加了 WebSocketService 类，但是没有将 `enable` 修改为 true。        

lumen使用Hhxsv5\LaravelS实现websocket通信 https://blog.csdn.net/zhangzeshan/article/details/101852666


## 移植 Vue 聊天项目
项目地址 https://github.com/hua1995116/webchat          
跟着学院君的Laravel Swoole 写聊天应用的教程敲，但是webchat的项目代码已经更新了不少，所以需要自己再做一些修改，不然前端没办法用起来。    
而且学院君的一些代码，我还没有理解清楚，在移植好webchat前端之后，还要再多分析一下后端的代码。       
这些都搞好了，再看看怎么做一个完善的聊天程序，群组、讨论组、语音、视频等，还有客户端、对应后台开发，彻底完善起来。      
等能弄一个差不多完善的聊天应用，再复用已有的后端，写一个客服应用，包含web、桌面、移动三端。     

有一些人用 uniapp、electorn-vue、flutter写出了模仿微信的应用，很厉害，不过主要是IM的工作原理，只要后端架构以及前后端交互弄清楚了。其他的都不会太难。        

webchat 开放相关文章：
[vue+websocket+express+mongodb实战项目（实时聊天）（一）](http://blog.csdn.net/blueblueskyhua/article/details/70807847)
[vue+websocket+express+mongodb实战项目（实时聊天）（二）](http://blog.csdn.net/blueblueskyhua/article/details/73250992)
[vue-chat项目之重构与体验优化](http://blog.csdn.net/blueblueskyhua/article/details/78159672)

socket.on()用于接受消息。socket.emit() 用于发送消息。       


### getRoomHistory 接口
登录之后的初始化操作，会获取各个聊天房间的信息
```js
// resources\js\socket-handle.js
export async function handleInit({
  name,
  id,
  src,
  roomList
}) {
    // 此处逻辑需要抽离复用
  socket.emit('login', {name, id, ...env});
  ['room1', 'room2'].forEach(item => {
    const obj = {
      name,
      src,
      roomid: item,
    };
    socket.emit('room', obj);
  })
  await store.dispatch('getRoomHistory', { selfId: id })
}
// resources\js\store\index.js
async getRoomHistory({state, commit}, data) {
    const res = await url.getRoomHistory(data);
    if(res.data.errno === 0) {
    const result = res.data.data;
    if(result) {
        commit('setAllmsg', result);
    }
    }
},
// resources\js\api\server.js
// 获取当前房间所有历史记录
getRoomHistory: data => Axios.get('/api/message/history/byUser', {
    params: data
}),
// 下面是node后端的代码
// 获取用户
router.get('/history/byUser', async (req, res) => {
  const { selfId } = req.query;

    // 待考虑，这一部分是否由客户端输入
    const checkFriend = await Friend.find({selfId}).populate({
      path: 'friendId',
      select: 'name src socketId'
    }).exec();

    const selfRoom = checkFriend.map(item => {
      return sort(item.selfId, item.friendId._id);
    });

    const allRooms = selfRoom.concat(['room1', 'room2']); // 数据库中的房间加上默认的两个房间

    const allMsg = allRooms.map(item => {
      return  Message.find( { roomid: item } ).sort({"_id": -1}).limit(20);
    })

    const results = await Promise.all(allMsg);

    const msgs = allRooms.reduce((obj, item, index) => {
      obj[item] = (results[index] || []).reverse();
      return obj;
    }, {})

    res.json({
      errno: 0,
      data: msgs
    })

})

```

### polling 自动切换为 websocket失败
socket.io 架构及机制的文档 https://socket.io/docs/internals/    
socket.io 原理分析 https://juejin.im/entry/5b3b388ae51d4519076922ce

=_= 下载学院君的源码，也是发几个请求成功交互之后，疯狂的长轮询。还是使用websocket协议，不高啥切换了。   

### 移植结构
学院君的后端代码跟最新的前端有一些不匹配，就拿获取房间聊天信息的记录来说，那个errno字段要data平级，结果放在data里面了。
而前端就取不到errno了，=_= 血坑啊。     

下拉获取聊天记录是有问题的，用户头像好像没有响应的图片文件。        
=_= 感觉电脑好像老化了一样，特别的卡，唉有钱换台高配的台式机。  
