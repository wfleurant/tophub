<?php namespace App\Tophub;

use Jenssegers\Mongodb\Model as Eloquent;
use Moloquent;
use Log;
use Input;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Request;

class Peerstats extends Moloquent {

	protected $collection = 'peerstats';
	protected $connection = 'mongo';

	function __construct($ipaddr=null, $peerstats=null,
								$c=null, $d=null, $e=null, $f=null) {

		$this->ipaddr=$ipaddr;
		$this->peerstats=$peerstats;

		$this->c=$c; $this->d=$d; $this->e=$e; $this->f=$f;
		// Thanks for playing...
		if (!is_array($this->peerstats)) {
			$this->insane = true;
			return;
		}

		// Log and Continue
		$this->log = new Logger('Peerstats');
		$this->log->pushHandler(new StreamHandler('Peerstats.log', Logger::NOTICE));

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

	} // __construct

	/* | Method | Endpoint             | Args            | Description           |
	   | ------ | -------------------- | --------------- | --------------------- |
	   | POST   | /v0/node/update.json | peerstats=array | Update your node info | */
	/* --------------------------------------------------------------------------- */

	public function tophub_peerstats() {

		/* Log */
		$LogInfo = [ "client-id" => $this->ipaddr, "total-peerstats" => count($this->peerstats) ];

		$thMetaHeader['tophub_revi'] = $this->tophub_revi;
		$thMetaHeader['tophub_host'] = $this->tophub_host;
		$thMetaHeader['tophub_sums'] = $this->tophub_sums;
		$thMetaHeader['tophub_head'] = $this->tophub_head;

		// $this->log->addNotice('ipaddr', [ $this->ipaddr ]);
		// $this->log->addNotice('peerstats',  $this->peerstats );
		/* ... */
		// $this->tophub_method  =  Request::getMethod();    /*  POST, GET      */
		// $this->tophub_method  = $request->getMethod();    /*  POST, GET      */
		// $this->tophub_query   = $request->getQuery();     /*  POST, GET      */
		// $this->tophub_path    = $request->getPath();      /*  /v0/node.json  */
		// $this->tophub_clihead = $request->getHeaders();   /*  array          */

		$this->log->addNotice('LogInfo()', $LogInfo);

		$tophub_Obj = function ($request='foo', $tophub_Response='bar') use ($LogInfo)
		{


			$function_validate_peerStats = function($p) {

				/* if '$insane' is true, we reject the peerstats post
					because it was not found within this->struct_peerStats.
				 */
				$insane = false;

				$this->struct_peerStats = [
					'version', 	// v15
					'label', 	// 0000.0000.0000.0013
					'pubkey', 	// xrj4g8klznc6ju2q1ljktlhff08c24lglmyqwtddtjy3vlsgrwq0.k
					'state', 	// ESTABLISHED
					'bytesin', 	// 25055604
					'bytesout', // 20660416
				];

				array_walk_recursive($p, function ($_v, $idx) use (&$insane) {
					if (in_array($idx, $this->struct_peerStats) == true) {
						// associated index acceptable
						//
					} else {
						// associated index rejected
						$insane = true;
					}
				});
				return ($insane === true) ? 'rejected' : 'acceptable';
			};

			$this->result = $function_validate_peerStats($this->peerstats);
			return (object) [ 'update' => $this->result, 'data' => $this->peerstats ];

		};

		/* Nuke it */
		return $tophub_Obj();

	}

	public function write ($data) {

		/* Todo Mongo upsert */
		$result[] = ['nosql' => 'updated', 'result' => [ '_id' => 'fc00cafe1234'] ];

		/* Todo MySQL insert */
		$result[] = ['mysql' => [ 'mysqli_insert_id' => rand(20000,99999)] ] ;

		/************************************************/
		return (object) [ $result ];
	}

	public function sqlHandoff ($data)
	{
		/* Async MySQL */

		/*
		$query = new React\MySQL\Query('UPDATE nodes SET ownername = ? WHERE addr = ?');
		$sql   = $query->bindParams($ownername, $addr)->getSql();

		$connection->connect(function(){});
		$connection->query($sql, function ($command, $conn)
			use ($loop_mysql, $tophub_query, $tophub_Response, $tophub_method)
		{
			$loop_mysql->stop();
		});

		$loop_mysql->run();
		$tophub_Response->end();
		*/
		return true;
	}

	public function yell($type=false,$string=false,$bulletlist=null) {

		if((is_array($string)) || is_object($string)){
			print_r($string);
			return;
		} else {
			$hub = ColorCLI::getColoredString('[topHub]', 'red');
			echo ColorCLI::getColoredString("$hub $string", 'white');
			print("\n");
		}
	}


}



// HubFunctions.php

class ColorCLI {
	// * Simple CLI color class -> https://gist.github.com/donatj/1315354
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
}

