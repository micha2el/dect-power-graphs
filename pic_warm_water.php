<?php

include("config.php");

$data_size = 2880;
$graph_name = "Warmwasser";
$graph_x_axis = "Zeit";
$graph_y_axis = "Leistung in W";
$graph_legend_0 = "Boiler";
$graph_legend_1 = "Pumpe";

########################################################################################
# no more config below
########################################################################################

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");
$data = array();
$data_pump = array();
$yaxis = array();
$xaxis = array();
$counter = 0;

$fh = fopen($datafile_heater,"r");
$points = array();
$points_pump = array();
while ($line = fgets($fh)) {
	$line_array = explode(",",$line);
	$single = substr($line_array[1],2);
	if (is_numeric($single)) {
		array_push($points,$single/1000);
	}
}
fclose($fh);

for ($i=sizeof($points)-1;$i>(sizeof($points)-$data_size)&&$i>-1;$i--){
	array_push($data,$points[$i]);
	array_push($xaxis,(-0.5 * 1/60 * $counter)."h");
	$counter++;
}

$fh = fopen($datafile_pump,"r");
while ($line = fgets($fh)) {
	$line_array = explode(",",$line);
	$single = substr($line_array[1],2);
	if (is_numeric($single)) {
		array_push($points_pump,$single/1000);
	}
}
fclose($fh);

for ($i=sizeof($points_pump)-1;$i>(sizeof($points_pump)-$data_size)&&$i>-1;$i--){
	array_push($data_pump,$points_pump[$i]);
	$counter++;
}

$graph = new Graph(1000,300,"auto");
$graph->ClearTheme();
$graph->SetScale("textlin");
$graph->SetYScale(0,"lin");
$lineplot0=new LinePlot($data);
$lineplot1=new LinePlot($data_pump);
$lineplot0->SetColor("blue");
$lineplot1->SetColor("red");
$lineplot0->SetLegend($graph_legend_0);
$lineplot1->SetLegend($graph_legend_1);
$graph->Add($lineplot0);
$graph->AddY(0,$lineplot1);
$graph->ynaxis[0]->SetColor("red");
#$graph->Add($lineplot1);
$graph->img->SetMargin(60,140,30,90);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->xaxis->SetTickLabels($xaxis);
$graph->xaxis->setTextTickInterval(105);
$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->yaxis->SetTitleMargin(35);
$graph->xaxis->SetTitleMargin(45);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->ygrid->SetFill(true,'#FEFEFE@0.5','#EDEDED@0.5');
$graph->legend->Pos(0.025,0.5,"right","center");
$graph->legend->SetLayout(0);
$graph->SetShadow();
$graph->Stroke();
?>

