<?php
/* RateLimitFrame : This is the number of hit you can make on League of Legends API within a numbers of seconds.
 * It's defined by the number of hits you can make ($pHitLimit) during a periods of seconds ($pTimeLimit).
 * When the first hit is call or when $pTime as expired ($pTime+$pTimeLimit), we set $pTime to the current time()
 * each call to hit will increase $pHit by 1 and return true elseif $pHit+1 > $pHitLimit the function will return false.
 */
class RateLimitFrame
{
	private $pTimeLimit; // Number of seconds for the frame
	private $pHitLimit; // Number max of hits for the frame
	private $pTime; // Frame start
	private $pHit; // Current hits
	
	public function __construct($HitLimit, $TimeLimit)
	{
		$this->pHitLimit = $HitLimit;
		$this->pTimeLimit = $TimeLimit;
	}
	
	
	public function hit()
	{
		if($this->getAvailableHit() >= 1)
		{
			if($this->getAvailableHit() == $this->pHitLimit)
				$this->pTime = time(); // If available hit == hit limit; No hit has been made so start the timer from the first hit.
		
			$this->pHit++;
			return true;
		}

		return false;
	}
	
	
	/* This function return the number of hits available before the frame expires.
	 */
	public function getAvailableHit()
	{
		if($this->getTimeLeft() < 0)
		{
			// The previous frame as expired; We reset the current number of hits.
			$this->pHit = 0;
		}
		
		return ($this->pHitLimit-$this->pHit);
	}
	
	
	/* This function return the numbers of seconds before the frame expires.
	 */
	public function getTimeLeft()
	{
		return (($this->pTime+$this->pTimeLimit)-time());
	}
	
	
	/* This function return the frame duration.
	 */
	public function getFrameDuration()
	{
		return $this->pTimeLimit;
	}
	
	
	/* This function return the max number of hits for the frame duration.
	 */
	public function getHitLimit()
	{
		return $this->pHitLimit;
	}
}


/* The rate limit handles multiple RateLimitFrame and make sure that each frame can take the hit before actually hit.
 * For example, let's say you have the following rate limits:
 * 100 calls per 1 second
 * 1,000 calls per 10 seconds
 * 60,000 calls per 10 minutes (600 seconds)
 * 360,000 calls per 1 hour (3600 seconds)
 * Check are done from the largest frame to the lowest. In this example 360000:3600 > 60000:600 > 1000:10 > 100:1. Since lower frame reset faster then largest
 * they should be available, but you will abuses the rate limits if you actually hit, leading to blacklist your apikey from League of Legends API.
 * RateLimit handle this. Use it.
 */
class RateLimit
{
	private $Frames;
	
	public function add(RateLimitFrame $frame)
	{
		$this->Frames[$frame->getFrameDuration()] = $frame;
		krsort($this->Frames);
	}
	
	
	/* Hit on all RateLimitFrame.
	 * Return true on success or false if one or more frame can't take the hit.
	 */
	public function hit()
	{
		foreach($this->Frames as $frame)
		{
			if($frame->getAvailableHit() == 0)
				return false;
		}
		
		foreach($this->Frames as &$frame)
		{
			if(!$frame->hit())
				return false;
		}
		
		return true;
	}
	
	
	/* This function return an array containing all frame duration available
	 */
	public function listFrame()
	{
		$list = array();
		foreach($this->Frames as $k => $v)
		{
			$list[] = $k;
		}
		
		return $k;
	}
	
	
	/* This function return a RateLimitFrame where the duration is set to $frameKey.
	 */
	public function getFrame($frameKey)
	{
		if(isset($this->Frames[$frameKey]) && !empty($this->Frames[$frameKey]))
			return $this->Frames[$frameKey];
		
		return null;
	}
	
	
	/* This function return the number of seconds before the next available hit.
	 */
	public function getTimeBeforeNextHit()
	{
		foreach($this->Frames as $frame)
		{
			if($frame->getAvailableHit() == 0)
				return $frame->getTimeLeft(); // No Hit available. Time left before the frame reset.
		}
		
		return 0; // Hit already available
	}
}
?>