<?php

include("config.php");

########################################################################################
## config variables
########################################################################################
$debug = false;
$scale = 1000;

########################################################################################
## no more config below
########################################################################################
$file = simplexml_load_file($datafile);
$output = "";

$temps = "<b>Temperaturen:</b><br>";
$leistungen = "<b>Leistungen:</b><br>";
$verbrauch = "<b>Verbrauch:</b><br>";
$blocks = "";

function createSquare($header,$text,$bgcolor,$color) {
	return "<table class='roundedTable'><tbody><tr><td style='background-color:".$bgcolor.";'>&nbsp;</td></tr><tr><td style='background-color:".$bgcolor.";color:".$color.";font-size:4vw;'>".$header."</td></tr><tr><td style='background-color:".$bgcolor.";color:".$color.";'>".$text."</td></tr><tr><td style='background-color:".$bgcolor.";'>&nbsp;</td></tr></tbody></table><br/>\n";
}
function createSwitchInner($verbrauch,$temp,$strom){
	return "<font style='font-size:4vw;'>Verbrauch:</font>".$verbrauch." kWh<br><font style='font-size:4vw;'>Strom:</font>".$strom. " W<br><font style='font-size:4vw;'>Temp:</font>".$temp." °C";
}

function createHeatingInner($temp,$tsoll){
	return "<font style='font-size:4vw;'>Temp:</font>".$temp." °C<br><font style='font-size:4vw;'>Soll Temp:</font><font style='font-size:6vw;'>".$tsoll. " °C</font>";
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
	$output.=createSquare("Aktueller Strombedarf",$smart_output." W",$color,"white");

	$content_verbrauch=explode(";",$content_verbrauch);
	$smart_output_verbrauch=substr($content_verbrauch[1],2);
	$timestampt=$content_verbrauch[0];

	$output.=createSquare("Aktueller Verbrauch",($smart_output_verbrauch/$scale)." kWh","lightblue","white");

	$content_einspeise=explode(";",$content_einspeise);
	$smart_output_einspeise=substr($content_einspeise[1],2);
	$timestampt=$content_einspeise[0];

	$output.=createSquare("Aktuelle Einspeisung",($smart_output_einspeise/$scale)." kWh","lightgreen","white");
}

for ($i=0;$i<sizeof($file->device);$i++){
	if ($file->device[$i]->attributes()->type == "1") {
		$output.=createSquare("Steckdose: <font style='font-size:8vw;'>".$file->device[$i]->attributes()->name."</font>",createSwitchInner(intval($file->device[$i]->verbrauch/$scale),$file->device[$i]->temp,($file->device[$i]->strom/$scale)),"lightblue","white");
	} else if ($file->device[$i]->attributes()->type == "2") {
		$output.=createSquare("Heizung: <font style='font-size:8vw;'>".$file->device[$i]->attributes()->name."</font>",createHeatingInner($file->device[$i]->temp,$file->device[$i]->tsoll),"orange","white");
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
