<?php

include("config.php");

$scale = 1000;
$graph_scale = 25;
$graph_name = "Stromerzeugung";
$graph_x_axis = "Zeit";
$graph_y_axis = "Stromerzeugung in kWh";
$graph_y_big_tick = 5;
$graph_y_small_tick = 1;

########################################################################################
# no more config below
########################################################################################

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");
require_once ($jpgraph_dir."jpgraph_bar.php");

$data = array();
$data_solar = array();
$yaxis = array();
$xaxis = array();
$last_triple = array();
$counter = 0;

$lastvalue = 0;
if (file_exists($datafile_solar_monthly)) {
	$fh = fopen($datafile_solar_monthly,"r");
	$firstvalue = -1;
	while ($line = fgets($fh)) {
		$line_array = explode(",",$line);
		$singledata= ((substr($line_array[2],2))/$scale);
		$lastvalue = $singledata;
		if ($firstvalue == -1) {
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
}
array_push($last_triple, $lastvalue);

for ($i=0;$i<200;$i++){
	$yaxis[$i] = $i;
}
// correct dates
for ($i=0;$i<sizeof($xaxis);$i++){
	$xaxis[$i] = date('M Y',(strtotime(substr($xaxis[$i],0,10))-10));
}
array_push($xaxis, date('M Y', time()));
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
$inner = ((substr($lastline[2],2))/$scale) - $last_triple[0];
array_push($data, $inner);
$graph = new Graph(1000,300,"auto");
$graph->SetScale("textlin",0,$graph_scale);
$graph->yscale->ticks->Set($graph_y_big_tick,$graph_y_small_tick);

$data = array_reverse($data);
$xaxis = array_reverse($xaxis);
$barplot=new BarPlot($data);
$barplot->SetWidth(2);
$barplot->SetFillColor("navy");
$barplot->SetColor("navy");
$barplot->ShowValue(true);
$barplot->SetValueFormat("%01.2f",90);

$gbplot = new GroupBarPlot(array($barplot));
$graph->Add($gbplot);
$graph->img->SetMargin(60,130,30,80);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->xaxis->SetTickLabels($xaxis);
$graph->yaxis->SetTickLabels($yaxis);
$graph->yaxis->SetTitleMargin(35);
$graph->yaxis->SetWeight(2);
$graph->yaxis->SetColor("red");
$graph->xaxis->setTextTickInterval(1);
$graph->xaxis->SetLabelAngle(90);

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->SetShadow();
$graph->Stroke();
?>

