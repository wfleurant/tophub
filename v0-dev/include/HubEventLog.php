<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function yell($type=false,$string=false,$bulletlist=null)
{
    if((is_array($string)) || is_object($string)){
        print_r($string);
        return;
    } else {
        echo ColorCLI::getColoredString($string, 'white');
        print("\n");
    }
}

?>