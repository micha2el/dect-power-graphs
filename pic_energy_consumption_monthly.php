<?php

include("config.php");

$graph_name = "Stromverbrauch, -erzeugung und -einspeise";
$graph_x_axis = "Zeit";
$graph_y_axis = "Strom in kWh";
$max_data = 12;

########################################################################################
# no config below
########################################################################################

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");
require_once ($jpgraph_dir."jpgraph_bar.php");

$data = array();
$data2 = array();
$data3 = array();
$data4 = array();
	
$data_einspeise = array();
$data_verbrauch = array();
$data_solar = array();
$yaxis = array();
$xaxis = array();
$last_triple = array();
$counter = 0;

if ($use_psql) {
	$w_pv = array();
	$w_pv_small = array();
	$einspeise = array();
	$verbrauch = array();
	$conn = pg_connect($hostString);
	$query=pg_query($conn,"select t1.*,t2.einspeisung,t3.verbrauch from inverter_stat_monthly t1,smartmeter_einspeisung_monthly t2,smartmeter_verbrauch_monthly t3 where t1.zeitpunkt=t2.zeitpunkt and t1.zeitpunkt=t3.zeitpunkt order by t1.zeitpunkt asc;");
	if ($query){
		while ($row = pg_fetch_row($query)) {
			array_push($w_pv,$row[1]/1000);
			array_push($w_pv_small,$row[2]/1000);
			array_push($einspeise,$row[7]/1000);
			array_push($verbrauch,$row[8]/1000);
			array_push($xaxis,$row[0]);
	        }
	}
	$query=pg_query($conn,"select cast(date_trunc('month',zeitpunkt) as date),sum(w_pv),max(w_pv_small),sum(home_from_pv),sum(home_from_bat),sum(co) from inverter_stat_daily where date_trunc('month',zeitpunkt)=date_trunc('month',now()) group by 1;");
	if ($query){
		$row = pg_fetch_row($query);
		array_push($xaxis,$row[0]);
		array_push($w_pv,$row[1]/1000);
		array_push($w_pv_small,$row[2]/1000);
	}
	$query=pg_query($conn,"select * from smartmeter_einspeisung order by zeitpunkt desc limit 1;");
	if ($query){
		$row = pg_fetch_row($query);
		array_push($einspeise,$row[1]/1000);
	}
	$query=pg_query($conn,"select * from smartmeter_verbrauch order by zeitpunkt desc limit 1;");
	if ($query){
		$row = pg_fetch_row($query);
		array_push($verbrauch,$row[1]/1000);
	}
	pg_close($conn);
	for ($i=1;$i<sizeof($xaxis);$i++){
		array_push($data3,($verbrauch[$i]-$verbrauch[$i-1]));
		array_push($data,($w_pv_small[$i]-$w_pv_small[$i-1]+$w_pv[$i]));
		array_push($data2,ceil($einspeise[$i]-$einspeise[$i-1]));
		array_push($data4,(int)(($w_pv_small[$i]-$w_pv_small[$i-1]+$w_pv[$i])-($einspeise[$i]-$einspeise[$i-1])));
	}
	array_shift($xaxis);
}else{
	$fh = fopen($datafile_solar_monthly,"r");
	$firstvalue = 0;
	$lastvalue = 0;
	while ($line = fgets($fh)) {
		$line_array = explode(",",$line);
		$singledata= ((substr($line_array[2],2))/1000);
		$lastvalue = $singledata;
		if ($firstvalue == 0) {
			$firstvalue = $singledata;
		}else {
			$currentdata = ($singledata-$firstvalue);
			array_push($data,$currentdata);
			#array_push($xaxis,(-1 * $counter)."h");
			array_push($xaxis,date('d-m-Y H:i:s', (int)substr($line_array[3],2)));
			$firstvalue = $singledata;
			$counter++;
		}
	}
	fclose($fh);
	array_push($last_triple, $lastvalue);

	$fh = fopen($datafile_einspeise_monthly,"r");
	$firstvalue = 0;
	$lastvalue = 0;
	$dates = array();
	while ($line = fgets($fh)) {
		$line_array = explode(";",$line);
		$time = substr($line_array[0],2,10);
		$kwh = doubleval(substr($line_array[1],2))/1000;
		$lastvalue = $kwh;
		if ($firstvalue == 0) {
			$firstvalue = $kwh;
		}else{
			$currentdata = $kwh-$firstvalue;
			array_push($data_einspeise,$currentdata);
			array_push($dates,date('d-m-Y H:i:s', (int)$time));
			$firstvalue = $kwh;
		}
	}
	fclose($fh);
	array_push($last_triple, $lastvalue);

	$fh = fopen($datafile_verbrauch_monthly,"r");
	$firstvalue = 0;
	$lastvalue = 0;
	$dates_verbrauch = array();
	while ($line = fgets($fh)) {
		$line_array = explode(";",$line);
		$time = substr($line_array[0],2,10);
		$kwh = doubleval(substr($line_array[1],2))/1000;
		$lastvalue = $kwh;
		if ($firstvalue == 0) {
			$firstvalue = $kwh;
		}else{
			$currentdata = $kwh-$firstvalue;
			array_push($data_verbrauch,$currentdata);
			array_push($dates_verbrauch,date('d-m-Y H:i:s', (int)$time));
			$firstvalue = $kwh;
		}
	}
	fclose($fh);
	array_push($last_triple, $lastvalue);

	for ($i=0;$i<sizeof($data);$i++){
		$basetime = strtotime(substr($xaxis[$i],0,10));
		$found = false;
		for ($j=0;$j<sizeof($data_einspeise);$j++) {
			$comparetime = strtotime(substr($dates[$j],0,10));
	#		echo $comparetime."?=".$basetime."<br>";
			if ($basetime == $comparetime) {
				$found = true;
	#			echo "pushing (j=".$j."):".$data_einspeise[$j]."<br>";
				array_push($data2, $data_einspeise[$j]);
				array_push($data3, $data_verbrauch[$j]);
				array_push($data4, $data[$i] - $data_einspeise[$j]);
				break;
			}else if ($comparetime > $basetime) {
#				echo "break c>b<br>";
				break;
			}
		}
		if (!$found) {
			array_push($data2,0);
			array_push($data3,0);
			array_push($data4,0);
		}
	}
	// correct dates
	for ($i=0;$i<sizeof($xaxis);$i++){
		$xaxis[$i] = date('M Y',(strtotime(substr($xaxis[$i],0,10))-10));
	}
}

for ($i=0;$i<150;$i++){
	$yaxis[$i] = $i;
}

if ($use_psql){
}else{
	// add current values
	array_push($xaxis, date('M Y', time()));
	$fh = fopen($data_smart_einspeise,"r");
	while ($line = fgets($fh)) {
		$line_array = explode(";",$line);
		$inner = (substr($line_array[1],2))/1000;
		array_push($data2, $inner - $last_triple[1]);
	}
	fclose($fh);
	$fh = fopen($data_smart_verbrauch,"r");
	while ($line = fgets($fh)) {
		$line_array = explode(";",$line);
		$inner = (substr($line_array[1],2))/1000;
		array_push($data3, $inner - $last_triple[2]);
	}
	fclose($fh);
	$f = fopen($datafile_solar, "r");
	$cursor = -1;
	$lastline = "";
	fseek($f, $cursor, SEEK_END);
	$char = fgetc($f);
	while ($char === "\n" || $char === "\r") {
	    fseek($f, $cursor--, SEEK_END);
	    $char = fgetc($f);
	}
	while ($char !== false && $char !== "\n" && $char !== "\r") {
	    $lastline = $char . $lastline;
	    fseek($f, $cursor--, SEEK_END);
	    $char = fgetc($f);
	}
	$lastline = explode(",",$lastline);
	$inner = ((substr($lastline[2],2))/1000) - $last_triple[0];
	array_push($data, $inner);
	array_push($data4, $inner - end($data2));
}

while (sizeof($xaxis)>$max_data){
	array_shift($data);
	array_shift($data2);
	array_shift($data3);
	array_shift($data4);
	array_shift($xaxis);
}
$data = array_reverse($data);
$data2 = array_reverse($data2);
$data3 = array_reverse($data3);
$data4 = array_reverse($data4);
$xaxis = array_reverse($xaxis);

// Create the graph. These two calls are always required
$graph = new Graph(1000,300,"auto");
#$graph->SetScale("textlin",0,$graph_energy_monthly_y_scale);
$graph->SetScale("textlog");
$graph->yscale->ticks->Set(10,5);

// Create the linear plot
$barplot=new BarPlot($data);
$barplot->SetWidth(2);
$barplot->SetFillColor("navy");
$barplot->SetColor("navy");
$barplot->ShowValue(true);
$barplot->SetValueFormat("%01.2f",90);
$barplot2=new BarPlot($data2);
$barplot2->SetWidth(1);
$barplot2->SetFillColor("green");
$barplot2->SetColor("green");
$barplot2->ShowValue(true);
$barplot2->SetValueFormat("%01.2f",90);
$barplot3=new BarPlot($data3);
$barplot3->SetWidth(1);
$barplot3->SetFillColor("red");
$barplot3->SetColor("red");
$barplot3->ShowValue(true);
$barplot3->SetValueFormat("%01.2f",90);
$barplot4=new BarPlot($data4);
$barplot4->SetWidth(1);
$barplot4->SetFillColor("yellow");
$barplot4->SetColor("yellow");
$barplot4->ShowValue(true);
$barplot4->SetValueFormat("%01.2f",90);

$gbplot = new GroupBarPlot(array($barplot3,$barplot,$barplot2,$barplot4));
// Add the plot to the graph
#$graph->Add($barplot);
$graph->Add($gbplot);

$graph->img->SetMargin(60,180,30,80);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->yaxis->SetTickLabels($yaxis);
$graph->yaxis->SetTitleMargin(35);
$graph->yaxis->SetWeight(2);
$graph->yaxis->SetColor("red");
#$graph->SetTickDensity(TICKD_VERYSPARSE);

$graph->xaxis->SetTickLabels($xaxis);
$graph->xaxis->setTextTickInterval(1);
$graph->xaxis->SetLabelAngle(90);

$barplot->SetLegend("Produzierter Strom");
$barplot2->SetLegend("Eingespeister Strom");
$barplot3->SetLegend("Verbrauchter Strom");
$barplot4->SetLegend("Genutzer Solarstrom");
$graph->legend->Pos(0.01,0.5,"right","center");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->SetShadow();
$graph->Stroke();
?>

