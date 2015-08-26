<?php		
function post($forum_id, $topic_id, $content)
{
	global $db;
	global $phpEx, $phpbb_root_path;
	include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

	$roller = $user->data['username'];
	$user_id = 2;

	$topic_title = "Roll";
	$icon_id = 0;
	$message = $content;
	$message_md5 = md5($content);

	$sql = 'INSERT INTO ' . 'forum_posts' . ' (poster_id, topic_id, forum_id, post_subject, post_text, post_checksum, post_edit_locked, post_visibility, post_time) VALUES 
											  (2, '.$topic_id.', '.$forum_id.', "Roll", "'.$message.'", "'.$message_md5.'", 1, 1, '.time().')';
	$db->sql_query($sql);
	
	$sql = 'UPDATE ' . 'forum_topics' . ' SET topic_posts_approved = topic_posts_approved + 1 WHERE topic_id = '.$topic_id;
	$db->sql_query($sql);
}
function post2($forum_id, $topic_id, $content) // Old method. Is a bit messed up because it posts the username of the poster instead of the roller
{ // This posts a roll
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
}
?>