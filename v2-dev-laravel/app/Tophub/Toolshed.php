<?php namespace App\Tophub;

class Toolshed {

	/**
	 * Previously included when running tophub in ReactPHP event loop
	 * where we iterated on 'data' .. however,
	 * this is now done with the Laravel + ReactPHP service provider
	 *
	 * @return object
	 * @author igel
	 **/
	public function TopHubCfg($var = null)
	{

		// HubConfig.php
		$this->tophub_revi = '1.0';
		$this->tophub_host = 'fc00::1';
		$this->tophub_sums = 0;
		$this->tophub_head = [
			'Content-Type'  => 'text/html; charset=UTF-8',
			'Date'          => 'Sun, 01 Jan 2015 00:00:00 GMT',
			'Server'        => 'tophub/' . $this->tophub_revi,
			'X-Powered-By'  => 'PHP/' . phpversion(),
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma'        => 'no-cache',
			'Expires'       => 'Sat, 21 Apr 2001 00:00:00 EST',
			'Connection'    => 'Close'
		];

	}

	/**
	 * Another previously included snippet
	 * Simple CLI color class -> https://gist.github.com/donatj/1315354
	 * 		via HubFunctions.php
	 **/
	static $foreground_colors = [
		'black'        => '0;30', 'dark_gray'    => '1;30',
		'blue'         => '0;34', 'light_blue'   => '1;34',
		'green'        => '0;32', 'light_green'  => '1;32',
		'cyan'         => '0;36', 'light_cyan'   => '1;36',
		'red'          => '0;31', 'light_red'    => '1;31',
		'purple'       => '0;35', 'light_purple' => '1;35',
		'brown'        => '0;33', 'yellow'       => '1;33',
		'light_gray'   => '0;37', 'white'        => '1;37',
	];

	static $background_colors = [
		'black'        => '40', 'red'          => '41',
		'green'        => '42', 'yellow'       => '43',
		'blue'         => '44', 'magenta'      => '45',
		'cyan'         => '46', 'light_gray'   => '47',
	];

	// Returns colored string
	public static function getColoredString($string, $foreground_color = null, $background_color = null)
	{
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
	public static function getForegroundColors() { return array_keys(self::$foreground_colors); }
	// Returns all background color names
	public static function getBackgroundColors() { return array_keys(self::$background_colors); }


	/**
	 * Output arrays, strings to console.. useful when debugging modes are
	 * set and logstash is incorporated.
	 *
	 * @return void
	 * @author igel
	 **/
	public function yell($type=false,$string=false,$bulletlist=null) {

		if((is_array($string)) || is_object($string)){
			print_r($string);
			return;
		} else {
			$hub = $this->getColoredString('[topHub]', 'red');
			echo $this->getColoredString("$hub $string", 'white');
			print("\n");
		}
	}
}

?>