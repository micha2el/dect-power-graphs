<?php

include("config.php");

$hostString="host=localhost port=5432 dbname=smarthome user=mila password=mila2009";

$graph_name = "Batterie Ladung (24h)";
$graph_x_axis = "Zeit";
$graph_y_axis = "Ladung in %";
$data_size = 86400;
$points_scale_factor = 5;

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

$conn = pg_connect($hostString);
$query=pg_query($conn,"select * from inverter_stat where zeitpunkt > now() - interval '24 hours' order by zeitpunkt asc;");

if ($query){
	while ($row = pg_fetch_row($query)) {
		array_push($points,$row[6]);
		array_push($dates, substr($row[0],0,strpos($row[0],".")));
        }
}
pg_close($conn);

$date_range = time()-$data_size;
// only allow last day
for ($i=sizeof($points)-1;$i>0;$i=$i-1){
	array_push($data,$points[$i]);
	array_push($xaxis,$dates[$i]);
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
$graph->xaxis->setTextTickInterval($points_scale_factor);
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

