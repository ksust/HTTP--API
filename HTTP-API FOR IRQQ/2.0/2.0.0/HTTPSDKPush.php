<?php
/**
 * Created by PhpStorm.
 * User: yugao
 * Date: 2018/2/2
 * Time: 23:08
 * version:2.0.0
 * 主动推送SDK：推送
 * 开发者邮箱 admin@ksust.com
 */

class HTTPSDKPush{

    //全局配置
    public static $serverURL='http://127.0.0.1:8888';
    public static $serverVerfy=true; //是否启用验证
    public static $serverKey='123';
    public static $serverSecret='456';
    //消息类型
    public static $TYPE_FRIEND=1;//发送私聊消息
    public static $TYPE_GROUP=2;
    public static $TYPE_DISCUSS=3;
    public static $TYPE_GROUP_TEMP=4;//群临时会话
    public static $TYPE_DISCUSS_TEMP=5;

    public static $TYPE_SEND_LIKE=20001;//点赞
    public static $TYPE_SEND_SHAKE=20002;//窗口抖动
    public static $TYPE_GROUP_BAN=20011;//群禁言（管理）
    public static $TYPE_GROUP_QUIT=20012;//主动退群
    public static $TYPE_GROUP_KICK=20013;// 踢群成员（管理）
    public static $TYPE_GROUP_SET_CARD=20021;//设置群名片（管理）
    public static $TYPE_GROUP_SET_ADMIN=20022;//设置群管理（群主）
    public static $TYPE_GROUP_HANDLE_GROUP_IN=20023;//入群处理（某人请求入群、我被邀请入群、某人被邀请入群）
    public static $TYPE_FRIEND_HANDLE_FRIEND_ADD=20024;//加好友处理（是否同意被加好友）
    public static $TYPE_GROUP_ADD_NOTICE=20031;//发群公告
    public static $TYPE_GROUP_ADD_HOMEWORK=20032;//发群作业

    public static $TYPE_GET_LOGIN_QQ=20101;//获取当前QQ
    public static $TYPE_GET_STRANGER_INFO=20102;//获取陌生人信息，JSON，昵称，性别，年龄，签名
    public static $TYPE_GET_GROUP_LIST=20103;//获取当前QQ群列表，JSON
    public static $TYPE_GET_GROUP_MEMBER_LIST=20104;//获取指定群成员列表，JSON
    public static $TYPE_GET_FRIEND_LIST=20105;//获取好友列表，JSON
    public static $TYPE_GET_GROUP_NOTICE=20106;//获取群公告列表，JSON


    private $returnData=['data'=>[]];//返回给插件的数据
    private $returnDataCell=[//返回数据中的data单元
        'Type'=>-1,//发送消息类型， 1 好友信息 2,群信息 3,讨论组信息 4,群临时会话 5,讨论组临时会话 ...,20001 群禁言,
        'SubType'=>0,//0普通，1匿名（需要群开启，默认0）
        'StructureType'=>0,//消息结构类型，0为普通文本消息（默认）、1为XML消息、2为JSON消息
        'Group'=>'',//操作或发送的群号或者讨论组号
        'QQ'=>'',//操作或者发送的QQ
        'Msg'=>'',//文本消息【标签等】，当发送类型为JSON、XML时为相应文本，禁言时为分钟数【分钟】
        'Send'=>0,//是否开启同步发送（1为开启同步发送【测试】，0为不开启【默认】）
        'Data'=>''//附加数据，用于特定操作等（文本型
    ];

    /**
     * IRSDK constructor.选择传入参数
     * @param null $URL 推送地址及端口
     * @param null $serverKey 验证key
     * @param null $serverSecret 验证secret
     * @param null $serverVerfy 是否开启验证
     */
    public function __construct($URL=null,$serverKey=null,$serverSecret=null,$serverVerfy=null)
    {
        IRSDK::$serverURL=$URL==null?IRSDK::$serverURL:$URL;
        IRSDK::$serverVerfy=$serverVerfy==null?IRSDK::$serverVerfy:$serverVerfy;
        IRSDK::$serverKey=$serverKey==null?IRSDK::$serverKey:$serverKey;
        IRSDK::$serverSecret=$serverSecret==null?IRSDK::$serverSecret:$serverSecret;
    }
    /**
     * 与主机动态交互：发送消息与获得结果
     * @param $url
     * @return mixed 返回json数据
     */
    private function sendData()
    {
        $url=HTTPSDKPush::$serverURL;
        $send_arr=$this->getServerVerifyCode()+$this->returnData;//加入加密信息。"+",若两个数组存在相同的key,前一会覆盖后一
        $this->returnData=['data'=>[]];//发送之后清空数据，以免影响下一次发送**
        $json_data=json_encode($send_arr);
        $result_url = $url;
        $conn = curl_init($result_url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);//参数1  不显示
        curl_setopt($conn,CURLOPT_POST,1);
        curl_setopt($conn,CURLOPT_POSTFIELDS,$json_data);
        $get = curl_exec($conn);
        return $get;
    }
    /**
     * 返回提交的验证：包含字段：time（时间戳），verify（加密数据）
     * @return mixed
     */
    private function getServerVerifyCode(){
        //加密验证方式
        $verify=[];
        if(IRSDK::$serverVerfy){
            $verify['time']=time();
            $verify['verify']=md5(IRSDK::$serverKey.$verify['time'].IRSDK::$serverSecret);
        }
        return $verify;
    }
    /**
     * 添加功能，原始方法：为了保持一致，此处参数使用大驼峰命名。和提交不同的是这里只允许提交一个消息：
     * @param $Type
     * @param int $SubType
     * @param int $StructureType
     * @param string $Group
     * @param string $QQ
     * @param string $Msg
     * @param string $Data
     * @param int $Send
     */
    private function addDataCell($Type,$SubType=0,$StructureType=0,$Group='',$QQ='',$Msg='',$Data='',$Send=0){
        $data=$this->returnDataCell;
        $data['Type']=$Type;
        $data['SubType']=$SubType;
        $data['StructureType']=$StructureType;
        $data['Group']=$Group;
        $data['QQ']=$QQ;
        $data['Msg']=$Msg;
        $data['Data']=$Data;
        $data['Send']=$Send;
        array_push($this->returnData['data'],$data);
        return $this;
    }


    /**********************************接下来是具体功能*************************************/
    /******************消息发送*************************/
    /**
     * 发送私聊消息
     * @param $QQ
     * @param $Msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType  XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return array json
     */
    public function sendPrivateMsg($qq,$msg,$structureType=0,$subType=0){
        $this->addDataCell(HTTPSDKPush::$TYPE_FRIEND,$subType,$structureType,'',$qq,$msg,'',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 发送群消息
     * @param $QQ
     * @param $Msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType  XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return $this
     */
    public function sendGroupMsg($group,$msg,$structureType=0,$subType=0){
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP,$subType,$structureType,$group,'',$msg,'',0);
        $this->sendData();
        return json_decode($this->sendData(),true);
    }
    /**
     * 发送讨论组消息
     * @param $QQ
     * @param $Msg
     * @param int $structureType 消息结构类型 0普通消息，1 XML消息，2 JSON消息
     * @param int $subType  XML、JSON消息发送方式下：0为普通（默认），1为匿名（需要群开启）
     * @return $this
     */
    public function sendDiscussMsg($discuss,$msg,$structureType=0,$subType=0){
        $this->addDataCell(HTTPSDKPush::$TYPE_DISCUSS,$subType,$structureType,$discuss,'',$msg,'',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 向QQ点赞
     * @param $QQ
     * @param $count int 默认为1，作为消息的 Msg项
     * @return $this
     */
    public function sendLike($qq,$count=1){
        $this->addDataCell(HTTPSDKPush::$TYPE_SEND_LIKE,0,0,'',$qq,$count,'',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 窗口抖动
     * @param $qq
     * @return HTTPSDK
     */
    public function sendShake($qq){
        $this->addDataCell(HTTPSDKPush::$TYPE_SEND_SHAKE,0,0,'',$qq,'','',0);
        return json_decode($this->sendData(),true);
    }

    /******************群操作、事件处理*************************/
    /**
     * 群禁言（管理）
     * @param $group string 群号
     * @param $qq string 禁言QQ，为空则禁言全群
     * @param $time int 禁言时间，单位秒，至少10秒。0为解除禁言
     * @return HTTPSDK
     */
    public function setGroupBan($group,$qq='',$time=10){
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_BAN,0,0,$group,$qq,$time,'',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 主动退群
     * @param $group
     * @return HTTPSDK
     */
    public function setGroupQuit($group){
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_QUIT,0,0,$group,'','','',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 踢人（管理）
     * @param $group  string
     * @param $qq string
     * @param bool $neverIn 是否不允许再加群
     * @return HTTPSDK
     */
    public function setGroupKick($group,$qq,$neverIn=false){
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_KICK,0,0,$group,$qq,$neverIn?1:0,'',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 设置群名片
     * @param $group
     * @param $qq
     * @param string $card
     * @return HTTPSDK
     */
    public function setGroupCard($group,$qq,$card=''){
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_SET_CARD,0,0,$group,$qq,$card,'',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 设置管理员（群主）
     * @param $Group
     * @param $QQ
     * @param bool $become true为设置，false为取消
     * @return HTTPSDK
     */
    public function setGroupAdmin($group,$qq,$become=false) {
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_SET_ADMIN,0,0,$group,$qq,$become?1:0,'',0);
        return json_decode($this->sendData(),true);
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
    public function handleGroupIn($group,$qq,$agree=true,$type=0,$msg=''){
       $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_HANDLE_GROUP_IN,$type,0,$group,$qq,$agree?1:0,$msg,0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 是否同意被加好友
     * @param $qq string
     * @param bool $agree 是否同意
     * @param string $msg 附加消息
     * @return HTTPSDK
     */
    public function handleFriendAdd($qq,$agree=true,$msg=''){
        $this->addDataCell(HTTPSDKPush::$TYPE_FRIEND_HANDLE_FRIEND_ADD,0,0,'',$qq,$agree?1:0,$msg,0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 发群公告（管理）
     * @param $group string
     * @param $title string 内容
     * @param $content string 信息
     * @return HTTPSDK
     */
    public function addGroupNotice($group,$title,$content){
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_ADD_NOTICE,0,0,$group,'',$title,$content,0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 发群作业（管理）。注意作业名和标题中不能含有#号
     * @param $group
     * @param $homeworkName  string 作业名
     * @param $title string  标题
     * @param $content string 内容
     * @return HTTPSDK
     */
    public function addGroupHomework($group,$homeworkName,$title,$content){
        $this->addDataCell(HTTPSDKPush::$TYPE_GROUP_ADD_HOMEWORK,0,0,$group,'',$homeworkName.'#'.$title,$content,0);
        return json_decode($this->sendData(),true);
    }

    /*********************** 获取信息 *******************************************/
    /**
     * 获取陌生人信息
     * @param $qq string
     * @return array  返回包含 Age Name Gender Sign
     */
    public function getStrangerInfo($qq){
        $this->addDataCell(HTTPSDKPush::$TYPE_GET_STRANGER_INFO,0,0,'',$qq,'','',0);
        return json_decode($this->sendData(),true);
    }
    /**
     * 获取当前登陆的QQ
     * @return string 返回 QQ号
     */
    public function getLoginQQ(){
        $this->addDataCell(HTTPSDKPush::$TYPE_GET_LOGIN_QQ,0,0,'','','','',0);
        return json_decode($this->sendData(),true);
    }

    /**
     * 获取当前QQ群列表
     * @return array  直接返回群列表数组
     */
    public function getGroupList(){
        $this->addDataCell(HTTPSDKPush::$TYPE_GET_GROUP_LIST,0,0,'','','','',0);
        return json_decode($this->sendData(),true)['Result'];
    }

    /**
     * 获取当前登陆QQ好友列表
     * @return array 好友列表数组
     */
    public function getFriendList(){
        $this->addDataCell(HTTPSDKPush::$TYPE_GET_FRIEND_LIST,0,0,'','','','',0);
        return json_decode($this->sendData(),true)['Result'];
    }

    /**
     * 获取指定群群成员列表
     * @param $groupid
     * @return array
     */
    public function getGroupMemberList($groupid){
        $this->addDataCell(HTTPSDKPush::$TYPE_GET_GROUP_MEMBER_LIST,0,0,$groupid,'','','',0);
        return json_decode($this->sendData(),true)['Result'];
    }

    /**
     * 获取群公告
     * @param $groupid
     * @return array
     */
    public function getGroupNotice($groupid){
        $this->addDataCell(HTTPSDKPush::$TYPE_GET_GROUP_NOTICE,0,0,$groupid,'','','',0);
        return json_decode($this->sendData(),true)['Result'];
    }
}