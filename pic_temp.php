<?php
$jpgraph_dir = "/usr/share/jpgraph/";
$datafile = "dect.xml";
$number_of_devices = 4;
$colors = array("blue","green","black","red");
$max_temp = 35;
$graph_name = "Temperatur";
$graph_x_axis = "Zeit";
$graph_y_axis = "Temperatur in C";

###### no config beyond here
require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_line.php");

$file = simplexml_load_file($datafile);
$outputs = array();
$names = array();
$yaxis = array();
$xaxis = array();

for ($i=0;$i<$number_of_devices;$i++){
	$data = null;
	$data = explode(",",$file->device[$i]->devicestats->temperature->stats);
	for ($j=0;$j<sizeof($data);$j++) {
		$data[$j] = $data[$j]/10;
	}
	array_push($outputs, $data);
	array_push($names, $file->device[$i]->attributes()->name);
}

for ($i=0;$i<$max_temp;$i++){
	$yaxis[$i] = $i;
}

for ($i=0;$i<sizeof($outputs[0]);$i++){
	$xaxis[$i] = (-0.25 * $i)."h";
}

$graph = new Graph(1000,300,"auto");
$graph->SetScale("textlin",0,$max_temp);

#for ($i=0;$i<sizeof($outputs);$i++){
#	$lineplot = null;
#	$lineplot = new LinePlot($outputs[$i]);
#	$lineplot->SetColor($colors[$i]);
#	$lineplot->SetLegend($names[$i]);
#	$graph->Add($lineplot);
#}

$lineplot0=new LinePlot($outputs[0]);
$lineplot0->SetColor("blue");
$lineplot0->SetLegend($names[0]);
$lineplot1=new LinePlot($outputs[1]);
$lineplot1->SetColor("green");
$lineplot1->SetLegend($names[1]);
$lineplot2=new LinePlot($outputs[2]);
$lineplot2->SetColor("black");
$lineplot2->SetLegend($names[2]);
$lineplot3=new LinePlot($outputs[3]);
$lineplot3->SetColor("red");
$lineplot3->SetLegend($names[3]);

$graph->Add($lineplot0);
$graph->Add($lineplot1);
$graph->Add($lineplot2);
$graph->Add($lineplot3);

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

