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
require 'include/db/HubMySQL.php';

require 'include/HubConfig.php';

$loop   = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http   = new React\Http\Server($socket);

$tophub_Obj = function ($request, $tophub_Response) use (&$tophub_sums, &$tophub_meta, $http)
{

    $tophub_sums++;
    $reqbody = '';

    $tophub_Response->writeHead(200, $tophub_meta['headers']);

    $request->on('data',function($data) use ($request, $tophub_Response, &$reqbody,
                                            &$recdata, $tophub_sums, $tophub_meta)
    {
        yell('string', 'Recieved Request((1))');

        $tophub_method  = $request->getMethod();    /* POST, GET */
        $tophub_query   = $request->getQuery();     /* POST, GET */
        $tophub_path    = $request->getPath();      /* /v0/node.json */
        $tophub_clihead = $request->getHeaders();   /* array */
        $tophub_clileng = (isset($tophub_clihead['Content-Length']))
                            ? (int)$tophub_clihead['Content-Length']
                            : false;

        if ( $tophub_method == 'POST' && ($tophub_clileng === 0)) {
            yell('error', "Content Length is not an Indexable Item: (0)");
            yell('array', $data);
            var_dump($data);
            return(1);
        }

        if ($tophub_method == 'GET') {
            yell('string', "*!* TophubMethod Logic: $tophub_method");

            if ($tophub_query) {
                /*
                | Method | Endpoint             | Args      | Description           |
                | ------ | -------------------- | --------- | --------------------- |
                | GET    | /v0/node/info.json   | n/a       | ...                   |
                | GET    | /v0/node/peers.json  | n/a       | ...                   |
                | GET    | /v0/node/update.json | n/a       | ...                   |
                */
                yell('string', "*!* GET with DATA");
                yell('array', $tophub_query);
            } else {

                /*
                | Method | Endpoint             | Args      | Description           |
                | ------ | -------------------- | --------- | --------------------- |
                | GET    | /v0/node/info.json   | ip=[addr] | Get basic information |
                | GET    | /v0/node/peers.json  | ip=[addr] | Get node peers        |
                | GET    | /v0/node/update.json | ip=[addr] | ??????????????        |
                */
                if ($tophub_path == '/v0/node/info.json') {
                    echo 'tophub_path' . $tophub_path;
                    print_r($tophub_query);
                }
                if ($tophub_path == '/v0/node/peers.json') {
                    echo 'tophub_path' . $tophub_path;
                    print_r($tophub_query);
                }
                if ($tophub_path == '/v0/node/update.json') {
                    echo 'tophub_path' . $tophub_path;
                    print_r($tophub_query);
                }

            }

        }
        elseif ($tophub_method == 'POST') {
            /*
            | Method | Endpoint             | Args       | Description           |
            | ------ | -------------------- | ---------- | --------------------- |
            | POST   | /v0/node/info.json   | ip=[addr]  | ????????????????????? |
            | POST   | /v0/node/peers.json  | ip=[addr]  | ????????????????????? |
            | POST   | /v0/node/update.json | info=array | Update your node info |
            */
            yell('string', "*!* TophubMethod Logic: $tophub_method");

            if (isset($tophub_clihead['Expect'])) {

                yell('string', "Chunked  Expect: \t" . $tophub_clihead['Expect']);
                /* @TODO See if we can force a HTTP 1.0 Only response... */
                // $tophub_Response->writeContinue();
                $tophub_Response->end(); // bail..
            }

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
// $http->on('get', $tophub_Obj);
$socket->listen($tophub_port, $tophub_host);
$loop->run();

?>
