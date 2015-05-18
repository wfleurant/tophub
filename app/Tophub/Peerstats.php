<?php namespace App\Tophub;

use Jenssegers\Mongodb\Model as Eloquent;
use Moloquent;
use Log;
use Input;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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



			/***********************************************************************/
			$tophub_sums = 0;
			$tophub_sums++;
			$reqbody = '';

			return json_encode($LogInfo);
			/* Back peddling code where Laravel ReactPHP hands off */

			// $tophub_Response->writeHead(200, $tophub_meta['headers']);
			// $request->on('data',function($data) {

				yell('string', 'Recieved Request((1))');
				include 'include/db/HubMySQL.php';
				$tophub_method  = $request->getMethod();    /* POST, GET */
				$tophub_query   = $request->getQuery();     /* POST, GET */
				$tophub_path    = $request->getPath();      /* /v0/node.json */
				$tophub_clihead = $request->getHeaders();   /* array */
				$tophub_clileng = (isset($tophub_clihead['Content-Length']))
									? (int)$tophub_clihead['Content-Length']
									: false;

				if ($post_sanity($tophub_method, $tophub_clileng, $tophub_clihead) === false) {
					$tophub_Response->end(); return;
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

						yell('string', "*!* GET with DATA"); yell('array', $tophub_query);

						$addr = (isset($tophub_query['ip'])) ? $tophub_query['ip'] : '';

						if ($tophub_path == '/v0/node/info.json') {

							$query = new React\MySQL\Query('SELECT * FROM nodes WHERE addr = ?');
							$sql   = $query->bindParams($addr)->getSql();

						} else if ($tophub_path == '/v0/node/peers.json') {

							$query = new React\MySQL\Query('SELECT * FROM nodes WHERE addr = ?');
							$sql   = $query->bindParams($addr)->getSql();

						} else {

							$tophub_Response->write(json_encode((object) array(), JSON_PRETTY_PRINT));
							$loop_mysql->stop();

						}
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
					| Method | Endpoint             | Args            | Description           |
					| ------ | -------------------- | --------------- | --------------------- |
					| POST   | /v0/node/update.json | info=array      | Update your node info |
					| POST   | /v0/node/update.json | peerstats=array | Update your node info |
					*/
					yell('string', "*!* TophubMethod Logic: $tophub_method");
					$recdata  = 0;
					$reqbody .= $data;
					$recdata += strlen($data);

					if ($recdata >= $tophub_clileng) {

						parse_str($reqbody, $tophub_Array);
						yell('string', 'tophub_Array:');
						// print_r(json_decode($reqbody));
						// var_dump($tophub_Array);

						$addr = (isset($tophub_Array['ip'])) ? $tophub_Array['ip'] : '';
						$ownername = (isset($tophub_Array['ownername'])) ? $tophub_Array['ownername'] : '';
						// $peerstats = (isset($reqbody['peerstats'])) ? $reqbody : '';
						if ($tophub_path == '/v0/node/update.json') {
							if(is_object(json_decode($reqbody))) {
								if(is_array(json_decode($reqbody)->peerstats)) {
									$tophub_Response->write(json_encode((object) array('update' => 'accepted'), JSON_PRETTY_PRINT));
									$peerstats_from = json_decode($reqbody)->ip;
									$peerstats = json_decode($reqbody)->peerstats;
									yell('string', 'Recieved ' . count($peerstats) . ' peerStats from: ' . $peerstats_from);
									/* Send to database */

									/*  [4] => stdClass Object
											(
												[version] => v15
												[label] => 0000.0000.0000.0013
												[pubkey] => xrj4g8klznc6ju2q1ljktlhff08c24lglmyqwtddtjy3vlsgrwq0.k
												[state] => ESTABLISHED
												[bytesin] => 25055604
												[bytesout] => 20660416
											) */
								}
							} else { // if array?

								/*
									UPDATE nodes SET cjdns_protocol = ? WHERE addr = ?;
									UPDATE nodes SET api_enabled = ?  WHERE addr = ?;
									UPDATE nodes SET api_keyid = ?, api_secretkey = ? WHERE addr = ?;
									UPDATE nodes SET country = ? WHERE addr = ?;
									UPDATE nodes SET hostname = ? WHERE addr = ?
									UPDATE nodes SET last_seen = ? WHERE addr = ?;
									UPDATE nodes SET lat = ? WHERE addr = ?;
									UPDATE nodes SET lng = ? WHERE addr = ?;
									UPDATE nodes SET map_privacy = ?  WHERE addr = ?;
									UPDATE nodes SET msg_enabled = ? WHERE addr = ?;
									UPDATE nodes SET msg_privacy = ?  WHERE addr = ?;
									UPDATE nodes SET ownername = ? WHERE addr = ?;
									UPDATE nodes SET public_key = ? WHERE addr = ?;
								*/

								$query = new React\MySQL\Query('UPDATE nodes SET ownername = ? WHERE addr = ?');
								$sql   = $query->bindParams($ownername, $addr)->getSql();
							}

						} else {
							$tophub_Response->write(json_encode((object) array(), JSON_PRETTY_PRINT));
							$loop_mysql->stop();
						}
					}
				}
				/****************************************************************************************************/

				if ((!isset($sql) || $sql=='')) {
					$tophub_Response->write(json_encode(array('error'=>'database'), JSON_PRETTY_PRINT));
					$tophub_Response->end();
				} else {
					$connection->connect(function () {});
					$connection->query($sql, function ($command, $conn) use ($loop_mysql, $tophub_query, $tophub_Response, $tophub_method) {
						if ($command->hasError()) {
							yell('string', 'Error: Database');
							$tophub_Response->write(json_encode(array('error'=>'database'), JSON_PRETTY_PRINT));
						} else {
							if ($tophub_method == "GET") {
								$results = $command->resultRows;
							} elseif ($tophub_method == "POST") {
								$results = $command;
							}
							$tophub_Response->write(json_encode((object) $results, JSON_PRETTY_PRINT));
						}
						$loop_mysql->stop();
					});
					$loop_mysql->run();
					$tophub_Response->end();
					echo PHP_EOL;
				}
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
