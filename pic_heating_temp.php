<?php
include("config.php");

$max_temp = 35;
$graph_name = "Heizungen";
$graph_x_axis = "Zeit";
$graph_y_axis = "Temperatur in C";

########################################################################################
# no config below
########################################################################################

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_line.php");

$file = simplexml_load_file($datafile);
$outputs = array();
$names = array();
$yaxis = array();
$xaxis = array();

for ($i=0;$i<sizeof($file->device);$i++){
	if ($file->device[$i]->attributes()->type == "2") {
		$data = null;
		$data = explode(",",$file->device[$i]->devicestats->temperature->stats);
		for ($j=0;$j<sizeof($data);$j++) {
			$data[$j] = $data[$j]/10;
		}
		array_push($outputs, $data);
		array_push($names, $file->device[$i]->attributes()->name);
	}
}

if (sizeof($outputs)<1) {
	array_push($outputs,array());
	array_push($outputs,"no data");
}

for ($i=0;$i<$max_temp;$i++){
	$yaxis[$i] = $i;
}

for ($i=0;$i<sizeof($outputs[0]);$i++){
	$xaxis[$i] = (-0.25 * $i)."h";
}

$graph = new Graph(1000,300,"auto");
$graph->SetScale("textlin",0,$max_temp);
$plots = array();
for ($i=0;$i<sizeof($outputs);$i++){
	array_push($plots, new LinePlot($outputs[$i]));
	end($plots)->SetColor("#".substr(md5(rand()), 0, 6));
	#end($plots)->SetColor($colors_dect[$i]);
	end($plots)->SetLegend($names[$i]);
}
for ($i=0;$i<sizeof($plots);$i++){
	$graph->Add($plots[$i]);
}

$graph->img->SetMargin(60,140,30,60);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->yaxis->SetTickLabels($yaxis);
$graph->xaxis->SetTickLabels($xaxis);
$graph->xaxis->setTextTickInterval(4);
$graph->yaxis->SetTitleMargin(35);
$graph->xaxis->SetTitleMargin(15);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->yaxis->setTextTickInterval(1);
$graph->SetShadow();

$graph->legend->Pos(0.035,0.5,"right","center");
$graph->Stroke();
?>

