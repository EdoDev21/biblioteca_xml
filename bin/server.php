<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\BibliotecaSocket;

require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new BibliotecaSocket()
        )
    ),
    8080
);

echo "Servidor Socket iniciado en el puerto 8080...\n";
$server->run();