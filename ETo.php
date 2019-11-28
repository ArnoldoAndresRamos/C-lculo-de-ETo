<?php
// Calculo de la evapotranpiracion de referncia
// usando metodo Ecuacion de Penman-Monteith 

//  Parametros usados en el calculo  

$Tmax=35.8;  // 1.- Temperatura Maxima 		-> Tmax °C
$Tmin=6.4;   // 2.- Temaperatura Minima 	-> Tmin °C
$Altitud=100;  // 3.- Altitud			-> Altitud Metros					
$u2=3.1;     // 4.- Velocidad del Viento	-> u2 metros/s-1
$HRmax=100;    // 5.- humedad relativa Maxima 	-> HRmax %
$HRmin=23.8;    // 6.- humedad relativa Maxima 	-> HRmin %
$Latitud=-36; 
$Dia_Juliano=20;
$n=9.25; 
$cons_StefanBoltzmann = 0.000000004903; // (MJK^4/m^2)/día^1

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

$u2_1 = 1+0.34*$u2;//   (1+0,34u2)
echo "(1+0,34u2) ".$u2_1."<br>";
			 
$zr = $r/($r+$y*$u2_1); //  Δ/[Δ+γ(1+0,34u2)]
echo "Δ/[Δ+γ(1+0,34u2)] ".$zr."<br>";

$zy = $y/($r+$y*($u2_1)); //  γ/[Δ+γ(1+0,34u2)]
echo "γ/[Δ+γ(1+0,34u2)] ".$zy."<br>";

$Tmedia_u2 = (900/($Tmedia + 273))*$u2; //[ 900 / (Tmedia + 273) ] u2
echo "[ 900 / (Tmedia + 273) ] u2 ".$Tmedia_u2."<br>";


// Calculo del déficit de Presión de vapor 
$e0_Tmax=0.6108*exp((17.27*$Tmax)/($Tmax+237.3)); // KPa
$e0_Tmin=0.6108*exp((17.27*$Tmin)/($Tmin+237.3)); // KPa

//Presión de saturación de vapor es = [(e°(Tmax) + e°(Tmin)]/2
$es= ($e0_Tmax + $e0_Tmin)/2; //KPa
echo "Presión de saturación de vapor es".$es."<br>";


// Presión real de vapor (ea) derivada de datos de humedad relativa
$ea = ((($e0_Tmin * $HRmax)/100)+(($e0_Tmax * $HRmin)/100))/2; //KPa
echo" presion real de vapor ".$ea."<br>";

/*  Déficit de presión de vapor (es-ea) calculada con HRmin y HRmax  */ 
$es_ea = $es - $ea; // KPa
echo" Déficit de presion de vapor ".$es_ea."<br>";



/*  Calculo de Radiación

Parametros
-Latitud
-Dia juliano
-n 
*/
$Latitud=-36;
$Dia_Juliano=20;
$n=9.25; //

 // /*
$dr= 1 + 0.033 * cos((2 * 3.14159265358979323846 / 365)*$Dia_Juliano);//dr inverso de la dist rel Tierra - Sol
echo " inverso de la distancia real tierra-sol ".$dr."<br>";

$ds= 0.409 * sin(((2 * 3.14159265358979323846 / 365)*$Dia_Juliano) - 1.39 ); // δ declinación solar en Radianes (rad)
echo " δ declinación solar ".$ds."<br>";

$ws= acos(-tan($Latitud * 3.14159265358979323846 /180 )* tan($ds)); // ωs ángulo solar de puesta de Sol en Radianes (rad)
echo " ωs ángulo solar de puesta de Sol ".$ws."<br>";

$se = sin($Latitud*3.14159/180 ) * sin($ds); // seno(latitud)*seno(δ) 
$co = cos($Latitud*3.14159/180 ) * cos($ds); //cos(latitud)*cos(δ)

$Ra = (24*60/3.14159265358979323846)*0.082*$dr*($ws* $se + $co*sin($ws)); //en MJm-2día-1
echo "Ra ".$Ra."<br>";

$N=(24/3.14159265358979323846)*$ws; // Duración máxima de la insolación (N)
echo " N ".$N."<br>";
$n_N = $n/$N; //duración relativa de la insolación
echo " n/N ".$n_N."<br>";

$Rs = (0.25+(0.5*$n_N))* $Ra; // Rs (R solar o de onda corta) en MJ m-2 día-1
echo "Rs ".$Rs."<br>";

//Radiación solar en un día despejado Rso (R solar o de onda corta, c. desp) en MJ m-2 día-1
$Rso =  (0.75+2*($Altitud)/100000)*$Ra; 
echo "Rso ".$Rso."<br>";

$Rs_Rso = $Ro/$Rso; //  Rs/Rso Radiación relativa de onda corta
echo "Rs/Rso ".$Rs_Rso."<br>";

$Rns = (1-0.23 )*$Rs; // Rns Radiación neta de onda corta MJ m-2 día-1
echo "Rns ".$Rns."<br>";


//  Calculo de la Radiación neta de onda larga (Rnl)
// σTmaxK4

$TmaxK4= $cons_StefanBoltzmann*pow(($Tmax+273.16),4);
$TmaxK4= $cons_StefanBoltzmann*pow(($Tmin+273.16),4);
$promedio=($TmaxK4+$TmaxK4)/2;
$Rnl= ($promedio)*(0.34-0.14*(sqrt($es)))*((1.35 *($Rs_Rso))-0.35); // Rnl (Radiación neta de onda larga) MJ m^2 día^1
echo "Rnl ".$Rnl."<br>";

// Calculo de radiacion neta (Rn=Rns-Rnl)
$Rn = $Rns-$Rnl; // MJ m^2 día^1
echo " Rn ".$Rn."<br>";

//Rn - G
$G = 0;
$Rn_G =  $Rn-$G; // MJ m^2 día^1
echo "Rn - G ".$Rn_G."<br>";

// 0.408(Rn - G)
$Rn_G_mm = 0.408*($Rn_G); // mm
echo " 0.408(Rn - G) ".$Rn_G_mm."<br>";

// Resultado de calculo de Evapotranspiracion de referencia  en mm/día
$ETo= ($zr * $Rn_G_mm)+($zy * $Tmedia_u2 *$es_ea);
echo "Evapotranspiracion de referencia :".$ETo."mm/día"."<br>";

?>
