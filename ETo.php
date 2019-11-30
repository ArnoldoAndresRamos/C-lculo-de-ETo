<?php
/*
	Calculo de la evapotranpiracion de referncia
 	usando metodo Ecuacion de Penman-Monteith 
*/
function ETo($Tmax, $Tmin, $HRmax,  $HRmin, $Latitud, $Altitud, $Dia_Juliano, $u2, $n ){
	
	$cons_SB = 0.000000004903;
	$Tmedia = ($Tmax+$Tmin)/2;
	$a=exp((17.27*$Tmedia)/($Tmedia+237.3));
	$b=0.6108*$a;
	$c=4098*$b;
	$d=pow(($Tmedia+237.3),2); 
	$r=$c/$d;
	$w=(293-(0.0065*$Altitud))/293;
	$w1=pow($w,5.26);
	$P=101.3*$w1;
	$y = 0.000665*$P;  
	$u2_1 = 1+0.34*$u2;
	$zr = $r/($r+$y*$u2_1); 
	$zy = $y/($r+$y*($u2_1)); 
	$Tmedia_u2 = (900/($Tmedia + 273))*$u2;	
	$e0_Tmax=0.6108*exp((17.27*$Tmax)/($Tmax+237.3)); 
	$e0_Tmin=0.6108*exp((17.27*$Tmin)/($Tmin+237.3));
	$es= ($e0_Tmax + $e0_Tmin)/2;
	$ea = ((($e0_Tmin * $HRmax)/100)+(($e0_Tmax * $HRmin)/100))/2; 
	$dr= 1 + 0.033 * cos((2 * 3.14159 / 365)*$Dia_Juliano);
	$ds= 0.409 * sin(((2 * 3.14159 / 365)*$Dia_Juliano) - 1.39 ); 
	$ws= acos(-tan($Latitud * 3.14159 /180 )* tan($ds)); 
	$se = sin($Latitud*3.14159/180 ) * sin($ds);
	$co = cos($Latitud*3.14159/180 ) * cos($ds);
	$Ra = (24*60/3.14159)*0.082*$dr*($ws* $se + $co*sin($ws)); //en MJm-2día-1
	$N=(24/3.14159)*$ws; 
	$n_N = $n/$N; 
	$Rs = (0.25+(0.5*$n_N))* $Ra; 
	$Rso =  (0.75+2*($Altitud)/100000)*$Ra; 
	$Rs_Rso = $Rs/$Rso; 
	$Rns = (1-0.23 )*$Rs; 
	$TmaxK4   = $cons_SB*pow(($Tmax+273.16),4);
	$TminK4   = $cons_SB*pow(($Tmin+273.16),4);
	$promedio = ($TmaxK4+$TminK4)/2;
	$Rs_Rso2  = (0.34-(0.14*sqrt($ea)));
	$Rs_Rso3  = ((1.35 *($Rs_Rso))-0.35);
	$Rnl= $promedio *  $Rs_Rso2 *  $Rs_Rso3; 
	$Rn = $Rns-$Rnl; 
	$G = 0;
	$Rn_G =  $Rn-$G; 
	$Rn_G_mm = 0.408*($Rn_G); 
	$ETo= ($zr * $Rn_G_mm)+($zy * $Tmedia_u2 *$es_ea);
	return $ETo;
   }
?>
