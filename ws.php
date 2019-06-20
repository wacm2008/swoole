<?php
$server = new swoole_websocket_server("0.0.0.0", 9502);

$server->on('open', function($server, $req) {
    echo "connection open: {$req->fd}\n";
});

$server->on('message', function($server, $frame) {
    print_r($frame);
    echo "received message: {$frame->data}\n";
    //$server->push($frame->fd, json_encode(["Hola", "todos"]));
    //aaaaa

    foreach ($server->connections as $k){
        var_dump($k);
        //检查是否有效的websocket客户端连接
        if($server->isEstablished($k)){
            $dato = json_decode($frame->data,true);
            print_r($dato);
            $data = [
                'text' => $dato['text'],
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