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
require 'include/db/HubMongo.php';
require 'include/db/HubMySQL.php';

$tophub_revi = '1.0';
$tophub_port = 1617;
$tophub_host = 'fccc:5b2c:2336:fd59:794d:c0fa:817d:8d8';
$tophub_host = '127.0.0.1';
$tophub_sums = 0;

$tophub_head = tophub_headers($tophub_revi);
$tophub_meta = array();
$tophub_meta['headers'] = $tophub_head;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$tophub_Obj = function ($request, $tophub_Response) use (&$tophub_sums, &$tophub_meta)
{

	$tophub_sums++;
    // print_r($request);
    $reqbody = '';
    $clihead = $request->getHeaders();
    if(!isset($clihead['Content-Length'])) {
        yell('error', "Content Length is not an Indexable Item");
        yell('array', $request);
        // var_dump($request);
        return(1);
    } else {
        $cliconnlen = (int)$clihead['Content-Length'];
    }

    $recdata = 0;

    if($cliconnlen == 0) {
        yell('verbose', "The client content length was zero (0)");
        yell('array', $request);
    }


    $request->on('data',function($data) use ($request, $tophub_Response, &$reqbody, &$recdata, $cliconnlen,
                                             $tophub_sums, $tophub_meta)
    {

        $tophub_Response->writeHead(200, $tophub_meta['headers']);
        $tophub_Response->end();


        yell('string',$data);
        $reqbody .= $data;
        $recdata += strlen($data);
        if ($recdata >= $cliconnlen) {
            parse_str($reqbody, $tophub_Array);

            // code...
        }
    });
};

// This is the main event of the evening
$http->on('request', $tophub_Obj);

// This is the moment you've all been waiting for
$socket->listen($tophub_port, $tophub_host);

// Let's get ready to rumble!
$loop->run();

?>

?>
