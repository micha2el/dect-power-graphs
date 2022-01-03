<?php

include("config.php");

########################################################################################
## config variables
########################################################################################
$debug = false;
$scale = 1000;


########################################################################################
# Text output
########################################################################################
$str_time = "Uhr";
$str_usage = "Verbrauch";
$str_consumption = "Strom";
$str_temp = "Temp";
$str_tsoll = "Soll Temp";
$str_tist = "Temp";
$str_outlet = "Steckdose";
$str_blinds = "Rollo";
$str_blinds_level = "Position";
$str_heating = "Heizung";
$str_battery = "Akku";
$str_window_open = "Fenster offen";
$str_boost_active = "Boost aktiv";
$str_current_consumption = "Aktueller Strombedarf";
$str_total_consumption = "Gesamtverbrauch";
$str_power_input = "Einspeisung";
$str_on = "An";
$str_off = "Aus";

########################################################################################
## no more config below
########################################################################################
$file = simplexml_load_file($datafile);
$output = "";

function createSquare($header,$text,$bgcolor,$color) {
	return "<table class='roundedTable'><tbody><tr><td style='background-color:".$bgcolor.";'>&nbsp;</td></tr><tr><td style='background-color:".$bgcolor.";color:".$color.";font-size:4vw;'>".$header."</td></tr><tr><td style='background-color:".$bgcolor.";color:".$color.";'>".$text."</td></tr><tr><td style='background-color:".$bgcolor.";'>&nbsp;</td></tr></tbody></table><br/>\n";
}
function createSwitchInner($verbrauch,$temp,$strom) {
	global $str_usage,$str_consumption,$str_temp;
	return "<font style='font-size:4vw;'>".$str_usage.":</font>".$verbrauch." kWh<br><font style='font-size:4vw;'>".$str_consumption.":</font>".$strom." W<br><font style='font-size:4vw;'>".$str_temp.":</font>".$temp." °C";
}

function createBlindsInner($level) {
	global $str_blinds_level;
	return "<font style='font-size:4vw;'>".$str_blinds_level.":</font>".$level." %";
}

function createHeatingInner($temp,$tsoll,$wopen,$wopentime="0",$battery="0",$boost="0",$boosttime="0"){
	global $str_window_open,$str_boost_active,$str_tist,$str_tsoll,$str_battery;
	$start="";$end="";
	if ($wopen=="1") {
		$start.="<font style='color:red;font-size:8vw;'>".$str_window_open."</font>";
		$end.="<br><font style='font-size:6vw;'>bis ".date('H:i:s',intval($wopentime))." Uhr</font>";
	}else if ($boost=="1"){
		$start.="<font style='color:green;font-size:8vw;'>".$str_boost_active."</font>";
		$end.="<br><font style='font-size:6vw;'>bis ".date('H:i:s',intval($boosttime))." Uhr</font>";
	}else{
		$start.="<font style='font-size:4vw;'>".$str_tist.":</font>".$temp." °C";
		$end.="";
	}
	return $start."<br><font style='font-size:4vw;'>".$str_tsoll.":</font><font style='font-size:6vw;'>".$tsoll. " °C</font> <font style='font-size:4vw;'>".$str_battery.": </font><font style='font-size:6vw;'>".intval($battery/10)."/10</font>".$end;
}

if ($use_smart_meter) {
	$myfile = fopen($data_smart_power, "r") or die("Unable to open file!");
	$content_power = fread($myfile,filesize($data_smart_power));
	fclose($myfile);
	$myfile = fopen($data_smart_verbrauch, "r") or die("Unable to open file!");
	$content_verbrauch = fread($myfile,filesize($data_smart_verbrauch));
	fclose($myfile);
	$myfile = fopen($data_smart_einspeise, "r") or die("Unable to open file!");
	$content_einspeise = fread($myfile,filesize($data_smart_einspeise));
	fclose($myfile);

	$content_power=explode(";",$content_power);
	$smart_output=substr($content_power[1],2);
	$timestampt=$content_power[0];
	if (substr($smart_output,0,1)=="-")
        	$color="green";
	else
        	$color="red";
	$output.=createSquare($str_current_consumption,$smart_output." W",$color,"white");

	$content_verbrauch=explode(";",$content_verbrauch);
	$smart_output_verbrauch=substr($content_verbrauch[1],2);
	$timestampt=$content_verbrauch[0];

	$output.=createSquare($str_total_consumption,($smart_output_verbrauch/$scale)." kWh","lightblue","black");

	$content_einspeise=explode(";",$content_einspeise);
	$smart_output_einspeise=substr($content_einspeise[1],2);
	$timestampt=$content_einspeise[0];

	$output.=createSquare($str_power_input,($smart_output_einspeise/$scale)." kWh","lightgreen","black");
}

for ($i=0;$i<sizeof($file->device);$i++){
	if ($file->device[$i]->attributes()->type == "1") {
		if ($file->device[$i]->state == "1") {
			$state = "<font style='font-size:4vw;color:green;'>".$str_on."</font>";
		}else{
			$state = "<font style='font-size:4vw;color:red;'>".$str_off."</font>";
		}
		$output.=createSquare($str_outlet.": <font style='font-size:8vw;'>".$file->device[$i]->attributes()->name."</font> ".$state,createSwitchInner(intval($file->device[$i]->verbrauch/$scale),$file->device[$i]->temp,($file->device[$i]->strom/$scale)),"lightblue","black");
	} else if ($file->device[$i]->attributes()->type == "2") {
		$output.=createSquare($str_heating.": <font style='font-size:8vw;'>".$file->device[$i]->attributes()->name."</font>",createHeatingInner($file->device[$i]->temp,$file->device[$i]->tsoll,$file->device[$i]->wopen,$file->device[$i]->wopentime,$file->device[$i]->battery,$file->device[$i]->boost,$file->device[$i]->boosttime),"orange","black");
	} else if ($file->device[$i]->attributes()->type == "31") {
		$output.=createSquare($str_blinds.": <font style='font-size:8vw;'>".$file->device[$i]->attributes()->name."</font>",createBlindsInner($file->device[$i]->level),"darkseagreen","black");
	}

}

$output.="<font style='font-size:10vw;'><a href='index.php'>See charts...</a></font>";

echo "<html><head>\n<meta http-equiv=\"refresh\" content=\"2\">\n";
echo "<style>\n";
echo ".roundedTable{border-radius: 20px 20px 20px 20px;border: 1px solid #000;border-spacing: 0;width: 100%;}\n";
echo ".roundedTable tr:first-child td:first-child {border-top-left-radius: 19px;}\n";
echo ".roundedTable tr:first-child td:last-child {border-top-right-radius: 19px;}\n";
echo ".roundedTable tr:last-child td:first-child {border-bottom-left-radius: 19px;}\n";
echo ".roundedTable tr:last-child td:last-child {border-bottom-right-radius: 19px;}\n";
echo ".roundedTable th, td{padding: 10px 10px 10px 10px; text-align: center; font-size: 10vw}\n";
echo "</style>\n";
echo "</head><body>\n";
echo $output."\n</body></html>";


?>
