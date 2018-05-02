<?php
namespace tystudy\swoole\console;

use yii;

use yii\base\ExitException;
use yii\helpers\ArrayHelper;
use tystudy\swoole\server\WebsocketServer;
//use tystudy\swoole\project\ProjectWebsocketServer;
use yii\helpers\FileHelper;
use yii\web\Application;
use yii\web\UploadedFile;

class SwooleController extends \yii\console\Controller
{

    public $host = "0.0.0.0";

    public $port = 9999;

    public $mode = SWOOLE_PROCESS;

    public $socketType = SWOOLE_TCP;

    public $rootDir = "";

    public $type = "advanced";

    public $app = "frontend";//如果type为basic,这里默认为空

    public $web = "web";

    public $debug = true;//是否开启debug

    public $env = 'dev';//环境，dev或者prod...

    public $swooleConfig = [];

    public $gcSessionInterval = 60000;//启动session回收的间隔时间，单位为毫秒

    public $project="";

    public function actionStart()
    {
        if( $this->getPid() !== false ){
            $this->stderr("server already  started");
            exit(1);
        }
        $projectServer=$this->project['server'][0];
        if($projectServer && class_exists($projectServer)){
            $server = new $projectServer($this->host, $this->port, $this->mode, $this->socketType, $this->swooleConfig, ['gcSessionInterval'=>$this->gcSessionInterval]);
        }else{
            throw new yii\base\Exception('找不到'.$projectServer.'请检查路径是否正确');
        }
        $this->stdout("server is running, listening {$this->host}:{$this->port}" . PHP_EOL);
        $server->run();
    }

    public function actionStop()
    {
        $this->sendSignal(SIGTERM);
        $this->stdout("server is stopped, stop listening {$this->host}:{$this->port}" . PHP_EOL);
    }

    public function actionReloadTask()
    {
        $this->sendSignal(SIGUSR2);
    }

    public function actionRestart()
    {
        $this->sendSignal(SIGTERM);
        $time = 0;
        while (posix_getpgid($this->getPid()) && $time <= 10) {
            usleep(100000);
            $time++;
        }
        if ($time > 100) {
            $this->stderr("Server stopped timeout" . PHP_EOL);
            exit(1);
        }
        if( $this->getPid() === false ){
            $this->stdout("Server is stopped success" . PHP_EOL);
        }else{
            $this->stderr("Server stopped error, please handle kill process" . PHP_EOL);
        }
        $this->actionStart();
    }

//    public function actionReload()
//    {
//        $this->actionRestart();
//    }

    private function sendSignal($sig)
    {
        if ($pid = $this->getPid()) {
            posix_kill($pid, $sig);
        } else {
            $this->stdout("server is not running!" . PHP_EOL);
            exit(1);
        }
    }


    private function getPid()
    {
        $pid_file = $this->swooleConfig['pid_file'];
        if (file_exists($pid_file)) {
            $pid = file_get_contents($pid_file);
            if (posix_getpgid($pid)) {
                return $pid;
            } else {
                unlink($pid_file);
            }
        }
        return false;
    }

}