<?php
/* VARS */
	
	//urls
		define("GET_DEVICESTATUS_URL" , "cgi-bin/CGIProxy.fcgi?cmd=getDevState&usr=%USER%&pwd=%PWD%");
		define("SET_SUBSTREAMTOMJPEG_URL" , "cgi-bin/CGIProxy.fcgi?cmd=setSubStreamFormat&format=1&usr=%USER%&pwd=%PWD%");
		define("GET_MJPEGSTREAM_URL" , "cgi-bin/CGIStream.cgi?cmd=GetMJStream&usr=%USER%&pwd=%PWD%");
		define("GET_MOTIONDETECTCONFIG_URL" , "cgi-bin/CGIProxy.fcgi?cmd=getMotionDetectConfig&usr=%USER%&pwd=%PWD%");
		define("SET_MOTIONDETECTCONFIG_URL" , "cgi-bin/CGIProxy.fcgi?cmd=setMotionDetectConfig&usr=%USER%&pwd=%PWD%%PARAMS%");
		define("GET_SNAPSHOT_URL" , "cgi-bin/CGIProxy.fcgi?cmd=snapPicture2&usr=%USER%&pwd=%PWD%");
		define("SET_FTPCONFIG_URL" , "cgi-bin/CGIProxy.fcgi?cmd=setFtpConfig&usr=%USER%&pwd=%PWD%&ftpPort=%FTPPORT%&mode=0&userName=%FTPUSER%&password=%FTPPASSWORD%&ftpAddr=%FTPSERVER%");
		define("SET_CAMERANAME_URL" , "cgi-bin/CGIProxy.fcgi?cmd=setDevName&devName=%NAME%&usr=%USER%&pwd=%PWD%");
		define("PTZ_CONTROL_URL" , "cgi-bin/CGIProxy.fcgi?cmd=%PTZCMD%&usr=%USER%&pwd=%PWD%");
		
	//motion detect
		//liste des parametres
		$MD_paramsList=array('isEnable','linkage','snapInterval','sensitivity','triggerInterval','schedule0','schedule1','schedule2','schedule3','schedule4','schedule5','schedule6','area0','area1','area2','area3','area4','area5','area6','area7','area8','area9');
		//liste des parametres modifiables depuis l'interface
		$MD_SETUPparamsList=array('isEnable','sensitivity','schedule0','schedule1','schedule2','schedule3','schedule4','schedule5','schedule6','area0','area1','area2','area3','area4','area5','area6','area7','area8','area9');
		//liste des parametres fixes
		$MD_FIXEDparamsList=array('linkage','snapInterval','triggerInterval');
		//valeures fixées
		define("MD_LINKAGE",4);				//snapshots
		define("MD_SNAPINTERVAL",1);		//1 FPS
		define("MD_TRIGGERINTERVAL",10);	//15 sec
		$fixedVAL=array();
		$fixedVAL['linkage']=4;
		$fixedVAL['snapInterval']=1;
		$fixedVAL['triggerInterval']=10;
		//valeures defauts
		define("MD_ISENABLE",0);			//off
		define("MD_SENSITIVITY",0);			// (0 = low, 1 = medium, 2 = high)
		define("MD_SCHEDULE0",281474976710655);	//tout le lundi
		define("MD_SCHEDULE1",281474976710655);	//tout le mardi
		define("MD_SCHEDULE2",281474976710655);	//tout le mercredi
		define("MD_SCHEDULE3",281474976710655);	//tout le jeudi
		define("MD_SCHEDULE4",281474976710655);	//tout le vendredi
		define("MD_SCHEDULE5",281474976710655);	//tout le samedi
		define("MD_SCHEDULE6",281474976710655);	//tout le dimanche
		define("MD_AREA0",1023);	//toute la ligne
		define("MD_AREA1",1023);	//toute la ligne
		define("MD_AREA2",1023);	//toute la ligne
		define("MD_AREA3",1023);	//toute la ligne
		define("MD_AREA4",1023);	//toute la ligne
		define("MD_AREA5",1023);	//toute la ligne
		define("MD_AREA6",1023);	//toute la ligne
		define("MD_AREA7",1023);	//toute la ligne
		define("MD_AREA8",1023);	//toute la ligne
		define("MD_AREA9",1023);	//toute la ligne
		
/* FIN VARS */
		
/* COMMAND LINE */
if(php_sapi_name()=='cli'){
	$action=$argv[1];
	if($action=='GETURL'){
		$cmd=$argv[2];
		switch($cmd){
			case 'DEVICESTATUS':
				die(GET_DEVICESTATUS_URL);
				break;
			case 'MJPEGSTREAM':
				die(GET_MJPEGSTREAM_URL);
				break;
			case 'SUBSTREAMTOMJPEG':
				die(SET_SUBSTREAMTOMJPEG_URL);
				break;
		}
	}
	if($action=='SETREQUIREDSETTINGS'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		$nom=$argv[6];
		//on recupere l'url pour configurer le substream en mode MJPEG
			$url=SET_SUBSTREAMTOMJPEG_URL;
			$setURL=str_replace('%USER%',$usr,$url);
			$setURL=str_replace('%PWD%',$pwd,$setURL);
			$fullUrl='http://'.$host.':'.$port.'/'.$setURL;
			//on envoi la commande a la camera
			$content=CURLIT($fullUrl);
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
	if($action=='SETFTPSETTINGS'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		$ftpserver=$argv[6];
		$ftpport=$argv[7];
		$ftpuser=$argv[8];
		$ftppass=$argv[9];
		//
		$ftpUrl='ftp://'.$ftpserver.'/';
		//on recupere l'url pour configurer la camera		
		$url=SET_FTPCONFIG_URL;
		$setURL=str_replace('%USER%',$usr,$url);
		$setURL=str_replace('%PWD%',$pwd,$setURL);
		$setURL=str_replace('%FTPSERVER%',$ftpUrl,$setURL);
		$setURL=str_replace('%FTPPORT%',$ftpport,$setURL);
		$setURL=str_replace('%FTPUSER%',$ftpuser,$setURL);
		$setURL=str_replace('%FTPPASSWORD%',$ftppass,$setURL);
		$fullUrl='http://'.$host.':'.$port.'/'.$setURL;
		//on envoi la commande a la camera
		$content=CURLIT($fullUrl);
		//
		echo "User: $usr\nPass: $pwd\nServer FTP: $ftpUrl\nPort FTP: $ftpport\nUser FTP: $ftpuser\nPass FTP: $ftppass\n\nUrl: $fullUrl\n\nResultat: $content\n\n";
		//
		die('OK');
	}
	if($action=='GETMOTIONDETECTPANEL'){
		$host=$argv[2];
		$port=$argv[3];
		$usr=$argv[4];
		$pwd=$argv[5];
		$id=$argv[6];
		//on recupere l'url pour interroger la camera		
		$url=GET_MOTIONDETECTCONFIG_URL;
		$checkURL=str_replace('%USER%',$usr,$url);
		$checkURL=str_replace('%PWD%',$pwd,$checkURL);
		$fullUrl='http://'.$host.':'.$port.'/'.$checkURL;
		//on interroge la camera
		$content=CURLIT($fullUrl);
		$params=array();
		foreach($MD_paramsList as $key => $value){
			$params[$value] = value_in($value , $content);
		}
		//on prepare l'url du snapshot
		$snapurl=GET_SNAPSHOT_URL;
		$snapshotURL=str_replace('%USER%',$usr,$snapurl);
		$snapshotURL=str_replace('%PWD%',$pwd,$snapshotURL);
		$snapshotURL='http://'.$host.':'.$port.'/'.$snapshotURL;
		//preparation du formulaire
		$chk_enable='';
		if($params['isEnable']==1){ $chk_enable='checked'; }
		$sl_sensitivity0="";
		$sl_sensitivity1="";
		$sl_sensitivity2="";
		switch(intVal($params['sensitivity'])){
			case 0:
				$sl_sensitivity0="selected";
				break;
			case 1:
				$sl_sensitivity1="selected";
				break;
			case 2:
				$sl_sensitivity2="selected";
				break;
		}
		//on converti les donnee du planing
		$binary0=decbin($params['schedule0']);
		$binary1=decbin($params['schedule1']);
		$binary2=decbin($params['schedule2']);
		$binary3=decbin($params['schedule3']);
		$binary4=decbin($params['schedule4']);
		$binary5=decbin($params['schedule5']);
		$binary6=decbin($params['schedule6']);
		$schedule0=strrev(substr("00000000000000000000000000000000000000000000000".$binary0,-48));
		$schedule1=strrev(substr("00000000000000000000000000000000000000000000000".$binary1,-48));
		$schedule2=strrev(substr("00000000000000000000000000000000000000000000000".$binary2,-48));
		$schedule3=strrev(substr("00000000000000000000000000000000000000000000000".$binary3,-48));
		$schedule4=strrev(substr("00000000000000000000000000000000000000000000000".$binary4,-48));
		$schedule5=strrev(substr("00000000000000000000000000000000000000000000000".$binary5,-48));
		$schedule6=strrev(substr("00000000000000000000000000000000000000000000000".$binary6,-48));
		$scheduleTab=array();
		$scheduleTab[0]=array();
		for($index=0;$index<strlen($schedule0);$index++){
			$scheduleTab[0][$index]=intval(substr($schedule0,$index,1));
		}
		$scheduleTab[1]=array();
		for($index=0;$index<strlen($schedule1);$index++){
			$scheduleTab[1][$index]=intval(substr($schedule1,$index,1));
		}
		$scheduleTab[2]=array();
		for($index=0;$index<strlen($schedule2);$index++){
			$scheduleTab[2][$index]=intval(substr($schedule2,$index,1));
		}
		$scheduleTab[3]=array();
		for($index=0;$index<strlen($schedule3);$index++){
			$scheduleTab[3][$index]=intval(substr($schedule3,$index,1));
		}
		$scheduleTab[4]=array();
		for($index=0;$index<strlen($schedule4);$index++){
			$scheduleTab[4][$index]=intval(substr($schedule4,$index,1));
		}
		$scheduleTab[5]=array();
		for($index=0;$index<strlen($schedule5);$index++){
			$scheduleTab[5][$index]=intval(substr($schedule5,$index,1));
		}
		$scheduleTab[6]=array();
		for($index=0;$index<strlen($schedule6);$index++){
			$scheduleTab[6][$index]=intval(substr($schedule6,$index,1));
		}
		//on converti les donnees de la zone de detection
		$binary0=decbin($params['area0']);
		$binary1=decbin($params['area1']);
		$binary2=decbin($params['area2']);
		$binary3=decbin($params['area3']);
		$binary4=decbin($params['area4']);
		$binary5=decbin($params['area5']);
		$binary6=decbin($params['area6']);
		$binary7=decbin($params['area7']);
		$binary8=decbin($params['area8']);
		$binary9=decbin($params['area9']);
		$area0=strrev(substr("000000000".$binary0,-10));
		$area1=strrev(substr("000000000".$binary1,-10));
		$area2=strrev(substr("000000000".$binary2,-10));
		$area3=strrev(substr("000000000".$binary3,-10));
		$area4=strrev(substr("000000000".$binary4,-10));
		$area5=strrev(substr("000000000".$binary5,-10));
		$area6=strrev(substr("000000000".$binary6,-10));
		$area7=strrev(substr("000000000".$binary7,-10));
		$area8=strrev(substr("000000000".$binary8,-10));
		$area9=strrev(substr("000000000".$binary9,-10));
		$areaTab=array();
		$areaTab[0]=array();
		for($index=0;$index<strlen($area0);$index++){
			$areaTab[0][$index]=intval(substr($area0,$index,1));
		}
		$areaTab[1]=array();
		for($index=0;$index<strlen($area1);$index++){
			$areaTab[1][$index]=intval(substr($area1,$index,1));
		}
		$areaTab[2]=array();
		for($index=0;$index<strlen($area2);$index++){
			$areaTab[2][$index]=intval(substr($area2,$index,1));
		}
		$areaTab[3]=array();
		for($index=0;$index<strlen($area3);$index++){
			$areaTab[3][$index]=intval(substr($area3,$index,1));
		}
		$areaTab[4]=array();
		for($index=0;$index<strlen($area4);$index++){
			$areaTab[4][$index]=intval(substr($area4,$index,1));
		}
		$areaTab[5]=array();
		for($index=0;$index<strlen($area5);$index++){
			$areaTab[5][$index]=intval(substr($area5,$index,1));
		}
		$areaTab[6]=array();
		for($index=0;$index<strlen($area6);$index++){
			$areaTab[6][$index]=intval(substr($area6,$index,1));
		}
		$areaTab[7]=array();
		for($index=0;$index<strlen($area7);$index++){
			$areaTab[7][$index]=intval(substr($area7,$index,1));
		}
		$areaTab[8]=array();
		for($index=0;$index<strlen($area8);$index++){
			$areaTab[8][$index]=intval(substr($area8,$index,1));
		}
		$areaTab[9]=array();
		for($index=0;$index<strlen($area9);$index++){
			$areaTab[9][$index]=intval(substr($area9,$index,1));
		}
		//
		$html='<div class="setupContainer">';
		$html.='<div class="title" onclick="tooglePanel(\'setupMotiondetectPanel\')" style="cursor:pointer"><table width=100% cellspacing=0 cellpadding=0><tr><td width=50 valign=top align=center><img src="motiondetectIcon.png"></img></td><td align=left valign=top><p>Détection de mouvement</p></td><td width=40 align=center valign=center><img id="setupMotiondetectPanelImg" src="down.png" width=20 height=20 style="width:20px !important;height:20px !important;"/></td></tr></table></div>';
		$html.='<div id="setupMotiondetectPanel" style="display:none"><br><table width=300 cellspacing=0 cellpadding=0 style="margin-left:40px;">';
		$html.='<tr><td width=110 align=left>Activer</td><td align=left><input id="isEnable" type="checkbox" '.$chk_enable.' /></td></tr>';
		$html.='<tr><td width="110" align=left>Sensibilité</td>';
		$html.='<td align=left><select readonly="" id="sensitivity" class="select">';
		$html.='<option value="0" '.$sl_sensitivity0.'>Basse</option>';
		$html.='<option value="1" '.$sl_sensitivity1.'>Moyenne</option>';
		$html.='<option value="2" '.$sl_sensitivity2.'>Elevée</option>';
		$html.='</select></td></tr></table><br>';
		$html.='<table width=550><tr><td align=left>Zone de détection:</td></tr>';
		$html.='</table><br>';
		$html.='<table id="detectAreaTable" width=300 height=200 cellspacing=0 cellpadding=0 border=1 style="margin-left:60px;background:url('.$snapshotURL.');background-size: 100% 100%;">';
		for($area=0;$area<10;$area++){
			$html.='<tr>';
			for($bit=0;$bit<10;$bit++){
				$visible='hidden';
				if($areaTab[$area][$bit]==0){ $visible='visible'; }
				$html.='<td width=30 height=20 area='.$area.' bit='.$bit.' class="detectionArea" onclick="toogleArea('.$area.','.$bit.');"><img src="MDmask.png" id="A'.$area.'-B'.$bit.'" width=30 height=20 style="display:block;visibility:'.$visible.'" /></td>';
			}
			$html.='</tr>';
		}
		$html.='</table><br><br><table width=550 cellspacing=0 cellpadding=0 style="margin-left:0;"></tr><td align=left>Planing:</td></tr></table><br>';
		$html.='<table width=550 cellspacing=0 cellpadding=0 style="margin-left:0;"></tr>';
		$html.='<td width=50% align=center><table width=100%><tr><td align=left width=50><div style="border:1px solid #333;width:30px;height:15px;background:#DDD !important"></div></td><td align=left style="font-size:0.8em !important">Aucune alertes</td></tr></table></td>';
		$html.='<td width=50% align=center><table width=100%><tr><td align=left width=50><div style="border:1px solid #333;width:30px;height:15px;background:#328AED !important"></div></td><td align=left style="font-size:0.8em !important">Détection de mouvements</td></tr></table></td></tr><tr>';
		$html.='<td colspan=2 align=left><table width=100%><tr><td align=left width=50><div style="border:1px solid #333;width:30px;height:15px;background:#F00 !important"></div></td><td align=left style="font-size:0.8em !important">Détection de mouvements + notifications</td></tr></table></td>';
		$html.='</tr></table><br>';
		$html.='<table id="planingTable" cellspacing=0 cellpadding=0 border=1 bordercolor="#000" style="background:#FFF;margin-left:0;">';
		$html.='<tr><td width=40 height=20 title="Tout sélectionner" style="cursor:pointer" onclick="selectPlaningAll(3)"></td>';
		for($a=0;$a<24;$a++){
			$num=substr("0".$a,-2);
			$html.='<td width=20 height=20 colspan=2 align=center title="sélectionner cette colonne d\'heure" style="cursor:pointer;" onclick="selectPlaningAllHeure(3,'.$a.')">'.$num.'</td>';
		}
		$html.='</tr>';
		$dayList=array('lun','mar','mer','jeu','ven','sam','dim');
		for($day=0;$day<7;$day++){
			$html.='<tr><td width=40 align=left valign=center title="sélectionner la journée complète" style="cursor:pointer;" onclick="selectPlaningAllDay(3,'.$day.')">'.$dayList[$day].'</td>';
			for($demiheure=0;$demiheure<48;$demiheure++){
				$visible='hidden';
				if($scheduleTab[$day][$demiheure]==1){ $visible='visible'; }
				$html.='<td width=10 height=20 day='.$day.' demiheure='.$demiheure.' class="planingArea" style="" onclick="tooglePlaningArea(3,'.$day.','.$demiheure.');"><img src="PLANINGmask.png" id="D'.$day.'-DH'.$demiheure.'" width=10 height=20 style="display:block;visibility:'.$visible.'" /></td>';
			}
			$html.='</tr>';
		}
		$html.='</table></br>';
		$html.='<table width=450 cellspacing=0 cellpadding=0 style="margin-left:0;">';
		$html.='<tr><td colspan=2 align=right><input type="button" value="Enregistrer" onclick="save_motiondetect_settings(\'FOSCAM9821W\',\''.$id.'\');" class="button"/></td></tr>';
		$html.='</table></div></div><br>';
		//
		die($html);
	}
}

header('Content-Type: text/html; charset=ISO-8859-1');
$action=$_GET['action'];
$output=array();

if($action=='GET_PANEL'){
	$id=$_GET['cameraid'];
	$Cname=$_GET['name'];
	$html='<h3>'.$Cname.'</h3>';
	$html.='<br>';
	//control panel
	$html.='<table width=100% cellspacing=0 cellpadding=0><tr><td valign=top width="160">';
	$html.='	<table id="Table_01" width="148" height="148" border="0" cellpadding="0" cellspacing="0">';
	$html.='		<tr><td><img src="controls/Slice.png" width="49" height="49" alt=""></td><td><img src="controls/Slice-02.png" width="50" height="49" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConUp\');$(this).attr(\'src\',\'controls/Slice-02-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-02.png\');" style="cursor:pointer"></td><td><img src="controls/Slice-03.png" width="49" height="49" alt=""></td></tr>';
	$html.='		<tr><td><img src="controls/Slice-04.png" width="49" height="50" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConLeft\');$(this).attr(\'src\',\'controls/Slice-04-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-04.png\');" style="cursor:pointer"></td><td><img src="controls/Slice-05.png" width="50" height="50" alt="" onclick="controlCamera(\''.$Cid.'\',\'presetHome\');" style="cursor:pointer" title="RAZ"></td><td><img src="controls/Slice-06.png" width="49" height="50" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConRight\');$(this).attr(\'src\',\'controls/Slice-06-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-06.png\');" style="cursor:pointer"></td></tr>';
	$html.='		<tr><td><img src="controls/Slice-07.png" width="49" height="49" alt=""></td><td><img src="controls/Slice-08.png" width="50" height="49" alt="" onmousedown="controlCamera(\''.$Cid.'\',\'moveConDown\');$(this).attr(\'src\',\'controls/Slice-08-active.png\');" onmouseup="controlCamera(\''.$Cid.'\',\'moveStop\');$(this).attr(\'src\',\'controls/Slice-08.png\');" style="cursor:pointer"></td><td><img src="controls/Slice-09.png" width="49" height="49" alt=""></td></tr>';
	$html.='	</table>';
	$html.='</td><td valign=top>';
	$html.='	<table cellspacing=0 cellpadding=2>';
	$html.='		<tr><td><img src="controls/hpatrol_up.png" style="cursor:pointer" title="patrouille horizontale" onclick="controlCamera(\''.$Cid.'\',\'hpatrol\');"/></td><td><img src="controls/R_stop_up.png" style="cursor:pointer" title="arreter la patrouille" onclick="controlCamera(\''.$Cid.'\',\'moveStop\');"/></td><td><img src="controls/vpatrol_up.png" style="cursor:pointer" title="patrouille verticale" onclick="controlCamera(\''.$Cid.'\',\'vpatrol\');"/></td></tr>';
	$html.='		<tr><td><img src="controls/switchon_up.png" style="cursor:pointer" title="activer Infra rouges" onclick="controlCamera(\''.$Cid.'\',\'wake\');"/></td><td><img src="controls/switchoff_up.png" style="cursor:pointer" title="desactiver infra rouges" onclick="controlCamera(\''.$Cid.'\',\'sleep\');"/></td><td><img src="controls/snapshot.png" style="cursor:pointer" title="prendre une photo" onclick="tabCameras['.$Cid.'].snapshot();"/></td></tr>';
	$html.='	</table>';
	die($html);
}

if($action=='GET_MOTIONDETECTCONFIG'){
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['usr'];
	$pwd=$_GET['pwd'];
	//on recupere l'url pour interroger la camera		
	$url=GET_MOTIONDETECTCONFIG_URL;
	$checkURL=str_replace('%USER%',$usr,$url);
	$checkURL=str_replace('%PWD%',$pwd,$checkURL);
	$fullUrl='http://'.$host.':'.$port.'/'.$checkURL;
	//
	$content=CURLIT($fullUrl);
	$output['model']='FOSCAM9821W';
	foreach($MD_paramsList as $key => $value){
		$output[$value] = value_in($value , $content);
	}
	//
	die(json_encode($output));
}

if($action=='SET_MOTIONDETECTCONFIG'){
	$id=$_GET['id'];
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['usr'];
	$pwd=$_GET['pwd'];
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
	$requeteSQL='UPDATE '.$tableCameras.' SET alertesPlan0 = '.$_GET['scheduleAlert0'].' , alertesPlan1 = '.$_GET['scheduleAlert1'].' , alertesPlan2 = '.$_GET['scheduleAlert2'].' , alertesPlan3 = '.$_GET['scheduleAlert3'].' , alertesPlan4 = '.$_GET['scheduleAlert4'].' , alertesPlan5 = '.$_GET['scheduleAlert5'].' , alertesPlan6 = '.$_GET['scheduleAlert6'].' WHERE id = '.$id;
	if(!mysql_query($requeteSQL)){
		mysql_close($liendb);
		die('ERREUR > Connexion SQL !');
	}
	//ferme connexion sql
	mysql_close($liendb);
	//on recupere les parametres pour la detection de mouvements
	$params=array();
	$parametres='';
	foreach($MD_SETUPparamsList as $key => $value){
		$params[$value] = $_GET[$value];
		$parametres.='&'.$value.'='.$params[$value];
	}
	foreach($fixedVAL as $key => $value){
		$parametres.='&'.$key.'='.$value;
	}
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
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['usr'];
	$pwd=$_GET['pwd'];
	$state=$_GET['state'];
	//
	if($state=='ON'){ $state='1'; }
	if($state=='OFF'){ $state='0'; }
	//on recupere l'url pour interroger la camera		
	$url=GET_MOTIONDETECTCONFIG_URL;
	$checkURL=str_replace('%USER%',$usr,$url);
	$checkURL=str_replace('%PWD%',$pwd,$checkURL);
	$fullUrl='http://'.$host.':'.$port.'/'.$checkURL;
	//
	$current=array();
	$content=CURLIT($fullUrl);
	foreach($MD_paramsList as $key => $value){
		$current[$value] = value_in($value , $content);
	}
	if(intVal($current['isEnable'])!=intVal($state)){
		$current['isEnable']=intVal($state);
		//on reprend les parametres actuels
		$parametres='';
		foreach($current as $key => $value){
			$parametres.='&'.$key.'='.$value;
		}
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
	}else{
		die('OK');
	}
	
	//
	//die($content);
}

if($action=='CONTROL'){
	$cmd=$_GET['cmd'];
	$host=$_GET['host'];
	$port=$_GET['port'];
	$usr=$_GET['login'];
	$pwd=$_GET['pwd'];
	//on convertie la commande en numero
		$command='ptzStopRun';
		switch($cmd){
			case 'moveConUp':		//haut
				$command='ptzMoveUp';
				break;
			case 'moveConDown':		//bas
				$command='ptzMoveDown';
				break;
			case 'moveConLeft':		//gauche
				$command='ptzMoveLeft';
				break;
			case 'moveConRight':	//droite
				$command='ptzMoveRight';
				break;
			case 'moveStop':		//stop
				$command='ptzStopRun';
				break;
			case 'presetHome':		//home
				$command='ptzReset';
				break;
		}
	//on recupere l'url pour interroger la camera		
		$url=PTZ_URL;
		$ptzURL=str_replace('%USER%',$usr,$url);
		$ptzURL=str_replace('%PWD%',$pwd,$ptzURL);
		$ptzURL=str_replace('%PTZCMD%',$command,$ptzURL);
		$fullUrl='http://'.$host.':'.$port.'/'.$ptzURL;
	//on interroge la camera
		$content=CURLIT($fullUrl);
	
	$output['status']='OK';
	die($_GET['callback'] .'('. json_encode($output) . ')');
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