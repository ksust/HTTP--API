<?php
/**
 * Created by PhpStorm.
 * User: yugao
 * Date: 2018/2/2
 * Time: 14:40
 * version 2.2.3
 * Note: HTTPSDK for PHP(适用于版本2.2.2插件):用于解析插件消息、构造返回数据，以及HTTP推送（发起HTTP请求）
 * Contact: 开发者邮箱 admin@ksust.com
 */
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
class HTTPSDK
{
    //全局配置
    private $SDKType = 0;//SDK模式，0为http提交返回，1为webSocket提交返回模式，2为httpPush推送模式，3为消息转发模式（http协议）
    private $serverURL = 'http://127.0.0.1:8888';
    private $serverKey = '123';
    private $serverSecret = '456';
    private $callbackSend = false;//在回调情况下是否开启发送消息，默认否
    //用于消息转发
    private $myqq = '';//消息转发，机器人QQ
    private $code = '';//消息转发，机器人授权码
    private $url = 'http://127.0.0.1:2047';//消息转发请求，代理服务器http地址
    private $token = '';//消息转发，验证token（由授权码、QQ获取）
    private static $TEMP_DIR = './temp/msgForward/';
    //消息类型
    public static $TYPE_FRIEND_TEMP = 0;//在线状态临时会话（Pro版可用）
    public static $TYPE_FRIEND = 1;//好友消息，发送私聊消息
    public static $TYPE_GROUP = 2;//群消息，发送群消息
    public static $TYPE_DISCUSS = 3;//讨论组消息，发送讨论组消息
    public static $TYPE_GROUP_TEMP = 4;//群临时会话
    public static $TYPE_DISCUSS_TEMP = 5;//讨论组临时会话
    public static $TYPE_ACCOUNT = 6;//收到财付通转账
    public static $TYPE_FRIEND_VERIFY_BACK = 7;//好友验证回复会话消息（Pro版可用）

    //请求处理事件
    public static $TYPE_HANDLE_AGREE = 10;//请求处理_同意
    public static $TYPE_HANDLE_REJECT = 20;//请求处理_拒绝
    public static $TYPE_HANDLE_IGNORE = 30;//请求处理_忽略

    //好友事件
    public static $TYPE_FRIEND_ADDED_SINGLE = 100;//被单项添加为好友
    public static $TYPE_FRIEND_ADDED = 101;//某人请求加为好友
    public static $TYPE_FRIEND_ADDED_AGREED = 102;//被同意加为好友
    public static $TYPE_FRIEND_ADDED_REJECTED = 103;//被拒绝加为好友
    public static $TYPE_FRIEND_DELETED = 104;//被删除好友
    public static $TYPE_FRIEND_FILE_OFFLINE = 105;//收到好友离线文件（Pro版可用）
    public static $TYPE_FRIEND_SIGNATURE_CHANGE = 106;//好友签名变更
    public static $TYPE_FRIEND_SAY_COMMENT = 107;//说说被某人评论

    //群事件
    public static $TYPE_GROUP_FILE_RECV = 218;//收到群文件
    public static $TYPE_GROUP_IN_WHO_REQUEST = 213;//某人请求入群
    public static $TYPE_GROUP_IN_ME_INVITED = 214;//被邀请加入群
    public static $TYPE_GROUP_IN_ME_AGREED = 220;//被批准入群
    public static $TYPE_GROUP_IN_ME_REJECTED = 221;//被拒绝入群
    public static $TYPE_GROUP_IN_WHO_INVITED = 215;//某人被邀请加入群
    public static $TYPE_GROUP_IN_WHO_INVITED_HAS = 219;//某人已被邀请加入群（群主或管理员邀请成员加群或开启了群成员100以内无需审核或无需审核直接进群，被邀请人同意进群后才会触发）
    public static $TYPE_GROUP_IN_WHO_AGREED = 212;//某人被批准加入了群

    public static $TYPE_GROUP_QUIT_WHO = 201;//某人退出群
    public static $TYPE_GROUP_QUITED_WHO = 202;//某人被管理移除群
    public static $TYPE_GROUP_INVALID = 216;//某群被解散
    public static $TYPE_GROUP_ADMIN_WHO_BECOME = 210;//某人成为管理
    public static $TYPE_GROUP_ADMIN_WHO_INVALID = 211;//某人被取消管理

    public static $TYPE_GROUP_BANED = 203;//对象被禁言
    public static $TYPE_GROUP_BANED_INVALID = 204;//对象被解除禁言
    public static $TYPE_GROUP_BANED_ALL = 205;//开启全群禁言
    public static $TYPE_GROUP_BANED_ALL_INVALID = 206;//关闭全群禁言
    public static $TYPE_GROUP_ANONYMOUS_OPEN = 207;//开启匿名聊天
    public static $TYPE_GROUP_ANONYMOUS_CLOSE = 208;//关闭匿名聊天
    public static $TYPE_GROUP_NOTICE_CHANGE = 209;//群公告变动
    public static $TYPE_GROUP_CARD_CHANGE = 217;//群名片变动


    //操作类型
    public static $TYPE_SEND_LIKE = 20001;//点赞
    public static $TYPE_SEND_SHAKE = 20002;//窗口抖动
    public static $TYPE_GROUP_BAN = 20011;//群禁言（管理）
    public static $TYPE_GROUP_QUIT = 20012;//主动退群
    public static $TYPE_GROUP_KICK = 20013;// 踢群成员（管理）
    public static $TYPE_GROUP_SET_CARD = 20021;//设置群名片（管理）
    public static $TYPE_GROUP_SET_ADMIN = 20022;//设置群管理（群主）
    public static $TYPE_GROUP_HANDLE_GROUP_IN = 20023;//入群处理（某人请求入群、我被邀请入群、某人被邀请入群）
    public static $TYPE_FRIEND_HANDLE_FRIEND_ADD = 20024;//加好友处理（是否同意被加好友）
    public static $TYPE_GROUP_ADD_NOTICE = 20031;//发群公告
    public static $TYPE_GROUP_ADD_HOMEWORK = 20032;//发群作业
    public static $TYPE_GROUP_JOIN = 20033;//主动申请加入群

    public static $TYPE_DIS_CREATE = 20041;//创建讨论组，返回讨论组ID（并且对外部接口支持直接根据好友列表创建讨论组）
    public static $TYPE_DIS_INVITE = 20042;//邀请加入某讨论组，多个用#隔开
    public static $TYPE_DIS_KICK = 20043;//踢出讨论组成员
    public static $TYPE_DIS_QUIT = 20044;//退出讨论组
    public static $TYPE_GROUP_INVITE = 20051;//邀请QQ入群（管理+普通成员）


    public static $TYPE_GET_LOGIN_QQ = 20101;//获取当前QQ
    public static $TYPE_GET_STRANGER_INFO = 20102;//获取陌生人信息，JSON，昵称，性别，年龄，签名
    public static $TYPE_GET_GROUP_LIST = 20103;//获取当前QQ群列表，JSON
    public static $TYPE_GET_GROUP_MEMBER_LIST = 20104;//获取指定群成员列表，JSON
    public static $TYPE_GET_FRIEND_LIST = 20105;//获取好友列表，JSON
    public static $TYPE_GET_GROUP_NOTICE = 20106;//获取群公告列表，JSON
    public static $TYPE_GET_DIS_LIST = 20107;//获取讨论组列表
    public static $TYPE_GET_QQ_LEVEL = 20111;//获取QQ等级
    public static $TYPE_GET_GROUP_MEMBER_CARD = 20112;//获取群成员名片
    public static $TYPE_GET_QQ_ONLINE_STATUS = 20113;//查询QQ是否在线
    public static $TYPE_GET_QQ_IS_FRIEND = 20114;//查询QQ是否好友
    public static $TYPE_GET_QQ_ROBOT_INFO = 20115;//获取机器人状态信息，JSON

    public static $TYPE_LIKE_COUNT_GET = 20201;//获取目标对象赞数目

    public static $TYPE_SET_INPUT_STATUS = 20301;//置正在输入状态（发送消息取消）

    public static $TYPE_TIMER_SEND = 30001;//定时任务提交类型

    //消息子类型
    public static $SUBTYPE_CALLBACK_SEND = 10001;//提交返回有反馈时，更改原数据中的subtype和msg（数据），向返回地址发送反馈

    private $msg = [];//客户端发来的消息解析
    private $returnData = ['data' => []];//返回给插件的数据
    private $returnDataCell = [//返回数据中的data单元
        'ID' => '-1',//消息唯一标识，ID，可使用毫秒时间戳、UUID等
        'Type' => -1,//发送消息类型， 1 好友信息 2,群信息 3,讨论组信息 4,群临时会话 5,讨论组临时会话 ...,20001 群禁言,
        'SubType' => 0,//0普通，1匿名（需要群开启，默认0）
        'StructureType' => 0,//消息结构类型，0为普通文本消息（默认）、1为XML消息、2为JSON消息
        'Group' => '',//操作或发送的群号或者讨论组号
        'QQ' => '',//操作或者发送的QQ
        'Msg' => '',//文本消息【标签等】，当发送类型为JSON、XML时为相应文本，禁言时为分钟数【分钟】
        'Send' => 0,//是否开启同步发送（1为开启同步发送【测试】，0为不开启【默认】）
        'Data' => ''//附加数据，用于特定操作等（文本型
    ];

    /**
     * http提交返回的构建方法，一般使用该方法
     * @return HTTPSDK
     */
    public static function httpGet()
    {
        $sdk = new HTTPSDK();
        $sdk->SDKType = 0;
        return $sdk;
    }

    /**
     * webSocket服务端模式下用于解析插件消息，传入接收到的消息即可（若有粘包等情况，请先以换行\r\n分开）
     * @param string $msg 接收到的消息（若有粘包等情况，请先以换行\r\n分开）
     * @return HTTPSDK
     */
    public static function webSocket($msg)
    {
        $sdk = new HTTPSDK($msg);
        $sdk->SDKType = 1;
        return $sdk;
    }

    /**
     * HTTP推送，HTTPSDKPush
     * @param string $URL 推送地址及端口，如http://127.0.0.1:8888
     * @param string $serverKey 验证key，默认123
     * @param string $serverSecret 验证secret，默认456
     * @param boolean $serverVerify 是否开启验证，默认开启
     * @return HTTPSDK
     */
    public static function httpPush($URL, $serverKey = null, $serverSecret = null)
    {
        $sdk = new HTTPSDK();
        $sdk->SDKType = 2;
        $sdk->serverURL = $URL;
        $sdk->serverKey = $serverKey;
        $sdk->serverSecret = $serverSecret;
        return $sdk;
    }

    /**
     * 消息转发推送，消息转发模式（http协议）。需要在到平台免费申请授权码：http://work.ksust.com
     * @param string $qq 机器人QQ
     * @param string $code 该机器人QQ的授权码，统一管理平台：http://work.ksust.com
     * @return HTTPSDK
     */
    public static function msgForwardPush($qq, $code)
    {
        $sdk = new HTTPSDK();
        $sdk->SDKType = 3;
        $sdk->myqq = $qq;
        $sdk->code = $code;
        list($sdk->url, $sdk->token) = self::getMsgForwardPushToken($qq, $code);
        return $sdk;
    }

    /**
     * 获取消息转发请求的token
     * @param string $qq 机器人QQ
     * @param string $code 该机器人QQ的授权码，统一管理平台：http://work.ksust.com
     * @return array url,token
     */
    private static function getMsgForwardPushToken($qq, $code)
    {

        //初始化，获取token（Redis缓存或本地文件缓存，远程获取）
        $token = '';
        $url = 'http://127.0.0.1:2047';
        $tokenKey = 'msgForwardToken-' . $qq;
        $urlKey = 'msgForwardURL-' . $qq;
        if (class_exists('Redis')) {
            //默认使用Redis，否则使用文件缓存
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            if ($redis->exists($tokenKey) && $redis->exists($urlKey)) {
                $token = $redis->get($tokenKey);
                $url = $redis->get($urlKey);
            } else {
                $verifyData = self::sendMsgForwardPush('http://qq.ksust.com/api/tool.php?func=get_server_dst'
                    , 3, 'user-' . $qq, 'plugin-' . $qq, $code);
                $verifyData = json_decode($verifyData, true);
                if ($verifyData['status'] == 1) {
                    $token = $verifyData['data']['token'];
                    $url = 'http://' . $verifyData['data']['ip'] . ':' . $verifyData['data']['http-port'] . '/api/user/request';
                    $redis->set($tokenKey, $token);
                    $redis->set($urlKey, $urlKey);
                }
            }
        } else {
            //文件缓存，创建./temp目录
            $tempDir = self::$TEMP_DIR;
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 777, true);
            }
            if (time() - filemtime($tempDir . $tokenKey) <= 60 * 60 * 24
                && time() - filemtime($tempDir . $urlKey) <= 60 * 60 * 24
                && strlen(file_get_contents($tempDir . $tokenKey)) > 5
            ) {
                $token = file_get_contents($tempDir . $tokenKey);
                $url = file_get_contents($tempDir . $urlKey);
            } else {
                $verifyData = self::sendMsgForwardPush('http://qq.ksust.com/api/tool.php?func=get_server_dst'
                    , 3, 'user-' . $qq, 'plugin-' . $qq, $code);
                $verifyData = json_decode($verifyData, true);
                if ($verifyData['status'] == 1) {
                    $token = $verifyData['data']['token'];
                    $url = 'http://' . $verifyData['data']['ip'] . ':' . $verifyData['data']['http-port'] . '/api/user/request';
                    file_put_contents($tempDir . $tokenKey, $token);
                    file_put_contents($tempDir . $urlKey, $url);
                }
            }
        }
        return [$url, $token];
    }

    /**
     * 消息转发请求协议封装
     * @param string $url
     * @param string $code
     * @param string $src
     * @param string $dst
     * @param string $token
     * @param array $dataIn
     * @return string 请求结果
     */
    private static function sendMsgForwardPush($url, $code, $src, $dst, $token, $dataIn = array())
    {
        $data['id'] = time();
        $data['code'] = $code;
        $data['src'] = $src;
        $data['dst'] = $dst;
        $data['time'] = time();
        $data['token'] = $token;
        $data['data'] = json_encode($dataIn);
        //请求
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);//参数1  不显示
        curl_setopt($conn, CURLOPT_POST, 1);
        curl_setopt($conn, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($conn, CURLOPT_HTTPHEADER, ["Content-Type: application/json; charset=utf-8"]);
        $get = curl_exec($conn);
        return $get;
    }

    /**
     * 解析客户端传来的消息
     * HttpApiLocalSDK constructor.
     * @param string $msg 解析的消息（JSON），http提交返回使用默认参数，ws等需要解析的消息则传入消息字符串
     */
    private function __construct($msg = '')
    {
        $this->parseMsg($msg);
    }

    /**
     * 获取并解析客户端传来的消息
     * @param string $msg 解析的消息（JSON），为空则从PHP输入流中获取
     */
    private function parseMsg($msg)
    {
        if ($msg == '')
            $this->msg = json_decode(urldecode(urldecode((file_get_contents("php://input")))), true);
        else
            $this->msg = json_decode(urldecode(urldecode($msg)), true);
    }

    /**
     * 添加功能，原始方法：为了保持一致，此处参数使用大驼峰命名
     * @param $Type
     * @param int $SubType
     * @param int $StructureType
     * @param string $Group
     * @param string $QQ
     * @param string $Msg
     * @param string $Data
     * @param int $Send
     * @return mixed
     */
    private function addDataCell($Type, $SubType = 0, $StructureType = 0, $Group = '', $QQ = '', $Msg = '', $Data = '', $Send = 0)
    {
        $data = $this->returnDataCell;
        $data['ID'] = md5(mt_rand(-time(), time()) . time() . $this->toJsonString());
        $data['Type'] = $Type;
        $data['SubType'] = $SubType;
        $data['StructureType'] = $StructureType;
        $data['Group'] = $Group;
        $data['QQ'] = $QQ;
        $data['Msg'] = $Msg;
        $data['Data'] = $Data;
        $data['Send'] = $Send;
        array_push($this->returnData['data'], $data);
        //分类型确定调用方式
        if ($this->SDKType == 2) {
            $pushReturn = $this->sendPushData();
            $pushReturnJson = json_decode($pushReturn, true);
            //若解析失败，则返回原文本；否则返回解析后的array
            return $pushReturnJson == null ? $pushReturn : $pushReturnJson;
        } else if ($this->SDKType == 3) {
            //消息转发
            $sendData = $this->returnData['data'][0];
            $this->returnData = ['data' => []];//发送之后清空数据，以免影响下一次发送**
            $forwardReturnJson = json_decode(self::sendMsgForwardPush($this->url, 3, 'user-' . $this->myqq, 'plugin-' . $this->myqq
                , $this->token, $sendData), true);
            if ($forwardReturnJson['code'] == -1) {
                //清空缓存
                file_put_contents(self::$TEMP_DIR . 'msgForwardToken-' . $this->myqq, '');
                file_put_contents(self::$TEMP_DIR . 'msgForwardURL-' . $this->myqq, '');
            }
            return $forwardReturnJson;

        }
        //非推送模式下返回null
        return null;
    }

    /**
     * httpPush：发送消息与获得结果
     * @return mixed 返回json数据
     */
    private function sendPushData()
    {
        $url = $this->serverURL;
        $verify = [];
        $verify['time'] = time();
        $verify['verify'] = md5($this->serverKey . $verify['time'] . $this->serverSecret);
        $send_arr = $verify + $this->returnData;//加入加密信息。"+",若两个数组存在相同的key,前一会覆盖后一
        $this->returnData = ['data' => []];//发送之后清空数据，以免影响下一次发送**
        $json_data = json_encode($send_arr);
        $result_url = $url;
        $conn = curl_init($result_url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);//参数1  不显示
        curl_setopt($conn, CURLOPT_POST, 1);
        curl_setopt($conn, CURLOPT_POSTFIELDS, $json_data);
        $get = curl_exec($conn);
        return $get;
    }

    /**
     * 返回客户端传来的消息
     * @return array
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * 当前消息体是否为插件反馈（用于提交返回模型下获取群列表等）
     * 若是，则默认不能够返回消息。若需要返回消息，调用 setCallbackSend(true)
     * @return boolean
     */
    public function isCallback()
    {
        return $this->getMsg()['SubType'] == self::$SUBTYPE_CALLBACK_SEND;
    }

    /**
     * 是否在插件反馈情况下返回消息（提交返回），默认否。该方法在PHP下无效
     * @param boolean $callbackSend 默认false
     */
    public function setCallbackSend($callbackSend = false)
    {
        $this->callbackSend = $callbackSend;
    }

    /**
     * 获取返回数据：已格式化，作为最后直接的输出返回.同时重置已发送消息（清空）
     * @return string 消息文本（json_encode）
     */
    public function toJsonString()
    {
        $ret = json_encode($this->returnData);
        $this->returnData = ['data' => []];
        return $ret;
    }


    /******************************接下来为具体功能，每添加一个功能就增加一条消息。****************************************************/
    /******************消息发送*************************/
    /**
     * 通用发送消息方法（为解决某些平台兼容问题）
     * @param int $type 消息类型，见TypeEnum（如1为好友消息，2为群消息，3为讨论组消息，4为群临时消息等）
     * @param string $group 群号
     * @param string $qq QQ
     * @param string $msg 消息内容
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return mixed
     */
    public function sendMsg($type, $group, $qq, $msg, $structureType = 0, $subType = 0)
    {
        return $this->addDataCell($type, $subType, $structureType, $group, $qq, $msg, '', 0);
    }

    /**
     * 发送私聊消息
     * @param string $qq
     * @param string $msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return mixed
     */
    public function sendPrivateMsg($qq, $msg, $structureType = 0, $subType = 0)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_FRIEND, $subType, $structureType, '', $qq, $msg, '', 0);
    }

    /**
     * 发送群消息
     * @param string $groupId
     * @param string $msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return mixed
     */
    public function sendGroupMsg($groupId, $msg, $structureType = 0, $subType = 0)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP, $subType, $structureType, $groupId, '', $msg, '', 0);
    }

    /**
     * 发送讨论组消息
     * @param string $discuss
     * @param string $msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return mixed
     */
    public function sendDiscussMsg($discuss, $msg, $structureType = 0, $subType = 0)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_DISCUSS, $subType, $structureType, $discuss, '', $msg, '', 0);
    }

    /**
     * 向QQ点赞
     * @param string $qq
     * @param int $count 默认为1，作为消息的 Msg项
     * @return mixed
     */
    public function sendLike($qq, $count = 1)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_SEND_LIKE, 0, 0, '', $qq, $count, '', 0);
    }

    /**
     * 窗口抖动
     * @param string $qq
     * @return mixed
     */
    public function sendShake($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_SEND_SHAKE, 0, 0, '', $qq, '', '', 0);
    }

    /******************群操作、事件处理*************************/
    /**
     * 群禁言（管理）
     * @param string $groupId 群号
     * @param string $qq 禁言QQ，为空则禁言全群
     * @param int $time 禁言时间，单位秒，至少10秒。0为解除禁言
     * @return mixed
     */
    public function setGroupBan($groupId, $qq = '', $time = 10)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_BAN, 0, 0, $groupId, $qq, $time, '', 0);
    }

    /**
     * 主动退群
     * @param string $groupId
     * @return mixed
     */
    public function setGroupQuit($groupId)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_QUIT, 0, 0, $groupId, '', '', '', 0);
    }

    /**
     * 踢人（管理）
     * @param string $groupId
     * @param string $qq
     * @param boolean $neverIn 是否不允许再加群
     * @return mixed
     */
    public function setGroupKick($groupId, $qq, $neverIn = false)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_KICK, 0, 0, $groupId, $qq, $neverIn ? 1 : 0, '', 0);
    }

    /**
     * 设置群名片
     * @param string $groupId
     * @param string $qq
     * @param string $card
     * @return mixed
     */
    public function setGroupCard($groupId, $qq, $card = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_SET_CARD, 0, 0, $groupId, $qq, $card, '', 0);
    }

    /**
     * 设置管理员（群主）
     * @param string $groupId
     * @param string $qq
     * @param boolean $become true为设置，false为取消
     * @return mixed
     */
    public function setGroupAdmin($groupId, $qq, $become = false)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_SET_ADMIN, 0, 0, $groupId, $qq, $become ? 1 : 0, '', 0);
    }

    /**
     * 处理加群事件，是否同意
     * @param string $groupId
     * @param string $qq
     * @param boolean $agree 是否同意加群
     * @param int $type 213请求入群  214我被邀请加入某群  215某人被邀请加入群 。为0则不管哪种
     * @param string $msg 消息，当拒绝时发送的消息
     * @return mixed
     */
    public function handleGroupIn($groupId, $qq, $agree = true, $type = 0, $msg = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_HANDLE_GROUP_IN, $type, 0, $groupId, $qq, $agree ? 1 : 0, $msg, 0);
    }

    /**
     * 是否同意被加好友
     * @param string $qq
     * @param boolean $agree 是否同意
     * @param string $msg 附加消息
     * @return mixed
     */
    public function handleFriendAdd($qq, $agree = true, $msg = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_FRIEND_HANDLE_FRIEND_ADD, 0, 0, '', $qq, $agree ? 1 : 0, $msg, 0);
    }

    /**
     * 发群公告（管理）
     * @param string $groupId
     * @param string $title 内容
     * @param string $content 信息
     * @return mixed
     */
    public function addGroupNotice($groupId, $title, $content)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_ADD_NOTICE, 0, 0, $groupId, '', $title, $content, 0);
    }

    /**
     * 发群作业（管理）。注意作业名和标题中不能含有#号
     * @param string $groupId
     * @param string $homeworkName 作业名
     * @param string $title 标题
     * @param string $content 内容
     * @return mixed
     */
    public function addGroupHomework($groupId, $homeworkName, $title, $content)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_ADD_HOMEWORK, 0, 0, $groupId, '', $homeworkName . '#' . $title, $content, 0);
    }

    /**
     * 主动申请加入群
     * @param string $groupId 群号
     * @param string $reason 加群理由
     * @return mixed
     */
    public function joinGroup($groupId, $reason = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_JOIN, 0, 0, $groupId, '', $reason, 0, 0);
    }

    /**
     * 创建讨论组
     * @param string $disName 讨论组名。并作为创建后第一条消息发送（激活消息）
     * @param array $qqList 需要添加到讨论组的QQ号列表
     * @return mixed 讨论组ID
     */
    public function disGroupCreate($disName, $qqList = [])
    {
        $qqListStr = '';
        $first = true;
        foreach ($qqList as $qq) {
            $qqListStr .= $first ? $qq : '#' . $qq;
            $first = false;
        }
        return $this->addDataCell(HTTPSDK::$TYPE_DIS_CREATE, 0, 0, '', '', $disName, $qqListStr, 0);
    }

    /**
     * 退出讨论组
     * @param string $disGroupId 讨论组ID
     * @return mixed
     */
    public function disGroupQuit($disGroupId)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_DIS_QUIT, 0, 0, $disGroupId, '', '', 0, 0);
    }

    /**
     * 踢出讨论组
     * @param string $disGroupId 讨论组ID
     * @param array $qqList 欲踢出的QQ号列表
     * @return mixed
     */
    public function disGroupKick($disGroupId, $qqList = [])
    {
        $qqListStr = '';
        $first = true;
        foreach ($qqList as $qq) {
            $qqListStr .= $first ? $qq : '#' . $qq;
            $first = false;
        }
        return $this->addDataCell(HTTPSDK::$TYPE_DIS_KICK, 0, 0, $disGroupId, '', $qqListStr, 0, 0);
    }

    /**
     * 添加讨论组成员
     * @param string $disGroupId 讨论组号
     * @param array $qqList 欲添加的QQ号列表
     * @return mixed
     */
    public function disGroupInvite($disGroupId, $qqList = [])
    {
        $qqListStr = '';
        $first = true;
        foreach ($qqList as $qq) {
            $qqListStr .= $first ? $qq : '#' . $qq;
            $first = false;
        }
        return $this->addDataCell(HTTPSDK::$TYPE_DIS_INVITE, 0, 0, $disGroupId, '', $qqListStr, 0, 0);
    }

    /**
     * 邀请QQ入群（管理+群员）
     *
     * @param string $groupId 群号
     * @param string $qq QQ
     * @return mixed 状态
     */
    public function groupInvite($groupId, $qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_INVITE, 0, 0, $groupId, '', $qq, 0, 0);
    }
    /*********************** 获取信息：注意获取反馈消息，通过ID识别 *******************************************/
    /**
     * 获取陌生人信息
     * @param string $qq
     * @return mixed
     */
    public function getStrangerInfo($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_STRANGER_INFO, 0, 0, '', $qq, '', '', 0);

    }

    /**
     * 获取当前登陆的QQ
     * @return mixed
     */
    public function getLoginQQ()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_LOGIN_QQ, 0, 0, '', '', '', '', 0);
    }

    /**
     * 获取当前QQ群列表
     * @return mixed
     */
    public function getGroupList()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_GROUP_LIST, 0, 0, '', '', '', '', 0);
    }

    /**
     * 获取当前登陆QQ好友列表
     * @return mixed
     */
    public function getFriendList()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_FRIEND_LIST, 0, 0, '', '', '', '', 0);
    }

    /**
     * 获取指定群群成员列表
     * @param string $groupId
     * @return mixed
     */
    public function getGroupMemberList($groupId)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_GROUP_MEMBER_LIST, 0, 0, $groupId, '', '', '', 0);
    }

    /**
     * 获取群公告
     * @param string $groupId
     * @return mixed
     */
    public function getGroupNotice($groupId)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_GROUP_NOTICE, 0, 0, $groupId, '', '', '', 0);
    }

    /**
     * 获取对象QQ赞数量
     * @param $qq
     * @return mixed
     */
    public function getLikeCount($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_LIKE_COUNT_GET, 0, 0, '', $qq, '', '', 0);
    }

    /**
     * 获取讨论组列表
     * @return mixed
     */
    public function getDisGroupList()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_DIS_LIST, 0, 0, '', '', '', '', 0);
    }

    /**
     * 获取QQ等级
     *
     * @param string $qq QQ
     * @return mixed 等级
     */
    public function getQQLevel($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_LEVEL, 0, 0, '', $qq, '', '', 0);
    }

    /**
     * 获取群成员名片
     *
     * @param string $groupId 群号
     * @param string $qq QQ
     * @return mixed 名片
     */
    public function getGroupMemberCard($groupId, $qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_LEVEL, 0, 0, $groupId, $qq, '', '', 0);
    }

    /**
     * 查询QQ是否在线
     *
     * @param string $qq QQ
     * @return mixed 是否在线
     */
    public function getQQIsOline($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_ONLINE_STATUS, 0, 0, '', $qq, '', '', 0);
    }

    /**
     * 查询QQ是否好友
     *
     * @param string $qq QQ
     * @return mixed 是否好友
     */
    public function getQQIsFriend($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_IS_FRIEND, 0, 0, '', $qq, '', '', 0);
    }

    /**
     * 获取当前QQ机器人状态信息（如是否在线）
     *
     * @return mixed 结构信息
     */
    public function getQQRobotInfo()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_ROBOT_INFO, 0, 0, '', '', '', '', 0);
    }

    /**
     * 置正在输入 状态，发送消息撤销
     *
     * @param string $qq QQ
     * @return mixed 状态
     */
    public function setInputStatus($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_SET_INPUT_STATUS, 0, 0, '', $qq, '', '', 0);
    }
}