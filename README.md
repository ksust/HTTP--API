HTTP-API: CoolQ/CleverQQ Extensioin
---

扩展你的QQ/微信机器人用途，提供跨框架平台的PHP/Java/Python/NodeJS等编程语言SDK。   

------

## HTTP-API简介
---   
HTTP-API是一款主要通过HTTP协议（另外也包含使用webSocket、socket等协议）与机器人进行通信的插件，主要提供PHP/Java/Python/NodeJS等编程语言SDK，以便将机器人用于WEB、大数据、机器学习等领域，尽可能地提现数据的价值。   
目前插件主要支持CleverQQ机器人框架，并兼容酷Q机器人应用，开发者可以通过使用SDK非常方便地对机器人进行操作，比如控制机器人发送消息、公告，获取机器人群列表、好友列表，通过及监听获取近一个月、或者几年的聊天记录。开发者可通过简单的操作完成一些小程序、小应用对的开发，亦可应用于大型应用中。   
此外，通过配合使用WEB管理平台（http://work.ksust.com），开发者可将自己开发的应用发布到应用商店中，通过用户安装的方式获得收入、使用数据等。用户可直接通过WEB平台安装、管理应用，以及可以批量管理机器人在线状态、收发消息等，让用户用最少的时间完成尽可能多的事。   
**注：以下表格描述中CleverQQ简称IR，CoolQ（酷Q）简称CQ**   
交流群：（QQ群，用户加入）537419179   
开发群：（QQ群，开发者加入）598629636   
管理平台：http://work.ksust.com

------
## 功能介绍
---
本插件主要包含三种工作机制，即**提交返回、主动推送、消息转发**，第一种为被动接受，后两种为主动操作，其中提交返回包括HTTP方式、webSocket方式，主动推送使用HTTP方式，消息转发目前仅公开使用HTTP方式，几种方式中HTTP提交返回最常用也最通用。   
下面将简单介绍插件的几种工作方式，相关配置请结合插件进行（页底使用引导）。
### 提交返回
提交返回是最常用的一种工作方式，其原理为：机器人插件作为客户端（做网页请求），开发者开发WEB服务端；在工作时，插件将收到的消息（如QQ消息）经过协议封装后发送到开发者的WEB服务，该服务队发送过来的数据进行处理并返回给插件一些操作命令（如发送消息）。至此，一次简单的工作流程结束。   
显然，该工作流程是被动的，开发者的WEB服务必须等待插件访问才能执行相关逻辑，在这种机制下某些操作不能完成。于是，本插件引入WEB回调，即WEB服务向插件发送回调指令（当然得插件先访问WEB服务），表达自己想要的数据（如获取群列表），插件再收到回调指令后会立即将相关数据封装（如群列表）后发送到WEB服务。   
在具体开发中，常用的协议为HTTP，即在插件中填入http://...的地址，将程序部署到WEB服务器上；另外，为了更便于双向通信，同时提供了webSocket支持，只需在相关位置填入类似ws://...地址即可。
### 主动推送
主动推送是通过HTTP协议对机器人插件进行相关操作的一种方式。其原理为：插件作为HTTP服务器，开发者的应用（使用SDK）作为客户端，开发者可直接主动地操作插件（如主动发送消息、主动获取群列表等）。   
此方式的相关操作均为主动操作，一般结合提交返回进行使用，并一般要求插件所在服务器/电脑有固定的公网IP（该条件限制太大，如果没有公网IP，下述消息转发是一种可用的替代方式）。
### 消息转发
消息转发解决了无公网IP以及不能主动操作的问题，原则上开发者不需要服务器也能获得提交返回、主动推送的功能，并且更加便捷。其原理为：除插件和开发者外，存在若干的转发服务器（转发服务器集群），这些服务器对开发者的操作以及插件产生的消息进行转发，以达到开发者与插件通信的目的。   
目前本方式只开放HTTP方式，即只能完成上述主动推送的任务（但是不要求有公网IP）。开发者只需要直到机器人的QQ、授权码即可对机器人进行主动操作，任意一方均不要求有公网IP。
## 快速开始
---
以下将使用提供的PHP/Java/Python/NodeJS编程语言SDK开发Demo，以便开发者快速入门。  
以下操作建立在配置好插件基础上，相关步骤参考请到跳转到页面底部了解。Demo中使用的SDK均可在本项目GITHUB上下载到，Demo适配的最低版本为2.2.2。
所示Demo包含本插件三大功能（提交返回[包含webSocket]、HTTP主动推送、消息转发推送），相关demo可在本项目demo中下载。

### PHP Demo
>提交返回   
```
<?php
//插件的提交返回地址中配为本文件的访问地址
error_reporting(0);
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
include 'HTTPSDK.php';
$sdk = HTTPSDK::httpGet();
$msg = $sdk->getMsg();//插件发送过来的消息
$sdk->sendPrivateMsg($msg['QQ'], '你发送了这样的消息：' . $msg['Msg']);//逻辑代码，向发送者回消息
echo $sdk->toJsonString();//命令返回
```
>webSocket（结合使用workman）
```
<?php
//websocket（结合使用workman）,插件的提交返回地址中配为相关地址（websocket://0.0.0.0:2346），命令行中启动本文件（php demo-ws.php restart）
//workman下载地址：https://www.workerman.net/download/workermanzip
header("Content-type:text/html;charset=utf-8");
require_once __DIR__ . '/workman/Autoloader.php';
use Workerman\Worker;

require_once 'HTTPSDK.php';
// Create a Websocket server
$ws_worker = new Worker("websocket://0.0.0.0:2346");
// 4 processes
$ws_worker->count = 4;
// Emitted when new connection come
$ws_worker->onConnect = function ($connection) {
    echo "New connection\n";
};
// Emitted when data received
$ws_worker->onMessage = function ($connection, $data) {
    $json = json_decode(urldecode(urldecode($data)), true);
    if (isset($json['type']) && $json['type'] == 'init') {
        $connection->send('{"type":"success"}');
    } else {
        //消息操作
        $sdk = HTTPSDK::webSocket($data);
        $msg = $sdk->getMsg();
        if ($msg['Msg'] == 'demo') {
            $sdk->sendPrivateMsg($msg['QQ'], '你发送了这样的消息：' . $msg['Msg']);//逻辑代码，向发送者回消息
        }
        //echo $sdk->returnJsonString();
        $connection->send($sdk->returnJsonString());
    }

};
// Emitted when connection closed
$ws_worker->onClose = function ($connection) {
    echo "Connection closed\n";
};
// Run worker
Worker::runAll();
```
>HTTP推送
```
<?php
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
include 'HTTPSDK.php';
//插件中开启推送，并设置端口为8080
$push = HTTPSDK::httpPush('http://127.0.0.1:8080');
var_dump($push->getLoginQQ());//获取登录的QQ
var_dump($push->sendPrivateMsg('QQ', 'hello~'));//向QQ发送消息
var_dump($push->getFriendList());//获取好友列表

```
>消息转发
```
<?php
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
include 'HTTPSDK.php';
//保证插件再2.2.2及以上并启动
$forward = HTTPSDK::msgForwardPush('QQ', 'code');//输入QQ和授权码（http://work.ksust.com）
var_dump($forward->getLoginQQ());//获取登录的QQ
var_dump($forward->sendPrivateMsg('QQ', 'hello~'));//向QQ发送消息
var_dump($forward->getFriendList());//获取好友列表

```
### Java Demo
>Java Demo项目地址：https://github.com/ksust/HTTP-API-Java-Demo

>SDK引入方式   

1. **Maven引入**   
由于暂时没有提交Maven中央仓库，需要配置Maven私服，直接在pom文件中配置。在pom中加入如下代码即可：
```
<dependencies>
    <dependency>
        <groupId>com.ksust.qq</groupId>
        <artifactId>http-api-sdk</artifactId>
        <version>2.2.2</version>
    </dependency>
</dependencies>

<!--私服仓库地址，也可以直接使用非maven jar包-->
    <repositories>
        <repository>
            <id>ksust-maven</id>
            <name>Ksust Maven Repository</name>
            <url>http://maven.ksust.com/repository/maven-public/</url>
            <releases>
                <enabled>true</enabled>
            </releases>
            <snapshots>
                <enabled>false</enabled>
            </snapshots>
        </repository>
    </repositories>
```

2. **jar包引入（需要手动解决依赖）**   
可直接使用jar包（包名如：http-api-sdk-2.2.2.jar），在项目中下载即可，需要手动解决依赖。所需要的依赖及版本如下：（另外可以直接使用3提供的独立jar包，不用解决依赖问题）
```
    <properties>
        <okttp.version>3.8.1</okttp.version>
        <validator.version>4.2.0.Final</validator.version>
        <lombok.version>1.16.10</lombok.version>
        <fastjson.version>1.2.31</fastjson.version>
    </properties>
    <dependencies>
        <dependency>
            <groupId>org.hibernate</groupId>
            <artifactId>hibernate-validator</artifactId>
            <version>${validator.version}</version>
        </dependency>
        <dependency>
            <groupId>org.projectlombok</groupId>
            <artifactId>lombok</artifactId>
            <version>${lombok.version}</version>
        </dependency>
        <dependency>
            <groupId>com.alibaba</groupId>
            <artifactId>fastjson</artifactId>
            <version>${fastjson.version}</version>
        </dependency>
        <dependency>
            <groupId>com.squareup.okhttp3</groupId>
            <artifactId>okhttp</artifactId>
            <version>${okttp.version}</version>
        </dependency>

    </dependencies>
```
3. **jar包引入（包含所需依赖，独立jar包）**   
包含后缀-with-dependencies的jar包中已经包含依赖，如http-api-sdk-2.2.2-jar-with-dependencies.jar

>提交返回    
```
//本样例基于SpringBoot，可运行的Demo在项目中可直接下载。主要代码如下：
@RestController
public class HTTPSDKDemo {
    @GetMapping("/test")
    public String test() {
        return "success";
    }


    private void test(MessageGet msg) {
        System.out.println(JSON.toJSONString(msg));
    }

    //提交返回演示：外部地址（插件中填的请求地址）：http://yourIP:9999/ip
    @PostMapping(value = "/qq")
    public String qq(@RequestBody String data) throws Exception {
        //外部消息通过这个方法进入，请求地址即为 POST host:port/qq
        //开始演示：发送消息回复（点赞等），并且演示通过提交返回获取群列表
        HTTPSDK httpsdk = HTTPSDK.httpGet(data);
        //插件发来的消息
        MessageGet msg = httpsdk.getMsg();
        test(msg);
        if (httpsdk.getMsg().getType() == TypeEnum.FRIEND.getCode() && !httpsdk.isCallback()) {
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
        return httpsdk.toJsonString();
    }
}
```
>webSocket  
```
//与PHP版本类似，传入获取的字符串即可。取决于用什么框架，和上述提交返回使用方法基本相同，这里不再赘述。
```
>HTTP推送   
```
//推送测试代码如下，插件中开启推送，并设置端口为8080
public class HTTPSDKPushDemo {
    public static void main(String[] args) {
        //首先擦创建对象，支持加密。详情请查看文档
        HTTPSDK httpsdkPush = HTTPSDK.httpPush("http://127.0.0.1:8080", null, null);
        //引号中为测试QQ号
        String qq = "1402549575";
        //发送消息
        httpsdkPush.sendPrivateMsg(qq, "Hello World[ksust,at_all:qq=all][ksust,music:name=明天]");
        httpsdkPush.sendGroupMsg("244510218", "hello[ksust,at_all:qq=all][ksust,music:name=明天]");
        //点赞
        httpsdkPush.sendLike(qq, 1);
        //查看点赞数量
        System.out.println(httpsdkPush.getLikeCount(qq));
        //查看机器人当前状态
        System.out.println(JSON.toJSONString(httpsdkPush.getQQRobotInfo()));
    }
}
```
>消息转发   
```
//保证插件再2.2.2及以上并启动
public class MsgForwardDemo {
    public static void main(String[] args) throws IOException {
        HTTPSDK forwardPush = HTTPSDK.msgForwardPush("QQ", "code");//输入QQ和授权码
        System.out.println(forwardPush.getLoginQQ());
        System.out.println(forwardPush.sendPrivateMsg("QQ", "hello"));
        List<GroupMember> groupMemberList = forwardPush.getGroupMemberList("群号");
        for (GroupMember groupMember : groupMemberList) {
            System.out.println(groupMember.getName() + "," + groupMember.getQq());
        }
    }
}
```
### Python Demo
编写中...   
>提交返回    

>webSocket（结合使用workman）  

>HTTP推送   

>消息转发   

### NodeJS Demo
编写中...   
>提交返回    

>webSocket（结合使用workman）  

>HTTP推送   

>消息转发   

------

## SDK参考手册
---
文档更新中...    

| 方法 | 说明 | 返回值 | 支持平台 | 参数1（从左到右） | 参数2 | 参数3  | 参数4 |
| ----------- | ----------- | ----------- | ----------- | ----------- |----------- | ----------- | ----------- |
| httpGet(String msg) | 提交返回模型，PHP SDK无需传入参数 |  HTTPSDK对象 | IR/CQ |获取到的原始消息（RequestBody）|-|-|-|
| webSocket(String msg) | 提交返回（webSocket）模型，传入获取到的原始消息 |  HTTPSDK对象 | IR/CQ |获取到的原始消息（RequestBody）|-|-|-|
| httpPush(String URL, String key, String secret) | HTTP推送模型 |  HTTPSDK对象 | IR/CQ |推送地址及端口，如http://127.0.0.1:8888|验证key 为空或null则表示不加密|验证secret 为空或null则表示不加密|-|
| msgForwardPush(String qq, String code)| 消息转发推送模型 |  HTTPSDK对象 | IR/CQ |机器人QQ|该机器人QQ的授权码，统一管理平台：http://work.ksust.com|-|-|
| getMsg() | 获取接收到的消息（结构化） |  JSON/MessageGet对象 | IR/CQ |-|-|-|-|
| isCallback() | 当前消息体是否为插件反馈（用于提交返回模型下获取群列表等） |  boolean | IR/CQ |-|-|-|-|
| setCallbackSend(boolean callbackSend)| 是否在插件反馈情况下返回消息（提交返回），默认 |  - | IR/CQ |true/false|-|-|-|
| toJsonString() | 构造返回的JSON String格式并清除当前MessageBackList。同时重置已发送消息（清空），仅针对于提交返回 |  String | IR/CQ |-|-|-|-|
| sendPrivateMsg(String qq, String msg, int structureType, int subType) | 发送私聊消息，默认非卡片形式 |  int | IR/CQ |目标QQ，好友|消息内容|消息结构类型 0普通消息，1 XML消息，2 JSON消息默认0|XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启），默认0|
| 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 |

------

## 事件ID及标签
---   
   
### 事件ID
其中值>20000的为操作代码，其余的为事件ID。
| 事件名 | 值 | 说明 | 支持平台 | 备注 | 
| ----------- |----------- | ----------- | ----------- | ----------- |
| TYPE_FRIEND_TEMP | 0 | 在线状态临时会话（Pro版可用）| IR | - |
| TYPE_FRIEND | 1 | 好友消息，发送私聊消息| IR | - |
| TYPE_GROUP | 2 | 群消息，发送群消息| IR | - |
| TYPE_DISCUSS | 3 | 讨论组消息，发送讨论组消息| IR | - |
| TYPE_GROUP_TEMP | 4 | 群临时会话| IR | - |
| TYPE_DISCUSS_TEMP | 5 | 讨论组临时会话| IR | - |
| TYPE_ACCOUNT | 6 | 收到财付通转账| IR | - |
| TYPE_FRIEND_VERIFY_BACK | 7 | 好友验证回复会话消息（Pro版可用）| IR | - |
| TYPE_HANDLE_AGREE | 10 | 请求处理_同意| IR | - |
| TYPE_HANDLE_REJECT | 20 | 请求处理_拒绝| IR | - |
| TYPE_HANDLE_IGNORE | 30 | 请求处理_忽略| IR | - |
| TYPE_FRIEND_ADDED_SINGLE | 100 | 被单项添加为好友| IR | - |
| TYPE_FRIEND_ADDED | 101 | 某人请求加为好友| IR | - |
| TYPE_FRIEND_ADDED_AGREED | 102 | 被同意加为好友| IR | - |
| TYPE_FRIEND_ADDED_REJECTED | 103 | 被拒绝加为好友| IR | - |
| TYPE_FRIEND_DELETED | 104 | 被删除好友| IR | - |
| TYPE_FRIEND_FILE_OFFLINE | 105 | 收到好友离线文件（Pro版可用）| IR | - |
| TYPE_FRIEND_SIGNATURE_CHANGE | 106 | 好友签名变更| IR | - |
| TYPE_FRIEND_SAY_COMMENT | 107 | 说说被某人评论| IR | - |
| TYPE_GROUP_FILE_RECV | 218 | 收到群文件| IR | - |
| TYPE_GROUP_IN_WHO_REQUEST | 213 | 某人请求入群| IR | - |
| TYPE_GROUP_IN_ME_INVITED | 214 | 被邀请加入群| IR | - |
| TYPE_GROUP_IN_ME_AGREED | 220 | 被批准入群| IR | - |
| TYPE_GROUP_IN_ME_REJECTED | 221 | 被拒绝入群| IR | - |
| TYPE_GROUP_IN_WHO_INVITED | 215 | 某人被邀请加入群| IR | - |
| TYPE_GROUP_IN_WHO_INVITED_HAS | 219 | 某人已被邀请加入群（群主或管理员邀请成员加群或开启了群成员100以内无需审核或无需审核直接进群，被邀请人同意进群后才会触发）| IR | - |
| TYPE_GROUP_IN_WHO_AGREED | 212 | 某人被批准加入了群| IR | - |
| TYPE_GROUP_QUIT_WHO | 201 | 某人退出群| IR | - |
| TYPE_GROUP_QUITED_WHO | 202 | 某人被管理移除群| IR | - |
| TYPE_GROUP_INVALID | 216 | 某群被解散| IR | - |
| TYPE_GROUP_ADMIN_WHO_BECOME | 210 | 某人成为管理| IR | - |
| TYPE_GROUP_ADMIN_WHO_INVALID | 211 | 某人被取消管理| IR | - |
| TYPE_GROUP_BANED | 203 | 对象被禁言| IR | - |
| TYPE_GROUP_BANED_INVALID | 204 | 对象被解除禁言| IR | - |
| TYPE_GROUP_BANED_ALL | 205 | 开启全群禁言| IR | - |
| TYPE_GROUP_BANED_ALL_INVALID | 206 | 关闭全群禁言| IR | - |
| TYPE_GROUP_ANONYMOUS_OPEN | 207 | 开启匿名聊天| IR | - |
| TYPE_GROUP_ANONYMOUS_CLOSE | 208 | 关闭匿名聊天| IR | - |
| TYPE_GROUP_NOTICE_CHANGE | 209 | 群公告变动| IR | - |
| TYPE_GROUP_CARD_CHANGE | 217 | 群名片变动| IR | - |
| TYPE_SEND_LIKE | 20001 | 点赞| IR | 操作类型 |
| TYPE_SEND_SHAKE | 20002 | 窗口抖动| IR | - |
| TYPE_GROUP_BAN | 20011 | 群禁言（管理）| IR | - |
| TYPE_GROUP_QUIT | 20012 | 主动退群| IR | - |
| TYPE_GROUP_KICK | 20013 |  踢群成员（管理）| IR | - |
| TYPE_GROUP_SET_CARD | 20021 | 设置群名片（管理）| IR | - |
| TYPE_GROUP_SET_ADMIN | 20022 | 设置群管理（群主）| IR | - |
| TYPE_GROUP_HANDLE_GROUP_IN | 20023 | 入群处理（某人请求入群、我被邀请入群、某人被邀请入群）| IR | - |
| TYPE_FRIEND_HANDLE_FRIEND_ADD | 20024 | 加好友处理（是否同意被加好友）| IR | - |
| TYPE_GROUP_ADD_NOTICE | 20031 | 发群公告| IR | - |
| TYPE_GROUP_ADD_HOMEWORK | 20032 | 发群作业| IR | - |
| TYPE_GROUP_JOIN | 20033 | 主动申请加入群| IR | - |
| TYPE_DIS_CREATE | 20041 | 创建讨论组，返回讨论组ID（并且对外部接口支持直接根据好友列表创建讨论组）| IR | - |
| TYPE_DIS_INVITE | 20042 | 邀请加入某讨论组，多个用#隔开| IR | - |
| TYPE_DIS_KICK | 20043 | 踢出讨论组成员| IR | - |
| TYPE_DIS_QUIT | 20044 | 退出讨论组| IR | - |
| TYPE_GROUP_INVITE | 20051 | 邀请QQ入群（管理+普通成员）| IR | - |
| TYPE_GET_LOGIN_QQ | 20101 | 获取当前QQ| IR | - |
| TYPE_GET_STRANGER_INFO | 20102 | 获取陌生人信息，JSON，昵称，性别，年龄，签名| IR | - |
| TYPE_GET_GROUP_LIST | 20103 | 获取当前QQ群列表，JSON| IR | - |
| TYPE_GET_GROUP_MEMBER_LIST | 20104 | 获取指定群成员列表，JSON| IR | - |
| TYPE_GET_FRIEND_LIST | 20105 | 获取好友列表，JSON| IR | - |
| TYPE_GET_GROUP_NOTICE | 20106 | 获取群公告列表，JSON| IR | - |
| TYPE_GET_DIS_LIST | 20107 | 获取讨论组列表| IR | - |
| TYPE_GET_QQ_LEVEL | 20111 | 获取QQ等级| IR | - |
| TYPE_GET_GROUP_MEMBER_CARD | 20112 | 获取群成员名片| IR | - |
| TYPE_GET_QQ_ONLINE_STATUS | 20113 | 查询QQ是否在线| IR | - |
| TYPE_GET_QQ_IS_FRIEND | 20114 | 查询QQ是否好友| IR | - |
| TYPE_GET_QQ_ROBOT_INFO | 20115 | 获取机器人状态信息，JSON| IR | - |
| TYPE_LIKE_COUNT_GET | 20201 | 获取目标对象赞数目| IR | - |
| TYPE_SET_INPUT_STATUS | 20301 | 置正在输入状态（发送消息取消）| IR | - |
| TYPE_TIMER_SEND | 30001 | 定时任务提交类型| IR | - |
| SUBTYPE_CALLBACK_SEND | 10001 | 提交返回有反馈时，更改原数据中的subtype和msg（数据），向返回地址发送反馈
| -- | -- | -- | - | - |

### 消息标签
**消息标签用于特殊消息发送，例如卡片、@全体成员、音乐标签等**，目前支持的标签如下：
| 标签 | 说明 | 举例 | 支持平台 | 备注 |
| ----------- |----------- | ----------- | ----------- |----------- |
| [ksust,music:name=歌曲名] | 多选音乐卡片，传入歌名 | [ksust,music:name=明天] |  IR | - |
| [ksust,link:url=链接网址,title=标题文字,content=内容文字,pic=图片链接]  | 简单图文（卡片）| - | IR | - |
| [ksust,link2:url=链接网址,title=标题文字,content=内容文字,pic=图片链接,bcontent=内容2文字,bpic=大图链接] | 简单图文，加大图和长文本 |- | IR | - |
| [ksust,at:qq=qq] | 统一标签，艾特某人。qq=all 或 qq=QQ号码。可跨平台兼容 | [ksust,at:qq=all] |IR/CQ |  - |
| [ksust,image:pic=图片地址]  | CQ、IRQQ统一网络图片标签，pic为网络图片地址，可跨平台兼容 | - | IR/CQ | - |
| [ksust,at_all:qq=all]  | 逐个@全体成员，避免@全体成员限制 | 固定用法 [ksust,at_all:qq=all] | IR | - |

### 数据结构
简单列出必要的几种数据结构（使用**JSON格式**描述），其余请开发者在开发中查看SDK或者打印。
>提交返回-插件提交-数据结构（接口getMsg()返回值）
```
{
	Myqq:机器人QQ,
	Type:"消息类型，举例：【详看消息类型】 -1 未定义事件 1 好友信息 2,群信息 3,讨论组信息 4,群临时会话 5,讨论组临时会话 6,财付通转账,
	SubType:接收财付通转账时 1为好友 2为群临时会话 3为讨论组临时会话    有人请求入群时，不良成员这里为1,
	From:此消息的来源，如：群号、讨论组ID、临时会话QQ、好友QQ,
	Group:来源群号，若无则为空,
	Discuss:来源讨论组，若无则为空,
	QQ:主动发送这条消息的QQ，踢人时为踢人管理员QQ,
	ObjectQQ:被动触发的QQ，如某人被踢出群，则此参数为被踢出人QQ,
	Msg:消息文本内容（当消息为JSON、XML时为相应内容）。消息反馈时为消息反馈文本（如取群列表）,
	ID:消息id，用于识别消息【如异步处理时】、撤回等，构成：消息序号-消息ID,
	Data:{
		//携带数据，如当消息为转账消息时，则有相关属性。以下举例转账消息
		"Comments": "接收到转账时留言",
		"Money": "接收到转账时金额（数字）",
		"Number": "接收到转账时订单号"
		}
}
```
>提交返回-返回处理-数据结构（WEB返回）
```
{
					
	data:[
		{
            ID：唯一标识,UUID，
			Type:发送消息类型【具体查看SDK】， 1 好友信息 2,群信息 3,讨论组信息 4,群临时会话 5,讨论组临时会话 ...,20001 点赞,20002 窗口抖动,20011 群禁言（管理）,20012 退群,20013 踢群成员（管理）,20021 设置群名片（管理）,20022 设置群管理（群主）,20023 入群处理（某人请求入群、我被邀请入群、某人被邀请入群）,20024 加好友处理（是否同意被加好友）,20031 发公告,20032 发作业 等
			SubType:子类型，0普通，1匿名（需要群开启，默认0）,
			StructureType:消息结构类型，0为普通文本消息（默认）、1为XML消息、2为JSON消息,
			Group:操作或发送的群号或者讨论组号,
			QQ:操作或者发送的QQ,
			Msg:文本消息【标签等】，当发送类型为JSON、XML时为相应文本，禁言时为分钟数【分钟】,
			Send:是否开启同步发送（1为开启同步发送【测试】，0为不开启【默认】）,
			Data:附加数据，用于特定操作等（文本型）
		} ,
		{
			...		
		}
	    ]
}
```
>推送（转发）-发送-数据结构
```
{
	time:验证的时间戳,
	verify:验证字符串,
	data:[ //暂时仅允许一个成员
		{
            ID：唯一标识,UUID，
			Type:发送消息类型， 1 好友信息 2,群信息 3,讨论组信息 4,群临时会话 5,讨论组临时会话 ...,20001 点赞,20002 窗口抖动,20011 群禁言（管理）,20012 退群,20013 踢群成员（管理）,20021 设置群名片（管理）,20022 设置群管理（群主）,20023 入群处理（某人请求入群、我被邀请入群、某人被邀请入群）,20024 加好友处理（是否同意被加好友）,20031 发公告,20032 发作业 
			SubType:子类型，0普通，1匿名（需要群开启，默认0）,
			StructureType:消息结构类型，0为普通文本消息（默认）、1为XML消息、2为JSON消息,
			Group:操作或发送的群号或者讨论组号,
			QQ:操作或者发送的QQ,
			Msg:文本消息【标签等】，当发送类型为JSON、XML时为相应文本，禁言时为分钟数【分钟】,
			Send:是否开启同步发送（1为开启同步发送【测试】，0为不开启【默认】）,
			Data:附加数据，用于特定操作等（文本型）
		}
	]
}
```

------

## 插件界面及使用引导
---
文档更新中...


------
**管理平台：http://work.ksust.com**
