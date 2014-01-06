<?php
use Cartalyst\Sentry\Users\Eloquent\User as SentryUserModel;

class User extends SentryUserModel {
	
	protected $fillable = array('username', 'password', 'email', 'bnet_url', 'bnet_id', 'bnet_name', 'char_code', 'league', 'img_url', 'team_id');

	protected $guarded = array('id');
		
	public function notifications() {
		return $this->belongsToMany('Notification')->withPivot('read')->withTimestamps();
	}

  public function getPassword() {
    return $this->password;
  }

	public function team() {
		return $this->belongsTo('Team');
	}

  public static function validates($input) {
    $rules = array(
      'username' => 'required|alpha_dash|between:3,80|unique:users',
      'email' => 'email|unique:users|required',
      'bnet_name' => 'required|alpha_num|between:3,80',
      'bnet_id' => 'required|numeric|unique:users',
      'char_code' => 'required|numeric',
      'league' => 'required|in:Bronze,Silver,Gold,Platinum,Diamond,Master,Grandmaster',
      'bnet_url' => 'required|url',
      'password' => 'required|confirmed',
        );
    return Validator::make($input, $rules);
  }

	/**
	* Takes an array of tournament ids
	*/
	public function hasPlayedGamesInTournaments($ids) {
		$played = DB::table('games')
		                ->leftJoin('matches', 'games.match_id', '=', 'matches.id')
										->leftJoin('groups', 'matches.group_id', '=', 'groups.id')
										->whereIn('groups.tournament_id', $ids)
										->where(function($query) {
												$query->where('games.player1', '=', $this->id)
													->orWhere('games.player2', '=', $this->id);
										})
										->count();
		return $played;

	}

	public function getWinrate() {
    $wins = Game::where('winner', '=', $this->id)->count();
		$losses = Game::whereRaw('(player1 = ? OR player2 = ?) AND winner > 0 AND winner <> ?',
		                          array($this->id, $this->id, $this->id))->count();
		if ($losses == 0) {
			$ratio = 0;
		} else {
			$ratio = number_format($wins / ($wins + $losses) * 100, 2);
		}

		return array('wins' => $wins, 'losses' => $losses, 'ratio' => $ratio);

	}
	public function canManageTeam($id) {
		if (Entrust::can('manage_teams')) {
			return true;
		} elseif (Entrust::can('manage_own_team')) {
			if ($this->team_id == $id) { 
				return true;
			}
		}
		return false;
	}
	
	public function leaveTeam() {
		$team = $this->team;
		if ($team->members->count() < 2 ) {
			Team::destroy($team->id);
			$this->detachRole(ROLE_TEAM_CAPTAIN);
		} elseif ($team->leader == $this->id) {
			return;
		}

		if ($team->contact == $this->id) {
			$team->contact = $team->leader;
			$team->save();
		}
		$this->team_id = null;
		$this->save();
	}

	static function getCaptains() {
		$arr = array();
		$role = Role::find(ROLE_TEAM_CAPTAIN);
		$users = $role->users()->get();
		foreach ($users as $user) {
			$arr[]= $user->id;
		}
		return $arr;
	}
	static function getAll() {
		return DB::table('users')->lists('id');
	}

  static function listAll() {
    $list = array();
    $users = DB::table('users')->select('id', 'bnet_name', 'char_code')->get();
    foreach ($users as $user) {
      $list[$user->id] = $user->bnet_name . "#" . $user->char_code;
    }
    return $list;
  }

}
