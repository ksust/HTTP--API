//插件推送端口为8888，且与本demo同主机
include "HTTPSDKPush.php";
$sdk=new HTTPSDKPush('http://127.0.0.1:8888');
$sdk->sendPrivateMsg('QQ号','一个XML标签：[ksust,link:title=标题,content=内容]');//向QQ发送消息，消息中包含XML标签的简化
var_dump($sdk->getGroupList());//获取群列表