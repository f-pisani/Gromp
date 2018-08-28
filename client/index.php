<pre>
<?php
require_once 'gromp.php';

$Gromp = new Gromp('http://localhost:8080/gromp/server/index.php');

$t1 = microtime(true);
	$summoner = $Gromp->GetSummonerByName('euw', 'Banana Blood');
	echo $summoner."\r\n";
	echo "---------------\r\n";
	foreach(json_decode($summoner, true) as $summoner_data)
	{
		$summoner_runes = $Gromp->GetSummonerRunes('euw', $summoner_data["id"]);
		$summoner_masteries = $Gromp->GetSummonerMasteries('euw', $summoner_data["id"]);
		$summoner_championmasteries = $Gromp->GetChampionMastery('euw', $summoner_data["id"]);
		$summoner_league = $Gromp->GetLeague('euw', $summoner_data["id"]);
		$summoner_rankedstats = $Gromp->GetStatsRanked('euw', 'SEASON2016', $summoner_data["id"]);
		$summoner_currentGame = $Gromp->GetCurrentGame('euw', $summoner_data["id"]);
		$summoner_matchlist = $Gromp->GetMatchlist('euw', $summoner_data["id"]);
		
		echo $summoner_runes."\r\n";
		echo "---------------\r\n";
		echo $summoner_masteries."\r\n";
		echo "---------------\r\n";
		echo $summoner_championmasteries."\r\n";
		echo "---------------\r\n";
		echo $summoner_league."\r\n";
		echo "---------------\r\n";
		echo $summoner_rankedstats."\r\n";
		echo "---------------\r\n";
		echo $summoner_currentGame."\r\n";
		echo "---------------\r\n";
		echo $summoner_matchlist."\r\n";
		echo "---------------\r\n";
		
		$summoner_matchlist = json_decode($summoner_matchlist, true);
		for($i=0; $i<10; $i++)
		{
			$summoner_matches[$i] = $Gromp->Getmatch('euw', $summoner_matchlist["matches"][$i]["matchId"]);
			
			echo $summoner_matches[$i]."\r\n";
			echo "---------------\r\n";
		}
	}
	
$t2 = microtime(true);
echo "<br/>Generated in: ".($t2-$t1)."<br/>";
?>
</pre>