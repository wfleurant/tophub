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

				/* Found in JS hubStats */
				$this->struct_peerStats_JS = [
					'version', 	// v15
					'label', 	// 0000.0000.0000.0013
					'pubkey', 	// xrj4g8klznc6ju2q1ljktlhff08c24lglmyqwtddtjy3vlsgrwq0.k
					'state', 	// ESTABLISHED
					'bytesin', 	// 25055604
					'bytesout', // 20660416
				];

				/* Found in Lua hubStats */
				$this->struct_peerStats_Lua = [
					'publicKey',
					'last',
					'switchLabel',
					'receivedOutOfRange',
					'bytesOut',
					'state',
					'addr',
					'isIncoming',
					'ipv6',
					'lostPackets',
					'bytesIn',
					'version',
					'duplicates',
					'user',
				];

				array_walk_recursive($p, function ($_v, $idx) use (&$insane) {
					if (in_array($idx, $this->struct_peerStats_JS) == true) {
						// associated index acceptable
					} elseif (in_array($idx, $this->struct_peerStats_Lua) == true) {
						// associated index acceptable
					} else {
						// associated index rejected:
						// print($idx . "\n");
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

		$data['node'] = $this->ipaddr;
		$col = \DB::collection($this->collection)
				->insert($data);

		$result[] = [ 'nosql'  => 'updated', 'result' => [ '_id' => $col ] ];

		/* Todo MySQL insert */
		// $result[] = ['mysql' => [ 'mysqli_insert_id' => rand(20000,99999)] ] ;

		/************************************************/
		return $result;
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



}
