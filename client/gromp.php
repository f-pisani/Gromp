<?php
class Gromp
{
	/**************************************************************************************************
	 * PRIVATE MEMBERS
	 **************************************************************************************************/
	private $request_url;
	
	/**************************************************************************************************
	 * PUBLIC METHODS
	 **************************************************************************************************/
	public function __construct($uri)
	{
		$this->request_url = $uri;
	}
	
	
	
	public function __destruct()
	{
	}
	
	
	
	/**************************************************************************************************
	 * PUBLIC METHODS (Query implementation)
	 **************************************************************************************************/	
	// Championmastery
	public function GetChampionMastery($region, $summoner_id){
		$Q = $this->json_formatted_query('championmastery',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	
	
	// Current-Game
	public function GetCurrentGame($region, $summoner_id){
		$Q = $this->json_formatted_query('currentgame',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	
	
	// Featured-games
	
	
	
	// Game
	
	
	
	// League
	public function GetLeague($region, $summoner_id){
		$Q = $this->json_formatted_query('league',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	
	
	// lol-static-data
	
	
	
	// lol-status
	
	
	
	// Match
	public function GetMatch($region, $match_id){
		$Q = $this->json_formatted_query('match',
										['region' => $region, 
										 'id' => $match_id]);
		return $this->send($Q);
	}
	
	
	
	// Matchlist
	public function GetMatchlist($region, $summoner_id){
		$Q = $this->json_formatted_query('matchlist',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	
	
	// Stats
	public function GetStatsRanked($region, $season, $summoner_id){
		$Q = $this->json_formatted_query('stats-ranked',
										['region' => $region, 
										 'season' => $season,
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	public function GetStatsSummary($region, $summoner_id){
		$Q = $this->json_formatted_query('stats-summary',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	
	
	// Summoner
	public function GetSummonerByID($region, $summoner_id){
		$Q = $this->json_formatted_query('summonerbyid',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	public function GetSummonerByName($region, $summoner_name){
		$Q = $this->json_formatted_query('summonerbyname',
										['region' => $region, 
										 'id' => $summoner_name]);
		return $this->send($Q);
	}
	
	public function GetSummonerRunes($region, $summoner_id){
		$Q = $this->json_formatted_query('summoner-runes',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	public function GetSummonerMasteries($region, $summoner_id){
		$Q = $this->json_formatted_query('summoner-masteries',
										['region' => $region, 
										 'id' => $summoner_id]);
		return $this->send($Q);
	}
	
	
	
	// Team
	
	
	
	/**************************************************************************************************
	 * PRIVATE METHODS
	 **************************************************************************************************/
	private function json_formatted_query($QueryName, $Params)
	{
		$Q['QueryName'] = $QueryName;
		$Q['QueryParams'] = $Params;
		
		return json_encode($Q);
	}
	
	/* This function return a comma-separated string by splitting and array values.
	 * If $array is already a string; it will just return the string without changes.
	 */
	private function array_to_string($array)
	{
		if(is_array($array))
		{
			$str = "";
			for($i=0; $i<count($array); $i++){
				$str .= $array[$i];
				if($i+1 < count($array))
					$str .= ",";
			}
			
			return $str;
		}
		
		return $array;
	}
	
	
	
	
	/* This function send $write to the server and retrieve the answer.
	 * No formatting is done.
	 */
	private function send($query)
	{
		$result = file_get_contents($this->request_url."?data=".rawurlencode($query));

		return $result;
	}
}
?>