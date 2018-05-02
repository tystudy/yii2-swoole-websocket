yii2-swoole-websocket
---------------
利用swoole搭建了websocket服务器  不修改yii2直接使用  可以根据demo进行修改满足自己的websocket使用场景
测试使用 php7.2 swoole1.10.3 yii2-advanced（目前只支持advanced）

安装
---------------
1.使用composer
composer的安装以及国内镜像设置请点击[此处](http://www.phpcomposer.com/)

```bash
$ composer require tystudy/yii2-swoole-websocket -vvv
```
or add

```bash
"tystudy/yii2-swoole-websocket": "*"
```

-------------
2.配置yii2

打开console/config/main.php（注意：并不是配置在components里面，而应该在最外层，即与components同级）。

```bash
'id' => 'app-console',
 ...//其他配置
'controllerMap'=>[
     ...//其他配置项
   'websocket' => [
		'class' => 'tystudy\swoole\console\SwooleController',
		'rootDir' => str_replace('console/config', '', __DIR__ ),//yii2项目根路径
		'app' => 'backend',
		'host' => '0.0.0.0',                                    //默认监听所有机器  可以填写127.0.0.1只监听本机 详见swoole文档
		'port' => 9998,
		'web' => 'web',                                         //默认为web rootDir app web目的是拼接yii2的根目录
		'debug' => true,                                        //默认开启debug，上线应置为false
		'env' => 'dev',                                         //默认为dev，上线应置为prod 
		'swooleConfig' => [                                     //swoole 相关配置 详见swoole文档
			'reactor_num' => 2,                                
			'worker_num' => 4,
			'task_worker_num' => 4,
			'daemonize' => false,
			'log_file' => __DIR__ . '/../../backend/runtime/logs/swoole.log',
			'log_level' => 0,
			'pid_file' => __DIR__ . '/../../backend/runtime/server.pid',
		],
		'project'=>[                                           //项目server路径 根据实际情况填写
			'server'=>[
				'tystudy\swoole\project\ProjectWebsocketServer',
			],
		]  
	],
    ...//其他配置
]
 ...//其他配置
```

-------------
3.启动命令
linux 进入项目目录

    * 启动 ./yii websocket/start
    * 关闭 ./yii websocket/stop
    * 重启 ./yii websocket/restart    

-------------
4.project项目demo文件 实现2人聊天的websocket应用
	使用说明：
		1.yii2 控制台中启动server  linux 下使用命令 ./yii websocket/start  
		2.将project文件夹下的chat_server_websocket.php chat_server_websocket2.php 放任意可访问目录，修改websocket server IP
		3.聊天使用mysql存储 

-------------
5.项目扩展应用
	参考project使用方法		
		
-------------
6.说明
  由于swoole的websocket server具有http server的功能，可以利用websocket server来实现对http请求的响应 
   一般不建议这样处理，线上环境通常是apache或nginx比swoole的功能更加完善，建议用来单纯的做websocket服务器
  实现php的长链接和一些推送任务，如果需要，可以使用多个swoole服务器，用nginx实现负载均衡
   