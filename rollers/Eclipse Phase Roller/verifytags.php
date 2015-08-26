<?php

function dotags($tags, $forum, $topic)
{
	global $auth, $db, $user, $config, $template;
	global $phpEx, $phpbb_root_path;
	include_once($phpbb_root_path . 'roller/commands.' . $phpEx);
	include_once($phpbb_root_path . 'roller/utilities.' . $phpEx);
	
	$results;
	foreach ($tags as $tag) 
	{
		$tag = str_replace(array( '{', '}' ), '', $tag);
		$tag = explode(" ", $tag);

		switch(strtoupper($tag[0]))
		{
			case "ROLL":
				// Do attack post
				$results .= roll($tag[1])."<br>";
				break;
			case "MULTIROLL":
				if($tag[2])
					$results .= multiroll($tag[1], $tag[2])."<br>";
				else
					$results .= multiroll($tag[1])."<br>";
				break;
			default:
				$results .= "<br />The $tag[0] command is not recognized. <br />";
				echo $results;
				die();
				// return false;
		}
    }
	post($forum, $topic, $results);
	return $results;	
}

?>