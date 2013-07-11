<?php
header('Content-Type: text/html; charset=ISO-8859-1');
$action=$_POST['action'];
$output=array();
if($action=='GET_PANEL'){
	$html='Dlink 930L';
	$html.='';
	die($html);
}
if($action=='GET_MOTIONDETECTCONFIG'){
	$host=$_POST['host'];
	$port=$_POST['port'];
	$usr=$_POST['usr'];
	$pwd=$_POST['pwd'];
	//on recupere l'url pour interroger la camera		
	//$url=GET_MOTIONDETECTCONFIG_URL;
	//$checkURL=str_replace('%USER%',$usr,$url);
	//$checkURL=str_replace('%PWD%',$pwd,$checkURL);
	//$fullUrl='http://'.$host.':'.$port.'/'.$checkURL;
	//
	//$content=CURLIT($fullUrl);
	$output['model']='DLINK930L';
	//
	die(json_encode($output));
}

if($action=='SET_MOTIONDETECTSTATE'){
	$host=$_POST['host'];
	$port=$_POST['port'];
	$usr=$_POST['usr'];
	$pwd=$_POST['pwd'];
	$state=$_POST['state'];
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