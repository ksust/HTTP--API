# HTTP--API 发布版
IRQQ HTTP接口（可对接qqv2系统），CQ 酷Q HTTP接口（扩展兼容qqv2系统）


HTTP-API FOR IRQQ 日志
1.0.0
	完成了基本功能
1.0.1
	增加了动态交互发送XML和JSON选项
1.0.2
	增加若干交互功能（见PHP SDK）
	处理提交数据时换行问题
	初步解决lite线程定时任务可能启动失败问题
1.0.3
	增加财付通转账返回Msg为JSON数组信息（此时增加成员为[money,message,number]）
1.0.4
	为方便发送常用卡片，增加标签 （同IRQQ标签，直接使用下列规范标签即可发送相关卡片）
		提示：标签中不能出现空格、回车
		[ksust,music:name=歌曲名]多选音乐卡片，传入歌名
		[ksust,link:url=链接网址,title=标题文字,content=内容文字,pic=图片链接] 简单图文连接 
		[ksust,link2:url=链接网址,title=标题文字,content=内容文字,pic=图片链接,bcontent=内容2文字,bpic=大图链接] 简单图文，加大图和长文本
1.0.5
	修复定时发送卡片解析问题和部分乱码问题
1.0.6
	修复卡片发送标签中的=问题
	
1.1.0
	增加队列命令，可通过队列指令控制框架向相应接口发送信息，解决框架挂在无固定IP或者端口未转发的计算机上不能获取如群列表等实时信息。
	增加队列命令：updateGroupList，基于队列命令的群信息更新，到qqv2系统数据库
	增加队列命令：updateFriendList，基于队列命令的好友信息更新，到qqv2系统数据库
1.1.1
	修复队列命令群列表自动更新失败问题
	增加队列命令：updateOneGroupMemberList，基于队列命令的群成员信息更新，到qqv2系统数据库
	
1.2.0
	设置界面优化
	*插件改名为 HTTP-API
	SDK增加点赞次数参数
	SDK优化
	发布在github上并逐渐完善文档
	
	
	

	
	
