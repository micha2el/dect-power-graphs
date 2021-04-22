<?php
$jpgraph_dir = "/usr/share/jpgraph/";
$datafile = "dect.xml";
$number_of_devices = 4;
$colors = array("blue","green","black","red");
$max_y = 1000;
$graph_name = "Leistung";
$graph_y_axis = "Leistung in W";
$graph_x_axis = "Zeit";

### no more config below

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");

$outputs = array();
$names = array();
$file = simplexml_load_file($datafile);
for ($i=0;$i<$number_of_devices;$i++) {
	$data = explode(",",$file->device[$i]->devicestats->power->stats);
	$name = $file->device[$i]->attributes()->name;
	for ($j=0;$j<sizeof($data);$j++){
		$data[$j] = $data[$j]/100;
		if ($data[$j] < 1) $data[$j] = 0;
	}
	array_push($outputs, $data);
	array_push($names, $name);
}

$yaxis = array();
$xaxis = array();

for ($i=0;$i<$max_y;$i++){
	$yaxis[$i] = $i;
}
for ($i=0;$i<sizeof($outputs[0]);$i++){
	$xaxis[$i] = (-10/60 * $i)."m";
}

// Create the graph. These two calls are always required
$graph = new Graph(1000,300,"auto");
$graph->SetScale("textlog");

// Create the linear plot
$lineplot0=new LinePlot($outputs[0]);
$lineplot0->SetColor("blue");
$lineplot0->SetLegend($names[0]);
$lineplot1=new LinePlot($outputs[1]);
$lineplot1->SetColor($colors[1]);
$lineplot1->SetLegend($names[1]);
$lineplot2=new LinePlot($outputs[2]);
$lineplot2->SetColor($colors[2]);
$lineplot2->SetLegend($names[2]);
$lineplot3=new LinePlot($outputs[3]);
$lineplot3->SetColor($colors[3]);
$lineplot3->SetLegend($names[3]);

// Add the plot to the graph
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
$graph->xaxis->setTextTickInterval(12);
$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->yaxis->SetTitleMargin(35);
$graph->xaxis->SetTitleMargin(15);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->SetShadow();
$graph->legend->Pos(0.035,0.5,"right","center");
$graph->Stroke();
?>
