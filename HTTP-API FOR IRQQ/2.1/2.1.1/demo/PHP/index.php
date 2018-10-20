include 'lib/HTTPSDK.php';
$sdk=new HTTPSDK();//获取操作对象并解析HTTP-API提交的消息
$msg=$sdk->getMsg();//获取传来的消息（array），详细请看数据结构
if($msg['Type']==HTTPSDK::$TYPE_FRIEND) {
    //私聊消息回复
    $sdk->sendPrivateMsg($msg['QQ'], '你发送了这样的消息:' . $msg['Msg']);//发送私聊消息
}
//解析群作业
$msg=$msg['Msg'];
$prefix="<\?xml version='1.0' encoding='UTF-8' standalone='yes'\?>.*http:\/\/qun.qq.com\/homework\/p\/features\/index.html";
preg_match("/".$prefix.".*<title>(.*)<\/title><summary>(.*)<\/summary>.*/",$msg,$m);
if (count($m)>=3){
	//作业
	$sdk->sendPrivateMsg('1402549575','标题：'.$m[1]."\n内容:".$m[2]);
}
echo $sdk->returnJsonString();//最终返回，一定要输出