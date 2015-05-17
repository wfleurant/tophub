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
        $ps = new Peerstats(Input::ip());
        $ps->peerstats = Input::json()->get('peerstats');
        // $result = $ps->PeersUpdate();
        $result = $ps->tophub();

		return(json_encode(['result' => $result ]));
	}

}
