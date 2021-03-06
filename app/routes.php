<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('test', function() {
    dd(Facebook::loginUrl());
    });
Route::get('refreshdoges', 'HomeController@refreshdoges');
Route::get('/', array('as' => 'home', "uses" => 'HomeController@index')); 
Route::get('contact', array('as' => 'home.contact', "uses" => 'HomeController@contact'));
Route::get('about', array('as' => 'home.about', 'uses' => 'HomeController@about'));
Route::get('format', array('as' => 'home.format', 'uses' => 'HomeController@format'));
Route::get('rules', array('as' => 'home.rules', 'uses' => 'HomeController@rules'));
Route::get('sponsors', array('as' => 'home.sponsors', 
                             'uses' => function() { return View::make('sponsors'); }));

Route::get('finals', array('as' => 'home.finals', 'uses' => 'HomeController@finals'));
Route::get('dogecoin', array('as' => 'dogecoin', 'uses' => 'HomeController@dogecoin'));
Route::get('help', array('as' => 'help', 'uses' => 'HomeController@help'));
Route::get('blog', array('as' => 'blog.index', 'uses' => 'BlogController@index'));
Route::get('blog/{id}', array('as' => 'blog.profile', 'uses' => 'BlogController@show'));
Route::get('stats', array('as' => 'stats', 'uses' => 'StatsController@index'));
Route::get('stats/highest_median_winrate', 'StatsController@highestMedianWR');
Route::get('stats/every_man_on_the_field/{id}', 'StatsController@allPlayedInTournament');
Route::get('stream', array('as' => 'stream', 'uses' => 'HomeController@stream'));
Route::get('stream/teams', array('as' => 'stream.getTeams', 'uses' => 'HomeController@getTeams'));

Route::get('game/{id}', array('as' => 'game.profile', 'uses' => 'GameController@show'));

Route::get('lineup/{id}', array('as' => 'lineup.show', 'uses' => 'LineupController@show'));
Route::get('lineup/{id}/matches', array('as' => 'lineup.matches', 'uses' => 'LineupController@matches'));
//caster authenticated

Route::get('auth/fb_logout', array('as' => 'auth.fbLogout', 'uses' => 'AuthenticationController@fblogout'));
Route::get('auth/fb_login', array('as' => 'auth.fbLogin', 'uses' => 'AuthenticationController@fbLogin'));

Route::group(array('before' => 'auth|perm:vods'), function() {
  Route::get('vod/create', array('as' => 'vod.create', 'uses' => 'VODController@create'));
  Route::post('vod', array('as' => 'vod.store', 'uses' => 'VODController@store'));
});



Route::group(array('before' => 'guest'), function() {

  Route::get('register', array('as' => 'user.register', 'uses' => 'UserController@register'));
  Route::post('user', array('as' => 'user.store', 'uses' => 'UserController@store'));
  Route::get('login/{return_url?}', array('as' => 'user.login', 'uses' => 'UserController@login'));
  Route::post('login', array('as' => 'user.auth', 'uses' => 'UserController@auth'));
  Route::get('login/reset/begin', array('as' => 'login.start_reset', 'uses' => 'UserController@start_reset'));
  Route::post('login/reset/send_token', array('as' => 'login.send_token', 'uses' => 'UserController@send_token'));
  Route::get('login/reset/finalize_password/{user_id}/{token}', array('as' => 'login.finalize_password', 'uses' => 'UserController@finalize_password'));
  Route::post('login/reset/finalize_password', array('as' => 'login.complete_reset', 'uses' => 'UserController@complete_reset'));
});

Route::group(array('before' => 'auth'), function() {
	Route::get('user/logout', array("as" => "user.logout", 'uses' => "UserController@logout"));
	Route::post('user/leaveteam', array('uses' => 'UserController@leaveteam'));
	Route::get('/team/create', array('as' => 'team.create', "uses" => 'TeamController@create'));
	Route::post('team', array('as' => 'team.store', 'uses' => 'TeamController@store'));
	Route::post('notification/{id}/mark', array('as' => 'notification.mark', 'uses' => 'NotificationController@mark'));
});

Route::group(array('before' => 'auth|register_lineup'), function() {
  Route::post('tournament/{id}/register', array('as' => 'tournament.register', 'uses' => 'TournamentController@register'));
});

Route::group(array('before' => 'auth|perm:codes'), function() {
	Route::get('code/create', array('as' => 'code.create', 'uses' => 'CodeController@create'));
	Route::post('code', array('as' => 'code.store', 'uses' => 'CodeController@store'));
});

Route::group(array('before' => 'auth|perm:create_notifications'), function() {
	Route::get('notification/create', array('as' => 'notification.create', 'uses' => 'NotificationController@create'));
	Route::post('notification', array('as' => 'notification.store', 'uses' => 'NotificationController@store'));
});

Route::group(array('before' => "auth|is_user"), function() {
	Route::get('user/{id}/edit', array('as' => 'user.edit', 'uses' => 'UserController@edit'));
	Route::post('user/{id}', array('as' => 'user.update', 'uses' => 'UserController@update'));
	Route::post('user/{id}/changepic', array('as' => 'user.changepic', 'uses' => 'AssetController@uploadProfileImage'));
});

//TODO make can_report
Route::group(array('before' => "auth|can_report:match"), function() {
  Route::get('match/{id}/report/{override?}', array('as' => 'match.report', 'uses' => 'MatchController@report'));
  Route::post('match/{id}/report', array('as' => 'match.report_default', 'uses' => 'MatchController@report_default')); 
	Route::get('match/{id}/wizard/{gno?}', array('as' => 'match.wizard', 'uses' => 'MatchController@wizard'));
	Route::get('match/{id}/wizard/{gno?}/nextgame', array('as' => 'match.wizard.nextgame', 'uses' => 'MatchController@nextgame'));
});

Route::group(array('before' => "auth|can_report:game"), function() {
	Route::post('game/{id}', array('as' => 'game.report', 'uses' => 'GameController@report'));
	Route::post('asset/replay/{id}', array('as' => 'replay.upload', 'uses' => 'AssetController@uploadReplay'));
});

Route::get('game/forfeit', array('as' => 'game.forfeit', function() { return View::make('game/forfeit'); }));

Route::group(array('before' => "auth|can_manage_team_members"), function() {
  Route::post('team/{id}/add', array('as' => 'team.add', 'uses' => 'TeamController@add'));
});

// TODO make team_owner and team_officer, team_captain
Route::group(array('before' => 'auth|team_owner'), function() {
  Route::put('team/{id}/addcontact', array('as' => 'team.addcontact', 'uses' => 'TeamController@addcontact'));
	Route::put('team/{id}/addleader', array('as' => 'team.addleader', 'uses' => 'TeamController@addleader'));
  Route::get('lineup/create/{id}', array('as' => 'lineup.create', 'uses' => "LineupController@create"));
  Route::delete('lineup/{id}', array('as' => 'lineup.delete', 'uses' => "LineupController@destroy"));
	Route::post('team/{id}/lineup', array('as' => 'lineup.store', 'uses' => "LineupController@store"));
});

Route::group(array('before' => 'auth|lineup_captain'), function() {
  Route::post('lineup/{id}', array('as' => 'lineup.update', 'uses' => 'LineupController@update'));
	Route::post('team/{id}/addmembers', 'TeamController@add');
	Route::put('team/evict', array('as' => 'team.evict', 'uses' => "TeamController@evict"));
	});

Route::post('lineup/{id}/change_rank', array('before' => 'auth|change_rank', 
                                             'as' => 'lineup.change_rank', 
                                             'uses' => "LineupController@change_rank"));

Route::post('team/{id}/remove', array('before' => 'auth|remove_member',
                                      'as' => 'team.remove',
                                      'uses' => 'TeamController@remove'));

Route::group(array('before' => 'auth|lineup_captain_on_team'), function() {
	Route::get('/team/{id}/edit', array('as' => 'team.edit', "uses" => "TeamController@edit"));
});

Route::group(array('before' => 'auth|lineup_officer'), function() {
  Route::get('team/{id}/modify', array('as' => 'team.editinfo', "uses" => "TeamController@editinfo"));
	Route::post('lineup/{id}/add_user', array('as' => 'lineup.add_user', 'uses' => "LineupController@add_user"));
	Route::post('lineup/{id}/remove_user', array('as' => 'lineup.remove_user', 'uses' => "LineupController@remove_user"));
});

// TODO fix protection
Route::group(array('before' => 'auth'), function() {
  Route::get('tournament/{id}/manage_rosters', array('as' => 'roster.index', 'uses' => 'RosterController@index'));
  Route::get('roster/create/{match_id}/{lineup_id}', array('as' => 'roster.create',
      'uses' => 'RosterController@create'));
  Route::get('roster/{id}/edit', array('as' => 'roster.edit', 'uses' => 'RosterController@edit'));
  Route::post('roster/{id}', array('as' => 'roster.update', 'uses' => 'RosterController@update'));
});

// TODO protect this
  Route::post('roster', array('as' => 'roster.store', 'uses' => 'RosterController@store'));
  
Route::group(array('before' => 'auth|perm:create_games'), function() {
	Route::get('game/create', array('as' => 'game.create', 'uses' => 'GameController@create'));
	Route::post('game', array('as' => 'game.store', 'uses' => 'GameController@store'));
});

Route::group(array('before' => 'auth|perm:superupser'), function() {
  Route::get('team/{id}/delete', array('as' => 'team.delete', 'uses' => 'TeamController@delete'));
  Route::delete('team/{id}', array('as' => 'team.destroy', 'uses' => 'TeamController@destroy'));
});

Route::get('user/checktaken/{type}/{val}', 'UserController@checkTaken');
Route::get('user/search/{term}', 'UserController@search');
Route::get('user', array('as' => 'user.index', 'uses' => 'UserController@index'));
Route::get('user/{id}', array('as' => 'user.profile', 'uses' => 'UserController@show'));

Route::get('team', array('as' => 'team.index', 'uses' => 'TeamController@index'));
Route::get('team/{id}', array('as' => 'team.profile', 'uses' => 'TeamController@show'));
Route::get('team/search/{term}', array('as' => 'team.search', 'uses' => 'TeamController@search'));

//TODO proper authorization
Route::post('team/{id}', array('as' => 'team.update', 'uses' => 'TeamController@update'));

Route::group(array('before' => 'auth|perm:delete_teams'), function() {
  Route::delete('team/{id}', array('before' => 'deleteteam', 'as' => 'team.destroy', 'uses' => 'TeamController@destroy'));
});

Route::group(array('before' => 'auth|perm:delete_users'), function() {
  Route::delete('user/{id}', array('before' => 'deleteuser', 'as' => 'user.destroy', 'uses' => 'UserController@destory'));
});

Route::group(array('before' => 'auth|perm:admin'), function() {
    Route::get('role/permission', array('as' => 'permission.index', 'uses' => 'PermissionController@index'));
  Route::get('role/permission/create', array('as' => 'permission.create', 'uses' => 'PermissionController@create'));
  Route::post('role/permission', array('as' => 'permission.store', 'uses' => 'PermissionController@store'));
  Route::get('role', array('as' => 'role.index', 'uses' => 'RoleController@index'));
  Route::get('role/create', array('as' => 'role.create', 'uses' => 'RoleController@create'));
  Route::get('role/{id}', array('as' => 'role.profile', 'uses' => 'RoleController@show'));
  Route::get('role/{id}/edit', array('as' => 'role.edit', 'uses' => 'RoleController@edit'));
  Route::put('role/{id}', array('as' => 'role.update', 'uses' => 'RoleController@update'));
  Route::post('role', array('as' => 'role.store', 'uses' => 'RoleController@store'));

    Route::get('giveaway/create', array('as' => 'giveaway.create', 'uses' => 'GiveawayController@create'));
    Route::post('giveaway', array('as' => 'giveaway.store', 'uses' => 'GiveawayController@store'));
});

// TODO read this if it's broken
// Must remain open until I refactor the javascript in the wizard to user report instead of update
// Route::put('game/{id}', array('as' => 'game.update', 'uses' => 'GameController@update'));
// Route::get('game/{id}', array('as' => 'game.profile', 'uses' => 'GameController@show'));

// admin matches
Route::group(array('before' => 'auth|perm:create_matches'), function() {
	Route::get('match/create', array('as' => 'match.create', 'uses' => 'MatchController@create'));
	Route::post('match', array('as' => 'match.store', 'uses' => 'MatchController@store'));
});

Route::group(array('before' => 'auth|perm:edit_matches'), function() {
	Route::get('match/{id}/edit', array('as' => 'match.edit', 'uses' => 'MatchController@edit'));
  Route::post('match/{id}', array('as' => 'match.update', 'uses' => 'MatchController@update'));
});

//TODO I think these are broken
Route::get('match/{id}', array('as' => 'match.profile', 'uses' => 'MatchController@show'));
Route::get('match/{id}/landing', array('as' => 'match.landing', 'uses' => 'MatchController@landing'));
Route::get('match/{id}/won', array('as' => 'match.won', 'uses' => 'MatchController@won'));

Route::group(array('before' => 'auth|perm:create_groups'), function() {
	Route::get('group/create', array('as' => 'group.create', 'uses' => 'GroupController@create'));
	Route::post('group', array('as' => 'group.store', 'uses' => 'GroupController@store'));
	Route::post('group/{id}/generatematch', array('as' => 'group.generate', 'uses' => 'GroupController@generatematch'));
});

Route::get('group', array('as' => 'group.index', 'uses' => 'GroupController@index'));
Route::get('group/{id}', array('as' => 'group.profile', 'uses' => 'GroupController@show'));

Route::group(array('before' => 'auth|perm:create_rounds'), function() {  
  Route::get('round/create', array('as' => 'round.create', 'uses' => 'RoundController@create'));
	Route::post('round', array('as' => 'round.store', 'uses' => 'RoundController@store'));
	Route::post('round/{id}/generatematches', array('as' => 'round.generate', 'uses' => 'RoundController@generatematches'));
});

Route::group(array('before' => 'auth|perm:create_tournaments'), function() {
  Route::get('tournament/create', array('as' => 'tournament.create', 'uses' => 'TournamentController@create'));
	Route::post('tournament', array('as' => 'tournament.store', 'uses' => 'TournamentController@store'));
  Route::post('season', array('as' => 'tournament.store_season', 'uses' => 'TournamentController@store_season'));
});

//TODO maybe editing of groups and rounds should be moved elsewhere?
Route::group(array('before' => 'auth|perm:edit_tournaments'), function() {
	Route::get('tournament/{id}/edit', array('as' => 'tournament.edit', 'uses' => 'TournamentController@edit'));
	Route::get('tournament/{id}/edit/groups', array('as' => 'tournament.groups', 'uses' => 'TournamentController@groups'));
	Route::get('tournament/{id}/edit/round', array('as' => 'tournament.round', 'uses' => 'TournamentController@round'));
	Route::put('tournament/{id}', array('as' => 'tournament.update', 'uses' => 'TournamentController@update'));
	Route::post('tournament/{id}/start', array('as' => 'tournament.start', 'uses' => 'TournamentController@start'));
});

// TODO I give up
Route::get('tournament', array('as' => 'tournament.index', 'uses' => 'TournamentController@index'));
Route::get('tournament/{id}', array('as' => 'tournament.profile', 'uses' => 'TournamentController@show'));
Route::get('tournament/{id}/phase/{phase}', array('as' => 'tournament.filterphase', 'uses' => 'TournamentController@show'));

Route::post('tournament/{id}/leave', array('as' => 'tournament.leave', 'uses' => 'TournamentController@leave'));

Route::get('dogetip/create/{id?}', array('as' => 'dogetip.create', 'uses' => 'DogetipController@create'));
Route::get('dogetip/list/{confirmation?}', array('as' => 'dogetip.list', 'uses' => 'DogetipController@index'));
Route::post('dogetip', array('as' => 'dogetip.store', 'uses' => 'DogetipController@store'));
Route::get('dogetip/scan', array('as' => 'dogetip.scan', 'uses' => 'DogetipController@scan'));
Route::get('dogetip/{id}', array('as' => 'dogetip.show', 'uses' => 'DogetipController@show'));

Route::get('giveaway/{id?}', array('as' => 'giveaway.index', 'uses' => 'GiveawayController@index'));
Route::post('giveaway/{id}/enter', array('as' => 'giveaway.enter', 'uses' => "GiveawayController@enter"));
Route::get('giveaway/{id}/success', array('as' => 'giveaway.success', 'uses' => 'GiveawayController@success'));
