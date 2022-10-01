<?php

include("config.php");

$graph_name = "Stromverbrauch, -erzeugung und -einspeise";
$graph_x_axis = "Zeit";
$graph_y_axis = "Strom in kWh";
$max_data = 12;

########################################################################################
# no config below
########################################################################################

if ($use_psql) {
	echo "<table border='1'>";
	echo "<tr><td>Datum</td><td>PV Gross</td><td>Stand PV Klein</td><td>Home_from_PV</td><td>home_from_bat</td><td>co</td><td>Einspeisung</td><td>Verbrauch</td></tr>";
	$conn = pg_connect($hostString);
	$query=pg_query($conn,"select t1.*,t2.einspeisung,t3.verbrauch from inverter_stat_monthly t1,smartmeter_einspeisung_monthly t2,smartmeter_verbrauch_monthly t3 where t1.zeitpunkt=t2.zeitpunkt and t1.zeitpunkt=t3.zeitpunkt order by t1.zeitpunkt asc;");
	if ($query){
		while ($row = pg_fetch_row($query)) {
			$output = "<tr><td>".$row[0]."</td><td>".($row[1]/1000)."</td><td>".($row[2]/1000)."</td><td>".($row[3]/1000)."</td><td>".($row[4]/1000)."</td><td>".$row[5]."</td><td>".($row[7]/1000)."</td><td>".($row[8]/1000)."</td></tr>";
			echo str_replace(".",",",$output);
	        }
	}
	echo "</table>";
	pg_close($conn);
}

?>

