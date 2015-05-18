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

		/* sanity required on
			$ipaddr
			$peerstats
		*/
		$this->ipaddr=$ipaddr;
		$this->peerstats=$peerstats;
			$this->c=$c;
			$this->d=$d;
			$this->e=$e;
			$this->f=$f;
		// tophub
		$this->log = new Logger('Peerstats');
		$this->log->pushHandler(new StreamHandler('Peerstats.log', Logger::NOTICE));

		// HubConfig.php
		$this->tophub_revi = '1.0';
		$this->tophub_host = 'fc00::1';
		$this->tophub_sums = 0;

		// HubConfig.php
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

	public function tophub() {

		/* Log */
		$LogInfo = [ "client-id" => $this->ipaddr, "total-peerstats" => count($this->peerstats) ];

		$thMetaHeader['tophub_revi'] = $this->tophub_revi;
		$thMetaHeader['tophub_host'] = $this->tophub_host;
		$thMetaHeader['tophub_sums'] = $this->tophub_sums;
		$thMetaHeader['tophub_head'] = $this->tophub_head;

		// $this->log->addNotice('ipaddr', [ $this->ipaddr ]);
		// $this->log->addNotice('peerstats',  $this->peerstats );
		$this->log->addNotice('LogInfo()', $LogInfo);

		/* Tophub */
		$tophub_Obj = function ($request='foo', $tophub_Response='bar') use ($LogInfo)
		{

			$tophub_sums = 0;
			$tophub_sums++;
			$reqbody = '';

			/***********************************************************************/
			/* Back peddling code where Laravel ReactPHP hands off */

			// $tophub_Response->writeHead(200, $tophub_meta['headers']);
			// $request->on('data',function($data) {

			$this->yell('string', 'Recieved Request((1))');

			/***********************************************************************/
			// $tophub_method  = $request->getMethod();    /* POST, GET */
			// $tophub_query   = $request->getQuery();     /* POST, GET */
			// $tophub_path    = $request->getPath();      /* /v0/node.json */
			// $tophub_clihead = $request->getHeaders();   /* array */
			/***********************************************************************/

			/*
			| Method | Endpoint             | Args            | Description           |
			| ------ | -------------------- | --------------- | --------------------- |
			| POST   | /v0/node/update.json | info=array      | Update your node info |
			| POST   | /v0/node/update.json | peerstats=array | Update your node info |
			*/

			$tophub_method  = Request::getMethod();    /* POST, GET */
			$this->yell('string', "*!* TophubMethod Logic: $tophub_method");
			return json_encode($LogInfo);
			$recdata  = 0;
			$reqbody .= $data;
			$recdata += strlen($data);

			parse_str($reqbody, $tophub_Array);
			$this->yell('string', 'tophub_Array:');

			/* '/v0/node/update.json' */

			$tophub_Response->write(json_encode((object) array('update' => 'accepted'), JSON_PRETTY_PRINT));
			$this->yell('string', 'Recieved ' . count($peerstats) . ' peerStats from: ' . $peerstats_from);

			/*
				[version] => v15
				[label] => 0000.0000.0000.0013
				[pubkey] => xrj4g8klznc6ju2q1ljktlhff08c24lglmyqwtddtjy3vlsgrwq0.k
				[state] => ESTABLISHED
				[bytesin] => 25055604
				[bytesout] => 20660416
			*/

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

		};

		/* Nuke it */
		$tophub_Obj();

	}



	public function yell($type=false,$string=false,$bulletlist=null) {

	    if((is_array($string)) || is_object($string)){
	        print_r($string);
	        return;
	    } else {
	        echo ColorCLI::getColoredString($string, 'white');
	        print("\n");
	    }
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
