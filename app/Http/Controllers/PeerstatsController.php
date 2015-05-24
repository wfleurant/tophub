<?php namespace App\Http\Controllers;

use Input;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Tophub\Peerstats;

class PeerstatsController extends Controller {

	protected $rejected_resp = ['result' => 'rejected' ];

	/**
	 * PeerStats Update
	 */
	public function peerstats_post()
	{

		/* Peerstats:: */
		$pStats = new Peerstats(Input::ip(), Input::json()->get('peerstats'));

		/* Sanity */
		if ($pStats->insane === true) {
			$pStats->yell('string', 'rejected: null or !array peerStats from: ' . Input::ip());
			return json_encode($this->rejected_resp);
		}

		/**
		 * @var $result Object. Sanity within 'response' (Acceptable' or 'Rejected')
		 */
		$result = (object) $pStats->tophub_peerstats();

		$psc = count($result->data);
		$psf = $pStats->ipaddr;
		$psr = $result->update;

		$pStats->yell('string', $psr . ': ' . $psc . ' peerStats from: ' .  $psf);
		/* ... */

		if ($result->update == 'acceptable') {
			// ::: acceptable :::

			$psw = $pStats->write($result->data);

			$pStats->yell('string', "Database wrote $psc PeerStats: " . json_encode($psw));

		} elseif ($result->update == 'rejected') {
			// ::: rejected :::
		}

		return json_encode(['result' => $result->update ]);
	}

}
