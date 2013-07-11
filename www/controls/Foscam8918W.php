<?php
$wwwDir='/var/www/';
$baseDir=$wwwDir.'www.cloudsecuritycam.com/csc/';

/* URL */
	define("GET_PARAMS_URL",'get_params.cgi?user=%USER%&pwd=%PWD%');
	define("SET_FTPCONFIG_URL",'set_ftp.cgi?user=%USER%&pwd=%PWD%&ftp_srv=%FTPSERVER%&ftp_port=%FTPPORT%&ftp_user=%FTPUSER%&ftp_pwd=%FTPPASSWORD%&ftp_dir=&ftp_mode=0&ftp_retain=0&ftp_upload_interval=1&filename=&numberoffiles=0&schedule_enable=0&schedule_sun_0=0&schedule_sun_1=0&schedule_sun_2=0&schedule_mon_0=0&schedule_mon_1=0&schedule_mon_2=0&schedule_tue_0=0&schedule_tue_1=0&schedule_tue_2=0&schedule_wed_0=0&schedule_wed_1=0&schedule_wed_2=0&schedule_thu_0=0&schedule_thu_1=0&schedule_thu_2=0&schedule_fri_0=0&schedule_fri_1=0&schedule_fri_2=0&schedule_sat_0=0&schedule_sat_1=0&schedule_sat_2=0');
	define("SET_CAMERANAME_URL",'set_alias.cgi?alias=%NAME%&user=%USER%&pwd=%PWD%');
	define("PTZ_URL",'decoder_control.cgi?user=%USER%&pwd=%PWD%&command=%CMD%');
	define("SNAPSHOT_URL",'snapshot.cgi?user=%USER%&pwd=%PWD%');
	
/* COMMAND LINE */
if(php_sapi_name()=='cli'){
	$action=$argv[1];
	if($action=='GETMOTIONDETECTPANEL'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		//on recupere l'url pour interroger la camera		
		$url=GET_PARAMS_URL;
		$checkURL=str_replace('%USER%',$usr,$url);
		$checkURL=str_replace('%PWD%',$pwd,$checkURL);
		$fullUrl='http://'.$host.':'.$port.'/'.$checkURL;
		//on interroge la camera
		$content=CURLIT($fullUrl);
		$params=array();
		
		
		$html='';
		//
		die($html);
	}
	if($action=='SETFTPSETTINGS'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		$ftpserver=$argv[6];
		$ftpuser=$argv[7];
		$ftppass=$argv[8];
		$ftppport=$argv[9];
		//on recupere l'url pour configurer la camera		
		$url=SET_FTPCONFIG_URL;
		$setURL=str_replace('%USER%',$usr,$url);
		$setURL=str_replace('%PWD%',$pwd,$setURL);
		$setURL=str_replace('%FTPSERVER%',$ftpserver,$setURL);
		$setURL=str_replace('%FTPPORT%',$ftpport,$setURL);
		$setURL=str_replace('%FTPUSER%',$ftpuser,$setURL);
		$setURL=str_replace('%FTPPASSWORD%',$ftppass,$setURL);
		$fullUrl='http://'.$host.':'.$port.'/'.$setURL;
		//on envoi la commande a la camera
		$content=CURLIT($fullUrl);
		//
		die('OK');
	}
	if($action=='SETREQUIREDSETTINGS'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		$nom=$argv[6];
		//on change le nom de la camera
			$url=SET_CAMERANAME_URL;
			$setURL=str_replace('%USER%',$usr,$url);
			$setURL=str_replace('%PWD%',$pwd,$setURL);
			$setURL=str_replace('%NAME%',$nom,$setURL);
			$fullUrl='http://'.$host.':'.$port.'/'.$setURL;
			//on envoi la commande a la camera
			$content=CURLIT($fullUrl);
		//
		die('OK');
	}
}

header('Content-Type: text/html; charset=ISO-8859-1');
$action=$_GET['action'];
$output=array();

if($action=='GET_PANEL'){
	$Cid=$_GET['cameraid'];
	$Cname=$_GET['name'];
	$html='<h3>'.$Cname.'</h3>';
	$html.='<br>';
	//control panel
	$html.='<table width=100% cellspacing=0 cellpadding=0><tr><td valign=top width="160" align=left>';
	$html.='	<table id="Table_01" width="148" height="148" border="0" cellpadding="0" cellspacing="0">';
	$html.='		<tr><td><img src="controls/Slice.png" width="49" height="49" alt=""></td><td><img src="controls/Slice-02.png" width="50" height="49" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConUp\');$(this).attr(\'src\',\'controls/Slice-02-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-02.png\');" style="cursor:pointer"></td><td><img src="controls/Slice-03.png" width="49" height="49" alt=""></td></tr>';
	$html.='		<tr><td><img src="controls/Slice-04.png" width="49" height="50" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConLeft\');$(this).attr(\'src\',\'controls/Slice-04-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-04.png\');" style="cursor:pointer"></td><td><img src="controls/Slice-05.png" width="50" height="50" alt="" onclick="controlCamera(\''.$Cid.'\',\'presetHome\');" style="cursor:pointer" title="RAZ"></td><td><img src="controls/Slice-06.png" width="49" height="50" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConRight\');$(this).attr(\'src\',\'controls/Slice-06-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-06.png\');" style="cursor:pointer"></td></tr>';
	$html.='		<tr><td><img src="controls/Slice-07.png" width="49" height="49" alt=""></td><td><img src="controls/Slice-08.png" width="50" height="49" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConDown\');$(this).attr(\'src\',\'controls/Slice-08-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-08.png\');" style="cursor:pointer"></td><td><img src="controls/Slice-09.png" width="49" height="49" alt=""></td></tr>';
	$html.='	</table>';
	$html.='</td><td valign=center align=right>';
	$html.='	<table cellspacing=0 cellpadding=2>';
	$html.='		<tr><td><img src="controls/hpatrol_up.png" style="cursor:pointer" title="patrouille horizontale" onclick="controlCamera(\''.$Cid.'\',\'hpatrol\');"/></td><td><img src="controls/vpatrol_up.png" style="cursor:pointer" title="patrouille verticale" onclick="controlCamera(\''.$Cid.'\',\'vpatrol\');"/></td></tr>';
	$html.='		<tr><td><img src="controls/snapshot.png" style="cursor:pointer" title="prendre une photo" onclick="tabCameras['.$Cid.'].snapshot();"/></td><td><img src="controls/R_stop_up.png" style="cursor:pointer" title="arreter la patrouille" onclick="controlCamera(\''.$Cid.'\',\'moveStop\');"/></td></tr>';
	$html.='		<tr><td><img src="controls/switchon_up.png" style="cursor:pointer" title="activer Infra rouges" onclick="controlCamera(\''.$Cid.'\',\'wake\');"/></td><td><img src="controls/switchoff_up.png" style="cursor:pointer" title="desactiver infra rouges" onclick="controlCamera(\''.$Cid.'\',\'sleep\');"/></td></tr>';
	$html.='	</table>';
	
	die($html);
}

if($action=='GET_MOTIONDETECTCONFIG'){
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['usr'];
	$pwd=$_GET['pwd'];
	//on recupere l'url pour interroger la camera		
	//$url=GET_MOTIONDETECTCONFIG_URL;
	//$checkURL=str_replace('%USER%',$usr,$url);
	//$checkURL=str_replace('%PWD%',$pwd,$checkURL);
	//$fullUrl='http://'.$host.':'.$port.'/'.$checkURL;
	//
	//$content=CURLIT($fullUrl);
	$output['model']='FOSCAM8918W';
	//
	die(json_encode($output));
}

if($action=='SET_MOTIONDETECTSTATE'){
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['usr'];
	$pwd=$_GET['pwd'];
	$state=$_GET['state'];
	//
	die('OK');
}

if($action=='CONTROL'){
	$cmd=$_GET['cmd'];
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['login'];
	$pwd=$_GET['pwd'];
	//on convertie la commande en numero
		$cmdNum=1;
		switch($cmd){
			case 'moveConUp':		//haut
				$cmdNum=0;
				break;
			case 'moveConDown':		//bas
				$cmdNum=2;
				break;
			case 'moveConLeft':		//gauche
				$cmdNum=6;
				break;
			case 'moveConRight':	//droite
				$cmdNum=4;
				break;
			case 'moveStop':		//stop
				$cmdNum=1;
				break;
			case 'presetHome':		//home
				$cmdNum=25;
				break;
			case 'wake':			//infra rouges ON
				$cmdNum=95;
				break;
			case 'sleep':			//infra rouges OFF
				$cmdNum=94;
				break;
			case 'hpatrol':			//patrouille horizontale
				$cmdNum=28;
				break;
			case 'vpatrol':			//patrouille verticale
				$cmdNum=26;
				break;	
		}
	//on recupere l'url pour interroger la camera		
		$url=PTZ_URL;
		$ptzURL=str_replace('%USER%',$usr,$url);
		$ptzURL=str_replace('%PWD%',$pwd,$ptzURL);
		$ptzURL=str_replace('%CMD%',$cmdNum,$ptzURL);
		$fullUrl='http://'.$host.':'.$port.'/'.$ptzURL;
	//on interroge la camera
		$content=CURLIT($fullUrl);
	
	$output['status']='OK';
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='SNAPSHOT'){
	$url=$_GET['url'];
	$name=$_GET['name'];
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['login'];
	$pwd=$_GET['pwd'];
	$name=remove_accents($name);
	//
	$url=SNAPSHOT_URL;
	$url=str_replace('%USER%',$usr,$url);
	$url=str_replace('%PWD%',$pwd,$url);
	$fullUrl='http://'.$host.':'.$port.'/'.$url;
	//$randomNum=rand(1111111111,9999999999);
	$date = date("d-m-Y");
	$heure = date("H-i-s");
	$outname=$name.'-'.$date.'-'.$heure.'.jpg';
	$outfile=$baseDir.'snapshots/'.$outname;
	$out4web='snapshots/'.$outname;
	$cmd='wget "'.$fullUrl.'" -O "'.$outfile.'"';
	exec($cmd);
	//$output['url']=$fullUrl;
	//$output['cmd']=$cmd;
	//die(json_encode($output));
	//
	$output['snapshot']=array();
	$output['snapshot']['filename']=$outname;
	$output['snapshot']['url']=$out4web;
	die(json_encode($output));
}

/* FONCTIONS ++ */
	//parse XML
	function value_in($element_name, $xml, $content_only = true) {
		if ($xml == false) {
			return false;
		}
		$found = preg_match('#<'.$element_name.'(?:\s+[^>]+)?>(.*?)'.
				'</'.$element_name.'>#s', $xml, $matches);
		if ($found != false) {
			if ($content_only) {
				return $matches[1];  //ignore the enclosing tags
			} else {
				return $matches[0];  //return the full pattern match
			}
		}
		// No match found: return false.
		return false;
	}
	function element_set($element_name, $xml, $content_only = false) {
		if ($xml == false) {
			return false;
		}
		$found = preg_match_all('#<'.$element_name.'(?:\s+[^>]+)?>' .
				'(.*?)</'.$element_name.'>#s',
				$xml, $matches, PREG_PATTERN_ORDER);
		if ($found != false) {
			if ($content_only) {
				return $matches[1];  //ignore the enlosing tags
			} else {
				return $matches[0];  //return the full pattern match
			}
		}
		// No match found: return false.
		return false;
	}
	//fin parse XML
	
	function CURLIT($url){
		////////// PARAMS
	 
		// Complétez $url avec l'url cible (l'url de la page que vous voulez télécharger)
		//$url='http://192.168.1.20:8001/get_status.cgi?user=admin&pwd=lezennes';
		 
		// Tableau contenant les options de téléchargement
		$options=array(
			  CURLOPT_URL            => $url,  // Url cible (l'url la page que vous voulez télécharger)
			  CURLOPT_RETURNTRANSFER => true,  // Retourner le contenu téléchargé dans une chaine (au lieu de l'afficher directement)
			  CURLOPT_HEADER         => false, // Ne pas inclure l'entête de réponse du serveur dans la chaine retournée
			  CURLOPT_FAILONERROR    => true   // Gestion des codes d'erreur HTTP supérieurs ou égaux à 400
		);
		 
		////////// MAIN
		 
		// Création d'un nouvelle ressource cURL
		$CURL=curl_init();
		// Erreur suffisante pour justifier un die()
		if(empty($CURL)){
			die("ERREUR curl_init : Il semble que cURL ne soit pas disponible.");
		}
		 
			  // Configuration des options de téléchargement
			  curl_setopt_array($CURL,$options);
		 
			  // Exécution de la requête
			  $content=curl_exec($CURL);       // Le contenu téléchargé est enregistré dans la variable $content. Libre à vous de l'afficher.
		 
			  // Si il s'est produit une erreur lors du téléchargement
			  if(curl_errno($CURL)){
					// Le message d'erreur correspondant est affiché
					echo "ERREUR curl_exec : ".curl_error($CURL);
			  }
		 
		// Fermeture de la session cURL
		curl_close($CURL);
		
		return $content;
	}

	function remove_accents($str, $charset='utf-8'){
		$str = htmlentities($str, ENT_NOQUOTES, $charset);
		
		$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
		$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
		$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
		
		return $str;
	}

?>