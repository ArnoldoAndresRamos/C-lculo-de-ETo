<?php
// Calculo de la evapotranpiracion de referncia
// usando metodo Ecuacion de Penman-Monteith 


//  Parametros usados en el calculo  

$Tmax=35.8;  // 1.- Temperatura Maxima 		-> Tmax °C
$Tmin=6.4;   // 2.- Temaperatura Minima 	-> Tmin °C
$Altitud=100;  // 3.- Altitud			-> Altitud Metros					
$u2=0;	     // 4.- Velocidad del Viento	-> u2 metros/s-1
$HRmax=0;    // 5.- humedad relativa Maxima 	-> HRmax
$HRmin=0;    // 6.- humedad relativa Maxima 	-> HRmin


// Temperatura media Tmedia °C
$Tmedia = ($Tmax+$Tmin)/2;


//  Δ (Pen curva de presión de vapor) en kPa°C-1
/*		(4098 [0.6108 e([17,27*Tmedia]/[Tmedia+237,3])])  
ecuación	________________________________________________
				(Tmedia+237,3)^2                       */

$a=exp((17.27*$Tmedia)/($Tmedia+237.3));
$b=0.6108*$a;
$c=4098*$b;
$d=pow(($Tmedia+237.3),2); //pow() para elevear al cuadrado
$r=$c/$d;
echo "Pendiente de la curva de preción de vapor ".$r."<br>";


// P (Presión atmosférica)
$w1=(293-(0.0065*$Altitud))/293;
$w1=pow($w,5.26);
$w1=101.3*$w1;
echo "Presion Atmosférica ".$w1;

?>
