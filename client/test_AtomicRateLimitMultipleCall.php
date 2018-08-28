<?php
$nb_hit = 0;
if(isset($_GET["hit"]) && !empty($_GET["hit"]))
	$nb_hit = $_GET["hit"];
else
	$nb_hit = rand(1, 100);

// Generate multiple clients
$handle = array();
$mh = curl_multi_init();
for($i=0; $i<$nb_hit; $i++)
{
	$handle[] = curl_init();
	$c = count($handle)-1;
	curl_setopt($handle[$c], CURLOPT_URL, "http://localhost:8080/gromp/client/test_AtomicRateLimit.php?hit=".rand(1, 100));
	curl_setopt($handle[$c], CURLOPT_HEADER, false);
	curl_setopt($handle[$c], CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle[$c], CURLOPT_FRESH_CONNECT, true);
	curl_multi_add_handle($mh, $handle[$c]);
}

$t1 = microtime(true);
// Execute calls (async !)
$active = null;
do
{
    $mrc = curl_multi_exec($mh, $active);
	curl_multi_select($mh);
}while($active > 0);
$t2 = microtime(true);
echo 'Generated in:'.($t2-$t1).'<br/>';
$dDATA = array();
// Get content and delete client
for($i=0; $i<$nb_hit; $i++)
{
	$dDATA[] = json_decode(curl_multi_getcontent($handle[$i]), true);
	curl_multi_remove_handle($mh, $handle[$i]);
}

curl_multi_close($mh);
?>
<center>
<form method="get">
Nb. Clients : <input type="number" name="hit" value="<?php echo @$_GET["hit"]; ?>" /> <input type="submit" value="Start" />
</form>

Simulate <?php echo $nb_hit; ?> clients at the same time
<table>
	<tr>
		<th>lock</th>
		<th>hit</th>
		<th>generated</th>
	</tr>
	<?php
	$dDATAnalysis["lock"]["min"] = 50000;
	$dDATAnalysis["lock"]["average"] = 0;
	$dDATAnalysis["lock"]["max"] = 0;
	$dDATAnalysis["hit"]["min"] = 50000;
	$dDATAnalysis["hit"]["average"] = 0;
	$dDATAnalysis["hit"]["max"] = 0;
	$dDATAnalysis["hit"]["sum"] = 0;
	$dDATAnalysis["generated"]["min"] = 50000;
	$dDATAnalysis["generated"]["average"] = 0;
	$dDATAnalysis["generated"]["max"] = 0;
	foreach($dDATA as $data)
	{
		echo '<tr>';
			echo '<td>'.$data["lock"].'</td>';
			echo '<td>'.$data["hit"].'</td>';
			echo '<td>'.$data["generated"].'</td>';
		echo '</tr>';
		
		if($dDATAnalysis["lock"]["min"] > $data["lock"])
			$dDATAnalysis["lock"]["min"] = $data["lock"];
		if($dDATAnalysis["lock"]["max"] < $data["lock"])
			$dDATAnalysis["lock"]["max"] = $data["lock"];
		$dDATAnalysis["lock"]["average"] += $data["lock"];
		
		if($dDATAnalysis["hit"]["min"] > $data["hit"])
			$dDATAnalysis["hit"]["min"] = $data["hit"];
		if($dDATAnalysis["hit"]["max"] < $data["hit"])
			$dDATAnalysis["hit"]["max"] = $data["hit"];
		$dDATAnalysis["hit"]["average"] += $data["hit"];
		
		if($dDATAnalysis["generated"]["min"] > $data["generated"])
			$dDATAnalysis["generated"]["min"] = $data["generated"];
		if($dDATAnalysis["generated"]["max"] < $data["generated"])
			$dDATAnalysis["generated"]["max"] = $data["generated"];
		$dDATAnalysis["generated"]["average"] += $data["generated"];
	}
	$dDATAnalysis["lock"]["average"] /= count($dDATA);
	$dDATAnalysis["hit"]["sum"] = $dDATAnalysis["hit"]["average"];
	$dDATAnalysis["hit"]["average"] /= count($dDATA);
	$dDATAnalysis["generated"]["average"] /= count($dDATA);
	?>
</table>

Analysis (<?php echo $dDATAnalysis["hit"]["sum"]; ?> hits)
<table>
	<tr>
		<th>Type</th>
		<th>Min</th>
		<th>Max</th>
		<th>Average</th>
	</tr>
	<?php
	foreach($dDATAnalysis as $k => $v)
	{
		echo '<tr>';
			echo '<td>'.$k.'</td>';
			echo '<td>'.$v["min"].'</td>';
			echo '<td>'.$v["max"].'</td>';
			echo '<td>'.$v["average"].'</td>';
		echo '</tr>';
	}
	?>
</table>
</center>