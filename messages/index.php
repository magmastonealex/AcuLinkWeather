<?php

ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
error_log( "Hello, errors!" );

require "../predis/autoload.php";
Predis\Autoloader::register();
$redis = new Predis\Client();

$POSTDATA = file_get_contents("php://input");
file_put_contents("inout.txt", $POSTDATA);
system('curl -v -d "'.$POSTDATA.'" http://acu-link.com/messages/ > test.txt');
//date('l jS \of F Y h:i:s A')
$redis->set("weather:last", "testing.");
$type = $_POST["mt"];
file_put_contents("types.txt",$type."\n",FILE_APPEND);
if($type=="5N1x38"){
//	file_put_contents("inout.txt", $POSTDATA);
	//windspeed=A001660000
	$humidity = $_POST["humidity"];
	$humid = floatval($humidity[2].$humidity[3].".".$humidity[4]);
	$temperature = $_POST["temperature"];
	$temp = floatval($temperature[1].$temperature[2].$temperature[3].".".$temperature[4].$temperature[5]);
	$redis->set("weather:humid", $humid);
	$redis->set("weather:temp", $temp);
$kelvin=$temp + 273;
$eTs=pow(10,((-2937.4/$kelvin)-4.9283*log($kelvin)/2.302585092994046 +23.5471));
$eTd=$eTs * $humid /100;
$humidex=$temp + (($eTd-10)*5/9);
$redis->set("weather:humidex", $humidex);
} else if($type=="5N1x31"){
//file_put_contents("inout.txt", $POSTDATA);
//rainfall=A0000000
$winddir_frag = $_POST["winddir"];
$winddir = $winddir_frag;
$wind_frag = $_POST["windspeed"];
$windspeed = (floatval($wind_frag[1].$wind_frag[2].$wind_frag[3].$wind_frag[4].$wind_frag[5])/100)*3.6;
$rain_frag = $_POST["rainfall"];
$rainfall = floatval($rain_frag[3].$rain_frag[4].$rain_frag[5])/10000;

	$redis->set("weather:wind:deg", $winddir);
	$redis->set("weather:wind:speed", $windspeed);
	$redis->set("weather:rainfall", $rainfall);

}else if($type=="pressure"){
$c11 = $_POST["C1"];
$c21 = $_POST["C2"];
$c31 = $_POST["C3"];
$c41 = $_POST["C4"];
$c51 = $_POST["C5"];
$c61 = $_POST["C6"];
$c71 = $_POST["C7"];
$a1=  $_POST["A"];
$b1=  $_POST["B"];
$c1=  $_POST["C"];
$d11=  $_POST["D"];
$pr1 = $_POST["PR"];
$tr1 = $_POST["TR"];

$C1 = hexdec($c11);
$C2 = hexdec($c21);
$C3 = hexdec($c31);
$C4 = hexdec($c41);
$C5 = hexdec($c51);
$C6 = hexdec($c61);
$C7 = hexdec($c71);
$A =  hexdec($a1);
$B =  hexdec($b1);
$C =  hexdec($c1);
$D =  hexdec($d11);
$D1 = hexdec($pr1);
$D2 = hexdec($tr1);
$COEF=$A;
if($D2 <=$C5){
$COEF=$A;
}else{
$COEF=$B;
}
$dUT=$D2-$C5-(($D2-$C5)/pow(2,7))*(($D2-$C5)/pow(2,7))*$COEF/pow(2,$C);
$OFF=($C2+($C4-1024)*$dUT/pow(2,14))*4;
$SENS=$C1+$C3*$dUT/pow(2,10);
$X=$SENS*($D1-7168)/pow(2,14)-$OFF;
$P=($X*10/pow(2,5))+$C7;
$T=250 + ($dUT*$C6/pow(2,16))-$dUT/pow(2,$D);

$redis->set("weather:barometer", $P/10.0);
$redis->set("weather:pressure", $P/10.0);
$redis->set("weather:inside", $T/10.0);
file_put_contents("baro.txt",$P."-".$T);
}
echo '{ "success": 1, "checkversion": "126" }';
?>
