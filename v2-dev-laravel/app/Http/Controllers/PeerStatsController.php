<?php namespace App\Http\Controllers;

use Input;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Tophub\Peerstats;
use App\Tophub\Toolshed;

class PeerStatsController extends Controller {

	protected $rejected_resp = ['result' => 'rejected' ];

	function __construct() {
		$this->shed = new Toolshed;
	}

	/**
	 * PeerStats Update
	 */
	public function peerstats_post()
	{
		/* Peerstats:: */
		$pStats = new Peerstats(Input::ip(), Input::json()->get('peerstats'));

		/* Sanity */
		if ($pStats->insane === true) {
			$this->shed->yell('string', 'rejected: null or !array peerStats from: ' . Input::ip());
			return json_encode($this->rejected_resp);
		}

		/**
		 * @var $result Object. Sanity within 'response' (Acceptable' or 'Rejected')
		 */
		$result = (object) $pStats->tophub_peerstats();

		$psc = count($result->data);
		$psf = $pStats->ipaddr;
		$psr = $result->update;

		$this->shed->yell('string', $psr . ': ' . $psc . ' peerStats from: ' .  $psf);
		/* ... */

		if ($result->update == 'acceptable') {
			// ::: acceptable :::
			$psw = $pStats->write($result->data);
			$this->shed->yell('string', "Database wrote $psc PeerStats: " . json_encode($psw, true));
		} elseif ($result->update == 'rejected') {
			// ::: rejected :::
		}

		return json_encode(['result' => $result->update ]);
	}

}
