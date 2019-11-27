<?php
# Calculo de la evapotranpiracion de referncia
# usando metodo Ecuacion de Penman-Monteith 


# Parametros usados en el calculo  

$Tmax=35.8;  // 1.- Temperatura Maxima 		-> Tmax °C
$Tmin=6.4;   // 2.- Temaperatura Minima 	-> Tmin °C
$Altitud=0;  // 3.- Altitud			-> Altitud Metros					
$u2=0;	     // 4.- Velocidad del Viento	-> u2 metros/s-1
$HRmax=0;    // 5.- humedad relativa Maxima 	-> HRmax
$HRmin=0;    // 6.- humedad relativa Maxima 	-> HRmin


# Temperatura media Tmedia °C
$Tmedia = ($Tmax+$Tmin)/2;


# Δ (Pen curva de presión de vapor) en kPa°C-1
/*		(4098 [0.6108 e([17,27*Tmedia]+[Tmedia+237,3])])  
ecuación	________________________________________________
				(Tmedia+237,3)^2                       */

$PenCurvaDePresionDeVapor =4098 (0.6108 * exp(  (17.27*$Tmedia)/($Tmedia+237.3) )   /   (($Tmedia+237.3)^2);
$PenCurvaDePresionDeVapor2 =((4098(0.6108*EXP((17.27*$Tmedia)/($Tmedia+237.3))))/(($Tmedia+237.3)^2));
echo $PenCurvaDePresionDeVapor."<br>";
echo $PenCurvaDePresionDeVapor2."<br>";


#


?>
