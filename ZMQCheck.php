<?php
/**
 * Created by PhpStorm.
 * User: henrygrech-cini
 * Date: 30/10/2014
 * Time: 22:39
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'ZMQContext class is '.(class_exists('ZMQContext')?'available':'missing').PHP_EOL;
