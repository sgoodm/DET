<?php

$app = basename(dirname(__DIR__));
$DOMAIN = "128.238.119.254";
$COM_DIR = dirname(__DIR__);
$MAIL_DIR = $DOMAIN . substr($COM_DIR, 13, strpos($COM_DIR, $app)-1);
var_dump($MAIL_DIR);
?>