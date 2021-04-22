<?php

include("config.php");

$myfile = fopen($data_smart_power, "r") or die("Unable to open file!");
$content = fread($myfile,filesize($data_smart_power));
fclose($myfile);

$content=explode(";",$content);
$output=substr($content[1],2);
$timestampt=$content[0];
if (substr($output,0,1)=="-")
	$color="green";
else
	$color="red";

echo "<html><head>\n<meta http-equiv=\"refresh\" content=\"1\">\n</head><body style=\"background-color:".$color.";color:white;text-align:center;font-size:80px\">\n";
echo $output." Watt</body></html>";


?>
