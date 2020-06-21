<template>
    <div id="danmu">
        <div class="stage">
            <vue-baberrage
                :isShow="barrageIsShow"
                :barrageList="barrageList"
                :loop="barrageLoop"
                :maxWordCount="60"
            >
            </vue-baberrage>
        </div>
        <div class="danmu-control">
            <div>
                <select v-model="position">
                    <option value="top">从上</option>
                    <option value="right">从右</option>
                </select>
                <input type="text" style="float: left;" v-model="msg" />
                <button type="button" style="float: left;" @click="sendMsg">发送</button>
            </div>
        </div>
    </div>
</template>

<script>
    import { MESSAGE_TYPE } from 'vue-baberrage';

    export default {
        name: 'danmu',
        data () {
            return {
                msg: '你好，学院君！',
                position: 'top',
                barrageIsShow: true,
                currentId: 0,
                barrageLoop: false,
                barrageList: [],
                websocket: null,
            }
        },
        mounted() {
            // 初始化websocket并定义回调函数
            this.websocket = new WebSocket("ws://lara-first.test/ws");
            this.websocket.onopen = (event) => {
                console.log("已建立 WebSocket 连接");
            };
            this.websocket.onmessage = (event) => {
                // 接收到WebSocket服务器返回消息时触发
                let data = JSON.parse(event.data);
                this.addToList(data.position, data.message);
            };
            this.websocket.onerror = (event) => {
                console.log("与WebSocket通信出错；" + error.toString());
            };
            this.websocket.onclose = (event) => {
                console.log("断开WebSocket连接");
            };
        },
        destroyed() {
            this.websocket.close();
        },
        methods: {
            removeList () {
                this.barrageList = [];
            },
            addToList (position, message) {
                if (position === 'top') {
                    this.barrageList.push({
                        id: ++this.currentId,
                        avatar: '/assets/images/girl_one.jpg',
                        msg: message,
                        barrageStyle: 'top',
                        time: 8,
                        type: MESSAGE_TYPE.FROM_TOP,
                        position: 'top'
                    });
                } else {
                    this.barrageList.push({
                        id: ++this.currentId,
                        avatar: '/assets/images/girl_two.jpg',
                        msg: message,
                        time: 15,
                        type: MESSAGE_TYPE.NORMAL,
                    });

                }
            },
            sendMsg () {
                // 发送消息到 WebSocket 服务器
                this.websocket.send(`{"position": "${this.position}", "message": "${this.msg}"}`);
            }
        },
    }
</script>

<style lang="scss" scoped>
    #danmu {
        font-family: 'Avenir', Arial, Helvetica, sans-serif;
        text-align: center;
        color: #2c3e50;
    }
    .stage {
        height: 300px;
        width: 100%;
        background-color: #025d63;
        margin: 0;
        position: relative;
        overflow: hidden;
    }
    h1, h2 {
        font-weight: normal;
    }
    ul {
        list-style: none;
        padding: 0;
    }
    li {
        display: inline-block;
        margin: 0 10px;
    }
    a {
        color: #42b983;
    }
    .baberrage-stage {
        z-index: 5;
    }
    .baberrage-stage .baberrage-item.normal {
        color: #fff;
    }
    .top {
        border: 1px solid #66aabb;
    }
    .danmu-control {
        position: absolute;
        margin: 0 auto;
        width: 100%;
        bottom: 300px;
        top: 70%;
        height: 69px;
        box-sizing: border-box;
        text-align: center;
        display: flex;
        justify-content: center;

        div {
            width: 300px;
            background-color: rgba($color: #000000, $alpha: 0.6);
            padding: 15px;
            border-radius: 5px;
            border: 2px solid #8ad9ff;
        }
        input, button, select {
            height: 35px;
            padding: 0;
            float: left;
            background-color: #027fbb;
            border: 1px solid #ccc;
            color: #fff;
            border-radius: 0;
            width: 18%;
            box-sizing: border-box;
        }
        select {
            height: 33px;
            margin-top: 1px;
            border: 0px;
            outline: 1px solid rgb(204, 204, 204);
        }
        input {
            width: 64%;
            height: 35px;
            background-color: rgba($color: #000000, $alpha: 0.7);
            border: 1px solid #8ad9ff;
            padding-left: 5px;
            color: #fff;
        }
    }
</style>
