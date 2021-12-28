<?php
include("config.php");

$max_y = 1000;
$graph_name = "Leistung";
$graph_y_axis = "Leistung in W";
$graph_x_axis = "Zeit";

########################################################################################
# no more config below
########################################################################################

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");

$outputs = array();
$names = array();
$file = simplexml_load_file($datafile);
for ($i=0;$i<sizeof($file->device);$i++) {
	if ($file->device[$i]->attributes()->type == "1") {
		$data = explode(",",$file->device[$i]->devicestats->power->stats);
		$name = $file->device[$i]->attributes()->name;
		for ($j=0;$j<sizeof($data);$j++){
			$data[$j] = $data[$j]/100;
			if ($data[$j] < 1) $data[$j] = 0;
		}
		array_push($outputs, $data);
		array_push($names, $name);
	}
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

// Create the linear plots and add to graph
$plots = array();
for ($i=0;$i<sizeof($outputs);$i++){
        array_push($plots, new LinePlot($outputs[$i]));
        if (isset($colors_dect) && sizeof($colors_dect)>$i) {
		end($plots)->SetColor($colors_dect[$i]);
	}else{
		end($plots)->SetColor("#".substr(md5(rand()), 0, 6));
	}
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
