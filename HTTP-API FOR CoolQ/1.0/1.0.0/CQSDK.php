<?php
/**
 * Created by PhpStorm.
 * User: yug
 * Version 1.0.3
 * Date: 2017/8/9
 * Time: 22:16
 * CQ php sdk   HTTP-API
 * 可用标签形式发送的功能（方法中未列出）
 * ThinkPHP vender引用：
 * CoolQ PHP SDK 兼容版
 */


class CQSDK{
    //全局配置
    public static $serverURL='http://127.0.0.1:8090';
    public static $serverVerfy=true; //是否启用验证
    public static $serverKey='123';
    public static $serverSecret='456';

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
     * @param $send_arr
     * @return mixed 返回json数据
     */
    public function sendData($url,$send_arr=[])
    {
        $send_arr=$this->getServerVerifyCode()+$send_arr;//加入加密信息。"+",若两个数组存在相同的key,前一会覆盖后一
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

    //接下来是功能函数:__FUNCTION__获取当前方法名
/*********************** 获取信息 *******************************************/
    /**
     * 获取陌生人信息
     * @param $QQ
     * @return mixed  返回包含 Old Name Gender
     */
    public function getStrangerInfo($QQ){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['QQ']=$QQ;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }
    /**
     * @param $QQ
     * @return mixed
     */
    public function getLoginQQ(){
        $sendArr['Func']=__FUNCTION__;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * @return mixed  直接返回群列表数组
     */
    public function getGroupList(){
        $sendArr['Func']=__FUNCTION__;
        return json_decode(json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true)['Result'],true);
    }

    /**
     * @return mixed
     */
    public function getFriendList(){
        $sendArr['Func']=__FUNCTION__;
        return json_decode(json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true)['Result'],true);
    }

    /**
     * @param $groupid
     * @return mixed
     */
    public function getGroupMemberList($groupid){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$groupid;
        return json_decode(json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true)['Result'],true);
    }

    /**
     * 获取群公告
     * @param $groupid
     * @return mixed
     */
    public function getGroupNotice($groupid){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$groupid;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }



    /*********************** 发送消息 *******************************************/
    /**
     * 发送私聊消息
     * @param $QQ
     * @param $Msg
     * @param int $type 消息类型 1普通消息，2 XML消息，3 JSON消息
     * @param int $subtype  type=1：气泡ID，默认-1。type=2：xml消息子类型 ，0为基本（默认），2为点歌。type=3：JSON消息发送方式，1为普通（默认），2为匿名
     * @return mixed
     */
    public function sendPrivateMsg($QQ,$Msg,$type=1,$subtype=-1){
        if($type==2&&$subtype==-1) $subtype=0;//防错
        if($type==3&&$subtype==-1) $subtype=1;
        $sendArr['Func']=__FUNCTION__;
        $sendArr['QQ']=$QQ;
        $sendArr['Msg']=$Msg;
        $sendArr['Type']=$type;
        $sendArr['SubType']=$subtype;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 发送群消息
     * @param $Group
     * @param $Msg
     * @param int $type 消息类型 1普通消息，2 XML消息，3 JSON消息
     * @param int $subtype type=1：气泡ID，默认-1。type=2：xml消息子类型 ，0为基本（默认），2为点歌。type=3：JSON消息发送方式，1为普通（默认），2为匿名
     * @return mixed
     */
    public function sendGroupMsg($Group,$Msg,$type=1,$subtype=-1){
        if($type==2&&$subtype==-1) $subtype=0;//防错
        if($type==3&&$subtype==-1) $subtype=1;
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Group;
        $sendArr['Msg']=$Msg;
        $sendArr['Type']=$type;
        $sendArr['SubType']=$subtype;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * @param $Discuss
     * @param $Msg
     * @param int $type 消息类型 1普通消息，2 XML消息，3 JSON消息
     * @param int $subtype type=1：气泡ID，默认-1。type=2：xml消息子类型 ，0为基本（默认），2为点歌。type=3：JSON消息发送方式，1为普通（默认），2为匿名
     * @return mixed
     */
    public function sendDiscussMsg($Discuss,$Msg,$type=1,$subtype=-1){
        if($type==2&&$subtype==-1) $subtype=0;//防错
        if($type==3&&$subtype==-1) $subtype=1;
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Discuss;
        $sendArr['Msg']=$Msg;
        $sendArr['Type']=$type;
        $sendArr['SubType']=$subtype;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 发送窗口抖动
     * @param $QQ
     * @return mixed 失败返回-1
     */
    public function sendShake($QQ){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['QQ']=$QQ;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 向QQ点赞
     * @param $QQ
     * @return mixed
     */
    public function sendLike($QQ,$count=1){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['QQ']=$QQ;
        $sendArr['Msg']=$count>10?10:$count;//原则上一天点赞次数不超过10并限制频率
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }


    /*********************** 事件处理 *******************************************/

    /***************** 群事件处理 *******************/

    /**
     * 退群
     * @param $Group
     * @return mixed 失败返回-1
     */
    public function setGroupQuit($Group){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Group;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 申请加群
     * @param $Group
     * @param string $Msg
     * @return mixed 失败返回-1
     */
    public function setGroupJoin($Group,$Msg='加群理由'){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Group;
        $sendArr['Msg']=$Msg;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }
    /**
     * @param $Group
     * @param null $QQ 为空时表示全群禁言
     * @param int $time 为0时表示解除禁言 单位秒，至少10秒
     * @return mixed 失败返回-1
     */
    public function setGroupBan($Group,$QQ=null,$time=10){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Group;
        $sendArr['Msg']=$time;
        $sendArr['QQ']=$QQ;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 设置踢人
     * @param $Group
     * @param $QQ
     * @param bool $neverIn 为真时不允许申请加入
     * @return mixed 失败返回-1
     */
    public function setGroupKick($Group,$QQ,$neverIn=false){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Group;
        $sendArr['Msg']=$neverIn==true?1:0;
        $sendArr['QQ']=$QQ;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 设置群名片
     * @param $Group
     * @param $QQ
     * @param null $card
     * @return mixed 失败返回-1
     */
    public function setGroupCard($Group,$QQ,$card=null){
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Group;
        $sendArr['Msg']=$card;
        $sendArr['QQ']=$QQ;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 设置群管理员
     * @param $Group
     * @param $QQ
     * @param bool $become true设置，false 取消
     * @return mixed 失败返回-1
     */
    public function setGroupAdmin($Group,$QQ,$become=false) {
        $sendArr['Func']=__FUNCTION__;
        $sendArr['Group']=$Group;
        $sendArr['Msg']=$become?1:0;
        $sendArr['QQ']=$QQ;
        return json_decode($this->sendData(IRSDK::$serverURL,$sendArr),true);
    }

    /**
     * 入群请求处理
     * @param $Group
     * @param $QQ
     * @param $inType
     * @param bool $agree
     * @param null $Msg 拒绝时的理由
     * @return mixed 失败返回-1
     */
    public function setGroupRequest($Group,$QQ,$inType,$agree=true,$Msg=null){
        $sendArr['Func'] = __FUNCTION__;
        $sendArr['Group'] = $Group;
        $sendArr['Msg'] = $Msg;
        $sendArr['QQ'] = $QQ;
        $sendArr['Type'] = $inType == 1 ? 213 : 214;//1 为请求入群，2为被邀请入群
        $sendArr['SubType'] = $agree ? 10 : 20;//同意为true
        return json_decode($this->sendData(IRSDK::$serverURL, $sendArr), true);
    }

    /**
     * 发群公告
     * @param $Group
     * @param $title
     * @param $content
     * @return mixed 失败返回-1
     */
    public function setGroupNotice($Group,$title,$content){
        $sendArr['Func'] = __FUNCTION__;
        $sendArr['Group'] = $Group;
        $sendArr['Msg'] = $title;
        $sendArr['Msg2'] = $content;
        return json_decode($this->sendData(IRSDK::$serverURL, $sendArr), true);
    }

    /**
     * 发群作业
     * @param $Group
     * @param $homeworkName
     * @param $title
     * @param $content
     * @return mixed 失败返回-1
     */
    public function setGroupHomework($Group,$homeworkName,$title,$content){
        $sendArr['Func'] = __FUNCTION__;
        $sendArr['Group'] = $Group;
        $sendArr['Msg'] = $homeworkName;
        $sendArr['Msg2'] = $title;
        $sendArr['Msg3'] = $content;
        return json_decode($this->sendData(IRSDK::$serverURL, $sendArr), true);
    }
}


