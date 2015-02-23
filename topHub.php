#!/usr/bin/env php
<?php

/**
 ,ggggggggggggggg                   ,ggg,        gg
dP""""""88"""""""                  dP""Y8b       88              ,dPYb,
Yb,_    88                         Yb, `88       88              IP'`Yb
 `""    88                          `"  88       88              I8  8I
        88                              88aaaaaaa88              I8  8'
        88   ,ggggg,   gg,gggg,         88"""""""88  gg      gg  I8 dP
        88  dP"  "Y8gggI8P"  "Yb        88       88  I8      8I  I8dP   88gg
  gg,   88 i8'    ,8I  I8'    ,8i       88       88  I8,    ,8I  I8P    8I
   "Yb,,8P,d8,   ,d8' ,I8 _  ,d8'       88       Y8,,d8b,  ,d8b,,d8b,  ,8I
     "Y8P'P"Y8888P"   PI8 YY88888P      88       `Y88P'"Y88P"`Y88P'"Y88P"'
                       I8
                       I8
                       I8
                       I8
                       I8
                       I8        Top of the Hub
				 End point @ Hub.Hyperboria -wfleurant
*/

require 'vendor/autoload.php';
require 'include/HubEventLog.php';
require 'include/HubFunctions.php';
require 'include/HubMessaging.php';
require 'include/db/HubMongo.php';
require 'include/HubConfig.php';

$loop   = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http   = new React\Http\Server($socket);

$tophub_Obj = function ($request, $tophub_Response) use (&$tophub_sums, &$tophub_meta, $http, $post_sanity)
{

    $tophub_sums++;
    $reqbody = '';

    $tophub_Response->writeHead(200, $tophub_meta['headers']);

    $request->on('data',function($data) use ($request, $tophub_Response, &$reqbody,
                                            &$recdata, $tophub_sums, $tophub_meta, $post_sanity)
    {
        yell('string', 'Recieved Request((1))');
        // print_r($data);

        $tophub_method  = $request->getMethod();    /* POST, GET */
        $tophub_query   = $request->getQuery();     /* POST, GET */
        $tophub_path    = $request->getPath();      /* /v0/node.json */
        $tophub_clihead = $request->getHeaders();   /* array */
        $tophub_clileng = (isset($tophub_clihead['Content-Length']))
                            ? (int)$tophub_clihead['Content-Length']
                            : false;

        if ($post_sanity($tophub_method, $tophub_clileng, $tophub_clihead) === false) {
            $tophub_Response->end(); // bail..
            return;
        }

        if ($tophub_method == 'GET') {
            yell('string', "*!* TophubMethod Logic: $tophub_method");

            if ($tophub_query) {
                /*
                | Method | Endpoint             | Args      | Description           |
                | ------ | -------------------- | --------- | --------------------- |
                | GET    | /v0/node/info.json   | ip=[addr] | Get basic information |
                | GET    | /v0/node/peers.json  | ip=[addr] | Get node peers        |
                */

                include 'include/db/HubMySQL.php';
                yell('string', "*!* GET with DATA"); yell('array', $tophub_query);

                $ip = (isset($tophub_query['ip'])) ? $tophub_query['ip'] : '';

                if ($tophub_path == '/v0/node/info.json') {

                    $query = new React\MySQL\Query('select * from nodes where addr = ?');
                    $sql   = $query->bindParams($ip)->getSql();

                } else if ($tophub_path == '/v0/node/peers.json') {

                    $query = new React\MySQL\Query('select * from nodes where addr = ?');
                    $sql   = $query->bindParams($ip)->getSql();

                } else if ($tophub_path == '/v0/node/update.json') {

                    $query = new React\MySQL\Query('select * from nodes where addr = ?');
                    $sql   = $query->bindParams($ip)->getSql();

                } else {
                    $tophub_Response->write(json_encode((object) array(), JSON_PRETTY_PRINT));
                }

                $connection->connect(function () {});
                $connection->query($sql, function ($command, $conn) use ($loop_mysql, $tophub_query, $tophub_Response) {
                    if ($command->hasError()) {
                        yell('string', 'Error: Database');
                        $tophub_Response->write(json_encode(array('error'=>'database'), JSON_PRETTY_PRINT));
                    } else {
                        $results = $command->resultRows;
                        $tophub_Response->write(json_encode((object) $results, JSON_PRETTY_PRINT));
                    }
                    $loop_mysql->stop();
                });
                $loop_mysql->run();

            } else {
                /*
                | Method | Endpoint             | Args      | Description           |
                | ------ | -------------------- | --------- | --------------------- |
                | GET    | /v0/node/info.json   | n/a       | ? return valid params |
                | GET    | /v0/node/peers.json  | n/a       | ? list all peers      |
                | GET    | /v0/node/update.json | n/a       | ? return valid params |
                */
                if (($tophub_path == '/v0/node/info.json')
                ||  ($tophub_path == '/v0/node/peers.json')
                ||  ($tophub_path == '/v0/node/update.json')
                ){
                    yell('string', 'tophub_path ' . $tophub_path);
                    print_r($tophub_query);
                    $tophub_Response->write(json_encode((object) array(), JSON_PRETTY_PRINT));

                }
            }

        }
        elseif ($tophub_method == 'POST') {
            /*
            | Method | Endpoint             | Args       | Description           |
            | ------ | -------------------- | ---------- | --------------------- |
            | POST   | /v0/node/update.json | info=array | Update your node info |
            */
            yell('string', "*!* TophubMethod Logic: $tophub_method");
            $recdata = 0;
            $reqbody .= $data;
            $recdata += strlen($data);

            if ($recdata >= $tophub_clileng) {
                parse_str($reqbody, $tophub_Array);
                yell('string', 'tophub_Array:');
                print_r($tophub_Array);
            }
        }
        echo PHP_EOL;
        $tophub_Response->end();
    });
};

echo $banner("Version " . $tophub_revi);
echo $banner("Binding to http://" . $tophub_host .':'. $tophub_port);

$http->on('request', $tophub_Obj);
$socket->listen($tophub_port, $tophub_host);
$loop->run();

?>
