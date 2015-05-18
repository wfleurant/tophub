<?php namespace App\Http\Controllers;

use Input;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Tophub\Peerstats;

class PeerstatsController extends Controller {

	/**
	 * Update
	 */
	public function update($req = 'req')
	{

		/* vars */
        $ipaddr    = Input::ip();
        $peerstats = Input::json()->get('peerstats');

        /* Peerstats  */
        $pStats = new Peerstats($ipaddr, $peerstats);
        $result = $pStats->tophub();
        $return = json_encode(['result' => $result ]);

        return $return;
	}

}
