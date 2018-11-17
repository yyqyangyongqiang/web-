<?php

use Workerman\Worker;
use Firebase\JWT\JWT;
require_once  'Workerman-master/Autoloader.php';
require('../vendor/autoload.php');

$worker = new Worker('webSocket://127.0.0.1:9999');
// 保存当前所有用户的数组
// 格式 用户id 用户名
$user = [];

// 定义数组 里边保存 用户对象
// 格式   用户id 用户对象
$userConn = [];

// 定义保存频道的数组
$room = [];
// 定义变量 用于保存 是否是 频道聊天  有值代表是 没有值代表 不是
$isRoom = '';
$worker->onConnect = function($connection){
    global $worker;
    $worker->onWebSocketConnect = function($connection,$http_header){
        global $user,$worker,$userConn,$isRoom,$room;
        if(!isset($_GET['id'])){
            // 解析JWT数据
            try{
                $key = "yang";
                $data = JWT::decode($_GET['token'],$key,array('HS256'));
                // 把当前用户的信息保存到conection中
                $connection->uid = $data->id;
                $connection->uname = $data->name;
                // 把当前信息保存到 user 数组中
                $user[$data->id] = $data->name;
                // 把当前对象保存到数组中
                $userConn[$data->id] = $connection;

                foreach($worker->connections as $c){
                    $c->send(
                        json_encode([
                            'type'=>'user',
                            'users'=>$user
                        ])
                    );
                }
            }
            catch( \Firebase\JWT\ExpiredException $e){
                $connection->close();
            }
            catch(\Exception $e){
                $connection->close();
            }
            // 把用户的id和名称 保存进user这个数组中
        }
        else{
            // 把当前id保存到isRoom中 表示是频道访问
            $isRoom = $_GET['id'];
            $key = "yang";
            $data = JWT::decode($_GET['token'],$key,array('HS256'));
            
            // 把当前id保存到房间数中去
            $connection->rid = $_GET['id'];
            $room[$_GET['id']][] = $connection;
        }
    };
    
};

$worker->onMessage = function($connection ,$data){
    global $worker,$userConn,$isRoom,$room;
    if($isRoom==''){
        var_dump('私人消息');
        $ret = explode(':',$data);
        $code = $ret[0];
        // 删除掉分号前边的内容
        unset($ret[0]);
        // 把剩下的内容凭借成字符串
        $ret = implode(":",$ret);
        if($code=='all'){
            // 群发
            foreach($worker->connections as $c){
                $c->send(json_encode([
                    'type'=>'message',
                    'message'=>$ret
                ]));
            };
        }else{
            // 私发
            $connection->send(json_encode([
                'type'=>'message',
                'message'=>$ret
            ]));
            $userConn[$code]->send(json_encode([
                'type'=>'message',
                'message'=>$ret
            ]));
        }
    }else{
        var_dump('频道消息');
        foreach($room[$connection->rid] as $c){
            $c->send(json_encode([
                'type'=>'message',
                'message'=>$data
            ]));
        }
    }
    
};

$worker->onClose = function($connection){
    global $user,$userConn,$worker;
    unset($user[$connection->uid]);
    unset($userConn[$connection->uid]);
    foreach($worker->connections as $c){
        $c->send(json_encode([
            'type'=>'user',
            'user'=>$user
        ]));
    }
};

Worker::runAll();