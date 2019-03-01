/**
 * Demo
 * User: yugao
 * Date: 2019/2/27
 * version 2.2.2
 * Note: HTTPSDK for NodeJS(适用于版本2.2.2插件):用于解析插件消息、构造返回数据，以及HTTP推送（发起HTTP请求）
 * Contact: 开发者邮箱 admin@ksust.com
 * 安装SDK：npm install http-api-sdk
 */

const HTTPSDK = require('http-api-sdk');

//推送演示，需要配置推送
push = HTTPSDK.httpPush('http://127.0.0.1:8080')
push.getGroupList().data(function (data) {
    console.log('push ' + data)
});
push.getLoginQQ().data(function (data) {
    console.log('push ' + data)
});
push.sendPrivateMsg('QQ', 'Hello').data(function (data) {
    console.log('push ' + data)
});

//消息转发演示，插件在线即可用
let forward = HTTPSDK.msgForwardPush('QQ', '授权码');
forward.getLoginQQ().data(function (data) {
    console.log('forward ' + data);
});

forward.getGroupList().data(function (data) {
    console.log('forward ' + data);
});
forward.sendPrivateMsg('QQ', 'Hello').data(function (data) {
    console.log('forward' + data)
});