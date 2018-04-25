yii2-swoole-websocket
=====================
利用swoole搭建了websocket服务器  不修改yii2直接使用  可以根据demo进行修改满足自己的websocket使用场景
测试使用 php7.2 swoole1.10.3 yii2-advance（目前只支持advance）
安装
---------------
1. 使用composer
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
                    'daemonize' => false,
                    'log_file' => __DIR__ . '/../../backend/runtime/logs/swoole.log',
                    'log_level' => 0,
                    'pid_file' => __DIR__ . '/../../backend/runtime/server.pid',
                ],
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
    
   