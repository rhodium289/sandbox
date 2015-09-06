<?php
/**
 * Created by PhpStorm.
 * User: henrygrech-cini
 * Date: 30/10/2014
 * Time: 22:46
 */

$ctx=new ZMQContext();
$server=new ZMQSocket($ctx, ZMQ::SOCKET_REP);
$server->bind('tcp://*:5454');
while(true) {
    $message = $server->recv();
    $server->send($message.' World');
}