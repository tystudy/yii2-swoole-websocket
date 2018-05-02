<?php
namespace tystudy\swoole\server;
use tystudy\swoole\web\Session;
use swoole_websocket_server;
/*
 * 单独的websocket服务器 
 */
abstract class WebsocketServer
{
    public $swoole;
    public $webRoot;
    public $config = ['gcSessionInterval' => 60000];
    public $runApp;

    public function __construct($host, $port, $mode, $socketType, $swooleConfig=[], $config=[])
    {
        $this->swoole = new swoole_websocket_server($host, $port, $mode, $socketType);
        if( !empty($this->config) ) $this->config = array_merge($this->config, $config);
        $this->swoole->set($swooleConfig);
        
        //task事件
        $this->swoole->on("task",[$this,'onTask']); 
        $this->swoole->on("finish",[$this,'onFinish']); 
        //worker
        $this->swoole->on('WorkerStart', [$this, 'onWorkerStart']);
        //websocket
        $this->swoole->on("open",[$this,'onOpen']); 
        $this->swoole->on("message",[$this,'onMessage']); 
        $this->swoole->on("close",[$this,'onClose']);	
    }

    /*
     * 监听task事件
     */
    public function onTask($serv,$taskId,$workderId,$data){

    }
    /*
     * 监听task关闭事件  
     */
    public function onFinish($serv,$taskId,$data){
    }
    /*
     * 一分钟清理一次session
     */
    public function onWorkerStart( $serv , $worker_id) {
        if( $worker_id == 0 ) {
            swoole_timer_tick($this->config['gcSessionInterval'], function(){
                (new Session())->gcSession();
            });
        }
    }
    /*
     * ws事件 继承根据业务重写
     */
    abstract protected function onOpen($swoole,$request);
    abstract protected function onMessage($swoole,$frame);
    abstract protected function onClose($swoole,$fd);
    
    /*
     * 启动server
     */
    public function run(){
        $this->swoole->start();
    }
    
    
}