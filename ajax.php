<?php
header('Content-Type: text/html; charset=ISO-8859-1');
//
ini_set('session.gc_maxlifetime', 120);	//duree maxi de la session 2h
ini_set( "session.name", "csc" );

//include('thumbs_builder.php');

include('../sql.conf');

$wwwDir='/var/www/';
$baseDir=$wwwDir.'www.cloudsecuritycam.com/csc/';
$systemlogfile=$baseDir.'log/systemlog.log';
$logfile=$baseDir.'log/log.log';
$eventsDir=$baseDir.'events/';
$webEventsDir='events/';
$scriptsDir=$baseDir.'scripts/';
$ftpDBfile='/etc/vsftpd/login.txt';
$controlDir=$baseDir.'controls/';
$ftpDefaultPassword='cloudsecuritycam';
$ftpServerAddr='10.160.52.30';
$ftpPort='21';

session_start();

$output=array();

if($_GET['action']=='LOGIN'){
	$login=$_GET['login'];
	$pwd=$_GET['password'];
		//connexion bdd
			$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
			if(!$liendb){
				systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
				die('ERREUR > Connexion SQL !');
			}
			mysql_select_db($bddnom);
		//recherche du user
			$table='users';
			$sql='select * from '.$table.' WHERE email = "'.$login.'"';
			$zmuser=mysql_query($sql);
			$nblignes=mysql_num_rows($zmuser);
			if($nblignes!=1){
				$resultat='AUTH ERROR';
			}else{
				$infosUser=mysql_fetch_array($zmuser);
				$password=$infosUser['password'];
				if($password==$pwd){
					$output['result']='OK';
					$_SESSION['user']['name']=$login;
					$_SESSION['user']['password']=$password;
					$_SESSION['user']['langue']=$infosUser['langue'];
					$_SESSION['user']['contrat']=$infosUser['contrat'];
					$_SESSION['user']['nom']=$infosUser['nom'];
					$_SESSION['user']['prenom']=$infosUser['prenom'];
					$_SESSION['user']['email']=$infosUser['email'];
					$_SESSION['user']['adresse']=$infosUser['adresse'];
					$_SESSION['user']['cp']=$infosUser['cp'];
					$_SESSION['user']['ville']=$infosUser['ville'];
					$_SESSION['user']['timezone']=$infosUser['timezone'];
					$_SESSION['user']['pays']=$infosUser['pays'];
				}else{
					$output['result']='AUTH ERROR';
				}
			}
			
		//ferme connexion sql
			mysql_close($liendb);
		//
		die($_GET['callback'] .'('. json_encode($output) . ')');
}

if(!isset($_SESSION['user']['name'])) {	//si la session n'existe pas
	$params='';
	foreach($_GET as $key => $value){
		$params.=$key.'='.$value.'&';
	}
	systemLog('*** AJAX FILE AUTH ERROR *** Requete envoyee au fichier ajax.php sans authentification valide ! POST="'.$params.'"');
	$errorAUTH=array();
	$errorAUTH['status']='ERROR';
	$errorAUTH['errorMsg']='ERREUR AUTHENTIFICATION';
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

//on verifi le login
	$login=$_SESSION['user']['name'];
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die('ERREUR > Connexion SQL !');
		}
		mysql_select_db($bddnom);
	//recherche du user
		$table='users';
		$sql='select * from '.$table.' WHERE email = "'.$login.'"';
		$zmuser=mysql_query($sql);
		$nblignes=mysql_num_rows($zmuser);
		if($nblignes!=1){
			mysql_close($liendb);
			die('AUTH ERROR');
		}
		$password=mysql_result($zmuser,0,1);
		if($password!=$_SESSION['user']['password']){
			mysql_close($liendb);
			die('AUTH ERROR');
		}
	//ferme connexion sql
		mysql_close($liendb);

function generateAuthHash($nom,$pass,$secret){
	$time = localtime();
	$authKey = $secret.$nom.$pass.$time[2].$time[3].$time[4].$time[5];
	$auth = md5( $authKey );
	return( $auth );
}
	
function getCamLink($user,$pass,$monitorId){
	include('conf/main.conf');
	$connkey=rand(1,999999);
	$random=rand(1111111111,9999999999);
	return('http://'.$monIP.'/cgi-bin/nph-zms?mode=jpeg&monitor='.$monitorId.'&scale=100&maxfps='.$maxfps.'&buffer=1000&auth='.generateAuthHash($user,$pass,$secret).'&connkey='.$connkey.'&rand='.$random);
}

function parseInt($string) {
//	return intval($string);
	if(preg_match('/(\d+)/', $string, $array)) {
		return $array[1];
	} else {
		return 0;
	}
}

function getGif($path,$id){
	return false;
	$outfile=$path.$id.'-preview.gif';
	//verif si existe
	if(file_exists($outfile)){
		return $outfile;
	}else{
		//sinon on cree un gif animé
		exec("convert -delay 50 -loop 0 '".$path."*.jpg' '".$outfile."'");
		return $outfile;
	}
}

function remove_accents($str, $charset='utf-8'){
    $str = htmlentities($str, ENT_NOQUOTES, $charset);
    
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
    
    return $str;
}

$action=$_GET['action'];

$output=array();

$errorSQL=array();
$errorSQL['status']='ERROR';
$errorSQL['errorMsg']='Erreur de connexion SQL !';

$listeMois=array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre');

//date du jour
	setlocale (LC_TIME, 'fr_FR','fra');
	$curYear=strftime("%y");	//annee sur 2 chiffres (ex: 13)
	$curMonth=strftime("%m");	//mois sur 2 chiffres (ex: 04)
	$curDay=strftime("%d");		//jour sur 2 chiffres (ex: 01)

if($action=='CHECK_LOGIN'){
	//on verifi le login
	$login=$_SESSION['user']['name'];
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die('ERREUR > Connexion SQL !');
		}
		mysql_select_db($bddnom);
	//recherche du user
		$table='users';
		$sql='select * from '.$table.' WHERE email = "'.$login.'"';
		$zmuser=mysql_query($sql);
		$nblignes=mysql_num_rows($zmuser);
		if($nblignes!=1){
			mysql_close($liendb);
			die('AUTH ERROR');
		}
		$password=mysql_result($zmuser,0,1);
		if($password!=$_SESSION['user']['password']){
			$output['status']='ERROR';
		}else{
			$output['status']='OK';
		}
		mysql_close($liendb);
		die($_GET['callback'] .'('. json_encode($output) . ')');
}	
	
if($action=='LOGOUT'){
	session_unset ();
	$output['status']='OK';
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='GET_USER_INFO'){
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
		
	//infos utilisateur cam
		mysql_select_db($bddnom);
		$table='users';
		$sql='select * from '.$table.' WHERE email = "'.$_SESSION['user']['name'].'"';
		$camuser=mysql_query($sql);
		$infosUser=mysql_fetch_array($camuser);
		$id=$infosUser['id'];
		$password=$infosUser['password'];
		$monitorids=$infosUser['cameras'];
		$userNom=$infosUser['nom'];
		$userPrenom=$infosUser['prenom'];
		$userEmail=$infosUser['email'];
		$userAdresse=$infosUser['adresse'];
		$userCP=$infosUser['cp'];
		$userVille=$infosUser['ville'];
		$userPays=$infosUser['pays'];
		$userTimezone=$infosUser['timezone'];
		$userLangue=$infosUser['langue'];
		$userFormule=$infosUser['contrat'];
		$userActif=$infosUser['actif'];
		if($monitorids==''){
			$monitorsTab=array();
		}else{
			$monitorsTab=explode(',',$monitorids);
		}
		
	//infos formule
		$table='formules';
		$sql='select * from '.$table.' WHERE id = "'.$userFormule.'"';
		$formule=mysql_query($sql);
		$infosFormule=mysql_fetch_array($formule);
		$formuleNom=$infosFormule['nom'];
		$formuleCamLimit=$infosFormule['max_cam'];
		$formuleFpsLimit=$infosFormule['max_fps'];
		$formuleRetentionLimit=$infosFormule['max_retention'];
		$formuleStorageLimit=$infosFormule['max_storage'];
		$formuleDownloadEnable=$infosFormule['download'];
		$formuleMotiondetectEnable=$infosFormule['motion_detect'];
		$formuleMailAlert=$infosFormule['mail_alert'];
		$formuleFtpAccess=$infosFormule['ftp_access'];	
		$formulePrice=$infosFormule['price'];
	
	//mise en forme des donnees
		$output['user']=array();
		$output['user']['id']=$id;
		$output['user']['cameras']=count($monitorsTab);
		$output['user']['cameraslimit']=$formuleCamLimit;
		$output['user']['formule']=$formuleNom;
		$output['user']['storagelimit']=$formuleStorageLimit;
		
		$output['user']['fpslimit']=$formuleFpsLimit;
		$output['user']['retentionlimit']=$formuleRetentionLimit;
		$output['user']['downloadenable']=$formuleDownloadEnable;
		$output['user']['motiondetectenable']=$formuleMotiondetectEnable;
		$output['user']['mailalert']=$formuleMailAlert;
		$output['user']['ftpaccess']=$formuleFtpAccess;
		$output['user']['price']=$formulePrice;
		
		$output['user']['nom']=$userNom;
		$output['user']['prenom']=$userPrenom;
		$output['user']['email']=$userEmail;
		$output['user']['pays']=$userPays;
		$output['user']['langue']=$userLangue;
		$output['user']['adresse']=$userAdresse;
		$output['user']['cp']=$userCP;
		$output['user']['ville']=$userVille;
		$output['user']['timezone']=$userTimezone;
		
	//ferme connexion sql
		mysql_close($liendb);
	
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='GET_DISK_USE'){
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
	
	//on recupere l'id du user et de ses cameras dans la base zm
		$table='Users';
		$sql='select * from '.$table.' WHERE Username = "'.$_SESSION['user']['name'].'"';
		$zmuser=mysql_query($sql);
		$id=mysql_result($zmuser,0,0);				//id
		$monitorids=mysql_result($zmuser,0,12);		//id des cameras
		$monitorsTab=explode(',',$monitorids);
		
	//on recupere la formule dans la base cam
		mysql_select_db($bdd2nom);
		$table='users';
		$sql='select * from '.$table.' WHERE id = "'.$id.'"';
		$camuser=mysql_query($sql);
		$infosUser=mysql_fetch_array($camuser);
		$userFormule=$infosUser['formule'];
		
	//on recupere la taille disque maxi
		$table='formules';
		$sql='select * from '.$table.' WHERE id = "'.$userFormule.'"';
		$formule=mysql_query($sql);
		$infosFormule=mysql_fetch_array($formule);
		$formuleStorageLimit=$infosFormule['storage_limit'];
	
	//calcul de l'utilisation du disque pour chaque cameras
		$diskUsed=0;
		if($monitorids!=''){
			$lst="";
			for($x=0;$x<count($monitorsTab);$x++){
				//$command='du -s '.$baseEventsDir.$monitorsTab[$x].'/ | cut -d"/" -f1';
				//exec($command,$rep);
				//$spaceUsed=intval($rep[0]);
				//$diskUsed+=$spaceUsed;
				$lst.=$baseEventsDir.$monitorsTab[$x]." ";
			}
			$command='du -s '.$lst.'| awk \'{print $1}\'';
			exec($command,$rep);
			for($y=0;$y<count($monitorsTab);$y++){
				$diskUsed+=intval($rep[$y]);
			}
			$total=$diskUsed;
			$diskUsed=format_taille($diskUsed*1024);
		}else{
			$diskUsed=0;
		}
	//calcul de l'espace dique maxi
			$units='Mo';
			if($formuleStorageLimit>=1024){
				$formuleStorageLimit=floor($formuleStorageLimit/1024);
				$units='Go';
			}
	//mise en forme des donnees
		$output['status']="SUCCESS";
		$output['storage']=array();
		$output['storage']['limit']=$formuleStorageLimit.$units;
		$output['storage']['used']=$diskUsed;

	//ferme connexion sql
		mysql_close($liendb);
	
	die($_GET['callback'] .'('. json_encode($output) . ')');
}
	
if($action=='LOAD_CAMERAS'){
	$output['camera']=array();
	
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
	
	//infos utilisateur
		$table='users';
		$sql='select * from '.$table.' WHERE email = "'.$_SESSION['user']['name'].'"';
		$zmuser=mysql_query($sql);
		$infosUser=mysql_fetch_array($zmuser);
		$monitorids=$infosUser['cameras'];		//id des cameras
		
		if($monitorids!=""){
			$monitorsTab=explode(',',$monitorids);
		}else{
			$monitorsTab=array();
		}
	
	//infos cameras
		$table='cameras';
		for($x=0;$x<count($monitorsTab);$x++){
			$cameraId=$monitorsTab[$x];
			$sql='select * from '.$table.' WHERE id = '.$cameraId;
			$zmcam=mysql_query($sql);
			$infoCam=mysql_fetch_array($zmcam);
			//
			$name=$infoCam['nom'];
			$function=$infoCam['fonction'];
			$protocol=$infoCam['protocol'];
			$host=$infoCam['host'];
			$port=$infoCam['port'];
			$path=$infoCam['path'];
			$width=$infoCam['width'];
			$height=$infoCam['height'];
			$login=$infoCam['login'];
			$password=$infoCam['password'];
			$controllable=$infoCam['controllable'];
			$preset=intval($infoCam['preset']);
			$alertesSupport=intval($infoCam['alertesSupport']);
			$planingAlertes0=$infoCam['alertesPlan0'];
			$planingAlertes1=$infoCam['alertesPlan1'];
			$planingAlertes2=$infoCam['alertesPlan2'];
			$planingAlertes3=$infoCam['alertesPlan3'];
			$planingAlertes4=$infoCam['alertesPlan4'];
			$planingAlertes5=$infoCam['alertesPlan5'];
			$planingAlertes6=$infoCam['alertesPlan6'];
			$controldevice='perso.php';
			
			if($preset!=0){
				$table2='presets';
				$sql2='select * from '.$table2.' WHERE id = '.$preset;
				$presetSQL=mysql_query($sql2);
				$infosPreset=mysql_fetch_array($presetSQL);
				$path=$infosPreset['path'];
				$width=$infosPreset['width'];
				$height=$infosPreset['height'];
				$controldevice=$infosPreset['controldevice'];
				$controllable=$infosPreset['controllable'];
			}
			
			//mise en forme des donnees
				$output['camera'][$x]=array();
				$output['camera'][$x]['id']=$cameraId;
				$output['camera'][$x]['name']=$name;
				$output['camera'][$x]['function']=$function;
				$output['camera'][$x]['protocol']=$protocol;
				$output['camera'][$x]['controllable']=$controllable;
				$output['camera'][$x]['controldevice']=substr($controldevice,0,-4);
				$output['camera'][$x]['devicepicture']='controls/'.substr($controldevice,0,-4).'.png';
				$output['camera'][$x]['preset']=$preset;
				$output['camera'][$x]['width']=$width;
				$output['camera'][$x]['height']=$height;
				$output['camera'][$x]['host']=$host;
				$output['camera'][$x]['port']=$port;
				$output['camera'][$x]['path']=$path;
				$output['camera'][$x]['login']=$login;
				$output['camera'][$x]['password']=$password;
				$output['camera'][$x]['alertesSupport']=$alertesSupport;
				$output['camera'][$x]['planingAlertes']=array();
				$output['camera'][$x]['planingAlertes'][0]=$planingAlertes0;
				$output['camera'][$x]['planingAlertes'][1]=$planingAlertes1;
				$output['camera'][$x]['planingAlertes'][2]=$planingAlertes2;
				$output['camera'][$x]['planingAlertes'][3]=$planingAlertes3;
				$output['camera'][$x]['planingAlertes'][4]=$planingAlertes4;
				$output['camera'][$x]['planingAlertes'][5]=$planingAlertes5;
				$output['camera'][$x]['planingAlertes'][6]=$planingAlertes6;
				//infos events
				$table3='events';
				$sql3='select * from '.$table3.' WHERE monitorid = '.$cameraId;
				$camEvents=mysql_query($sql3);
				$nbEvents=mysql_num_rows($camEvents);
				$output['camera'][$x]['events']=$nbEvents;
		}
	
	//ferme connexion sql
		mysql_close($liendb);
	
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='GET_CAM_LINK'){
	$id=$_GET['id'];
	die(getCamLink($_SESSION['user']['name'],$password,$id));
}

if($action=='CAM_RELOAD'){
	$id=$_GET['id'];
	$monitor = MYSQL_FETCHONE( "select * from Monitors where Id = '".$id."'" );
	zmcControl( $monitor, "restart" );
	zmaControl( $monitor, "reload" );
	systemLog('USER: '.$_SESSION['user']['name'].' - Redemarrage de la camera: '.$id);
	die('DONE');
}

if($action=='SNAPSHOT'){
	$url=$_GET['url'];
	$name=$_GET['name'];
	$name=remove_accents($name);
	//$randomNum=rand(1111111111,9999999999);
	$date = date("d-m-Y");
	$heure = date("H-i-s");
	$outname=$name.'-'.$date.'-'.$heure.'.jpg';
	$outfile=$baseDir.'snapshots/'.$outname;
	$out4web='snapshots/'.$outname;
	$cmd='wget "'.$url.'" -O "'.$outfile.'"';
	exec($cmd);
	//
	$output['snapshot']=array();
	$output['snapshot']['filename']=$outname;
	$output['snapshot']['url']=$out4web;
	systemLog('USER: '.$_SESSION['user']['name'].' - Snapshot: NOM="'.$name.'" URL="'.$url.'"');
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='LOGTHIS'){
	$id=$_GET['id'];
	$txt=$_GET['logtxt'];
	//si le fichier n'existe pas on le creer
	if(!file_exists($logfile)){
		$test=fopen($logfile,'w+');
		fclose($test);
		//on verifie qu'on a reussi sinon on retourne l'erreur
		if(!file_exists($logfile)){
			$output['status']='ERROR';
			$output['errorMsg']='Impossible de creer le fichier de log';
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Impossible de creer le fichier de log: "'.$logfile.'"');
			die($_GET['callback'] .'('. json_encode($output) . ')');
		}
	}
	//
	$ligne=$txt."\n";
	//on charge le contenu du fichier log
		$oldLog=file_get_contents($logfile);
	//ecriture du fichier
		$fichier=fopen($logfile,'w+');
		fwrite($fichier,$ligne.$oldLog);
		fclose($fichier);
	//	
	$output['status']='SUCCESS';
	$output['successMsg']='Log mis a jour !';
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='SAVE_CAMERA_SETTINGS'){
	$cameraID=$_GET['id'];
	//on prepare les parametres par defaut
		$id='null';
		$nom='Test';
		$host='localhost';
		$port=80;
		$path='/';
		$protocol='http';
		$preset=0;
		$width=320;
		$height=240;
		$controllable=0;
		$login='';
		$password='';
		$fonction='none';
	//on met a jour avec ceux recu
		$preset=intval($_GET['preset']);
		if($preset==0){
			$nom=$_GET['nom'];
			$host=$_GET['host'];
			$port=intval($_GET['port']);
			$login=$_GET['login'];
			$password=$_GET['password'];
			$protocol=$_GET['protocol'];
			$path=$_GET['path'];
			$width=intval($_GET['width']);
			$height=intval($_GET['height']);
		}else{
			$nom=$_GET['nom'];
			$host=$_GET['host'];
			$port=intval($_GET['port']);
			$login=$_GET['login'];
			$password=$_GET['password'];
		}
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
	//
		$table='cameras';
		$sql = "UPDATE ".$table." SET nom = '$nom' , host = '$host',port = '$port', login = '$login', password = '$password', preset = '$preset', protocol = '$protocol', path = '$path', width = '$width', height = '$height', controllable = '$controllable' WHERE id = ".$cameraID;
		if(mysql_query($sql)){
			$output['status']='SUCCESS';
		}else{
			$output['status']='ERROR';
			$output['errorMsg']='Modification de la camera impossible';
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec lors de la requete MYSQL: "'.$sql.'"');
		}
		
	//ferme connexion sql
		mysql_close($liendb);

	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='DELETE'){
	$id=$_GET['id'];
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
	//on recupere les cameras
		$table='users';
		$sql='select * from '.$table.' WHERE email = "'.$_SESSION['user']['name'].'"';
		$zmuser=mysql_query($sql);
		$infosUser=mysql_fetch_array($zmuser);
		$monitorids=$infosUser['cameras'];		//id des cameras
	//
	$tabIds=explode(',',$monitorids);
	$check=array_search(intval($id),$tabIds);
	if($check===null){
		$output['status']='ERROR';
		$output['errorMsg']='Camera introuvable';
		systemLog('USER: '.$_SESSION['user']['name'].' - *** ERROR *** Echec lors de la suppression de la camera: "'.$id.'". Camera introuvable dans la liste du user.');
	}else{
		array_splice($tabIds, $check, 1);
		$newIds=implode(',',$tabIds);
		//on met a jour
			$query="cameras = '".$newIds."'";
			$sql = "UPDATE ".$table." SET ".$query.' WHERE email = "'.$_SESSION['user']['name'].'"';
			if(mysql_query($sql)){
				$output['status']='SUCCESS';
				systemLog('USER: '.$_SESSION['user']['name'].' - Suppression de la camera : '.$id.'  --> SQL: "'.$sql.'"');
			}else{
				$output['status']='ERROR';
				$output['errorMsg']='Suppression de la camera impossible';
				systemLog('USER: '.$_SESSION['user']['name'].' - *** ERROR *** Echec lors de la requete MYSQL: "'.$sql.'"');
			}
		//on supprime la camera
			$table='cameras';
			$sql='DELETE from '.$table.' WHERE id = '.$id;
			mysql_query($sql);
		//on supprime les alertes
			$table='events';
			$sql='DELETE from '.$table.' WHERE monitorid = '.$id;
			mysql_query($sql);
		//on supprime le user FTP
			$donnee = file($ftpDBfile);
			$out4file='';
			$detect=0;
			foreach($donnee as $d){
				if($detect==0){
					if(trim($d)!=$id){
						$out4file.=$d;
					}else{
						$detect=1;	//on passe la ligne (login)
					}
				}else{
					$detect=0;		//on passe la deuxieme ligne (password)
				}
			}
			
			$fichier = fopen($ftpDBfile, "w+");
			fwrite($fichier,$out4file);
			fclose($fichier);
		//lancement du script de mise a jour du FTP
			exec('sudo bash '.$scriptsDir.'update-vsftpd-userdb-4.7.sh',$retour);
			systemLog('USER: '.$_SESSION['user']['name'].' - Suppression du user FTP: '.$id);
		//suppression des donnees FTP
			exec('rm -rf '.$eventsDir.$id.'/');
			systemLog('USER: '.$_SESSION['user']['name'].' - Suppression du dossier FTP: "'.$eventsDir.$id.'"');
	}
	
	//ferme connexion sql
		mysql_close($liendb);
	
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='CREATE_VIDEO'){
	$id=$_GET['id'];
	$overwrite=$_GET['overwrite'];
	
	$output['status']='';
	$output['videofile']='';
	
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('USER:'.$_SESSION['user']['name'].' - *** ERROR *** Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
	//infos event
		$table='events';
		$sql='select * from '.$table.' WHERE id = '.$id;
		$event=mysql_query($sql);
		$eventTab=mysql_fetch_array($event);
		$eventDate=$eventTab['date'];
		$cameraId=$eventTab['monitorid'];
		
	//infos cameras
		$table='cameras';
		$sql='select * from '.$table.' WHERE id = '.$cameraId;
		$cam=mysql_query($sql);
		$infoCam=mysql_fetch_array($cam);
		$camPreset=$infoCam['preset'];
	
	//ferme connexion sql
		mysql_close($liendb);
		
	$Year=date('y',$eventDate);
	$Month=date('m',$eventDate);
	$Day=date('d',$eventDate);
	$Hour=date('H',$eventDate);
	$Min=date('i',$eventDate);
	$Sec=date('s',$eventDate);
	
	//prepa du chemin du dossier de l'evenement
	if($camPreset=='2'){	//Foscam 9821W
		$extra='/snap';
	}else{
		$extra='';
	}
	$eventPathDir=$webEventsDir.$cameraId.$extra.'/'.$Year.'/'.$Month.'/'.$Day.'/'.$id.'/';
	//on cree la video
	exec('ffmpeg -f image2 -r 1 -pattern_type glob -i "'.$eventPathDir.'*.jpg" -c:v libx264 "'.$eventPathDir.$id.'.mp4"',$ret,$status);
	
	$output['status']='SUCCESS';
	$output['videofile']=$eventPathDir.$id.'.mp4';
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='UPDATECAMERA'){
	$id=$_GET['ID'];
	$fields=$_GET['FIELD'];
	$values=$_GET['VALUE'];
		//preparation de la requete SQL
		$tabFields=split(',',$fields);
		$tabValues=split(',',$values);
		$query='';
		for($i=0;$i<count($tabFields);$i++){
			if($i==0){
				$query.="$tabFields[$i] = '$tabValues[$i]'";
			}else{
				$query.=", $tabFields[$i] = '$tabValues[$i]'";
			}
		}
		$sql = "UPDATE cameras SET ".$query." WHERE id = '".$id."'";
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('*** ERROR *** USER: '.$_SESSION['user']['name'].' - Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
	
	//on envoi la requete
		$resultSQL=mysql_query($sql);
		
	//mise en forme des donnees
		if($resultSQL==true){
			$output['status']='SUCCESS';
			$output['successMsg']='Modifications appliquees';
			systemLog('USER: '.$_SESSION['user']['name'].' - Envoi de la requete SQL > '.$sql);
		}else{
			$output['status']='ERROR';
			$output['errorMsg']='Erreur lors de la requete: "'.$sql.'"';
			systemLog('USER: '.$_SESSION['user']['name'].' - *** ERROR *** Erreur requete SQL > '.$sql);
		}
	
	//ferme connexion sql
		mysql_close($liendb);
	
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

if($action=='CHECK_ONLINE'){
	$id=$_GET['id'];
	$line=file($baseDir.'watchdog/online.dat');
	$onlineCamerasTab=explode(',',trim($line[0]));
	if(in_array($id,$onlineCamerasTab)){
		$output['status']='online';
	}else{
		$output['status']='offline';
	}
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

/* FONCTIONS MYSQL */

if($action=='MYSQL'){
	$command=$_GET['COMMAND'];
	
	switch($command){
		case 'UPDATE':
			$table=$_GET['TABLE'];
			$fields=$_GET['FIELDS'];
			$values=$_GET['VALUES'];
			$sField=$_GET['SEARCHFIELD'];
			$sValue=$_GET['SEARCHVALUE'];
			//preparation de la requete SQL
			$tabFields=split(',',$fields);
			$tabValues=split(',',$values);
			$query='';
			for($i=0;$i<count($tabFields);$i++){
				if($i==0){
					$query.="$tabFields[$i] = '$tabValues[$i]'";
				}else{
					$query.=", $tabFields[$i] = '$tabValues[$i]'";
				}
			}
			$sql = "UPDATE ".$table." SET ".$query." WHERE ".$sField." = '".$sValue."'";
			break;
	}
	
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('*** ERROR *** USER: '.$_SESSION['user']['name'].' - Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			die($_GET['callback'] .'('. json_encode($errorSQL) . ')');
		}
		mysql_select_db($bddnom);
	
	//on envoi la requete
		$resultSQL=mysql_query($sql);
		
	//mise en forme des donnees
		if($resultSQL==true){
			$output['status']='SUCCESS';
			$output['successMsg']='Modifications appliquees';
			systemLog('USER: '.$_SESSION['user']['name'].' - Envoi de la requete SQL > '.$sql);
		}else{
			$output['status']='ERROR';
			$output['errorMsg']='Erreur lors de la requete: "'.$sql.'"';
			systemLog('USER: '.$_SESSION['user']['name'].' - *** ERROR *** Erreur requete SQL > '.$sql);
		}
	
	//ferme connexion sql
		mysql_close($liendb);
	
	die($_GET['callback'] .'('. json_encode($output) . ')');
}

function MYSQL_FETCHONE($sql){
	include('conf/sql.conf');
	//connexion bdd
		$liendb=mysql_connect($sqlserver,$sqluser,$sqlpass);
		if(!$liendb){
			systemLog('*** ERROR *** USER: '.$_SESSION['user']['name'].' - Echec de connexion a la base MYSQL. SERVER="'.$sqlserver.'" USER="'.$sqluser.'" PASS="'.$sqlpass.'"');
			$out=array();
			return $out;
		}
		mysql_select_db($bddnom);
	
	//on execute la requete
		$out=array();
		$result=mysql_query($sql);
		if($result){
			$out=mysql_fetch_array($result);
		}	
	
	//ferme connexion sql
		mysql_close($liendb);
		
	return $out;
}

/* FIN MYSQL */

function format_taille($size) {
  if ($size == 0) return "0";
  $liste = array(" octets", " Ko", " Mo", " Go", " To"); 
  $index = floor(log($size)/log(1024)); 
  $frm = (($size > 1023) ? ("%3.2f") : ("%d")); 
  
  return sprintf("$frm%s", (($size) ? ($size/pow(1024, $index)) : "0"), $liste[$index]); 
}

function systemLog($logtxt){
	global $systemlogfile;
	//si le fichier n'existe pas on le creer
	if(!file_exists($systemlogfile)){
		$test=fopen($systemlogfile,'w+');
		fclose($test);
		//on verifie qu'on a reussi sinon on retourne l'erreur
		if(!file_exists($systemlogfile)){
			return false;
		}
	}
	//
	setlocale (LC_TIME, 'fr_FR.utf8','fra');
	$ladate=strftime("%A %d %B %Y %T");
	//
	$ligne=$ladate.' -- '.$logtxt."\n";
	//on charge le contenu du fichier log
		$oldLog=file_get_contents($systemlogfile);
	//ecriture du fichier
		$fichier=fopen($systemlogfile,'w+');
		fwrite($fichier,$ligne.$oldLog);
		fclose($fichier);
	//	
	return true;
}

?>