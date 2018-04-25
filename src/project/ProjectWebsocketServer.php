<?php
namespace tystudy\swoole\project;

/*
 * 具体项目websocket server 根据业务要求重写websocket的相关方法
 */
class ProjectWebsocketServer extends \tystudy\swoole\server\WebsocketServer
{
    
    /*
     * websocket连接事件
     */
    public function onOpen($swoole,$request){
        echo "child extedns request->fd:{$request->fd}\n";
    }
    /*
     * websocket接收到消息
     */
    public function onMessage($swoole,$frame){
        echo "frame->fd:{$frame->fd}\n";
        echo "server-push-message:{$frame->data}\n";    
        //将消息发送给客户端
        $swoole->push($frame->fd,"server-push:".date("Y-m-d H:i:s"));
    }
    /*
     * 客户端关闭链接
     */
    public function onClose($swoole,$fd){
//            \app\common\lib\redis\Predis::getInstance()->sRem(config('redis.live_game_key'),$fd);
        echo "clientid:{$fd}\n";					
    }


}