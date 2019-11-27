<?php
// Calculo de la evapotranpiracion de referncia
// usando metodo Ecuacion de Penman-Monteith 

//  Parametros usados en el calculo  

$Tmax=35.8;  // 1.- Temperatura Maxima 		-> Tmax °C
$Tmin=6.4;   // 2.- Temaperatura Minima 	-> Tmin °C
$Altitud=100;  // 3.- Altitud			-> Altitud Metros					
$u2=3.1;     // 4.- Velocidad del Viento	-> u2 metros/s-1
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

//    P  Presión atmosférica  en KPa
$w=(293-(0.0065*$Altitud))/293;
$w1=pow($w,5.26);
$P=101.3*$w1;
echo "Presion Atmosférica ".$P."<br>";

//    γ Constante pscicrométrica en kPa°C-1

$y = 0.000665*$P;  // $P es Presión Atmosférica
echo "Constante psicrometrica ".$y."<br>";

//   (1+0,34u2)
$u2_1=1+0.34*$u2;
echo "(1+0,34u2) ".$u2_1."<br>";

			 
$zr = $r/($r+$y*$u2_1); //  Δ/[Δ+γ(1+0,34u2)]
echo "Δ/[Δ+γ(1+0,34u2)] ".$zr."<br>";

$zy = $y/[$r+$y($u2_1)]; //  γ/[Δ+γ(1+0,34u2)]
echo "γ/[Δ+γ(1+0,34u2)] ".$zy."<br>";

$Tmedia_u2 = (900/($Tmedia + 273))*$u2; //[ 900 / (Tmedia + 273) ] u2
echo "[ 900 / (Tmedia + 273) ] u2 ".$Tmedia_u2."<br>";


// Calculo del déficit de Presión de vapor 

$e0_Tmax=0.6108*exp((17.27*$Tmax)/($Tmax+237.3));
$e0_Tmin=0.6108*exp((17.27*$Tmin)/($Tmin+237.3));

//Presión de saturación de vapor es = [(e°(Tmax) + e°(Tmin)]/2
$es= ($e0_Tmax + $e0_Tmin)/2;
echo "Presión de saturación de vapor es".$es."<br>";

?>
