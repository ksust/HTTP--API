/**
 * Demo
 * User: yugao
 * Date: 2019/2/27
 * version 2.2.2
 * Note: HTTPSDK for NodeJS(适用于版本2.2.2插件):用于解析插件消息、构造返回数据，以及HTTP推送（发起HTTP请求）
 * Contact: 开发者邮箱 admin@ksust.com
 * 安装SDK：npm install http-api-sdk
 */
const http = require('http');
const HTTPSDK = require('http-api-sdk');

const server = http.createServer((req, res) => {
    req.on('data', function (data) {
        let sdk = HTTPSDK.httpGet(data.toString());
        //console.log(sdk.getMsg());//获取到的消息
        sdk.sendPrivateMsg(sdk.getMsg()['QQ'], '你发送了这样的消息：' + sdk.getMsg()['Msg']);
        sdk.getLoginQQ();
        //回调演示，提交返回获取群列表、登录QQ等
        if (sdk.isCallback() && parseInt(sdk.getMsg()['Type']) === HTTPSDK.TYPE_GET_LOGIN_QQ) {
            console.log('Login QQ:' + sdk.getLoginQQ());
        }
        res.end(sdk.toJsonString());
    });

});
server.on('clientError', (err, socket) => {
    socket.end('HTTP/1.1 400 Bad Request\r\n\r\n');
});
server.listen(8000);