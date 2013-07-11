<?php
/* VARS */
	
	//urls
		define("GET_ALLPARAMS_URL" , "http://%USER%:%PWD%@%HOST%:%PORT%/cgi-bin/admin/getparam.cgi");
		define("GET_PARAM_URL" , "http://%USER%:%PWD%@%HOST%:%PORT%/cgi-bin/admin/getparam.cgi?%PARAM%");
		define("SET_PARAM_URL" , "http://%USER%:%PWD%@%HOST%:%PORT%/cgi-bin/admin/setparam.cgi?%PARAM%");
		//motion detect
			//liste des parametres modifiables depuis l'interface
			$MD_SETUPparamsList=array('motion_c0_win_i0_sensitivity','event_i0_enable','event_i1_enable','event_i2_enable','event_i0_begintime','event_i0_endtime','event_i0_weekday','event_i1_begintime','event_i1_endtime','event_i1_weekday','event_i2_begintime','event_i2_endtime','event_i2_weekday');
		
		
/* FIN VARS */

/* COMMAND LINE */
if(php_sapi_name()=='cli'){
	$action=$argv[1];
	if($action=='SETFTPSETTINGS'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		$ftpserver=$argv[6];
		$ftpuser=$argv[7];
		$ftppass=$argv[8];
		$ftppport=$argv[9];
		//
		$parametres='server_i0_name=FTP&server_i0_type=ftp&server_i0_ftp_address='.$ftpserver.'&server_i0_ftp_username='.$ftpuser.'&server_i0_ftp_passwd='.$ftppass.'&server_i0_ftp_port='.$ftpport.'&server_i0_ftp_location=/&server_i0_ftp_passive=1';
		//on recupere l'url pour configurer la camera		
		$url=SET_PARAM_URL;
		$setURL=str_replace('%USER%',$usr,$url);
		$setURL=str_replace('%PWD%',$pwd,$setURL);
		$setURL=str_replace('%PARAMS%',$parametres,$setURL);
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
		//
		$parametres='system_hostname='.$nom;
		//on change le nom de la camera
			$url=SET_PARAM_URL;
			$setURL=str_replace('%USER%',$usr,$url);
			$setURL=str_replace('%PWD%',$pwd,$setURL);
			$setURL=str_replace('%PARAMS%',$parametres,$setURL);
			$fullUrl='http://'.$host.':'.$port.'/'.$setURL;
			//on envoi la commande a la camera
			$content=CURLIT($fullUrl);
		//
		die('OK');
	}
	if($action=='GETMOTIONDETECTPANEL'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		$id=$argv[6];
		//
		
		//on recupere l'url pour interroger la camera		
		$url=GET_ALLPARAMS_URL;
		$checkURL=str_replace('%USER%',$usr,$url);
		$checkURL=str_replace('%PWD%',$pwd,$checkURL);
		$checkURL=str_replace('%HOST%',$host,$checkURL);
		$checkURL=str_replace('%PORT%',$port,$checkURL);
		//
		$content=CURLIT($checkURL);
		//traite le contenu
		$params=array();
		$contentTab=explode("\n",$content);
		foreach ($contentTab as $line) {
			$tmp=explode("='",$line);
			$params[$tmp[0]]=substr(trim($tmp[1]),0,-1);
			unset($tmp);
		}
		//preparation des events
			$events_table='<table cellspacing=0 cellpadding=3 style="box-shadow:1px 1px 2px #000">';
			$events_table.='<tr style="text-align:center;background:#D3D3D3"><td width=100>Nom</td><td width=30>Etat</td><td width=30>Mail</td><td width=30>Dim</td><td width=30>Lun</td><td width=30>Mar</td><td width=30>Mer</td><td width=30>Jeu</td><td width=30>Ven</td><td width=30>Sam</td><td width=45>Début</td><td width=45>Fin</td></tr>';
			$events=array();
			for($x=0;$x<3;$x++){
				$events[$x]=array();
				$events[$x]['name']=$params['event_i'.$x.'_name'];
				$events[$x]['enable']=$params['event_i'.$x.'_enable'];
				$events[$x]['weekday']=$params['event_i'.$x.'_weekday'];
				$events[$x]['begintime']=$params['event_i'.$x.'_begintime'];
				$events[$x]['endtime']=$params['event_i'.$x.'_endtime'];
				//ON/OFF
					$checked='';
					if(intval($events[$x]['enable'])==1){ $checked='checked'; }
					$state='<input id="eventEnable'.$x.'" type="checkbox" '.$checked.'/>';
				//Mail
					$mail='<input id="eventMailEnable'.$x.'" type="checkbox"/>';
				//jour de la semaine
				$binary=decbin(intval($events[$x]['weekday']));
				$schedule=substr("000000".$binary,-7);
				$scheduleTab=str_split($schedule);
				$IMGday=array();
				for($a=0;$a<7;$a++){
					if($scheduleTab[$a]==0){
						$IMGday[$a]='res/empty.png';
					}else{
						$IMGday[$a]='res/checked.png';
					}
				}
				//
				if($params['event_i'.$x.'_name']!=''){
					$events_table.='<tr style="text-align:center;background:#FFF"><td>'.$events[$x]['name'].'</td><td>'.$state.'</td><td>'.$mail.'</td><td><img src="'.$IMGday[0].'" id="event'.$x.'-day0" onclick="changeDay('.$x.',0)" style="cursor:pointer"></img></td><td><img src="'.$IMGday[1].'" id="event'.$x.'-day1" onclick="changeDay('.$x.',1)" style="cursor:pointer"></img></td><td><img src="'.$IMGday[2].'" id="event'.$x.'-day2" onclick="changeDay('.$x.',2)" style="cursor:pointer"></img></td><td><img src="'.$IMGday[3].'" id="event'.$x.'-day3" onclick="changeDay('.$x.',3)" style="cursor:pointer"></img></td><td><img src="'.$IMGday[4].'" id="event'.$x.'-day4" onclick="changeDay('.$x.',4)" style="cursor:pointer"></img></td><td><img src="'.$IMGday[5].'" id="event'.$x.'-day5" onclick="changeDay('.$x.',5)" style="cursor:pointer"></img></td><td><img src="'.$IMGday[6].'" id="event'.$x.'-day6" onclick="changeDay('.$x.',6)" style="cursor:pointer"></img></td><td><input type="text" id="event'.$x.'-begin" value="'.$events[$x]['begintime'].'" size=5 /></td><td><input type="text" id="event'.$x.'-end" value="'.$events[$x]['endtime'].'" size=5 /></td></tr>';
				}
			}
			$events_table.='</table><br>';
		//
		$sSensitivity='<select id="sensitivity" class="select">';
		for($i=0;$i<101;$i++){
			$selected='';
			if($i==$params['motion_c0_win_i0_sensitivity']){
				$selected='selected';
			}
			$sSensitivity.='<option value="'.$i.'" '.$selected.'>'.$i.'%</option>';
		}
		$sSensitivity.='</table>';
		//
		$html='<div class="setupContainer">';
		$html.='<div class="title" onclick="tooglePanel(\'setupMotiondetectPanel\')" style="cursor:pointer"><table width=100% cellspacing=0 cellpadding=0><tr><td width=50 valign=top align=center><img src="motiondetectIcon.png"></img></td><td align=left valign=top><p>Détection de mouvement</p></td><td width=40 align=center valign=center><img id="setupMotiondetectPanelImg" src="down.png" width=20 height=20 style="width:20px !important;height:20px !important;"/></td></tr></table></div>';
		$html.='<div id="setupMotiondetectPanel" style="display:none"></br>';
		$html.='<table width=300 cellspacing=0 cellpadding=0 style="margin-left:20px;">';
		$html.='<tr><td width="110" align=left>Sensibilité</td>';
		$html.='<td align=left>'.$sSensitivity.'</td></tr>';
		$html.='</table>';
		$html.='<br><table width=550 cellspacing=0 cellpadding=0 style="margin-left:0;"></tr><td colspan=2 align=left>Planing:</td></tr></table><br>';
		$html.='<table width=550 cellspacing=0 cellpadding=0 style="margin-left:0;"></tr>';
		$html.='<td width=50% align=center><table width=100%><tr><td align=left><div style="border:1px solid #333;width:30px;height:15px;background:#DDD !important"></div></td><td align=left>Aucune alertes</td></tr></table></td>';
		$html.='<td width=50% align=center><table width=100%><tr><td align=left><div style="border:1px solid #333;width:30px;height:15px;background:#328AED !important"></div></td><td align=left>Détection de mouvements</td></tr></table></td>';
		$html.='</tr></table><br>';
		$html.=$events_table;
		$html.='<br></br>';
		$html.='<table width=550 cellspacing=0 cellpadding=0 style="margin-left:0;">';
		$html.='<tr><td colspan=2 align=right><input type="button" value="Enregistrer" onclick="save_motiondetect_settings(\'VIVOTEKIP8332\',\''.$id.'\');" class="button"/></td></tr>';
		$html.='</table></div></div><br>';
		//
		die($html);
	}
}
/* FIN CLI */

header('Content-Type: text/html; charset=ISO-8859-1');
$action=$_POST['action'];
$output=array();

if($action=='GET_PANEL'){
	$html='Vivotek IP8332';
	$html.='';
	die($html);
}

if($action=='GET_MOTIONDETECTCONFIG'){
	$host=$_POST['host'];
	$port=$_POST['port'];
	$usr=$_POST['usr'];
	$pwd=$_POST['pwd'];
	//on recupere l'url pour interroger la camera		
	$url=GET_ALLPARAMS_URL;
	$checkURL=str_replace('%USER%',$usr,$url);
	$checkURL=str_replace('%PWD%',$pwd,$checkURL);
	$checkURL=str_replace('%HOST%',$host,$checkURL);
	$checkURL=str_replace('%PORT%',$port,$checkURL);
	//
	$content=CURLIT(checkURL);
	//
	$output['model']='VIVOTEKIP8332';
	//traite le contenu
	$contentTab=$content.split("\n");
	foreach ($contentTab as $line) {
		$tmp=$line.split("=");
		$output[$tmp[0]]=$tmp[1];
		unset($tmp);
	}
	//
	die(json_encode($output));
}

if($action=='SET_MOTIONDETECTCONFIG'){
	$id=$_POST['id'];
	$host=$_POST['host'];
	$port=$_POST['port'];
	$usr=$_POST['usr'];
	$pwd=$_POST['pwd'];
	//on recupere les parametres pour la detection de mouvements
	$params=array();
	$parametres='';
	foreach($MD_SETUPparamsList as $key => $value){
		$params[$value] = $_POST[$value];
		$parametres.='&'.$value.'='.$params[$value];
	}
	//foreach($fixedVAL as $key => $value){
	//	$parametres.='&'.$key.'='.$value;
	//}
	//recupere les parametres des notifications
	include('../sql.conf');
	//connexion bdd
	$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
	if(!$liendb){
		systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
		die('ERREUR > Connexion SQL !');
	}
	mysql_select_db($bddnom);
	$tableCameras='cameras';
	$requeteSQL='UPDATE '.$tableCameras.' SET alertesPlan0 = '.$_POST['event_0_notification'].' , alertesPlan1 = '.$_POST['event_1_notification'].' , alertesPlan2 = '.$_POST['event_2_notification'].' WHERE id = '.$id;
	if(!mysql_query($requeteSQL)){
		mysql_close($liendb);
		die('ERREUR > Connexion SQL !');
	}
	//ferme connexion sql
	mysql_close($liendb);
	//on recupere l'url pour parametrer la camera		
	$url=SET_MOTIONDETECTCONFIG_URL;
	$setURL=str_replace('%USER%',$usr,$url);
	$setURL=str_replace('%PWD%',$pwd,$setURL);
	$setURL=str_replace('%PARAMS%',$parametres,$setURL);
	$fullUrl='http://'.$host.':'.$port.'/'.$setURL;
	//
	$content=CURLIT($fullUrl);
	//
	$resultat=value_in('result' , $content);
	if(intVal($resultat)==0){
		die('OK');
	}else{
		die('ERROR');
	}
	//
	//die($content);
}

if($action=='SET_MOTIONDETECTSTATE'){
	$host=$_POST['host'];
	$port=$_POST['port'];
	$usr=$_POST['usr'];
	$pwd=$_POST['pwd'];
	$state=$_POST['state'];
	//
	$stateTab=explode(',',$state);
	//
	$parametres='motion_c0_enable='.$stateTab[0].'&motion_c1_enable='.$stateTab[1].'&motion_c2_enable='.$stateTab[2];
	//on recupere l'url pour parametrer la camera		
		$url=SET_PARAM_URL;
		$setURL=str_replace('%USER%',$usr,$url);
		$setURL=str_replace('%PWD%',$pwd,$setURL);
		$setURL=str_replace('%HOST%',$host,$setURL);
		$setURL=str_replace('%PORT%',$port,$setURL);
		$setURL=str_replace('%PARAMS%',$parametres,$setURL);
		$fullUrl='http://'.$host.':'.$port.'/'.$setURL;
		//
		$content=CURLIT($fullUrl);
	//
	die('OK');
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

?>