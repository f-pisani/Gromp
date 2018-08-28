<?php
require '../server/RateLimit.php';

function test_hit($RateLimit, $amount_hits)
{
	for($i=0; $i<$amount_hits; $i++)
	{
		if(!$RateLimit->hit())
		{
			echo 'Hit stop at #'.$i.'<br/>';
			break;
		}	
	}
}

/*
 * 100 calls per 1 second
 * 1,000 calls per 10 seconds
 * 60,000 calls per 10 minutes (600 seconds)
 * 360,000 calls per 1 hour (3600 seconds)
 */
 
$RateLimit = new RateLimit();
$RateLimit->add(new RateLimitFrame(100, 1));
$RateLimit->add(new RateLimitFrame(1000, 10));
$RateLimit->add(new RateLimitFrame(60000, 600));
$RateLimit->add(new RateLimitFrame(360000, 3600));
echo 'var_dump $RateLimit.';
echo var_dump($RateLimit);

// ---------------------------------------------------------------------------------------------------
echo 'Simulate 98 hit.<br/>';
test_hit($RateLimit, 98);

echo 'var_dump $RateLimit.';
echo var_dump($RateLimit);
// ---------------------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
echo 'Simulate 198 hit.<br/>';
test_hit($RateLimit, 198);

echo 'var_dump $RateLimit.';
echo var_dump($RateLimit);
// ---------------------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
sleep(2);
echo 'Wait 2second and Simulate 72 hit.<br/>';
test_hit($RateLimit, 72);

echo 'var_dump $RateLimit.';
echo var_dump($RateLimit);
// ---------------------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
sleep(11);
echo 'Wait 11seconds and Simulate 88 hit.<br/>';
test_hit($RateLimit, 88);

echo 'var_dump $RateLimit.';
echo var_dump($RateLimit);
// ---------------------------------------------------------------------------------------------------
for($i=0; $i<9; $i++)
{
	// ---------------------------------------------------------------------------------------------------
	sleep(2);
	echo 'Wait 2seconds and Simulate 100 hit.<br/>';
	test_hit($RateLimit, 100);

	echo 'var_dump $RateLimit.';
	echo var_dump($RateLimit);
	// ---------------------------------------------------------------------------------------------------
}
?>