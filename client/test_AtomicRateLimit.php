<?php
require '../server/RateLimit.php';

$nb_hit = 0;
if(isset($_GET["hit"]) && !empty($_GET["hit"]))
	$nb_hit = $_GET["hit"];
else
	$nb_hit = rand(1, 100);

function test_hit($RateLimit, $amount_hits)
{
	for($i=0; $i<$amount_hits; $i++)
	{
		if(!$RateLimit->hit())
			break;
	}
}

/*
 * 100 calls per 1 second
 * 1,000 calls per 10 seconds
 * 60,000 calls per 10 minutes (600 seconds)
 * 360,000 calls per 1 hour (3600 seconds)
 */
 
$dDATA = array();
$dDATA["lock"] = 0;
$dDATA["hit"] = $nb_hit;
$RateLimit = new RateLimit();
$RateLimit->add(new RateLimitFrame(100, 1));
$RateLimit->add(new RateLimitFrame(1000, 10));
$RateLimit->add(new RateLimitFrame(60000, 600));
$RateLimit->add(new RateLimitFrame(360000, 3600));

$memcache_obj = memcache_connect('localhost', 11211);

$t1 = microtime(true);
for($t=0;$t<$nb_hit; $t++)
{
	// Try to set a mutex on $RateLimit
	$t1_mutex = microtime(true);
	$RateLimitMutex = false;
	do
	{
		$RateLimitMutex = $memcache_obj->add('GrompTestAtomic_RateLimitMutex', uniqid(), null, 5);
		if($RateLimitMutex)
		{
			$dDATA["lock"] += (microtime(true)-$t1_mutex);
			break;
		}
	}while(!$RateLimitMutex);

	// Try to access $RateLimit
	if(!$memcache_obj->add('GrompTestAtomic_RateLimit', serialize($RateLimit)))
		$RateLimit = unserialize($memcache_obj->get('GrompTestAtomic_RateLimit')); // $RateLimit already exists, retrieve data


	test_hit($RateLimit, 1);

	// Update $RateLimit status
	$memcache_obj->set('GrompTestAtomic_RateLimit', serialize($RateLimit));
	// Remove the mutex on $RateLimit
	$memcache_obj->delete('GrompTestAtomic_RateLimitMutex');
}
$t2 = microtime(true);

$dDATA["generated"] = ($t2-$t1);
$dDATA["lock"] /= $dDATA["hit"];

echo json_encode($dDATA, true);

?>