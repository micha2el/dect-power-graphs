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

$output.="Ausgelesen am: ".date("d.m.Y H:i:s.", filemtime($datafile))."<br><br><a href='index_quick.php'>Quickinfo</a><br><br>";
$temps = "<b>Temperaturen:</b><br>";
$leistungen = "<b>Leistungen:</b><br>";
$verbrauch = "<b>Verbrauch:</b><br>";
$blocks = "";

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
	$leistungen.= "<a href='index_smart.php'>Smartmeter</a>: <font color=\"".$color."\">".$smart_output." W</font><br>";
	$blocks.="<b>Details Smartmeter:</b><br>Leistung: <font color=\"".$color."\">".$smart_output." W</font><br>";

	$content_verbrauch=explode(";",$content_verbrauch);
	$smart_output_verbrauch=substr($content_verbrauch[1],2);
	$timestampt=$content_verbrauch[0];
	$verbrauch.= "Smartmeter Verbrauch: ".($smart_output_verbrauch/$scale)." kWh</font><br>";
	$blocks.="Verbrauch: ".$smart_output_verbrauch." Wh<br>";

	$content_einspeise=explode(";",$content_einspeise);
	$smart_output_einspeise=substr($content_einspeise[1],2);
	$timestampt=$content_einspeise[0];
	$verbrauch.= "Smartmeter Einspeisung: ".($smart_output_einspeise/$scale)." kWh</font><br>";
	$blocks.="Einspeise: ".$smart_output_einspeise." Wh<br><br>";
}

for ($i=0;$i<sizeof($file->device);$i++){
	if ($file->device[$i]->attributes()->type == "1") {
		$temps.=$file->device[$i]->attributes()->name.": ".$file->device[$i]->temp." °C<br>";
		$leistungen.=$file->device[$i]->attributes()->name.": ".(($file->device[$i]->strom)/$scale)." W<br>";
		$verbrauch.=$file->device[$i]->attributes()->name.": ".(($file->device[$i]->verbrauch)/$scale)." kWh<br>";

		$blocks.="<b>Details ".$file->device[$i]->attributes()->name." (AIN=".$file->device[$i]->attributes()->ain."):</b><br>";
		$blocks.="Temperatur: ".$file->device[$i]->temp." °C<br>";
		$blocks.="Leistung: ".($file->device[$i]->strom/$scale)." W<br>";
		$blocks.="Verbrauch: ".($file->device[$i]->verbrauch/$scale)." kWh<br>";
		$blocks.="<br>";
	} else if ($file->device[$i]->attributes()->type == "2") {
		$temps.=$file->device[$i]->attributes()->name.": ".$file->device[$i]->temp." °C<br>";
		
		$blocks.="<b>Details ".$file->device[$i]->attributes()->name." (AIN=".$file->device[$i]->attributes()->ain."):</b><br>";
		$blocks.="Temperatur: ".$file->device[$i]->temp." °C<br>";
		$blocks.="<br>";
	}

}
if ($use_blocks){
	$output.=$blocks."<br>";
}else{
	$output.=$leistungen."<br>";
	$output.=$verbrauch."<br>";
	$output.=$temps."<br><br>";
}

if ($use_smart_meter) {
	$output.='<img src="pic_smart.php" />';
}else{
	$output.='<img src="pic_verbrauch.php" />';
	$output.='<img src="pic_verbrauch_monthly.php" />';
}
if ($show_energy_consumption) {
	$output.='<img src="pic_energy_consumption_daily.php" />';
	$output.='<img src="pic_energy_consumption_monthly.php" />';
}
if ($use_solar_power) {
	$output.='<img src="pic_solar_power.php" />';
	$output.='<img src="pic_solar_power_daily.php" />';
}
if ($use_solar_production) {
	$output.='<img src="pic_solar_production.php" />';
	$output.='<img src="pic_solar_production_monthly.php" />';
}
$output.='<img src="pic_energy.php" />';
$output.='<img src="pic_temp.php" />';
if ($use_heating_temp) {
	$output.='<img src="pic_heating_temp.php" />';
}

if ($debug) {
	$output.="<br><br>DEBUG:<br><br>";
	$output.=print_r($file,true);
}
echo "<html><head>\n<meta http-equiv=\"refresh\" content=\"".$chart_view_refresh."\">\n</head><body>\n";
echo $output."</body></html>";


?>
