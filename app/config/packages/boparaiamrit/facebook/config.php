<?php 

return array(
	'secret' => array(
		//put your app id and secret
								'appId'  => '1437821873132248',
						  	'secret' => 'b1560fc5e4b095793843775ffa3d04b2'
							),
	//Redirect after successfull login
	'redirect' => route('giveaway.index'),
	//When Someone Logout from your Site
	'logout' => route('giveaway.index'),
	//you can add scope according to your requirement
	'scope' => 'user_likes'
	);
