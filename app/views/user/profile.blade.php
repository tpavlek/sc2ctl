@extends('layout')
@section('title')
	{{ $user->username }}'s Profile
@stop

@section('content')
<div class="padded-content splash colour-purple">
<div class="pure-g-r">
	@if (Sentry::check() && Sentry::getUser()->id == $user->id)
	<div class="pure-u-1">
		<h3>Notifications</h3>
		<div class="notification-box">
			@include('notification/notificationsPartial')
		</div>
	</div>
	@endif
	
	<div class="pure-u-1-3">
		<h3>User</h3>
		@include('user/profileCardPartial')
		@if ($user->team_id != 0)
			<h3>Team</h3>
			@include('team/profileCardPartial', array('team' => $user->team))
	  @endif
	</div>
	<div class="pure-u-1-3">
		<h3>Recent Games</h3>
		@if ($games->count())
      <table class="pure-table">
        <thead>
          <tr>
            <th>Vs.</th>
            <th>Result</th>
            <th>Actions</th>
          </tr>
        </thead>
			@foreach ($games as $game)
        <tr>
          <td>{{ $game->opponent($user->id)->bnetInfo() }}</td>
          <td>{{ $game->won($user->id) }}</td>
          <td>
            <a class="pure-button pure-button-primary" href="{{ URL::route('game.profile', $game->id) }}">
					    View Game
				    </a>
          </td>
        </tr>
			@endforeach
      </table>
		@else
			<span class="splash-subhead">No games played yet</span>
		@endif
	</div>
	<div class="pure-u-1-3">
		<h3>Statistics</h3>
		<table class="pure-table">
			<thead>
				<tr>
					<th>Wins</th>
					<th>Losses</th>
					<th>%</th>
				</tr>
			</thead>
			<tr>
				<td>{{ $wins }}</td>
				<td>{{ $losses }}</td>
				<td>{{ $ratio }}</td>
			</tr>
		</table>
	</div>
</div>
</div>
@if (Sentry::check() && Sentry::getUser()->id == $user->id)
  <a href="{{ URL::route('user.edit', $user->id) }}" class="pure-button pure-button-primary">
	  Edit Profile
  </a>
@endif
@stop
