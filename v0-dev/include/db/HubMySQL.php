<?php
$loop_mysql = React\EventLoop\Factory::create();
$connection = new React\MySQL\Connection($loop_mysql, array(
    'dbname' => 'tophub',
    'user'   => 'tophub',
    'passwd' => 'tophub'
));
?>
