<?php

/*
| Application Routes
*/

Route::get('/', 'HomeController@index');
// Route::get('/nodes', 'NodeController@view');
Route::get('/nodes', 'HomeController@index');

Route::group(['prefix' => 'api'], function()
{
    Route::post('v0/node/update.json', 'PeerStatsController@peerstats_post');
    Route::post('v0/node/info.json',  function() {
        // 'PeerStatsController@peerstats_post');
        /* | Method | Endpoint             | Args            | Description           |
           | ------ | -------------------- | --------------- | --------------------- |
           | POST   | /v0/node/update.json | info=array      | Update your node info | */
        return (object) json_encode(['tophub_info' => false]);
    });
    /*******************/
    /* Autocomplete v1 */
    /*******************/
    Route::get('v1/node/autocomplete.json', 'NodeController@autocompleteJson');
    /************************************/
    /* Node v0 - Spec not yet finalized */
    /************************************/
/*    Route::get('node/{id}/info.json', function($id) {
       return response()->json([
        'response_code'=> 500,
        'error'=>true,
        'api_status' => 'depreciated',
        'error_message'=>'Endpoint not yet available. Please use the experimental v0 api.',
        'api_use_instead' => 'http://dev.hub.hyperboria.net/api/v0/node/'.$id.'/info.json',
        ], 500, [], JSON_PRETTY_PRINT);
   });
    Route::get('v0/node/{ip}/info.json', function(){
       return response()->json([
        'response_code'=> 500,
        'error'=>true,
        'api_status' => 'temporarily unavailable',
        'error_message'=>'Endpoint is temporarily unavailable.',
        ], 500, [], JSON_PRETTY_PRINT);
   });
   */
    Route::get('v0/node/{ip}/peers.json', 'ApiController@getNodePeers');
    /******************************************/
    /* Node Website APIs (not for public use) */
    /******************************************/
    Route::post('web/node/update.json', 'ApiController@updateNode');
    /**********/
    /* Map v1 */
    /**********/
    Route::get('v1/map/graph/data.json', 'MapController@sigmaJson');
    Route::get('v1/map/graph/node.json', 'MapController@graphNodeJson');
    Route::get('v1/map/graph/edge.json', 'MapController@graphEdgeJson');

});

Route::group(['prefix' => 'nodes'], function()
{
    /*********************************************************************/
    /* TODO: Move to POST routes to API, no more POSTS to non-api routes */
    /*********************************************************************/
    Route::match(['get', 'post'], '{ip}/follow.json', 'NodeController@follow');
    Route::match(['get', 'post'], '{ip}/comments', 'NodeController@comments');
    Route::match(['get', 'post'], '{ip}/comment/add', 'CommentController@store');
    Route::match(['get', 'post'], '{ip}/unfollow.json', 'NodeController@unfollow');
    Route::match(['get', 'post'], '{ip}/followed.json', 'NodeController@followed');
    Route::get('{ip}/activity', [
        'as'    => 'node.activity',
        'uses'  => 'NodeController@activity'
        ]);
    Route::get('{ip}/peers', [
        'as'    => 'node.peers',
        'uses'  => 'NodeController@peers'
        ]);
    Route::get('{ip}/services', [
        'as'    => 'node.services',
        'uses'  => 'NodeController@services'
        ]);
    Route::get('{ip}', [
        'as'    => 'node.view',
        'uses'  => 'NodeController@view'
        ]);
});


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
