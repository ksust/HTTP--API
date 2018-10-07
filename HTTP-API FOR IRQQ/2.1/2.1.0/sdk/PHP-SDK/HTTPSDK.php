<?php
/**
 * Created by PhpStorm.
 * User: yugao
 * Date: 2018/2/2
 * Time: 14:40
 * version 2.1.0
 * 提交功能SDK，构造返回提交返回数据，用于服务端构造数据
 * 开发者邮箱 admin@ksust.com
 */
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
class HTTPSDK
{

    //消息类型
    public static $TYPE_FRIEND = 1;//发送私聊消息
    public static $TYPE_GROUP = 2;
    public static $TYPE_DISCUSS = 3;
    public static $TYPE_GROUP_TEMP = 4;//群临时会话
    public static $TYPE_DISCUSS_TEMP = 5;

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
     * 默认获取并解析客户端传来的消息
     * HttpApiLocalSDK constructor.
     */
    public function __construct()
    {
        $this->parseMsg();
    }

    /**
     * 获取并解析客户端传来的消息
     */
    public function parseMsg()
    {
        $this->msg = json_decode(urldecode(urldecode((file_get_contents("php://input")))), true);
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
    public function addDataCell($Type, $SubType = 0, $StructureType = 0, $Group = '', $QQ = '', $Msg = '', $Data = '', $Send = 0)
    {
        $data = $this->returnDataCell;
        $data['Type'] = $Type;
        $data['SubType'] = $SubType;
        $data['StructureType'] = $StructureType;
        $data['Group'] = $Group;
        $data['QQ'] = $QQ;
        $data['Msg'] = $Msg;
        $data['Data'] = $Data;
        $data['Send'] = $Send;
        array_push($this->returnData['data'], $data);
        return $this;
    }

    /**
     * 获取返回数据的数组
     */
    public function returnArray()
    {
        return $this->returnData;
    }

    /**
     * 获取返回数据：已格式化，作为最后直接的输出返回
     */
    public function returnJsonString()
    {
        return json_encode($this->returnData);
    }


    /******************************接下来为具体功能，每添加一个功能就增加一条消息。****************************************************/
    /******************消息发送*************************/
    /**
     * 发送私聊消息
     * @param $QQ
     * @param $Msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return $this
     */
    public function sendPrivateMsg($qq, $msg, $structureType = 0, $subType = 0)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_FRIEND, $subType, $structureType, '', $qq, $msg, '', 0);
    }

    /**
     * 发送群消息
     * @param $QQ
     * @param $Msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return $this
     */
    public function sendGroupMsg($group, $msg, $structureType = 0, $subType = 0)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP, $subType, $structureType, $group, '', $msg, '', 0);
    }

    /**
     * 发送讨论组消息
     * @param $QQ
     * @param $Msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return $this
     */
    public function sendDiscussMsg($discuss, $msg, $structureType = 0, $subType = 0)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_DISCUSS, $subType, $structureType, $discuss, '', $msg, '', 0);
    }

    /**
     * 向QQ点赞
     * @param $QQ
     * @param $count int 默认为1，作为消息的 Msg项
     * @return $this
     */
    public function sendLike($qq, $count = 1)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_SEND_LIKE, 0, 0, '', $qq, $count, '', 0);
    }

    /**
     * 窗口抖动
     * @param $qq
     * @return HTTPSDK
     */
    public function sendShake($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_SEND_SHAKE, 0, 0, '', $qq, '', '', 0);
    }

    /******************群操作、事件处理*************************/
    /**
     * 群禁言（管理）
     * @param $group string 群号
     * @param $qq string 禁言QQ，为空则禁言全群
     * @param $time int 禁言时间，单位秒，至少10秒。0为解除禁言
     * @return HTTPSDK
     */
    public function setGroupBan($group, $qq = '', $time = 10)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_BAN, 0, 0, $group, $qq, $time, '', 0);
    }

    /**
     * 主动退群
     * @param $group
     * @return HTTPSDK
     */
    public function setGroupQuit($group)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_QUIT, 0, 0, $group, '', '', '', 0);
    }

    /**
     * 踢人（管理）
     * @param $group  string
     * @param $qq string
     * @param bool $neverIn 是否不允许再加群
     * @return HTTPSDK
     */
    public function setGroupKick($group, $qq, $neverIn = false)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_KICK, 0, 0, $group, $qq, $neverIn ? 1 : 0, '', 0);
    }

    /**
     * 设置群名片
     * @param $group
     * @param $qq
     * @param string $card
     * @return HTTPSDK
     */
    public function setGroupCard($group, $qq, $card = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_SET_CARD, 0, 0, $group, $qq, $card, '', 0);
    }

    /**
     * 设置管理员（群主）
     * @param $Group
     * @param $QQ
     * @param bool $become true为设置，false为取消
     * @return HTTPSDK
     */
    public function setGroupAdmin($group, $qq, $become = false)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_SET_ADMIN, 0, 0, $group, $qq, $become ? 1 : 0, '', 0);
    }

    /**
     * 处理加群事件，是否同意
     * @param $group
     * @param $qq
     * @param bool $agree 是否同意加群
     * @param int $type 213请求入群  214我被邀请加入某群  215某人被邀请加入群 。为0则不管哪种
     * @param string $msg 消息，当拒绝时发送的消息
     * @return HTTPSDK
     */
    public function handleGroupIn($group, $qq, $agree = true, $type = 0, $msg = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_HANDLE_GROUP_IN, $type, 0, $group, $qq, $agree ? 1 : 0, $msg, 0);
    }

    /**
     * 是否同意被加好友
     * @param $qq string
     * @param bool $agree 是否同意
     * @param string $msg 附加消息
     * @return HTTPSDK
     */
    public function handleFriendAdd($qq, $agree = true, $msg = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_FRIEND_HANDLE_FRIEND_ADD, 0, 0, '', $qq, $agree ? 1 : 0, $msg, 0);
    }

    /**
     * 发群公告（管理）
     * @param $group string
     * @param $title string 内容
     * @param $content string 信息
     * @return HTTPSDK
     */
    public function addGroupNotice($group, $title, $content)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_ADD_NOTICE, 0, 0, $group, '', $title, $content, 0);
    }

    /**
     * 发群作业（管理）。注意作业名和标题中不能含有#号
     * @param $group
     * @param $homeworkName  string 作业名
     * @param $title string  标题
     * @param $content string 内容
     * @return HTTPSDK
     */
    public function addGroupHomework($group, $homeworkName, $title, $content)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_ADD_HOMEWORK, 0, 0, $group, '', $homeworkName . '#' . $title, $content, 0);
    }

    /**
     * 主动申请加入群
     * @param $group string 群号
     * @param string $reson string 加群理由
     * @return mixed
     */
    public function joinGroup($group, $reason = '')
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_JOIN, 0, 0, $group, '', $reason, 0, 0);
    }

    /**
     * 创建讨论组
     * @param $disName string 讨论组名。并作为创建后第一条消息发送（激活消息）
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
     * @param $disGroupId string 讨论组ID
     * @return mixed
     */
    public function disGroupQuit($disGroupId)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_DIS_QUIT, 0, 0, $disGroupId, '', '', 0, 0);
    }

    /**
     * 踢出讨论组
     * @param $disGroupId string 讨论组ID
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
     * @param $disGroupId string 讨论组号
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
     * @param groupId string  群号
     * @param qq string QQ
     * @return mixed 状态
     */
    public function groupInvite($groupId, $qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GROUP_INVITE, 0, 0, $groupId, '', $qq, 0, 0);
    }
    /*********************** 获取信息：注意获取反馈消息，通过ID识别 *******************************************/
    /**
     * 获取陌生人信息
     * @param $qq string
     * @return HTTPSDK
     */
    public function getStrangerInfo($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_STRANGER_INFO, 0, 0, '', $qq, '', '', 0);

    }

    /**
     * 获取当前登陆的QQ
     * @return HTTPSDK
     */
    public function getLoginQQ()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_LOGIN_QQ, 0, 0, '', '', '', '', 0);
    }

    /**
     * 获取当前QQ群列表
     * @return HTTPSDK
     */
    public function getGroupList()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_GROUP_LIST, 0, 0, '', '', '', '', 0);
    }

    /**
     * 获取当前登陆QQ好友列表
     * @return HTTPSDK
     */
    public function getFriendList()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_FRIEND_LIST, 0, 0, '', '', '', '', 0);
    }

    /**
     * 获取指定群群成员列表
     * @param $groupid
     * @return HTTPSDK
     */
    public function getGroupMemberList($groupid)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_GROUP_MEMBER_LIST, 0, 0, $groupid, '', '', '', 0);
    }

    /**
     * 获取群公告
     * @param $groupid
     * @return HTTPSDK
     */
    public function getGroupNotice($groupid)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_GROUP_NOTICE, 0, 0, $groupid, '', '', '', 0);
    }

    /**
     * 获取对象QQ赞数量
     * @param $qq
     * @return HTTPSDK
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
     * @param qq string QQ
     * @return mixed 等级
     */
    public function getQQLevel($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_LEVEL, 0, 0, '', $qq, '', '', 0);
    }

    /**
     * 获取群成员名片
     *
     * @param groupId string 群号
     * @param qq string QQ
     * @return mixed 名片
     */
    public function getGroupMemberCard($groupId, $qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_LEVEL, 0, 0, $groupId, $qq, '', '', 0);
    }

    /**
     * 查询QQ是否在线
     *
     * @param qq string QQ
     * @return mixed 是否在线
     */
    public function getQQIsOline($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_ONLINE_STATUS, 0, 0, '', $qq, '', '', 0);
    }

    /**
     * 查询QQ是否好友
     *
     * @param qq string QQ
     * @return mixed 是否好友
     */
    public function getQQIsFriend($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_IS_FRIEND, 0, 0, '', $qq, '', '', 0);
    }

    /**
     * 获取当前QQ机器人状态信息（如是否在线）
     *
     * @return string 结构信息
     */
    public function getQQRobotInfo()
    {
        return $this->addDataCell(HTTPSDK::$TYPE_GET_QQ_ROBOT_INFO, 0, 0, '', '', '', '', 0);
    }

    /**
     * 置正在输入 状态，发送消息撤销
     *
     * @param qq string QQ
     * @return mixed 状态
     */
    public function setInputStatus($qq)
    {
        return $this->addDataCell(HTTPSDK::$TYPE_SET_INPUT_STATUS, 0, 0, '', $qq, '', '', 0);
    }
}