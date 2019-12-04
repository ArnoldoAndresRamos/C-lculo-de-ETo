<?php

// Calculo de la evapotranpiracion de referncia
// usando metodo Ecuacion de Penman-Monteith 

//  Parametros usados en el calculo  
$Tmax=35.8;  // 1.- Temperatura Maxima 		-> Tmax °C
$Tmin=6.4;   // 2.- Temaperatura Minima 	-> Tmin °C
$HRmax=100;    // 5.- humedad relativa Maxima 	-> HRmax %
$HRmin=23.8;    // 6.- humedad relativa Maxima 	-> HRmin %
$Latitud=-36; 
$Altitud=100;  // 3.- Altitud			-> Altitud Metros
$Dia_Juliano=20;

$u2=3.1;     // 4.- Velocidad del Viento	-> u2 metros/s-1
$n=9.25; 
$fecha="24-02-2019";

function numDia($fecha){
	
	$dia = $fecha[0].$fecha[1];
	$mes =  $fecha[3].$fecha[4];
	$anio = $fecha[6].$fecha[7].$fecha[8].$fecha[9];
	$dias_mes = [31,28,31,30,31,30,31,31,30,31,30,31];
	$i=0;
	$n=1;
	$num_dia=$dia;

	while($mes>$n)
	{
		$num_dia = $num_dia + $dias_mes[i];
		$i = $i+1;
		$n = $n+1; 
		echo "num_dia:".$num_dia." i:".$i." n:".$n."<br>";
	}

	if($anio % 4 ==0 and $mes>2) 
	{
		$num_dia = $num_dia + 1;
	}

	//return $num_dia;
	echo "mes :".$mes." dia:".$dia."<br>"; 
	echo $num_dia;
};
numDia($fecha)."<br>";


function ETo($Tmax, $Tmin, $HRmax,  $HRmin, $Latitud, $Altitud, $Dia_Juliano, $u2, $n ){
	
	//constante de Boltzmann
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
	// echo "Pendiente de la curva de preción de vapor ".$r."<br>";


	//    P  Presión atmosférica  en KPa
	$w=(293-(0.0065*$Altitud))/293;
	$w1=pow($w,5.26);
	$P=101.3*$w1;
	//echo "Presion Atmosférica ".$P."<br>";


	//    γ Constante pscicrométrica en kPa°C-1
	$y = 0.000665*$P;  // $P es Presión Atmosférica
	//echo "Constante psicrometrica ".$y."<br>";

	$u2_1 = 1+0.34*$u2;//   (1+0,34u2)
	//echo "(1+0,34u2) ".$u2_1."<br>";

	$zr = $r/($r+$y*$u2_1); //  Δ/[Δ+γ(1+0,34u2)]
	//echo "Δ/[Δ+γ(1+0,34u2)] ".$zr."<br>";

	$zy = $y/($r+$y*($u2_1)); //  γ/[Δ+γ(1+0,34u2)]
	//echo "γ/[Δ+γ(1+0,34u2)] ".$zy."<br>";

	$Tmedia_u2 = (900/($Tmedia + 273))*$u2; //[ 900 / (Tmedia + 273) ] u2
	

	// Calculo del déficit de Presión de vapor 
	$e0_Tmax=0.6108*exp((17.27*$Tmax)/($Tmax+237.3)); // KPa
	$e0_Tmin=0.6108*exp((17.27*$Tmin)/($Tmin+237.3)); // KPa

	//Presión de saturación de vapor es = [(e°(Tmax) + e°(Tmin)]/2
	$es= ($e0_Tmax + $e0_Tmin)/2; //KPa
	
	// Presión real de vapor (ea) derivada de datos de humedad relativa
	$ea = ((($e0_Tmin * $HRmax)/100)+(($e0_Tmax * $HRmin)/100))/2; //KPa
	
	/*  Déficit de presión de vapor (es-ea) calculada con HRmin y HRmax  */ 
	$es_ea = $es - $ea; // KPa
	


	/*  Calculo de Radiación

	Parametros
	-Latitud
	-Dia juliano
	-n 
	*/
	$Latitud = $Latitud;
	$Dia_Juliano = $Dia_Juliano;
	$n = $n; 

	//dr inverso de la dist rel Tierra - Sol
	$dr= 1 + 0.033 * cos((2 * 3.14159265358979323846 / 365)*$Dia_Juliano);
	
	// δ declinación solar en Radianes (rad)
	$ds= 0.409 * sin(((2 * 3.14159265358979323846 / 365)*$Dia_Juliano) - 1.39 ); 

	// ωs ángulo solar de puesta de Sol en Radianes (rad)
	$ws= acos(-tan($Latitud * 3.14159265358979323846 /180 )* tan($ds)); 
	$se = sin($Latitud*3.14159/180 ) * sin($ds); // seno(latitud)*seno(δ) 
	$co = cos($Latitud*3.14159/180 ) * cos($ds); //cos(latitud)*cos(δ)
	$Ra = (24*60/3.14159265358979323846)*0.082*$dr*($ws* $se + $co*sin($ws)); //en MJm-2día-1
	
	
	// Duración máxima de la insolación (N)
	$N=(24/3.14159265358979323846)*$ws; 
	
	//duración relativa de la insolación
	$n_N = $n/$N; 
		
	// Rs (R solar o de onda corta) en MJ m-2 día-1
	$Rs = (0.25+(0.5*$n_N))* $Ra; 
	
	//Radiación solar en un día despejado Rso (R solar o de onda corta, c. desp) en MJ m-2 día-1
	$Rso =  (0.75+2*($Altitud)/100000)*$Ra; 
	
	//  Rs/Rso Radiación relativa de onda corta
	$Rs_Rso = $Rs/$Rso; 
	
	// Rns Radiación neta de onda corta MJ m-2 día-1
	$Rns = (1-0.23 )*$Rs; 
	
	
	/*   Calculo de la Radiación neta de onda larga (Rnl)  */

	// σTmaxK4
	$TmaxK4   = $cons_StefanBoltzmann*pow(($Tmax+273.16),4);
	$TminK4   = $cons_StefanBoltzmann*pow(($Tmin+273.16),4);
	$promedio = ($TmaxK4+$TminK4)/2;
	
	$Rs_Rso2  = (0.34-(0.14*sqrt($ea)));
	$Rs_Rso3  = ((1.35 *($Rs_Rso))-0.35);
	
	$Rnl= $promedio *  $Rs_Rso2 *  $Rs_Rso3; // Rnl (Radiación neta de onda larga) MJ m^2 día^1
	

	/*      Calculo de radiacion neta (Rn=Rns-Rnl)    */
	$Rn = $Rns-$Rnl; // MJ m^2 día^1
	

	/*  Rn - G  */
	$G = 0;
	$Rn_G =  $Rn-$G; // MJ m^2 día^1
	

	/*    0.408(Rn - G)    */
	$Rn_G_mm = 0.408*($Rn_G); // mm
	

	/*    Resultado de calculo de Evapotranspiracion de referencia  en mm/día  */
	$ETo= ($zr * $Rn_G_mm)+($zy * $Tmedia_u2 *$es_ea);
	echo $ETo; //"mm/día"."<br>"

}

ETo($Tmax, $Tmin, $HRmax,  $HRmin, $Latitud, $Altitud, $Dia_Juliano, $u2, $n );
?>
