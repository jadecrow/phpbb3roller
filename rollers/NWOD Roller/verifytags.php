<?php
function dotags($tags, $topic)
{
	global $auth, $db, $user, $config, $template;
	global $phpEx, $phpbb_root_path;
	
	include_once($phpbb_root_path . 'roller/evalmath.class.' . $phpEx); // Need the maths
	
	$results;
	foreach ($tags as $tag) 
	{
		$tag = str_replace(array( '{', '}' ), '', $tag);
		$tag = explode(" ", $tag);
		$secondparam = "";
		for($i = 1; $i < count($tag); $i++)
		{
			$secondparam = $secondparam." ".$tag[$i];
		}
		$m = new EvalMath;
		$math = $m->evaluate($secondparam);
		switch(strtoupper($tag[0]))
		{
			case "ROLL":
				$results .= roll($math, 10);
				break;
			case "9AGAIN":
				$results .= roll($math, 9);
				break;
			case "8AGAIN":
				$results .= roll($math, 8);
				break;
			case "9-AGAIN":
				$results .= roll($math, 9);
				break;
			case "8-AGAIN":
				$results .= roll($math, 8);
				break;
			default:
				$results .= "<br />The $tag[0] command is not recognized. <br />";
				// return false;
		}
    }
	return $results;
}
function roll($dice = 1)
{
	$dice = intval($dice, $again = 10);
	if($dice < 1)
		$chancedie = true;
	if($dice > 50)
	{
		return "Too many dice at once.";
	}
	$result = "";
	$reroll = 0;
	$successes = 0;
	$rdotorg = true;
	$rollbunch = randnum($dice, 1, 10);
	for($i = 0; $i < $dice; $i++)
	{
		$thisgo = $rollbunch[$i];
		if(!is_int($thisgo) or !$rdotorg)
		{
			$thisgo = mt_rand(1,10);
			$rdotorg = false;
		}
		if($thisgo > ($again - 1))
			$reroll++;
		if($chance and $thisgo > 9)
			$success++;
		else if($thisgo > 7)
			$successes++;
		$result .= $thisgo." ";
	}
	if(!$rdotorg)
		$result = "* ".$result;
	if ($chancedie)
		return "Chance Roll! ".mt_rand(1,10)."*<br>";
	else if($reroll < 1)
		return $result."// Successes: ".$successes."<br>";
	else
		return $result."// Successes: ".$successes."<br> Rerolls: ".roll($reroll);
}
function randnum($dice = 0, $min = 1, $max = 10)
{
	usleep(10000);
	// Validate parameters
    $max = ((int) $max >= 1) ? (int) $max : 100;
    $min = ((int) $min < $max) ? (int) $min : 1;
    // Curl options
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => '',
        CURLOPT_USERAGENT => 'PHP',
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_MAXREDIRS => 10,
    );
	
	$ch = curl_init('http://www.random.org/integers/?num='.$dice.'&min='
        . $min . '&max=' . $max . '&col=1&base=10&format=plain&rnd=new');
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    curl_close($ch);
    return explode(PHP_EOL, $content);
}
function outputpost($forum_id, $topic_id, $content) // Old method. Is a bit messed up because it posts the username of the poster instead of the roller
{
	global $auth, $db, $user, $config, $template;
	
   // Start session management
   $user->session_begin();
   
	// Backup User Data
   $backup = array(
      'user'   => $user,
      'auth'   => $auth,
   );
   
   // Change User
   $user_id = 2; // Valid ID for registered user
   $sql = 'SELECT *
      FROM ' . USERS_TABLE . '
      WHERE user_id = ' . $user_id;
   $result = $db->sql_query($sql);
   $row = $db->sql_fetchrow($result);
   $db->sql_freeresult($result);
   
   $user->data = array_merge($user->data, $row);
   $auth->acl($user->data);
   
   $user->ip = '0.0.0.0';
   
   $user->setup();
   
   // note that multibyte support is enabled here 
   $my_subject = "Roll";
   $my_text    = $content;
   
   // variables to hold the parameters for submit_post
   $poll = $uid = $bitfield = $options = ''; 
   
   generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
   generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);
   
   $data = array( 
      'forum_id'      => $forum_id,    // Desired Forum ID
      'topic_id'      => $topic_id,    // Desired Topic ID
      'icon_id'       => false,
   
      'enable_bbcode'     => true,
      'enable_smilies'    => true,
      'enable_urls'       => true,
      'enable_sig'        => true,
   
      'message'       => $my_text,
      'message_md5'   => md5($my_text),
               
      'bbcode_bitfield'   => $bitfield,
      'bbcode_uid'        => $uid,
   
      'post_edit_locked'  => 0,
      'topic_title'       => $my_subject,
      'notify_set'        => false,
      'notify'            => false,
      'post_time'         => 0,
      'forum_name'        => '',
      'enable_indexing'   => true,
      
      'force_approved_state'    => true,
   );
   
   submit_post('reply', $my_subject, '', POST_NORMAL, $poll, $data);
   
   // Restore User Data
   $user = $backup['user'];
   $auth = $backup['auth'];
   
	/*
 // This posts a roll
	global $db;
	global $phpEx, $phpbb_root_path;
	include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

	$roller = $user->data['username'];

	$backup = array(
	'user'   => $user,
	'auth'   => $auth,
	);

	$user_id = 2;

	$sql = 'SELECT *
	FROM ' . USERS_TABLE . '
	  WHERE user_id = ' . $user_id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	// $user->data = array_merge($user->data, $row);
	// $auth->acl($user->data);

	// $user->ip = '0.0.0.0';

	$post_data['topic_title'] = "Roll";
	$post_data['forum_id'] = $forum_id;
	$post_data['topic_id'] = $topic_id;
	$post_data['icon_id'] = 0;
	$post_data['enable_bbcode'] = 0;
	$post_data['enable_smilies'] = 0;
	$post_data['enable_urls'] = 0;
	$post_data['enable_sig'] = 0;
	$post_data['message'] = $content;
	$post_data['message_md5'] = md5($content);
	$post_data['bbcode_bitfield'] = "";
	$post_data['bbcode_uid'] = "";
	$post_data['post_edit_locked'] = 1;

	submit_post('reply', $post_data['topic_title'], "Roller", 'POST_NORMAL', $poll, $post_data, $update_message, ($update_message || $update_subject) ? true : false);
	//submit_post('reply', $post_data['topic_title'], "", $post_data); 

	$user = $backup['user'];
	$auth = $backup['auth'];
	*/
}
?>