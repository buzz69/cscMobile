var FOSCAM_8918W_CONTROL_ID=1;
var FOSCAM_9821W_CONTROL_ID=2;
var VIVOTEK_IP8332_CONTROL_ID=4;
function Camera(Cid,Cname,Cfunction,Clogin,Cpassword,Cprotocol,Ccontrollable,Ccontroldevice,Cdevicepicture,Cwidth,Cheight,Chost,Cport,Cpath,Cpath2,Cpreset,Nbevents,CalertesSupport,CalertesPlaning) {
	var Cid=Cid;
	var Cname=Cname;
	var Cfunction=Cfunction;
	var Cprotocol=Cprotocol;
	var Ccontrollable=Ccontrollable;
	var Ccontroldevice=Ccontroldevice;
	var Cdevicepicture=Cdevicepicture;
	var Cpreset=Cpreset;
	var Cwidth=Cwidth;
	var Cheight=Cheight;
	var Chost=Chost;
	var Cport=Cport;
	var Cpath=Cpath;
	var Cpath2=Cpath2;
	var Clogin=Clogin;
	var Cpassword=Cpassword;
	var Cevents=Nbevents;
	var CeventsTab=null;
	var CalertesSupport=CalertesSupport;
	var CalertesPlaning=CalertesPlaning;
	//var Cfullpath=Cpath.replace('%HOST%',Chost);
	var Cfullpath=Cpath2.replace('%HOST%',Chost);
	Cfullpath=Cfullpath.replace('%PORT%',Cport);
	if(Cpath.indexOf('%USR%')!=-1){
		Cfullpath=Cfullpath.replace('%USR%',Clogin);
	}
	if(Cpath.indexOf('%PWD%')!=-1){
		Cfullpath=Cfullpath.replace('%PWD%',Cpassword);
	}
	var Clink=Cprotocol+'://'+Cfullpath;
	this.Cid=Cid;
	this.Cname=Cname;
	this.Cfunction=Cfunction;
	this.Cprotocol=Cprotocol;
	this.Ccontrollable=Ccontrollable;
	this.Ccontroldevice=Ccontroldevice;
	this.Cdevicepicture=Cdevicepicture;
	this.Cpreset=Cpreset;
	this.Cwidth=Cwidth;
	this.Cheight=Cheight;
	this.Chost=Chost;
	this.Cport=Cport;
	this.Cpath=Cpath;
	this.Cpath2=Cpath2;
	this.Clogin=Clogin;
	this.Cpassword=Cpassword;
	this.Cevents=Nbevents;
	this.CeventsTab=null;
	this.CalertesSupport=CalertesSupport;
	this.CalertesPlaning=CalertesPlaning;
	this.Cfullpath=Cfullpath;
	this.Clink=Cprotocol+'://'+Cfullpath;
	
	//alert(Cname+"\n"+Clink);
	
	//variable pour le test de connexion
	var Conline='offline';
	this.Conline=Conline;
	
	//CREATION DE LA CAMERA DANS LA LISTE
		this.create = function(container) {
			$.ajax({
				type       : "GET",
				url        : "http://www.cloudsecuritycam.com/csc/mobileapp/ajax.php",
				contentType: "application/json;charset=iso859-15",  
				dataType   : 'jsonp', 
				async	   : false,
				data       : {action: 'CHECK_ONLINE' , id: Cid },
				success    : function(rep) {
					Conline=rep.status;
					this.Conline=rep.status;
					//
					ConlineTxt='<font style="color:#E55">offline</font>';
					action='';
					if(Conline=='online'){
						ConlineTxt='<font style="color:#5E5">online</font>';
						action='tabCameras['+Cid+'].display();';
					}
					CfunctionTxt='<font style="color:#AAA">Désactivé</font>';
					if(Cfunction=='monitor'){ CfunctionTxt='<font style="color:#000">Activé</font>'; }
					if(Cfunction=='modect'){ CfunctionTxt='<font style="color:#55E">Détection de mouvements</font>'; }
					html='<li data-icon="false"><a href="#" onclick="'+action+'return false;"><img width=80 height=80 src="'+Cdevicepicture+'"/><h3>'+Cname+'</h3><p>'+CfunctionTxt+'</p><p class="ui-li-aside"><strong>'+ConlineTxt+'</strong></p></a></li>';
					$('#'+container).append(html).listview('refresh');
				}
			});
	}
	
	//DESTRUCTION
		this.destruct = function() {			//supprime de la liste des cameras	
			$('#container'+Cid).remove();
		}
		this.erase = function(){				//efface la vue
			$('#displayedCamera'+Cid).remove();
		}
	
	//AFFICHAGE
		this.display = function() {
			currentCamera['link']=Clink;
			currentCamera['name']=Cname;
			currentCamera['id']=Cid;
			$.mobile.changePage( "view.html", { transition: "slide"} );
		}
	
	//RAFRAICHIR AFFICHAGE
		this.displayRefresh = function(){
			if($('#displayedCamera'+Cid)){
				$('#displayedCamera'+Cid+' img').attr('src',Clink);
			}
			$('#container'+Cid+' img.containerPreview').attr('src',Clink);
		}
		this.linkRefresh = function(){
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "ajax.php",
				data: "action=GET_CAM_LINK&id="+Cid,
				success: function (rep) {
					Clink=rep;
					this.Clink=Clink;
					tabCameras[Cid].displayRefresh();
					tabCameras[Cid].refreshState();
				}
			});
		}
		
	//PANEL
		this.getPanel = function(container) {
				panel='';
				//panel+=radiosFunction+'<br>';
				//panneau de controle
					controlPanel='';
					if(Ccontrollable==1){		//si pilotable
						controlPanel='<center>';
						
						if(Cpreset==FOSCAM_8918W_CONTROL_ID){
							controlPanel+='<table width=90%><tr><td width=50% align=left valign=top>';
							controlPanel+='<fieldset data-role="controlgroup" data-mini="true" style="max-width:150px">';
							controlPanel+='<legend>Vidéo:</legend>';
							controlPanel+='<input type="checkbox" class="checkbtn" name="flip" id="flipped" value="flip" onchange="tabCameras['+Cid+'].flipCamera()"/>';
							controlPanel+='<label for="flipped">Flip</label>';
							controlPanel+='<input type="checkbox" class="checkbtn" name="mirror" id="mirrored" value="mirror" onchange="tabCameras['+Cid+'].flipCamera()" />';
							controlPanel+='<label for="mirrored">Miroir</label>';
							controlPanel+='</fieldset>';
							controlPanel+='</td><td width=50% align=right valign=center>';
						}
						
						controlPanel+='	<table id="Table_01" width="150" height="150" border="0" cellpadding="0" cellspacing="0">';
						controlPanel+='		<tr><td></td><td><div class="ctrlbtn" command="moveConUp" flip="moveConDown" mirror="moveConUp" mirrorflip="moveConDown" data-role="button" data-icon="arrow-u" data-iconpos="notext"  onmousedown="controlCamera(\''+Cid+'\',\'moveConUp\');" style="cursor:pointer"></div></td><td></td></tr>';
						controlPanel+='		<tr><td><div class="ctrlbtn" command="moveConLeft" flip="moveConLeft" mirror="moveConRight" mirrorflip="moveConRight" data-role="button" data-icon="arrow-l" data-iconpos="notext"  style="cursor:pointer"></div></td><td><div data-role="button" data-icon="home" data-iconpos="notext"  onclick="controlCamera(\''+Cid+'\',\'presetHome\');return false;" style="cursor:pointer" title="Centrer la caméra"></div></td><td><div class="ctrlbtn" command="moveConRight" flip="moveConRight" mirror="moveConLeft" mirrorflip="moveConLeft" data-role="button" data-icon="arrow-r" data-iconpos="notext"  style="cursor:pointer"></div></td></tr>';
						controlPanel+='		<tr><td></td><td><div class="ctrlbtn" command="moveConDown" flip="moveConUp" mirror="moveConDown" mirrorflip="moveConUp" data-role="button" data-icon="arrow-d" data-iconpos="notext"  style="cursor:pointer"></div></td><td></td></tr>';
						controlPanel+='	</table>';
						
						if(Cpreset==FOSCAM_8918W_CONTROL_ID){
							controlPanel+='</td></tr></table>';
						}
						
						controlPanel+='</center>';
					}
				//
				panel+=controlPanel;
				
				$('#'+container).html(panel);
				$('#'+container).show();
				
				//evenement sur les boutons de control PTZ
					//taphold
						$( ".ctrlbtn" ).on( 'taphold', tapholdHandler );
						// Callback function references the event target 
						function tapholdHandler( event ) {
							command=$(event.currentTarget).attr('command');
							mirror=$(event.currentTarget).attr('mirror');
							flip=$(event.currentTarget).attr('flip');
							mirrorflip=$(event.currentTarget).attr('mirrorflip');
							//
							if(Cpreset==FOSCAM_8918W_CONTROL_ID){
								mirrored=$('#mirrored').is(':checked');
								flipped=$('#flipped').is(':checked');
								state=0;
								if(mirrored){ state=state+2;}
								if(flipped){ state=state+1;}
								if(state==0){
									cmd=command;
								}
								if(state==1){
									cmd=flip;
								}
								if(state==2){
									cmd=mirror;
								}
								if(state==3){
									cmd=mirrorflip;
								}
							}else{
								cmd=command;
							}
							//console.log(cmd);
							controlCamera(Cid,cmd);
						}
					//release
						$( ".ctrlbtn" ).on( 'vmouseup', vmouseupHandler );
						// Callback 
						function vmouseupHandler( event ) {
							//console.log('release');
							controlCamera(Cid,'moveStop');
						}
				//
				if(Cpreset==FOSCAM_8918W_CONTROL_ID){
					url='http://'+Chost+':'+Cport+'/get_camera_params.cgi?user='+Clogin+'&pwd='+Cpassword;
					tabCameras[Cid].getVideoParams(url);
				}
				
		}
		this.refreshState = function(){
			tabFonctions=new Array();
			tabFonctions['disable']=new Array('function-none.png','Désactivée');
			tabFonctions['monitor']=new Array('function-monitor.png','Activée');
			tabFonctions['modect']=new Array('function-modect.png','Détection de mouvements');
			txt='<table width=100% cellspacing=0 cellpadding=0><tr><td align=left valign=center>'+Cwidth+'x'+Cheight+"</td><td align=right valign=center><img src='"+tabFonctions[Cfunction][0]+"' title='"+tabFonctions[Cfunction][1]+"'/></td></tr></table>";
			$('#container'+Cid+' div.infos').html(txt);
		}
	
	//VERIF CONNEXION
		this.checkOnline = function(){
			$.ajax({
				type       : "GET",
				url        : "http://www.cloudsecuritycam.com/csc/mobileapp/ajax.php",
				contentType: "application/json;charset=iso859-15",  
				dataType   : 'jsonp', 
				async	   : false,
				data       : {action: 'CHECK_ONLINE' , id: Cid },
				success    : function(rep) {
					Conline=rep.status;
					this.Conline=rep.status;
					return Conline;
				}
			});
		}
		this.getOnlineStatus = function(){
			return Conline;
		}
	
	//SETUP
		this.getSettings = function(){
			settings=new Array();
			settings['id']=Cid;
			settings['name']=Cname;
			settings['protocol']=Cprotocol;
			settings['controllable']=Ccontrollable;
			settings['controlid']=Ccontrolid;
			settings['width']=Cwidth;
			settings['height']=Cheight;
			settings['host']=Chost;
			settings['port']=Cport;
			settings['path']=Cpath;
			settings['controldevice']=Ccontroldevice;
			settings['controladdress']=Ccontroladdress;
			return settings;
		}
		
	//EVENEMENTS
		this.refreshEvents = function(){
			$('#refreshBtn').css('color','#3E78FD');
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "ajax.php",
				data: "action=GET_EVENTS_COUNT&id="+Cid,
				success: function (nb) {
					Cevents=parseInt(nb);
					this.Cevents=Cevents;
					tabCameras[Cid].getPanel('cameraPanel');
					$('#refreshBtn').css('color','#333');
				}
			});
		}
		this.loadEvents = function(){
			logThis('Chargement des Alertes de la caméra: '+Cname,Cid);
			showLoader();
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				dataType:"json",
				url: "ajax.php",
				data: "action=GET_CAM_EVENTS&id="+Cid,
				success: function (eventsList) {
					CeventsTab=eventsList;
					this.CeventsTab=CeventsTab;
					tabCameras[Cid].viewChart('year');
					showEvents();
					hideLoader();
				}
			});
		}
		this.viewChart = function(filter,annee,mois,jour){
			//alert(filter+','+annee+','+mois+','+jour);
			imagesPreload=new Array();
			currentCamera['id']=Cid;
			currentCamera['filter']=filter;
			currentCamera['annee']=annee;
			currentCamera['mois']=mois;
			currentCamera['jour']=jour;
			//alert('Function viewChart(filter,annee,mois,jour)'+"\nFilter: "+filter+"\nAnnee: "+annee+"\nMois: "+mois+"\nJour: "+jour);
			if(playStatus=="play"){
				clearInterval(playInterval);
				playStatus="stop";
			}
			if(currentEvent.length==4){
				currentEvent=new Array();
			}
			if(preloading==true){
				preloading=false;
				clearInterval(checkPreload);
				tabCameras[Cid].hidePreloader();
			}
			
			switch(filter){
				case 'year':
					//alert(numProps(CeventsTab.Events));
					if(numProps(CeventsTab.Events)==1){		//s'il n'y a qu'un seul choix alors on le prend par defaut
						for(var year in CeventsTab.Events){
							uniqueYear=year;
						}
						tabCameras[Cid].viewChart('month',uniqueYear);
						return false;
					}
					html='<h1>Alertes de '+Cname+'</h1><img src="close.gif" class="exitEvent" onclick="hideEvents()"/>';
					//html+='<a href="#" onclick="tabCameras['+Cid+'].viewChart(\'year\');return false;">'+Cname+'</a></br><hr/>';
					html+='</br><hr/>';
					html+='<table width=100% cellspacing=0 cellpadding=0><tr><td valign=top align=left>';
					html+='<div class="eventListDiv">';
					html+='<table width=120 cellspacing=0 cellpadding=0 class="eventsTable">';
					html+='<tr><th width=60>Annee</th><th width=60>Alertes</th></tr>';
					for(var year in CeventsTab.Events){
						//calcul du nombres d'evenements de cette annee
							nb=0
							for(var month in CeventsTab.Events[year]){
								for(var day in CeventsTab.Events[year][month]){
									nb+=numProps(CeventsTab.Events[year][month][day]);
								}
							}
						//
						html+='<tr onclick="tabCameras['+Cid+'].viewChart(\'month\',\''+year+'\')"><td>20'+year+'</td><td>'+nb+'</td></tr>';
					}
					html+='</table></div>';
					html+='</td><td valign=top valign=right>';
					html+='<div id="previewEventDiv"></div>';
					html+='</td></tr></table>';
					break;
				/* ORIGINAL
				case 'month':
					if(numProps(CeventsTab.Events[annee])==1){
						for(var month in CeventsTab.Events[annee]){
							uniqueMonth=month;
						}
						tabCameras[Cid].viewChart('day',annee,uniqueMonth);
						return false;
					}
					html='<div class="eventsHeader">Alertes de l\'année 20'+annee+'</div><div class="exitEvent" onclick="hideEvents()">X</div>';
					html+='<a href="#" onclick="tabCameras['+Cid+'].viewChart(\'year\');return false;">'+Cname+'</a> > 20'+annee+'</br><hr/>';
					html+='<table width=100% cellspacing=0 cellpadding=0><tr><td valign=top align=left>';
					html+='<div class="eventListDiv">';
					html+='<table width=140 cellspacing=0 cellpadding=0 class="eventsTable">';
					html+='<tr><th width=80>Mois</th><th width=60>Alertes</th></tr>';
					for(var month in CeventsTab.Events[annee]){
						//calcul du nombres d'evenements de ce mois
							nb=0
							for(var day in CeventsTab.Events[annee][month]){
								nb+=numProps(CeventsTab.Events[annee][month][day]);
							}
						//
						moisName=monthName(month);
						html+='<tr onclick="tabCameras['+Cid+'].viewChart(\'day\',\''+annee+'\',\''+month+'\')"><td>'+moisName+'</td><td>'+nb+'</td></tr>';
					}
					html+='</table></div>';
					html+='</td><td valign=top valign=right>';
					html+='<div id="previewEventDiv"></div>';
					html+='</td></tr></table>';
					break;
				case 'day':
					if(numProps(CeventsTab.Events[annee][mois])==1){
						for(var day in CeventsTab.Events[annee][mois]){
							uniqueDay=day;
						}
						tabCameras[Cid].viewChart('events',annee,mois,uniqueDay);
						return false;
					}
					moisName=monthName(mois);
					html='<div class="eventsHeader">Evènements de '+moisName+' 20'+annee+'</div><div class="exitEvent" onclick="hideEvents()">X</div>';
					html+='<a href="#" onclick="tabCameras['+Cid+'].viewChart(\'year\');return false;">'+Cname+'</a> > <a href="#" onclick="tabCameras['+Cid+'].viewChart(\'month\',\''+annee+'\');return false;">20'+annee+'</a> > '+moisName+'</br><hr/>';
					html+='<table width=100% cellspacing=0 cellpadding=0><tr><td valign=top align=left>';
					html+='<div class="eventListDiv">';
					html+='<table width=120 cellspacing=0 cellpadding=0 class="eventsTable">';
					html+='<tr><th width=60>Date</th><th width=60>Events</th></tr>';
					for(var day in CeventsTab.Events[annee][mois]){
						nb=numProps(CeventsTab.Events[annee][mois][day]);
						html+='<tr onclick="tabCameras['+Cid+'].viewChart(\'events\',\''+annee+'\',\''+mois+'\',\''+day+'\')"><td>'+day+'</td><td>'+nb+'</td></tr>';
					}
					html+='</table></div>';
					html+='</td><td valign=top valign=right>';
					html+='<div id="previewEventDiv"></div>';
					html+='</td></tr></table>';
					break;
				*/ //FIN ORIGINAL
				
				//MODIFS
				case 'month':
					if(numProps(CeventsTab.Events[annee])==1){		//s'il n'y a qu'un seul choix alors on le prend par defaut
						for(var month in CeventsTab.Events[annee]){
							uniqueMonth=month;
						}
						tabCameras[Cid].viewChart('day',annee,uniqueMonth);
						return false;
					}
					colors=['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];
					namesMonths=['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
					data=new Array();
					categories=new Array();
					for(i=0;i<12;i++){
						categories[i]=namesMonths[i];
						data[i]=new Object();
						data[i].y=0;
						data[i].color=colors[0];
					}	
					var graphName='';
					var Xlabel, Ylabel;
					colorNum=0;
					itemSort='alertes';
					moisName=monthName(mois);
					newFilter='day';
					graphName=' 20'+annee;
					Xlabel='Mois';
					Ylabel='Alertes';
					cnt=0;
					colorNum=0;
					//on prepare la boite de dialog
						html='<h1>Alertes de '+graphName+'</h1><img src="close.gif" class="exitEvent" onclick="hideEvents()"/>';
						html+='&nbsp;&nbsp;<a href="#" onclick="tabCameras['+Cid+'].viewChart(\'year\');return false;">'+Cname+'</a> > 20'+annee+'</br><hr/>';
						html+='<table width=100% cellspacing=0 cellpadding=0><tr><td valign=top align=center>';
						html+='<div id="chartWrapper" style="height:450px;width:950px;overflow:auto">';
						html+='</td></tr></table>';
						$('#eventsDiv').html(html);
					//
					for(var month in CeventsTab.Events[annee]){
						//calcul du nombres d'evenements de ce mois
							nb=0
							for(var day in CeventsTab.Events[annee][month]){
								nb+=numProps(CeventsTab.Events[annee][month][day]);
							}
						//
						num=parseInt(month)-1;
						data[num].y=nb;
						data[num].color=colors[colorNum];
						if(colorNum==(colors.length-1)){
							colorNum=0;
						}else{
							colorNum++;
						}
					}
					//on cree le graphique
					colors = Highcharts.getOptions().colors;
					categories = categories;
					name = 'Evenement';
					data = data;
					
					chart = new Highcharts.Chart({
						chart: {
							renderTo: 'chartWrapper',
							type: 'column'
						},
						title: {
							text: graphName
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: categories,
							title: {
								text: Xlabel
							},
							labels: {
								rotation: -25,
								align: 'right',
								style: {
									fontSize: '14px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						},
						yAxis: {
							title: {
								text: Ylabel
							}
						},
						plotOptions: {
							column: {
								cursor: 'pointer',
								point: {
									events: {
										click: function() {
											var navItem = this.x;
												item=categories[navItem];
												item=nameToMonth(item);
												tabCameras[currentCamera['id']].viewChart(newFilter,annee,item);
										}
									}
								},
								dataLabels: {
									enabled: true,
									color: colors[0],
									style: {
										fontWeight: 'bold'
									},
									formatter: function() {
										return this.y;
									}
								}
							}
						},
						tooltip: {
							formatter: function() {
								if(filter=='events'){
									navItem=this.x;
									//alert(navItem);
									tabCameras[currentCamera['id']].previewEvent(annee,mois,jour,navItem);
									var point = this.point,
									s = '<b>'+ this.y +' alertes</b><br/>';
									return s;
								}
								var point = this.point,
									s = this.x + ': <b>'+ this.y +' alertes</b><br/>';
								return s;
							}
						},
						series: [{
							name: name,
							data: data,
							color: 'green'
						}],
						exporting: {
							enabled: false
						}
					});
					return false;
					break;
				case 'day':
					if(numProps(CeventsTab.Events[annee][mois])==1){		//s'il n'y a qu'un seul choix alors on le prend par defaut
						for(var day in CeventsTab.Events[annee][mois]){
							uniqueDay=day;
						}
						tabCameras[Cid].viewChart('events',annee,mois,uniqueDay);
						return false;
					}
					colors=['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];
					data=new Array();
					categories=new Array();
					for(i=0;i<31;i++){
						journum=i+1;
						journum=journum.toString();
						if(journum.length==1){journum='0'+journum;}
						categories[i]=journum;
						data[i]=new Object();
						data[i].y=0;
						data[i].color=colors[0];
					}	
					var graphName='';
					var Xlabel, Ylabel;
					colorNum=0;
					itemSort='alertes';
					moisName=monthName(mois);
					newFilter='events';
					graphName=moisName+' 20'+annee;
					Xlabel='Jour du mois';
					Ylabel='Alertes';
					cnt=0;
					colorNum=0;
					//on prepare la boite de dialog
						html='<h1>Alertes de '+graphName+'</h1><img src="close.gif" class="exitEvent" onclick="hideEvents()"/>';
						html+='&nbsp;&nbsp;<a href="#" onclick="tabCameras['+Cid+'].viewChart(\'year\');return false;">'+Cname+'</a> > <a href="#" onclick="tabCameras['+Cid+'].viewChart(\'month\',\''+annee+'\');return false;">20'+annee+'</a> > '+moisName+'</br><hr/>';
						html+='<table width=100% cellspacing=0 cellpadding=0><tr><td valign=top align=center>';
						html+='<div id="chartWrapper" style="height:450px;width:950px;overflow:auto">';
						html+='</td></tr></table>';
						$('#eventsDiv').html(html);
					//
					for(var day in CeventsTab.Events[annee][mois]){
						nb=numProps(CeventsTab.Events[annee][mois][day]);
						//
						x=day.toString();
						if(x.substr(0,1)=='0'){
							x=x.substr(1,(x.length-1));
						}
						x=parseInt(x)-1;
						//alert(day+':'+x);
						data[x].y=nb;
						data[x].color=colors[colorNum];
						if(colorNum==(colors.length-1)){
							colorNum=0;
						}else{
							colorNum++;
						}
					}
					//on cree le graphique
					colors = Highcharts.getOptions().colors;
					categories = categories;
					name = 'Evenement';
					data = data;
					
					chart = new Highcharts.Chart({
						chart: {
							renderTo: 'chartWrapper',
							type: 'column'
						},
						title: {
							text: graphName
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: categories,
							title: {
								text: Xlabel
							},
							labels: {
								rotation: 0,
								align: 'right',
								style: {
									fontSize: '10px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						},
						yAxis: {
							title: {
								text: Ylabel
							}
						},
						plotOptions: {
							column: {
								cursor: 'pointer',
								point: {
									events: {
										click: function() {
											var navItem = this.x;
												item=categories[navItem];
												tabCameras[currentCamera['id']].viewChart(newFilter,annee,mois,item);
										}
									}
								},
								dataLabels: {
									enabled: true,
									color: colors[0],
									style: {
										fontWeight: 'bold'
									},
									formatter: function() {
										return this.y;
									}
								}
							}
						},
						tooltip: {
							formatter: function() {
								if(filter=='events'){
									navItem=this.x;
									//alert(navItem);
									tabCameras[currentCamera['id']].previewEvent(annee,mois,jour,navItem);
									var point = this.point,
									s = '<b>'+ this.y +' alertes</b><br/>';
									return s;
								}
								var point = this.point,
									s = 'le ' + this.x +' '+moisName+': <b>'+ this.y +' alertes</b><br/>';
								return s;
							}
						},
						series: [{
							name: name,
							data: data,
							color: 'green'
						}],
						exporting: {
							enabled: false
						}
					});
					return false;
					break;
				//FIN MODIFS
				
				case 'events':
					moisName=monthName(mois);
					html='<h1>Alertes du '+jour+' '+moisName+' 20'+annee+'</h1><img src="close.gif" class="exitEvent" onclick="hideEvents()"/>';
					html+='&nbsp;&nbsp;<a href="#" onclick="tabCameras['+Cid+'].viewChart(\'year\');return false;">'+Cname+'</a> > <a href="#" onclick="tabCameras['+Cid+'].viewChart(\'month\',\''+annee+'\');return false;">20'+annee+'</a> > <a href="#" onclick="tabCameras['+Cid+'].viewChart(\'day\',\''+annee+'\',\''+mois+'\');return false;">'+moisName+'</a> > '+jour+'</br><hr/>';
					html+='<table width=100% cellspacing=0 cellpadding=0><tr><td width=330 valign=top align=left>';
					/*
					html+='<div class="CSSTableGenerator">';
					html+='<table width=280 cellspacing=0 cellpadding=0 class="eventsTable">';
					//html+='<tr><th width=70>Heure</th><th width=60>Durée</th><th width=80>Cause</th><th width=50>Score</th></tr>';
					html+='<tr><td width=70>Heure</td><td width=60>Durée</td><td width=80>Cause</td><td width=50>Score</td></tr>';
					for(var key in CeventsTab.Events[annee][mois][jour]){
						//preload des autres images
						imagesPreload[key]=new Image();
						imagesPreload[key].src=CeventsTab.Events[annee][mois][jour][key]['path']+'001-capture.jpg';
						//recuperation de l'heure
						heure=CeventsTab.Events[annee][mois][jour][key]['time']['heure']+':'+CeventsTab.Events[annee][mois][jour][key]['time']['minute']+':'+CeventsTab.Events[annee][mois][jour][key]['time']['seconde'];
						html+='<tr onmouseover="tabCameras['+Cid+'].previewEvent(\''+annee+'\',\''+mois+'\',\''+jour+'\',\''+key+'\')" onclick="tabCameras['+Cid+'].viewEvent(\''+annee+'\',\''+mois+'\',\''+jour+'\',\''+key+'\')"><td class="eventsDate">'+heure+'</td><td class="eventsDuree">'+CeventsTab.Events[annee][mois][jour][key]['Length']+'s</td><td class="eventsNotes">'+CeventsTab.Events[annee][mois][jour][key]['Cause']+'</td><td class="eventsScore">'+CeventsTab.Events[annee][mois][jour][key]['TotScore']+'</td></tr>';
					}
					html+='</table></div>';
					*/
					html+='<table><tr><td width=70>&nbsp;<b>Heure</b></td><td width=60>&nbsp;<b>Durée</b></td><td width=80>&nbsp;<b>Cause</b></td><td width=50>&nbsp;<b>Score</b></td></tr></table>';
					html+='<div id="listeEvenementsDiv">';
					//imagesPreloaded=0;
					//imagesPreload=new Array();
					//preloadCounter=0;
					for(var key in CeventsTab.Events[annee][mois][jour]){
						//preload des autres images
						/*
						preloadCounter++;
						imagesPreload[key]=new Image();
						imagesPreload[key].src=CeventsTab.Events[annee][mois][jour][key]['path']+'001-capture.jpg';
						imagesPreload[key].onload = function(){
							imagesPreloaded++;
						}
						*/
						//recuperation de l'heure
						heure=CeventsTab.Events[annee][mois][jour][key]['time']['heure']+':'+CeventsTab.Events[annee][mois][jour][key]['time']['minute']+':'+CeventsTab.Events[annee][mois][jour][key]['time']['seconde'];
						html+='<li id="line'+key+'" onmouseover="tabCameras['+Cid+'].previewEvent(\''+annee+'\',\''+mois+'\',\''+jour+'\',\''+key+'\')" onclick="tabCameras['+Cid+'].viewEvent(\''+annee+'\',\''+mois+'\',\''+jour+'\',\''+key+'\')"><table><tr><td width=70>'+heure+'</td><td width=60>'+CeventsTab.Events[annee][mois][jour][key]['Length']+'s</td><td width=80>'+CeventsTab.Events[annee][mois][jour][key]['Cause']+'</td><td width=60>'+CeventsTab.Events[annee][mois][jour][key]['TotScore']+'</td></tr></table></li>';
					}
					html+='</div>';
					html+='</td><td valign=top valign=right>';
					html+='<div id="previewEventDiv"><img id="previewEventImg" src="empty.png" width=500 height=375/></div>';
					html+='</td></tr></table>';
					//on lance le status du preloader
						//showPreloader();
					//
					break;
			}
			$('#eventsDiv').html(html);
		}
		this.previewEvent = function(year,month,day,eventID){
			if(currentEvent.length==5){
				return false;
			}
			if(preloading==true){
				preloading=false;
				clearInterval(checkPreload);
				tabCameras[Cid].hidePreloader();
			}
			//alert(year+'/'+month+'/'+day+' '+eventID);
			selectedEvent=CeventsTab.Events[year][month][day][eventID];
			heure=selectedEvent['time']['heure']+':'+selectedEvent['time']['minute']+':'+selectedEvent['time']['seconde'];
			//on recupere une image d'analyse s'il y en a une
			numimage='001';
			typeimage='capture';
			if(CeventsTab.Stats[eventID]){
				if(CeventsTab.Stats[eventID]['frames'].length>0){
					typeimage='analyse';
					numimage=CeventsTab.Stats[eventID]['frames'][0];
					numimage=numimage.toString();
					if(numimage.length==1){numimage='00'+numimage;}
					if(numimage.length==2){numimage='0'+numimage;}
				}
			}
			/*
			if(selectedEvent['video']!=''){
				hasvideo='oui';
			}else{
				hasvideo='non';
			}
			//*/
			html='<table width=100%><tr><td align=left width=500 valign=top><b>Photo</b><br><img id="previewEventImg" src="'+selectedEvent['path']+numimage+'-'+typeimage+'.jpg" width=500 height=375/></td><td align=right valign=top><b>Infos</b></br>';
			html+='<font style="font-size:0.8em">';
			html+='Heure: '+heure+'</br>';
			html+='Durée: '+selectedEvent['Length']+'s<br>';
			html+='Score total: '+selectedEvent['TotScore']+'<br>';
			html+='Images: '+selectedEvent['Frames']+'<br>';
			html+='Détails: '+selectedEvent['Notes']+'<br>';
			//html+='Vidéo: '+hasvideo+'<br>';
			html+='</font></td></tr></table>';
			//
			$('#previewEventDiv').html(html);
		}
		this.viewEvent = function(year,month,day,eventID,force){
			currentFilter=0;		//filtre "normal"
			forceMode=false;
			if(force){
				if(force=='force'){
					forceMode=true;
				}
			}
			if(playStatus=="play"){
				clearInterval(playInterval);
				playStatus="stop";
			}
			if(preloading==true){
				preloading=false;
				clearInterval(checkPreload);
				tabCameras[Cid].hidePreloader();
			}
			//si deja un evenement affiche on le deselectionne
			if(forceMode==false){
				if(currentEvent.length==5){
					lineNum=currentEvent[3];
					$('#line'+lineNum).removeClass("selectedLine");
					currentEvent=new Array();
					$("#listeEvenementsDiv li").each(function(n) {
						$(this).removeClass("disabledLI");
					});
					return false;
				}
				$("#listeEvenementsDiv li").each(function(n) {
					$(this).addClass("disabledLI");
				});
				imgSrc=$('#previewEventImg').attr('src');
				currentEvent=new Array(year,month,day,eventID,imgSrc);
				$('#line'+eventID).removeClass("disabledLI");
				$('#line'+eventID).addClass("selectedLine");
				selectedEvent=CeventsTab.Events[year][month][day][eventID];
			}else{
				imgSrc=currentEvent[4];
			}
			//
			//if(selectedEvent['video']!=''){
			//	hasvideo='oui';
			//}else{
			//	hasvideo='non';
			//}
			//
			heure=selectedEvent['time']['heure']+':'+selectedEvent['time']['minute']+':'+selectedEvent['time']['seconde'];
			html='<table width=100%><tr><td align=left width=500 valign=top><b>Photo</b><br><div id="viewContainer"><img id="previewEventImg" src="'+imgSrc+'" width=500 height=375/></div></td><td align=right valign=top><b>Infos</b></br>';
			html+='<font style="font-size:0.8em">';
			html+='Heure: '+heure+'</br>';
			html+='Durée: '+selectedEvent['Length']+'s<br>';
			html+='Score total: '+selectedEvent['TotScore']+'<br>';
			html+='Images: '+selectedEvent['Frames']+'<br>';
			html+='Détails: '+selectedEvent['Notes']+'<br>';
			//html+='Vidéo: '+hasvideo+'<br>';
			if(navigateur=="chrome" || navigateur=="safari"){
				html+='<div id="filterNameDiv">Filtre: aucun</div>';
			}
			html+='<div id="scoreInfos"></div>';
			html+='<div id="eventButtons">';
			//if(selectedEvent['video']!=''){
			//	html+='<input type="button" value="Video" onclick="tabCameras['+Cid+'].launchVideo(\''+selectedEvent['path']+selectedEvent['video']+'\',\''+imgSrc+'\');"/>';
			//}else{
				html+='<input type="button" value="Download" onclick="tabCameras['+Cid+'].generateVideo(\''+year+'\',\''+month+'\',\''+day+'\',\''+eventID+'\',\''+selectedEvent['path']+'\',\''+imgSrc+'\');"/>';
			//}
			if(navigateur=="chrome" || navigateur=="safari"){
				html+='<input type="button" onclick="tabCameras['+Cid+'].changeFilter();" value="Changer filtre"/>';
			}
			html+='</div>';
			html+='</font></td></tr></table>';
			html+='<div id="controlsContainer"><table width=500 cellspacing=0 cellpadding=0><tr><td width=20><div id="eventPlayBtn" onclick="tabCameras['+Cid+'].preloadEventFrames(\''+year+'\',\''+month+'\',\''+day+'\',\''+eventID+'\');return false"><img src="play.png" width=20 height=20/></div></td></tr></table></div></br>';
			$('#previewEventDiv').html(html);
			//tabCameras[Cid].changeFilter();
			//
		}
		this.showEventImageNum = function(num){
			num=parseInt(num);
			before=currentImage+1;
			currentImage=num;
			//showArray(imagesAnalyseList);
			checkVal=num+1;
			checkVal=checkVal.toString();
			imageNum=num+1;
			test=imagesAnalyseList.indexOf(checkVal);
			$('#previewEventImg').hide();
			$('#image-'+before).hide();
			$('#image-'+imageNum).show();
			if(test!=-1){
				$('#image-'+imageNum).css('border','3px solid red');
				$('#scoreInfos').html('Score: <font style="color:red">'+imagesScore[num]+'</font>');
				fontcolor='red';
			}else{
				$('#image-'+imageNum).css('border','3px solid white');
				$('#scoreInfos').html('Score: -');
				fontcolor='white';
			}
			$('#imagesCounter').html('<font style="color:'+fontcolor+'">'+(num+1)+'</font>/'+imagesList.length);
		}
		this.changeFilter = function(){
			total=filters.length;
			if(filters[currentFilter]!=''){
				$('#viewContainer img').each(function(){
					$(this).removeClass(filters[currentFilter]);	//on retire l'ancien effet
				});
			}
			if(currentFilter==(total-1)){
				currentFilter=0;
				filterName='aucun';
			}else{
				currentFilter++;
				if(filters[currentFilter]!=''){
					$('#viewContainer img').each(function(){
						$(this).addClass(filters[currentFilter]);	//on applique le nouvel effet
					});
					filterName=filters[currentFilter];
				}else{
					filterName='aucun';
				}
			}
			$('#filterNameDiv').html('Filtre: '+filterName);
		}
		
	//VIDEO
		this.generateVideo = function(year,month,day,eventID,path,poster){
			logThis('Création d\'une vidéo de la caméra: '+Cname+' pour l\'alerte '+eventID+' du '+day+'/'+month+'/20'+year,Cid);
			showLoader();
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "../zm/index.php",
				dataType:"json",
				data: "view=request&request=event&action=video&id="+eventID+"&videoFormat=mp4&rate=100&scale=100&overwrite=0",
				success: function (rep) {
					hideLoader();
					//alert(rep.result+"\n"+rep.response);
					CeventsTab.Events[year][month][day][eventID]['video']=rep.response;
					//tabCameras[Cid].launchVideo(path+rep.response,poster);
					path=path.substr(3,(path.length-3));
					//alert(path);
					window.location.href='videos/downloadVideo.php?filepath='+escape(path+rep.response);
				}
			});
		}
		this.launchVideo = function(videoFile,poster){
			html='<div id="videoWrapper" style="padding:10px;"></div>';
			html+='<script type="text/javascript">';
			html+='if(navigateur=="chrome" || navigateur=="msie" || navigateur=="safari"){ $("<video controls=\'controls\' autoplay=\'true\' src=\''+videoFile+'\' poster=\'loading-video.jpg\' width=500></video>").appendTo($("#videoWrapper")); }';
			html+='if(navigateur=="firefox" || navigateur=="opera"){';
			html+='var flashvars = { file:"'+videoFile+'",autostart:"true" };';
			html+='var params = { allowscriptaccess:"always",allowfullscreen:"true" };';
			html+='var attributes = { id:"videoPlayer", name:"videoPlayer" };';
			html+='swfobject.embedSWF("player.swf","videoWrapper","500","375","9.0.115","false",flashvars, params, attributes);';
			html+='}';
			html+='</script>';
			$('#viewContainer').html(html);
			control='<input type="button" value="Voir les photos" onclick="tabCameras['+Cid+'].viewEvent(\''+currentEvent[0]+'\',\''+currentEvent[1]+'\',\''+currentEvent[2]+'\',\''+currentEvent[3]+'\',\'force\');"/>';
			$('#controlsContainer').html('');
			$('#eventButtons').html(control);
		}
	
	//PRELOADER
		this.preloadEventFrames = function(year,month,day,eventID){
			$('#controlsContainer').html('');
			//recherche des images d'analyses
				if(CeventsTab.Stats[eventID]){
					imagesAnalyseList=CeventsTab.Stats[eventID]['frames'];
				}else{
					imagesAnalyseList=new Array();
				}
				currentImage=0;
			//prechargement des images
				liste=new Array();
				liste2=new Array();
				scores=new Array();
				selectedEvent=CeventsTab.Events[year][month][day][eventID];
				nbImages=parseInt(selectedEvent['Frames']);
				cnt=0;
				imagesPreload[eventID]=new Array();
				imagesPreloaded[eventID]=0;
				preloadCounter[eventID]=0;
				for(x=1;x<=nbImages;x++){
					imageNum=x.toString();
					if(imageNum.length==1){
						imageNum='00'+imageNum;
					}
					if(imageNum.length==2){
						imageNum='0'+imageNum;
					}
					checkVal=x.toString();
					test=imagesAnalyseList.indexOf(checkVal);
					if(test!=-1){
						liste2[cnt]=selectedEvent['path']+imageNum+'-analyse.jpg';
						index=CeventsTab.Stats[eventID]['frames'].indexOf(checkVal);
						scores[cnt]=CeventsTab.Stats[eventID]['score'][index];
					}
					preloadCounter[eventID]++;
					imagesPreload[eventID][x]=new Array();
					imagesPreload[eventID][x]['id']=x;
					if(test!=-1){
						imagesPreload[eventID][x]['src']=selectedEvent['path']+imageNum+'-analyse.jpg';
					}else{
						imagesPreload[eventID][x]['src']=selectedEvent['path']+imageNum+'-capture.jpg';
					}
					liste[cnt]=selectedEvent['path']+imageNum+'-capture.jpg';
					cnt++;
				}
				imagesList=liste;
				imagesList2=liste2;
				imagesScore=scores;
			//on lance le status du preloader
				tabCameras[Cid].showPreloader(eventID);
			//
		}
		this.updatePreloader = function(id){
			total=preloadCounter[id];
			if(preloading==false){	//1er passage = lancement du loader
				preloading=true;
				loadingIMG=new Array();
				for(x=1;x<=5;x++){
					loadingIMGnum=x;
					loadingIMG[x]=new Image();
					loadingIMG[x].src=imagesPreload[id][x]['src'];
					loadingIMG[x].id=imagesPreload[id][loadingIMGnum]['id'];
					loadingIMG[x].onload = function(){
						imagesPreloaded[id]++;
						$('#viewContainer').append('<img id="image-'+this.id+'" src="'+this.src+'" width=500 style="display:none"/>');
						tabCameras[Cid].preloadNextFrame(id);
					}
				}
			}else{
				if(imagesPreloaded[id]>=total){
					clearInterval(checkPreload);
					preloading=false;
					$('#preloadStatus').html('100%');
					setTimeout("tabCameras["+Cid+"].hidePreloader()",1000);
					//on affiche
					currentImage=0;
					selectedEvent=CeventsTab.Events[currentEvent[0]][currentEvent[1]][currentEvent[2]][currentEvent[3]];
					heure=selectedEvent['time']['heure']+':'+selectedEvent['time']['minute']+':'+selectedEvent['time']['seconde'];
					html='<table width=500 cellspacing=0 cellpadding=0><tr><td width=20><div id="eventPlayBtn" onclick="playEvent();return false"><img src="play.png" width=20 height=20/></div></td>';
					html+='<td width=420 id="slideWrap"><input type="range" style="width:420px" id="eventSlide" value="0" min="0" max="'+(imagesList.length-1)+'" onchange="tabCameras['+Cid+'].showEventImageNum(this.value);return false"/></td>';
					html+='<td width=60><div id="imagesCounter">&nbsp;</div></td></tr></table>';
					$('#controlsContainer').html(html);
					setTimeout("playEvent()",1500);
					//
					tabCameras[Cid].showEventImageNum(currentImage);
				}else{
					pourcent=Math.ceil((imagesPreloaded[id]*100)/total);
					$('#preloadStatus').html(pourcent+'%');
					//$('#preloadStatus').html(imagesPreloaded[id]+' images sur '+total);
				}
			}
		}
		this.preloadNextFrame = function(id){
			total=preloadCounter[id];
			if(loadingIMGnum!=total){
				loadingIMGnum++;
				loadingIMG[loadingIMGnum]=new Image();
				loadingIMG[loadingIMGnum].src=imagesPreload[id][loadingIMGnum]['src'];
				loadingIMG[loadingIMGnum].id=imagesPreload[id][loadingIMGnum]['id'];
				loadingIMG[loadingIMGnum].onload = function(){
					imagesPreloaded[id]++;
					$('#viewContainer').append('<img id="image-'+this.id+'" src="'+this.src+'" width=500 style="display:none"/>');
					tabCameras[Cid].preloadNextFrame(id);
				}
			}
		}
		this.showPreloader = function(id){
			$('#preloaderDiv').show();
			positionnePreloader();
			$('#preloaderDiv').animate({'opacity':0.8},{
				duration:200,
				queue:false,
				complete:function(){
					tabCameras[Cid].updatePreloader(id);
					checkPreload=setInterval("tabCameras["+Cid+"].updatePreloader("+id+")", 100);
				}
			});
		}
		this.hidePreloader = function(){
			$('#preloaderDiv').animate({'opacity':0},{
				duration:200,
				queue:false,
				complete:function(){
					$('#preloaderDiv').hide();
					$('#preloadStatus').html('Chargement des images...');
					imagesPreload=new Array();
					imagesPreloaded=new Array();
					preloadCounter=new Array();
					checkPreload=null;
					preloading=false;
					loadingIMG=null;
					loadingIMGnum=null;
				}
			});
		}
	
	//SNAPSHOT
		this.snapshot = function(){
			logThis('Photo de la caméra: '+Cname,Cid);
			showLoader();
			path=Cpath;
			posChar=path.indexOf('?');
			param=path.substr(posChar+1,(path.length-posChar-1));
			//FOSCAM 8918W
				url='http://'+Chost+':'+Cport+'/snapshot.cgi?'+param;
			//alert(url);
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "ajax.php",
				dataType:"json",
				data: "action=SNAPSHOT&name="+escape(Cname)+"&url="+escape(url),
				success: function (rep) {
					snapshotUrl=rep.snapshot['url'];
					snapshotName=rep.snapshot['filename'];
					hideLoader();
					html='<h1>'+snapshotName+'</h1><img src="close.gif" class="exitEvent" onclick="hideSnapshot()"/>';
					html+='<img src="'+snapshotUrl+'" width=640 style="margin-left:10px;margin-top:-15px;margin-bottom:5px;"></img><br>';
					html+='&nbsp;&nbsp;<a href="snapshots/download.php?filepath='+escape(snapshotUrl)+'" target="_blank"><img src="download.png"/></a>';
					$('#snapshotDiv').html(html);
					showSnapshot();
				}
			});
		}
	
	//ENABLE / DISABLE
		this.setEnabled = function(){
			showLoader();
			//on prepare les variables
			SQLtable='Monitors';
			SQLfields='Enabled';
			SQLsearchfield='Id';
			SQLsearchvalue=Cid;
			//on repurere l'etat du checkbox
			valEnable=document.getElementById('Cenabled').checked;
			if(valEnable) {	//si coché
				logThis('Activation de la caméra: '+Cname,Cid);
				SQLvalues=1;
				//on envoi la requete
				$.ajax({
					type: "POST",
					contentType: "application/x-www-form-urlencoded;charset=iso859-1",
					url: "ajax.php",
					async: false,
					dataType:"json",
					data: "action=MYSQL&COMMAND=UPDATE&BDD=1&TABLE="+escape(SQLtable)+"&FIELDS="+escape(SQLfields)+"&VALUES="+escape(SQLvalues)+"&SEARCHFIELD="+escape(SQLsearchfield)+"&SEARCHVALUE="+escape(SQLsearchvalue),
					success: function (rep) {
						if(rep.status=='SUCCESS'){
							Cenabled=1;
							this.Cenabled=Cenabled;
							//alert(rep.successMsg);
							$.ajax({
								type: "POST",
								contentType: "application/x-www-form-urlencoded;charset=iso859-1",
								url: "ajax.php",
								data: "action=CAM_RELOAD&id="+Cid,
								success: function (rep) {
									//alert(rep);
									tabCameras[Cid].getPanel('cameraPanel');
									tabCameras[Cid].linkRefresh();
									hideLoader();
								}
							});
						}else{
							alert("ERREUR !\n"+rep.errorMsg);
							tabCameras[Cid].getPanel('cameraPanel');
							tabCameras[Cid].linkRefresh();
						}
					}
				});
			}else{
				logThis('Désactivation de la caméra: '+Cname,Cid);
				SQLvalues=0;
				//on envoi la requete
				$.ajax({
					type: "POST",
					contentType: "application/x-www-form-urlencoded;charset=iso859-1",
					url: "ajax.php",
					async: false,
					dataType:"json",
					data: "action=MYSQL&COMMAND=UPDATE&BDD=1&TABLE="+SQLtable+"&FIELDS="+SQLfields+"&VALUES="+SQLvalues+"&SEARCHFIELD="+SQLsearchfield+"&SEARCHVALUE="+SQLsearchvalue,
					success: function (rep) {
						if(rep.status=='SUCCESS'){
							Cenabled=0;
							this.Cenabled=Cenabled;
							//alert(rep.successMsg);
							Clink='inactive.png';
							this.Clink=Clink;
							$.ajax({
								type: "POST",
								contentType: "application/x-www-form-urlencoded;charset=iso859-1",
								url: "ajax.php",
								async: false,
								data: "action=CAM_RELOAD&id="+Cid,
								success: function (rep) {
									//alert(rep);
									tabCameras[Cid].getPanel('cameraPanel');
									tabCameras[Cid].displayRefresh();
									hideLoader();
								}
							});
						}else{
							tabCameras[Cid].getPanel('cameraPanel');
							tabCameras[Cid].displayRefresh();
							alert("ERREUR !\n"+rep.errorMsg);
						}
					}
				});
			}
		}
	
	//CHANGE FUNCTION
		this.setFunction = function(){
			showLoader();
			//on prepare les variables
			SQLtable='Monitors';
			SQLfields='Function';
			SQLsearchfield='Id';
			SQLsearchvalue=Cid;
			//on repurere la valeur du select
			valFunction=$('#Cfunction').val();
			SQLvalues=valFunction;
			//log
			logThis('Passage en mode "'+valFunction+'" de la caméra: '+Cname,Cid);
			//on envoi la requete
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "ajax.php",
				async: false,
				dataType:"json",
				data: "action=MYSQL&COMMAND=UPDATE&BDD=1&TABLE="+escape(SQLtable)+"&FIELDS="+escape(SQLfields)+"&VALUES="+escape(SQLvalues)+"&SEARCHFIELD="+escape(SQLsearchfield)+"&SEARCHVALUE="+escape(SQLsearchvalue),
				success: function (rep) {
					if(rep.status=='SUCCESS'){
						Cfunction=valFunction;
						this.Cfunction=Cfunction;
						//alert(rep.successMsg);
						tabCameras[Cid].getPanel('cameraPanel');
						//
						valEnable=document.getElementById('Cenabled').checked;
						if(valEnable) {	//si coché
							enabled=1;
						}else{
							enabled=0;
						}
						$.ajax({
							type: "POST",
							contentType: "application/x-www-form-urlencoded;charset=iso859-1",
							url: "ajax.php",
							data: "action=CAM_RELOAD&id="+Cid,
							success: function (rep) {
								//alert(rep);
								hideLoader();
								tabCameras[Cid].refreshState();
							}
						});
					}else{
						alert("ERREUR !\n"+rep.errorMsg);
					}
				}
			});
		}
		this.getFunction = function(){
			return Cfunction;
		}
	
	//RELOAD
		this.reloadDaemon = function(){
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "ajax.php",
				data: "action=CAM_RELOAD&id="+Cid,
				success: function (rep) {
					//alert(rep);
				}
			});
		}
	
	//CONTROL
		this.control = function(cmd){
			$.ajax({
				type       : "GET",
				url        : "http://www.cloudsecuritycam.com/csc/mobileapp/www/controls/"+Ccontroldevice+".php",
				contentType: "application/json;charset=iso859-15",  
				dataType   : 'jsonp',  
				data       : {action: 'CONTROL', cmd: cmd, host: Chost, port: Cport, login: Clogin, pwd: Cpassword  },
				success    : function(rep) {
					//alert('Result: '+rep.result+"\nMessage: "+rep.message);
				}
			});
		}
	
	/*** FOSCAM ***/
	this.flipCamera = function(){
		if(Cpreset==FOSCAM_8918W_CONTROL_ID){
			mirrored=$('#mirrored').is(':checked');
			flipped=$('#flipped').is(':checked');
			state=0;
			if(mirrored){ state=state+2;}
			if(flipped){ state=state+1;}
			//log
			console.log('Changement de l\'orientation de la caméra: '+Cname+' en mode '+state);
			//
			url='http://'+Chost+':'+Cport+'/camera_control.cgi';
			params='param=5&value='+state+'&user='+Clogin+'&pwd='+Cpassword;
			$.ajax({
				type: "GET",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: url,
				data: params,
				success: function (rep) {
					//alert('Result: '+rep.result+"\nMessage: "+rep.message);
				}
			});
		}
	}
	this.getVideoParams = function(url){
		$("#contentbody").append('<script type="text/javascript" src="' + url + '"></script>');
		setTimeout('tabCameras['+Cid+'].showVideoParams()',500);
	}
	this.showVideoParams = function(){
		if(typeof flip != "undefined"){
			//alert("Resolution: "+resolution+"\nLuminosite: "+brightness+"\nContrast: "+contrast+"\nMode: "+mode+"\nFlip: "+flip+"\nFps: "+fps);
			//partie flip/mirror
				if(flip==1){
					$('#flipped').attr('checked','checked').checkboxradio('refresh');
				}
				if(flip==2){
					$('#mirrored').attr('checked','checked').checkboxradio('refresh');
				}
				if(flip==3){
					$('#mirrored').attr('checked','checked').checkboxradio('refresh');
					$('#flipped').attr('checked','checked').checkboxradio('refresh');
				}
			//partie luminosite contrast	
				/*
				$('#brightness').val(brightness);
				$('#brightnessVal').html(brightness);
				$('#contrast').val(contrast);
				$('#contrastVal').html(contrast);
				*/
		}else{
			setTimeout('tabCameras['+Cid+'].showVideoParams()',500);
		}
	}
	this.setBrightness = function(){
		brightness=$('#brightness').val();
		//log
		logThis('Changement de la luminosité de la caméra: '+Cname+' à la valeure de: '+brightness,Cid);
		//
		url='http://'+Chost+':'+Cport+'/camera_control.cgi';
		params='param=1&value='+brightness+'&'+Ccontroldevice;
		$.ajax({
			type: "GET",
			contentType: "application/x-www-form-urlencoded;charset=iso859-1",
			url: url,
			data: params,
			success: function (rep) {
				//alert('Result: '+rep.result+"\nMessage: "+rep.message);
			}
		});
	}
	this.setContrast = function(){
		contrast=$('#contrast').val();
		//log
		logThis('Changement du contrast de la caméra: '+Cname+' à la valeure de: '+contrast,Cid);
		//
		url='http://'+Chost+':'+Cport+'/camera_control.cgi';
		params='param=2&value='+contrast+'&'+Ccontroldevice;
		$.ajax({
			type: "GET",
			contentType: "application/x-www-form-urlencoded;charset=iso859-1",
			url: url,
			data: params,
			success: function (rep) {
				//alert('Result: '+rep.result+"\nMessage: "+rep.message);
			}
		});
	}
	/*** FIN FOSCAM ***/
	
	//fonctions utiles
	function monthName(mois){	//retourne le nom du mois a partir du nombre (ex: 02 --> Février)
		month=parseInt(mois);
		month=month-1;
		monthList=new Array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre');
		return monthList[month];
	}
	function nameToMonth(mois){ 	//retourne le numero du mois sur 2 chiffres en fonction de son nom
		mois=mois.toLowerCase();
		monthList=new Array('janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre');
		num=monthList.indexOf(mois);
		num++;
		num=num.toString();
		if(num.length=1){
			num='0'+num;
		}
		return num;
	}
}
/*** PARTIE SNAPSHOT ***/
	function showSnapshot(){
		overlayON();
		window.scrollTo(0,0);
		$('#snapshotDiv').show();
		$('#snapshotDiv').center();
		$('#snapshotDiv').animate({'opacity':1},{
				duration:200,
				queue:false,
				complete:function(){
					
				}
			});
	}
	function hideSnapshot(){
		$('#snapshotDiv').animate({'opacity':0},{
				duration:200,
				queue:false,
				complete:function(){
					$('#snapshotDiv').hide();
					overlayOFF();
				}
			});
	}
/*** FIN PARTIE SNAPSHOT ***/

function positionnePreloader(){
	var origin=$('#eventsDiv').position();
	originx=origin.left;
	originy=origin.top;
	//on recupere la position de repere
		var posDiv=$('#viewContainer').position();
		posx=posDiv.left;
		posy=posDiv.top;
	//
	newPosX=posx+originx+2;
	newPosY=posy+originy+2;
	//on place la div
		$('#preloaderDiv').css('left',newPosX+'px');
		$('#preloaderDiv').css('top',newPosY+'px');
		//alert(newPosX+','+newPosY);
}

function controlCamera(id,command){
	//log
		commandsList=new Array();
		commandsList['moveConUp']='UP';
		commandsList['moveConDown']='DOWN';
		commandsList['moveConLeft']='LEFT';
		commandsList['moveConRight']='RIGHT';
		commandsList['moveStop']='STOP MOVE';
		commandsList['wake']='IR ON';
		commandsList['sleep']='IR OFF';
		commandsList['presetGoto']='GOTO';
		commandsList['presetSet']='SAVETO';
		//
		txt='';
		if(command.substr(0,6)=='preset'){
			if(command.substr(0,9)=='presetSet'){
				num=command.substr(17,1);
				txt=commandsList['presetSet']+' PRESET '+num;
			}else{
				if(command.substr(0,10)=='presetGoto'){
					num=command.substr(10,1);
					txt=commandsList['presetGoto']+' PRESET '+num;
				}
			}
		}else{
			txt=commandsList[command];
		}
		//
		if(txt==null){
			txt=command;
		}
		//
		logThis('Envoi de la commande "'+txt+'" à la caméra: '+Cname,id);
	//
	$.ajax({
		type: "POST",
		contentType: "application/x-www-form-urlencoded;charset=iso859-1",
		url: "../zm/index.php",
		dataType:"json",
		data: "control="+command+"&id="+id+"&request=control&view=request",
		success: function (rep) {
			//alert('Result: '+rep.result+"\nMessage: "+rep.message);
		}
	});
}

function playEvent(){
	curVal=parseInt($('#eventSlide').val());
	minVal=parseInt($('#eventSlide').attr('min'));
	maxVal=parseInt($('#eventSlide').attr('max'));
	//
	if(playStatus=="play"){
		clearInterval(playInterval);
		playStatus="stop";
		$('#eventPlayBtn img').attr('src','play.png');
	}else{
		if(curVal!=maxVal){
			playInterval=setInterval("nextFrame()", 100);
			playStatus="play";
			$('#eventPlayBtn img').attr('src','pause.png');
		}
	}
}
function nextFrame(){
	curVal=parseInt($('#eventSlide').val());
	minVal=parseInt($('#eventSlide').attr('min'));
	maxVal=parseInt($('#eventSlide').attr('max'));
	//
	if(curVal!=maxVal){
		newVal=curVal+1;
		$('#eventSlide').val(newVal);
		tabCameras[Cid].showEventImageNum(newVal);
	}else{
		clearInterval(playInterval);
		playStatus="stop";
		$('#eventPlayBtn img').attr('src','play.png');
		$('#eventSlide').val(0);
		tabCameras[Cid].showEventImageNum(0);
		currentImage=0;
	}
}
function prevFrame(){
	curVal=parseInt($('#eventSlide').val());
	minVal=parseInt($('#eventSlide').attr('min'));
	maxVal=parseInt($('#eventSlide').attr('max'));
	//
	if(curVal!=minVal){
		newVal=curVal-1;
		$('#eventSlide').val(newVal);
		tabCameras[Cid].showEventImageNum(newVal);
	}
}