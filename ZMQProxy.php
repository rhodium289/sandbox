<?php
/**
 * Created by PhpStorm.
 * User: henrygrech-cini
 * Date: 30/10/2014
 * Time: 22:48
 */

// A very simple HTTP to ZMQ Proxy
// This could be setup as an asynchronous proxy
// but currently we wait for the response.

// The socket can also be invoked externally
// port 5454 has been opened on the AWS EC2 instance

// code to show the code
echo 'Code shown here to illustrate'.PHP_EOL;
echo show_source(__FILE__);

// the actual code

// create the ZMQ Context and Socket
$ctx=new ZMQContext();
$req=new ZMQSocket($ctx, ZMQ::SOCKET_REQ);

// connect to the ZMQ
$req->connect('tcp://localhost:5454');

// prepare the message to send to ZMQ
$message=isset($_GET['msg'])?$_GET['msg']:'Default Hello';

// send the prepared message
$req->send($message);

// output the response
echo PHP_EOL.'[  '.$req->recv().'  ]'.PHP_EOL;

// Actual Response SHOW BELOW //
