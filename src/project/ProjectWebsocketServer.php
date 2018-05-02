<?php
namespace tystudy\swoole\project;

/*
 * 具体项目websocket server 根据业务要求重写websocket的相关方法
 * 注意不能 如果在子类中使用__construct前要parent::__construct
 * 
 */
class ProjectWebsocketServer extends \tystudy\swoole\server\WebsocketServer
{
    public $conn;
    /*
     * 监听ws连接事件 继承必须实现
     * function onOpen(swoole_websocket_server $svr, swoole_http_request $req);
     * $req 是一个Http请求对象，包含了客户端发来的握手请求信息
     * onOpen事件函数中可以调用push向客户端发送数据或者调用close关闭连接
     * onOpen事件回调是可选的
     */
    public function onOpen($swoole,$request){
        $this->initDb();
        echo "request->fd:{$request->fd}\n";
    }
    
    /*
     * 监听ws消息事件 继承必须实现
     * function onMessage(swoole_server $server, swoole_websocket_frame $frame)
     * $frame 是swoole_websocket_frame对象，包含了客户端发来的数据帧信息
     * 共有4个属性，分别是
     *   $frame->fd，客户端的socket id，使用$server->push推送数据时需要用到
     *   $frame->data，数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断
     *   $frame->opcode，WebSocket的OpCode类型，可以参考WebSocket协议标准文档
     *   $frame->finish， 表示数据帧是否完整，一个WebSocket请求可能会分成多个数据帧进行发送
     */
    public function onMessage($swoole,$frame){
        $pData = json_decode($frame->data);
        $data = array();
        if (isset($pData->content)) {
            $data = $this->add($pData->fid, $pData->tid, $pData->content); //保存消息
            $tfd = $this->getFd($pData->tid); //获取绑定的fd
            if($tfd){
                $swoole->push($tfd, json_encode($data)); //推送到接收者
            }
        } else {
            $this->unBind(null,$pData->fid); //首次接入，清除绑定数据 解除发送者和fd的绑定关系
            if ($this->bind($pData->fid, $frame->fd)) {  //绑定fd
                $data = $this->loadHistory($pData->fid, $pData->tid); //加载历史记录
            } else {
                $data = array("content" => "无法绑定fd");
            }
        }
        $swoole->push($frame->fd, json_encode($data)); //推送到发送者
    }
    
    /*
     * 监听ws关闭事件 继承必须实现
     * TCP客户端连接关闭后，在worker进程中回调此函数。函数原型：
     *   function onClose(swoole_server $server, int $fd, int $reactorId);
     *   $server 是swoole_server对象
     *   $fd 是连接的文件描述符
     *   $reactorId 来自那个reactor线程
     */
    public function onClose($swoole,$fd){
        $this->unBind($fd);
        echo "connection close: " . $fd;				
    }
 
    
    /************************具体业务实现流程**************************************/
    public function initDb(){
        $conn = new \yii\db\Connection([
            'dsn' => 'mysql:host=localhost;dbname=chat',
            'username' => 'root',
            'password' => 'root',
        ]);
        //$conn->open();
        $this->conn=$conn;
    }
    public function add($fid, $tid, $content)
    {
        $sql = "insert into msg (fid,tid,content) values ($fid,$tid,'$content')";
        if ($this->conn->createCommand($sql)->execute()) {
            $id = $this->conn->getLastInsertId();
            $data = $this->loadHistory($fid, $tid, $id);
            return $data;
        }
    }

    public function bind($uid, $fd)
    {
        $sql = "insert into fd (uid,fd) values ($uid,$fd)";
        if ($this->conn->createCommand($sql)->execute()) {
            return true;
        }
    }

    public function getFd($uid)
    {
        $sql = "select * from fd where uid=$uid limit 1";
        $row = "";
        if ($query = $this->conn->createCommand($sql)->queryOne()) {
            $data=$query;
            $row = $data['fd'];
        }
        return $row;
    }

    public function unBind($fd, $uid = null)
    {
        if ($uid) {
            $sql = "delete from fd where uid=$uid";
        } else {
            $sql = "delete from fd where fd=$fd";
        }
        if ($this->conn->createCommand($sql)->execute()) {
            return true;
        }
    }

    public function loadHistory($fid, $tid, $id = null)
    {
        $and = $id ? " and id=$id" : '';
        $sql = "select * from msg where ((fid=$fid and tid = $tid) or (tid=$fid and fid = $tid))" . $and;
        $data = [];
        if ($query = $this->conn->createCommand($sql)->queryAll()) {
            $data=$query;
        }
        return $data;
    }
    
    

}