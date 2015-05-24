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
	public function peerstats_post($req = 'req')
	{

        /* Peerstats:: */
        $pStats = new Peerstats(Input::ip(), Input::json()->get('peerstats'));

		/* Sanity */
		if ($pStats->insane === true) {
			$pStats->yell('string', 'rejected: null or !array peerStats from: ' . Input::ip());
			return json_encode($this->rejected_resp);
		}

        /* ... */
        $result = $pStats->tophub_peerstats();

        /* ... */
		$psc = count($pStats->peerstats);
		$psf = $pStats->ipaddr;
		$psr = $result->update;

		$pStats->yell('string', $psr . ': ' . $psc . ' peerStats from: ' .  $psf);

        if ($result->update == 'acceptable') {
			// ::: acceptable :::
		} elseif ($result->update == 'rejected') {
			// ::: rejected :::
		}

		return json_encode(['result' => $result->update ]);
	}

}
