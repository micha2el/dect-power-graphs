<?php

include("config.php");

$scale = 1;
$graph_scale = 1500;
$nr_days = 31;
$graph_name = "Stromerzeugung (".$nr_days." Tage)";
$graph_x_axis = "Zeit";
$graph_y_axis = "Stromerzeugung in Wh";
$graph_y_big_tick = 100;
$graph_y_small_tick = 50;

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

$fh = fopen($datafile_solar_daily,"r");
$firstvalue = 0;
$lastvalue = 0;
while ($line = fgets($fh)) {
	$line_array = explode(",",$line);
	$singledata= ((substr($line_array[2],2))/$scale);
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
for ($i=0;$i<$graph_scale;$i++){
	$yaxis[$i] = $i;
}
// correct dates
for ($i=0;$i<sizeof($xaxis);$i++){
	$xaxis[$i] = date('d-m-Y',(strtotime(substr($xaxis[$i],0,10))-10));
}
// add current values
array_push($xaxis, date('d-m-Y', time()));
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

$data = array_slice($data,0,$nr_days);
$xaxis = array_slice($xaxis,0,$nr_days);

$barplot=new BarPlot($data);
$barplot->SetWidth(2);
$barplot->SetFillColor("navy");
$barplot->SetColor("navy");
$barplot->ShowValue(true);
if ($scale==1) {
	$barplot->SetValueFormat("%01.0f",90);
}else{
	$barplot->SetValueFormat("%01.2f",90);
}
$gbplot = new GroupBarPlot(array($barplot));
$graph->Add($gbplot);

$graph->img->SetMargin(60,140,30,80);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->xaxis->SetTickLabels($xaxis);
$graph->xaxis->setTextTickInterval(1);
$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->yaxis->SetTitleMargin(35);

#$barplot->SetLegend("Produzierter Strom");
#$graph->legend->Pos(0.01,0.5,"right","center");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->SetShadow();
$graph->Stroke();
?>

