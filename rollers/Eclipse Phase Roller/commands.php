<?php
function multiroll($dice, $add = 0)
{
	if($dice > 25)
	{
		return "Too many dice at once.";
	}
	$sum = 0;
	$result = "";
	for($i = 0; $i < $dice; $i++)
	{
		$thisgo = randnum(1, 10);
		$result .= $thisgo." ";
		$sum += $thisgo;
	}
	return $result." Sum: ".($sum + $add);
}
function roll($target)
{ // returns 1 1d100
	if ($target == "")
		$target = -1;
	$string = array(randnum(), 0);
	usleep(1000);
	$string[1] = randnum();
	$result = $string[0].", ".$string[1];
	$sum = ($string[0]*10) + $string[1];
	$critical = "";
	if($string[0] == $string[1])
		$critical = "critical ";
	/* Old Fudge Dice stuff
	
	foreach($string as $die)
	{
		if($die < 3)
		{
			$result .= "- ";
			$sum--;
		}
		elseif($die < 5)
		{
			$result .= "0 ";
		}
		else
		{
			$result .= "+ ";
			$sum++;
		}
	}
	*/
	if($sum == 99)
		return "Critical Success!";
	else if($sum == 0)
		return "Critical Failure!";
	else if ($target < 0)
		return $result." ".$critical;
	else if($sum > $target)
		return $result." (".$critical."failure, margin: ".($sum - $target).") [".$target."]";
	else
		return $result." (".$critical."success, margin: ".$sum.") [".$target."]";
}
function randnum($min = 0, $max = 9)
{
	usleep(1000);
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
	
	$ch = curl_init('http://www.random.org/integers/?num=1&min='
        . $min . '&max=' . $max . '&col=1&base=10&format=plain&rnd=new');
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    curl_close($ch);
    return trim($content);
}
?>