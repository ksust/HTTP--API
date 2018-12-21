package com.example.demo;

import com.ksust.qq.http_api_sdk.HTTPSDK;
import com.ksust.qq.http_api_sdk.entity.MessageGet;
import com.ksust.qq.http_api_sdk.entity.vo.Group;
import com.ksust.qq.http_api_sdk.enums.TypeEnum;
import com.ksust.qq.http_api_sdk.impl.HTTPSDKDefault;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RestController;

/**
 * Demo：提交返回
 * Created by yugao on 2018/10/7.
 */
@RestController
public class HTTPSDKDemo {
    @GetMapping("/test")
    public String test() {
        return "success";
    }

    //提交返回演示：外部地址（插件中填的请求地址）：http://yourIP:9999/ip
    @PostMapping(value = "/qq")
    public String qq(@RequestBody String data) throws Exception {
        //外部消息通过这个方法进入，请求地址即为 POST host:port/qq
        //开始演示：发送消息回复（点赞等），并且演示通过提交返回获取群列表
        HTTPSDK httpsdk = new HTTPSDKDefault(data);
        //插件发来的消息
        MessageGet msg = httpsdk.getMessageGet();
        if (httpsdk.getMessageGet().getType() == TypeEnum.FRIEND.getCode() && !httpsdk.isCallback()) {
            //私聊消息
            //点赞、抖窗、回复
            httpsdk.sendLike(msg.getQQ(), 2);
            httpsdk.sendShake(msg.getQQ());
            httpsdk.sendPrivateMsg(msg.getQQ(), "Hello，这里是HTTP-API演示程序，你发送的消息为：" + msg.getMsg()
                    + "。我还给你点了两个赞哦~");
            //发起获取群列表的请求（提交请求下不能立即得到），待下一次请求时携带消息
            httpsdk.getGroupList();
        }

        //接收 WEB回调消息。
        if (httpsdk.isCallback()) {
            //打印群列表
            if (msg.getType() == TypeEnum.GET_GROUP_LIST.getCode()) {
                for (Group group : httpsdk.getGroupList()) {
                    System.out.println(group.getGroupName() + "," + group.getGroupId());
                }
            }
        }
        //必须！！！向插件回复消息并清空当前对象待发送消息
        return httpsdk.send();
    }
}
