This roller allows users posting in PHPBB3 to roll dice using a bracketed {roll} command. The results are posted under the admin's name, assuming your admin is user 2 as usual.

You have to do a file edit to posting.php and pick a roller out of "rollers" to use. Place the files from the chosen roller into a directory named "roller", put "roller" in your root phpbb3 install directory, and you're all set. 
--------------
The NWoD Roller
--------------
This gives you results compatible with any New World of Darkness games. It accepts both single numbers and equations, and it rolls in the order you give the commands. All of the following are valid:
{roll 1}
{roll 2 + 2}
{RoLl 3 - 2}
---
{roll -20} < Rolls a chance die
It also has the power to round stuff to the nearest whole number.
{roll 3 / 2}
And it can handle order of operations, not that you should need all this...
{roll 3 * 2 + 4 / 2}
If you want to add something to the results, you're a bit out of luck. You can just put something like...
{roll 5} + 3
...and just mental-math on the results later.
---
Here are some 9-Again and 8-Again rolls. If you put a space inbetween the number and "again", bad things will happen...
{9again 5}
{8again 2}
{9-again 1}
{8-again 2}
--------------
The Eclipse Phase Roller
--------------
{roll} gives a plain roll
{roll #} where # is your target number gives whether you succeeded or failed, plus the margin of success or failure.
---
--------------
Edits For PHPBB 3.0
--------------
To get this to work you have to open posting.php in your root PHPBB3 directory and find this around line 1138:
---
			// The last parameter tells submit_post if search indexer has to be run
			$redirect_url = submit_post($mode, $post_data['post_subject'], $post_author_name, $post_data['topic_type'], $poll, $data, $update_message, ($update_message || $update_subject) ? true : false);
---
Under that put this snippet:
---
			$tags = preg_match_all("/[{][^{]*[}]/", $message_parser->message, $matches);
			if ($tags != NULL and $mode != 'edit')
			{
				$nextpost = "";
				include_once($phpbb_root_path . 'roller/verifytags.' . $phpEx);
				foreach($matches as $match)
				{
					$nextpost .= dotags($match, $data['topic_id']);
				}
				outputpost($data['forum_id'], $data['topic_id'], $nextpost);
			}
---
--------------
Edits for PHPBB 3.1
--------------
To get this to work you have to open posting.php in your root PHPBB3 directory and find this around line 1392:
---
			$vars = array(
				'post_data',
				'poll',
				'data',
				'mode',
				'page_title',
				'post_id',
				'topic_id',
				'forum_id',
				'post_author_name',
				'update_message',
				'update_subject',
				'redirect_url',
				'submit',
				'error',
			);
---
Under that put this snippet:
---
			$tags = preg_match_all("/[{][^{]*[}]/", $message_parser->message, $matches);
			if((int) $topic_id < 1)
			{
				echo "Topic ID is 0";
				die();
			}
			if ($tags != NULL)
			{
				include_once($phpbb_root_path . 'roller/verifytags.' . $phpEx);
				foreach($matches as $match)
				{
					dotags($match, $forum_id, $topic_id);
				}
			}
--------------
I'll get automod to do some of this stuff later. 


