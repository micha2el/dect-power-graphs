<?php

include("config.php");

$graph_name = "Verbrauch Smartmeter (heute)";
$graph_x_axis = "Zeit";
$graph_y_axis = "Leistung in W";
$data_size = 86400;
$points_scale_factor = 6;

########################################################################################
# no config below
########################################################################################

require_once ($jpgraph_dir."jpgraph.php");
require_once ($jpgraph_dir."jpgraph_log.php");
require_once ($jpgraph_dir."jpgraph_line.php");
require_once ($jpgraph_dir."jpgraph_scatter.php");

$data = array();
$yaxis = array();
$xaxis = array();

$fh = fopen($data_smart,"r");
$points = array();
$dates = array();
$zero_line = array();
$zero_line_dates = array();
while ($line = fgets($fh)) {
	$line_array = explode(";",$line);
	$single = (int)substr($line_array[1],2);
	if (is_numeric($single)) {
		array_push($points,$single);
		$single_date = ((int)substr(substr($line_array[0],2),0,10));
		array_push($dates,$single_date);
		if (sizeof($zero_line_dates) == 0){
			array_push($zero_line_dates, $single_date);
			array_push($zero_line, 0);
		}
	}
}
fclose($fh);
$date_range = time()-$data_size;
// only allow last day
for ($i=sizeof($points)-1;((int)$dates[$i])>$date_range;$i=$i-$points_scale_factor){
	array_push($data,$points[$i]);
	array_push($xaxis,date('d-m-Y H:i:s', (int)$dates[$i]));
	array_push($zero_line, 0);
}
$counter=0;
for ($i=-1200;$i<7000;$i++){
	$yaxis[$counter] = $i;
	$counter++;
}
$graph = new Graph(1000,400,"auto");
$graph->SetScale("textint");

$lineplot0=new LinePlot($data);
$lineplot0->SetColor("blue");
$lineplot1 = new LinePlot($zero_line);
$lineplot1->SetColor("red");
$graph->Add($lineplot0);
$graph->Add($lineplot1);

$graph->img->SetMargin(60,140,30,160);
$graph->title->Set($graph_name);
$graph->xaxis->title->Set($graph_x_axis);
$graph->yaxis->title->Set($graph_y_axis);
$graph->xaxis->SetTickLabels($xaxis);
$graph->xaxis->setTextTickInterval((int)(1500/$points_scale_factor));
$graph->xaxis->SetTitleMargin(110);
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetPos("min");

$graph->yaxis->SetTitleMargin(40);
$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->ygrid->Show(true,false);

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->SetShadow();
$graph->Stroke();
?>

