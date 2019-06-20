<?php
$http = new swoole_http_server("0.0.0.0", 9501);//0.0.0.0不只是从本地访问

$http->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$http->on("request", function ($request, $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hola Bruno\n");
});

$http->start();