<?php
// HubConfig.php
$tophub_revi = '1.0';
$tophub_port = 1617;
$tophub_host = 'fccc:5b2c:2336:fd59:794d:c0fa:817d:8d8';
$tophub_host = '127.0.0.1';
$tophub_host = 'fc00::1';
$tophub_sums = 0;

$tophub_head = tophub_headers($tophub_revi);
$tophub_meta = array();
$tophub_meta['headers'] = $tophub_head;
$tophub_meta['Server'] = $tophub_head;

// HubEventLog.php
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

// HubFunctions.php
/*
	Simple CLI color class -> https://gist.github.com/donatj/1315354

	echo ColorCLI::getColoredString('Test');
	echo ColorCLI::getColoredString('Test', 'blue');
	echo ColorCLI::getColoredString('Test', null, 'blue');

	print_r(ColorCLI::getForegroundColors());
	print_r(ColorCLI::getBackgroundColors());

 */
class ColorCLI {
	static $foreground_colors = array(
		'black'        => '0;30', 'dark_gray'    => '1;30',
		'blue'         => '0;34', 'light_blue'   => '1;34',
		'green'        => '0;32', 'light_green'  => '1;32',
		'cyan'         => '0;36', 'light_cyan'   => '1;36',
		'red'          => '0;31', 'light_red'    => '1;31',
		'purple'       => '0;35', 'light_purple' => '1;35',
		'brown'        => '0;33', 'yellow'       => '1;33',
		'light_gray'   => '0;37', 'white'        => '1;37',
		);

	static $background_colors = array(
		'black'        => '40', 'red'          => '41',
		'green'        => '42', 'yellow'       => '43',
		'blue'         => '44', 'magenta'      => '45',
		'cyan'         => '46', 'light_gray'   => '47',
		);

	// Returns colored string
	public static function getColoredString($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";

	// Check if given foreground color found
		if ( isset(self::$foreground_colors[$foreground_color]) ) {
			$colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
		}
	// Check if given background color found
		if ( isset(self::$background_colors[$background_color]) ) {
			$colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
		}

	// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		return $colored_string;
	}

	// Returns all foreground color names
	public static function getForegroundColors() {
		return array_keys(self::$foreground_colors);
	}

	// Returns all background color names
	public static function getBackgroundColors() {
		return array_keys(self::$background_colors);
	}
}

$banner = function($msg='') {
	$hub = ColorCLI::getColoredString('[topHub]', 'red');
	return ($msg === '') ? $hub . PHP_EOL
			     : $hub .' '. ColorCLI::getColoredString($msg, 'white') . PHP_EOL;
};

function tophub_headers($tophub_revi='0.1')
{
	return array(
	    'Content-Type'  => 'text/html; charset=UTF-8',
	    'Date'          => 'Sun, 01 Jan 2015 00:00:00 GMT',
	    'Server'        => 'tophub/' . $tophub_revi,
	    'X-Powered-By'  => 'PHP/' . phpversion(),
	    'Cache-Control' => 'no-cache, no-store, must-revalidate',
	    'Pragma'        => 'no-cache',
	    'Expires'       => 'Sat, 21 Apr 2001 00:00:00 EST',
	    'Connection'    => 'Close'
	);
}

$post_sanity = function($tophub_method, $tophub_clileng, $tophub_clihead) {
	if ( $tophub_method == 'POST' ) {
		if ($tophub_clileng === 0) {
			yell('error', "Content Length is not an Indexable Item: (0)");
			return(false);
		}  else if (isset($tophub_clihead['Expect'])) {
			yell('error', "Unsupported: Chunked Expect: \t" . $tophub_clihead['Expect']);
			/* @TODO See if we can force a HTTP 1.0 Only response... */
			/* $tophub_Response->writeContinue(); */
			return(false);
		} else {
			return(true);
		}
	} else {
		return(true);
	}
}
?>
