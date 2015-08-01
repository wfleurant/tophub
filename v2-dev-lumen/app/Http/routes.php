<?php

use App\Tophub\Peerstats;
use App\Tophub\Logger;
use App\Tophub\Req;

$app->get('/', function () {
    return response()->json(['version'=> 'v2-staging']);
});

/*===================================================================================
        88""Yb 888888 888888 88""Yb .dP"Y8 888888    db    888888 .dP"Y8
        88__dP 88__   88__   88__dP `Ybo."   88     dPYb     88   `Ybo."
        88"""  88""   88""   88"Yb  o.`Y8b   88    dP__Yb    88   o.`Y8b
        88     888888 888888 88  Yb 8bodP'   88   dP""""Yb   88   8bodP'
=====================================================================================*/
$app->post('/api/v0/node/update.json', function () {
    $ipaddr = Req::ip();
    $peerstats = \Request::json()->get('peerstats');

    Logger::writeln('info', 'Peerstats from: ' . $ipaddr);

    if (!is_array($peerstats)) {
        return response()->json(['errors.custom']);
    }

    $pStats = new Peerstats($ipaddr, $peerstats);
    $result = (object) $pStats->tophub_peerstats();

    if ($result->update == 'acceptable') {

        $psw = $pStats->save();

        // ::: acceptable :::
        if ($psw) {
            $psc = count($result->data);
            $psf = $pStats->ipaddr;
            $psr = $result->update;
            Logger::writeln('info', "Database wrote $psc PeerStats: " . json_encode($psw, true));
        } else {
            Logger::writeln('error', "Database Unreachable - Lost $psc PeerStats: " . json_encode($psw, true));
        }

    } else if ($result->update == 'rejected') {
        // ::: rejected :::
        $save_rejected = false;
        if ($save_rejected) {
            $psw = $pStats->save();
            Logger::writeln('info',
                "Tophub Rejecting PeerStats: Attempting to save to DB");
        } else {
            Logger::writeln('info',
                "Tophub Rejecting PeerStats: Nothing written to DB");
        }
    } else {
        Logger::writeln('error', "Unknown peerstats error!");
    }

    return json_encode(['result' => $result->update ]);

});
/*====================================================================================*/

/*

*/

/*====================================================================================*/
/* xxx Template */
$app->get('/xxx', function () {
    // 'uses' => 'xxx@Template'
    return [ 'xxx' => 'xxx' ];
});
/*====================================================================================*/



/*====================================================================================*/
/* peers.json @ getNodePeers */
$app->get('/api/v0/node/{ip}/peers.json', function ($ip) {
    // 'uses' => 'peers.json@getNodePeers'
    // $results = DB::select();
    // $peers = DB::whereAddr($ip)->firstOrFail()->peers->lists('peer_key');
    $peers = DB::where('addr', '=', $addr)->firstOrFail()->peers->lists('peer_key');
    return response()->json([ '/api/v0/node/{ip}/peers.json' => $peers ]);
});
/*====================================================================================*/


/*====================================================================================*/
/* /web/node/update.json updateNode */
$app->post('/web/node/update.json', function ($request) {
    // 'uses' => 'web/node/update.json@updateNode'
    return [ 'web/node/update.json' => 'web/node/update.json' ];

    $input = $request::all();
    $ip = Request::ip();
    if ( !\hash_equals(sha1($ip.$input['_token']), $input['web_token'] ) ) {
        return response()->json([
            'response'      => 403,
            'status'        => 'forbidden',
            'error'         => true,
            'error_msg'     => 'Endpoint is exclusive to the website, please use the public endpoint.',
            'use_instead'   => 'http://dev.hub.hyperboria.net/api/v0/node/'.$ip.'/update.json',
            ],
            403,
            [],
            JSON_PRETTY_PRINT);
    }

    $node = Node::where('addr', '=', $ip)->firstOrFail();
    $act_desc = [];
    unset($input['_token'], $input['web_token']);
    foreach ($input as $k => $v) {
        if( !empty($v) ) {
            $v = trim($v);
            $act_desc[] = $k;
            switch ($k) {
                case 'hostname':
                    $node->hostname = $v;
                    break;
                case 'ownername':
                    $node->ownername = $v;
                    break;
                case 'bio':
                    $node->bio = $v;
                    break;
                case 'city':
                    $node->city = $v;
                    break;
                case 'province':
                    $node->province = $v;
                    break;
                case 'country':
                    $node->country = $v;
                    break;
                case 'lat':
                    $node->lat = floatval($v);
                    break;
                case 'lng':
                    $node->lng = floatval($v);
                    break;
                default:
                    break;
            }
        }
    }

    $node->update();
    \Activity::log([
        'actor_user_id'   => $ip,
        'action_id'   => $ip,
        'action_user_id'   => \Auth::id(),
        'action_type' => 'Node@Update',
        'action'      => 'Update Info',
        'description' => 'Updated node info.',
        'source'      => 'Web',
        'details'     => 'Updated '.implode(',', $act_desc).' field(s)',
    ]);
    return redirect('/node/'.$ip);

});
/*====================================================================================*/

/*====================================================================================*/
/* v0/nodes/known.json getAllNodes */
$app->get('/api/v0/nodes/known.json', function () {
    // 'uses' => '/api/v0/nodes/known.json@getAllNodes'
    // DB::prepare(nodes[addr]);
    return [ '/api/v0/nodes/known.json' => '/api/v0/nodes/known.json' ];
});
/*====================================================================================*/

/*====================================================================================*/
/* v0/nodes/known/timestamps.json getNodesTimestamped */
$app->get('v0/nodes/known/timestamps.json', function () {
    // 'uses' => 'v0/nodes/known/timestamps.json@getNodesTimestamped'
    // DB::prepare(nodes[addr,created_at,updated_at]);
    return [ 'v0/nodes/known/timestamps.json' => 'v0/nodes/known/timestamps.json' ];
});
/*====================================================================================*/



