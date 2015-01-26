<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function yell($type=false,$string=false,$bulletlist=null)
{

    if(is_array($string)){ print("\n");
        $string=var_dump($string);
        echo ColorCLI::getColoredString($string, 'white');
        print("\n");
        return;
    }

    if($type == "monolog") {

        // $log = new Logger('name');
        // $log->pushHandler(new StreamHandler('./log_ioStats.log', Logger::WARNING));

        // $log->addWarning('Foo');
        // $log->addError("$string");

    }
    // if ($type == "error") {
        echo ColorCLI::getColoredString($string, 'white');
        print("\n");
        return;
    // }
}

?>