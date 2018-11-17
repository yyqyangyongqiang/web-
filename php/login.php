<?php
require('../vendor/autoload.php');

use Firebase\JWT\JWT;

$pdo = new \PDO('mysql:host=127.0.0.1;dbname=chat', 'root', '123456');
$pdo->exec('SET NAMES utf8');

// 接收原始的数据
$post = file_get_contents('php://input');

$_POST = json_decode($post,TRUE);

$stmt = $pdo->prepare("select * from user where name = ? and password = ?");
$stmt->execute([
    $_POST['username'],
    md5($_POST['password'])
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($user){
    $key = 'yang';
    $now = time();
    $data = [
        'id'=>$user['id'],
        'name'=>$user['name']
    ];
    // 为这个数据生成jwt加密的数据
    $jwt = JWT::encode($data,$key);
    // 返回json数据
    echo json_encode(
        [
        'code'=>'200',
        'jwt'=>$jwt
        ]
    );

}else{
    echo json_encode(
        [
        'code'=>'403',
        'error'=>'用户名或者密码错误'
        ]
    );
}