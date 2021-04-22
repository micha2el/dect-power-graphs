<?php
$jpgraph_dir = "/usr/share/jpgraph/";

$datafile = "/var/www/dect/dect_30secs_116300250339.data";
$data_size = 2880*7;
$scale_factor = 4;
$graph_name = "Leistung Solaranlage (taeglich)";
$graph_x_axis = "Zeit";
$graph_y_axis = "Leistung in W";

# no more config below

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");

$data = array();
$yaxis = array();
$xaxis = array();
$counter = 0;

$fh = fopen($datafile,"r");
$points = array();
$dates = array();
while ($line = fgets($fh)) {
	$line_array = explode(",",$line);
	$single = substr($line_array[1],2);
	if (is_numeric($single)) {
		array_push($points,($single/1000));
		array_push($dates, date("d.m H:i",((int)substr($line_array[3],2))+3600));
	}
}
fclose($fh);

for ($i=sizeof($points)-1;$i>(sizeof($points)-$data_size)&&$i>-1;$i=$i-$scale_factor){
	array_push($data,$points[$i]);
	array_push($xaxis,$dates[$i]);
	$counter++;
}

$graph = new Graph(1000,300,"auto");
$graph->SetScale("textlin");
$lineplot0=new LinePlot($data);
$graph->Add($lineplot0);
$graph->img->SetMargin(60,140,30,90);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->xaxis->SetTickLabels($xaxis);
$graph->xaxis->setTextTickInterval((int)(500/$scale_factor));
$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->yaxis->setTitleMargin(35);
$graph->xaxis->setTitleMargin(50);
#$graph->yaxis->setTextTickInterval(100);

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$lineplot0->SetColor("blue");

$graph->SetShadow();
$graph->Stroke();
?>

