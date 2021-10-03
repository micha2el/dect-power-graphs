<?php

include("config.php");

$scale = 1000;
$graph_scale = 5;
$show_only_values = 14;
$graph_name = "Stromverbrauch (".$show_only_values." Tage)";
$graph_x_axis = "Zeit";
$graph_y_axis = "Stromverbrauch in kWh";

########################################################################################
# no config more config below
########################################################################################

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");
require_once ($jpgraph_dir."jpgraph_bar.php");

$yaxis = array();
$xaxis = array();
$last_triple = array();

$outputs = array();
for ($i=0;$i<sizeof($files);$i++) {
	$counter = 0;
	$fh = fopen($files[$i],"r");
	$firstvalue = -1;
	$lastvalue = 0;
	$inner_dates = array();
	$inner_data = array();
	while ($line = fgets($fh)) {
		$line_array = explode(",",$line);
		$singledata= ((substr($line_array[2],2))/$scale);
		$lastvalue = $singledata;
		if ($firstvalue == -1) {
			$firstvalue = $singledata;
		}else {
			// check if current value is below previous value (dect counter could be reseted)
			if ($singledata < $firstvalue)
				$currentdata = $singledata;
			else
				$currentdata = ($singledata-$firstvalue);
			array_push($inner_data,$currentdata);
			array_push($inner_dates,date('d-m-Y H:i:s', (int)substr($line_array[3],2)));
			if ($i==0) {
				array_push($xaxis,date('d-m-Y H:i:s', (int)substr($line_array[3],2)));
			}
			$firstvalue = $singledata;
			$counter++;
		}
	}
	fclose($fh);
	array_push($last_triple, $lastvalue);

	if ($i>0) {
		$output_data = array();
		for ($z=0;$z<sizeof($outputs[0]);$z++){
			$basetime = strtotime(substr($xaxis[$z],0,10));
			$found = false;
			for ($j=0;$j<sizeof($inner_data);$j++) {
				$comparetime = strtotime(substr($inner_dates[$j],0,10));
				if ($basetime == $comparetime) {
					$found = true;
					array_push($output_data, $inner_data[$j]);
					break 1;
				}else if ($comparetime > $basetime) {
					break 1;
				}
			}
			if (!$found) {
				array_push($output_data,0);
			}
		}
		array_push($outputs, $output_data);
		#print_r("Next ".$i." :");
		#print_r($output_data);
		#print_r("<br><br>");
	}else{
		array_push($outputs, $inner_data);
		#print_r("Inner:");
		#print_r($inner_data);
		#print_r("<br><br>");
	}
}

for ($i=0;$i<$graph_scale;$i++){
	$yaxis[$i] = $i;
}
// correct dates
for ($i=0;$i<sizeof($xaxis);$i++){
	$xaxis[$i] = date('d-m-Y',(strtotime(substr($xaxis[$i],0,10))-10));
}
// add current values
array_push($xaxis, date('d-m-Y', time()));
for ($z=0;$z<sizeof($current_files);$z++) {
	$f = fopen($current_files[$z], "r");
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
	$curvalue = ((substr($lastline[2],2))/$scale);
	// check if there was a reset within the data - then we need to start with the new value
	if ($curvalue < $last_triple[$z])
		$inner = $curvalue;
	else
		$inner = $curvalue - $last_triple[$z];
	array_push($outputs[$z], $inner);
}
#for ($i=0;$i<sizeof($outputs);$i++){
#	print_r("output ".$i." :");
#	print_r($outputs[$i]);
#	print_r("<br><br>");
#}

// Create the graph. These two calls are always required
$graph = new Graph(1000,300,"auto");
$graph->SetScale("textlin",0,$graph_scale);

$xaxis = array_reverse($xaxis);
$xaxis = array_splice($xaxis,0,$show_only_values);

$plot_array = array();
#array_push($plot_array,$barplot);
for ($i=0;$i<sizeof($outputs);$i++){
	$inner_data = array_reverse($outputs[$i]);
	$inner_data = array_splice($inner_data,0,$show_only_values);
	$inner_barplot = new BarPlot($inner_data);
	$inner_barplot->SetWidth(2);
	$inner_barplot->SetFillColor($colors[$i]);
	$inner_barplot->SetColor($colors[$i]);
	$inner_barplot->ShowValue(true);
	$inner_barplot->SetValueFormat("%01.2f",90);
	$inner_barplot->SetLegend($names[$i]);
	array_push($plot_array,$inner_barplot);
}

$gbplot = new GroupBarPlot($plot_array);
$graph->Add($gbplot);

$graph->img->SetMargin(60,180,30,80);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->yaxis->SetTickLabels($yaxis);
$graph->xaxis->SetTickLabels($xaxis);
$graph->xaxis->setTextTickInterval(1);
$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->yaxis->SetTitleMargin(35);

$graph->legend->Pos(0.05,0.5,"right","center");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->SetShadow();
$graph->Stroke();
?>

