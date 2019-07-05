<?php
$server = new swoole_websocket_server("0.0.0.0", 9502);
$num = 0;
$server->on('open', function($server, $req) use(&$num) {
    echo "connection open: {$req->fd}\n";
    //print_r($server);
    //print_r($req);
    $num++;
    echo $num;echo '</br>';
});

$server->on('message', function($server, $frame) {
    print_r($frame);
    echo "received message: {$frame->data}\n";
    //$server->push($frame->fd, json_encode(["Hola", "todos"]));
    $dato = json_decode($frame->data,true);
    $name = $dato['name'];
    $content = $dato['content'];
    $time = date('Y-m-d H:i:s',time());
    $store_id = mt_rand(1,20);
    $user = 'root';
    $pwd = '123456abc';
    $pdo = new PDO('mysql:host=192.168.254.131;dbname=slowlog',$user,$pwd);
    //$con = new mysqli('192.168.254.201',$user,$pwd,'charla');
    $sql = "insert into charla(name,content,add_time,store_id) values('$name','$content','$time','$store_id')";
    //$sql = "insert into charla(name,content,add_time,store_id) values ('$name','$content','$time','$store_id')";
    $pdo->query($sql);
    //mysqli_query($con,"set names 'utf8'");
    //防乱码
    //$pdo->query('set names utf-8');
    //错误处理
    $errorCode = $pdo->errorCode();
    $errorInfo = $pdo->errorInfo();
    if($errorCode !=='00000'){
        echo $errorCode.'<br>';
        echo $errorInfo[2];
        exit();
    }

    foreach ($server->connections as $k){
        var_dump($k);
        //检查是否有效的websocket客户端连接
        if($server->isEstablished($k)){
            $dato = json_decode($frame->data,true);
            print_r($dato);
            $data = [
                'content' => $dato['content'],
                'name' => $dato['name'],
                'date' => date('Y-m-d H:i:s',time())
            ];
            $server->push($k,json_encode($data,JSON_UNESCAPED_UNICODE));
        }
    }
});

$server->on('close', function($server, $fd) {
    echo "connection close: {$fd}\n";
});

$server->start();
