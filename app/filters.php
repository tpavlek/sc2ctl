<?php

use SC2CTL\DotCom\Filters\IsUserFilter;
use SC2CTL\DotCom\Filters\RequiresBnetFilter;
use SC2CTL\DotCom\ViewComposers\ErrorPartialComposer;

App::before(function ($request) {
    //
});


App::after(function ($request, $response) {
    //
});

/*
 * Register the filters for our application domain
 */

Route::filter('is_user', IsUserFilter::class);
Route::filter('requires_bnet', RequiresBnetFilter::class);

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function ($route, $request) {
    if (!Auth::check()) {
        Session::put('redirect', URL::current());
        return Redirect::route('user.login');
    }
});


Route::filter('auth.basic', function () {
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function () {
    if (Auth::check()) {
        return Redirect::route('home.index');
    }
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function () {
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

Route::filter('perm', function ($route, $request, $value) {
    $perms = explode(',', $value);
    $user = Auth::getUser();
    foreach ($perms as $perm) {
        if (!$user->hasAccess($perm)) {
            App::abort('401', "You do not have permission to do that");
        }
    }
});
Route::filter('register_lineup', function ($route, $request) {
    $lineup_id = Input::get('lineup_id');
    if (!Auth::getUser()->registerableLineups()->contains($lineup_id)) {
        App::abort('401', "You're not authorized to do that");
    }

});
Route::filter('can_report', function ($route, $request, $value) {
    if ($value == "match") {
        $obj = Match::findOrFail($route->getParameter('id'));
    } else if ($value == "game") {
        $obj = Game::findOrFail($route->getParameter('id'));
    }
    if (!$obj->canReport(Auth::getUser())) {
        App::abort('401', "You're not Authorized to do that");
    }
});

Route::filter('create_roster', function ($route, $request) {
    $lineup = Lineup::findOrFail($route->getParameter('lineup_id'));
    if (!$lineup->canCreateRoster(Auth::getUser())) {
        App::abort('401', "You're not authorized to do that!");
    }

});

Route::filter('remove_member', function ($route, $request) {
    $team = Team::findOrFail($route->getParameter('id'));
    if (!$team->canRemoveMembers(Auth::getUser())) {
        if (!Input::get('user_id') == Auth::getUser()->id) {
            App::abort('401', "You're not Authorized to do that");
        }
    }
});

Route::filter('change_rank', function ($route, $request) {
    $lineup = Lineup::findOrFail($route->getParameter('id'));
    if (!$lineup->canChangeRanks(Auth::getUser())) {
        App::abort('401', "You're not Authorized to do that");
    }
});
Route::filter('lineup_captain', function ($route, $request) {
    $lineup = Lineup::findOrFail($route->getParameter('id'));
    if (!$lineup->canRename(Auth::getUser())) {
        App::abort('401', "You're not Authorized to do that");
    }
});

Route::filter('lineup_captain_on_team', function ($route, $request) {
    $team = Team::findOrFail($route->getParameter('id'));
    /*if (!$lineup->canRename(Auth::getUser())) {
      App::abort('401', "You're not Authorized to do that");
    }*/
    //Todo
});


View::composer('team/profile', function ($view) {
    if (!isset($view['edit'])) {
        $view->with('edit', false);
    } else {
    }
});
View::composer(array('team/profileCardPartial', 'team/lineup/profileCardPartial'), function ($view) {
    if (!isset($view['smallCard'])) {
        $view->with('smallCard', false);
    }
});

View::composer(array('team/lineupPartial'), function ($view) {
    $view->with('select', $view['lineup']->team->availablePlayers()->lists('qualified_name', 'id'));
});

View::composer(array('match/matchCardPartial'), function ($view) {
    $score = $view['match']->score();
    while (count($score) < 2) {
        $score['NULL#' . uniqid()] = array('wins' => 0, 'losses' => 0, 'id' => 0, 'won' => false);
    }
    $view->with('matchScore', $score);
    $view->with('keys', array_keys($score));

});

View::composer('user/profileCardPartial', function ($view) {
    if (!isset($view['smallCard'])) {
        $view->with('smallCard', false);
    }
    if (!isset($view['dispTip'])) {
        $view->with('dispTip', false);
    }

    if (!isset($view['dispCharcode'])) {
        $view->with('dispCharcode', true);
    }

    if (!isset($view['win'])) {
        $view->with('win', false);
    }

    if (!isset($view['loss'])) {
        $view->with('loss', false);
    }

    if (!isset($view['is_default'])) {
        $view->with('is_default', false);
    }

    if (!isset($view['replay_url']) || $view['replay_url'] == NULL || $view['replay_url'] == "") {
        $view->with('replay_url', "#");
    }
});

View::composer('errors/errorPartial', ErrorPartialComposer::class);

View::composer('dogetip/create', function ($view) {
    $default = ($view['user_id']) ? $view['user_id'] : null;
    $view->with('default', $default);
    $view->with('players', User::listAll());
});

