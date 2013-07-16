		$( document ).bind( "mobileinit", function() {  
				// Make your jQuery Mobile framework configuration changes here!  
				$.mobile.allowCrossDomainPages = true;
				$.mobile.defaultPageTransition = "slide";
				$.event.special.tap.tapholdThreshold=100;
		});
		
		function onLoad(){
			document.addEventListener("deviceready", onDeviceReady, false);
		}
		
		function onDeviceReady() {
			// Register the event listener
			document.addEventListener("backbutton", function() { showConfirm();	}, false);
		}
		
		function showConfirm() {
      	  navigator.notification.confirm(
            "Fermer l'application ?",  // message
            onConfirm,              // callback to invoke with index of button pressed
            'CloudSecurityCam',            // title
            'Oui,Non'          // buttonLabels
     	   );
   		 }
		
		function onConfirm(buttonIndex) {
   		     if(buttonIndex==1){
				txt='OUI';
			 }
			 if(buttonIndex==2){
				txt='NON';
			 }
			 alert('Bouton: ' + txt);
  		}
		
		var play='off';
		var ctx=null;
		
		var mobile=true;
		var userTab=new Array();
		var tabCameras=new Array();
		var tabDisplayedCameras=new Array();
		var nbCameras=0;
		var currentCamera=new Array();
		
		var currentID=0;
		var userAgent = navigator.userAgent.toLowerCase();
		//
		var version='0';
		var navigateur='';
		//
		version=(userAgent.match( /.+(?:rv|it|ra|ie|me)[\/: ]([\d.]+)/ ) || [])[1];
		if(/mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )){
			navigateur='firefox';
		}
		if(/msie/.test( userAgent ) && !/opera/.test( userAgent )){
			navigateur='msie';
		}
		if(/webkit/.test( userAgent ) && !/chrome/.test( userAgent )){
			navigateur='safari';
		}
		if(/chrome/.test( userAgent )){
			navigateur='chrome';
		}
		if(/opera/.test( userAgent )){
			navigateur='opera';
		}
		//
		function next(Idnext) {
			var Next = $("#"+Idnext);
			if(window.event.keyCode == '13')
			Next.focus();
		} 
		function checkLogin(){
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "ajax.php",
				data: "action=CHECK_LOGIN",
				success: function (rep) {
					if(rep=='OK'){
						$.mobile.changePage( "cameras.html", { transition: "slideup"} );
						//getUserInfos();
						//loadCameras();
					}
				}
			})
		}
		function logout(){
			$.ajax({
				type       : "GET",
				url        : "http://www.cloudsecuritycam.com/csc/mobileapp/ajax.php",
				contentType: "application/json;charset=iso859-15",  
				dataType   : 'jsonp',  
				data       : {action: 'LOGOUT'},
				success    : function(rep) {
					location.href='index.html';
				}
			});
		}
		
		function getUserInfos(){
			//showLoader('Récupération des infos utilisateurs...');
			$.ajax({
				type       : "GET",
				url        : "http://www.cloudsecuritycam.com/csc/mobileapp/ajax.php",
				contentType: "application/json;charset=iso859-15",  
				dataType   : 'jsonp',  
				data       : {action: 'GET_USER_INFO'},
				success    : function(rep) {
						$('#user_nom').html('<i class="icon-user icon-large" style="color:#FFF !important"></i>&nbsp;&nbsp;'+rep.user['nom'].toUpperCase());
						$('#user_formule').html('<i class="icon-star icon-large" style="color:#FFF !important"></i>&nbsp;&nbsp;'+rep.user['formule'].toUpperCase());
						userTab['id']=rep.user['id'];
						userTab['nom']=rep.user['nom'];
						userTab['prenom']=rep.user['prenom'];
						userTab['email']=rep.user['email'];
						userTab['formule']=rep.user['formule'];
						userTab['pays']=rep.user['pays'];
						userTab['langue']=rep.user['langue'];
						userTab['cameraslimit']=rep.user['cameraslimit'];
						userTab['storagelimit']=rep.user['storagelimit'];
						userTab['fpslimit']=rep.user['fpslimit'];
						userTab['retentionlimit']=rep.user['retentionlimit'];
						userTab['downloadenable']=rep.user['downloadenable'];
						userTab['motiondetectenable']=rep.user['motiondetectenable'];
						userTab['mailalert']=rep.user['mailalert'];
						userTab['ftpaccess']=rep.user['ftpaccess'];
						userTab['price']=rep.user['price'];
						//taille disque
							units='Mo';
							StorageLimit=parseInt(rep.user['storagelimit']);
							if(StorageLimit>=1024){
								StorageLimit=Math.ceil(StorageLimit/1024);
								units='Go';
							}
							userTab['storagelimit2']=StorageLimit+units;
				},
				error      : function() {
					//console.error("error");
					$('#statusdiv').html('Impossible de contacter le serveur !');
					$('#statusdiv').toast('show');							
				}
			});
			return false;
		}
		function loadCameras(){
			//showLoader('Récupération des caméras...');
			//$('#footerDiv').addClass("blink");
			$('#footerDiv').html('<br><center><table><tr><td align=center valign=center width=35><img src="glyphish-icons/55-network.png"></img></td><td align=center valign=center><div id="footerTxt">Chargement en cours...</div></td></tr></table></center></br>');
			$('#camerasListe').empty().listview("refresh");
			$('#camerasListe2').empty().listview("refresh");
			$.ajax({
				type       : "GET",
				url        : "http://www.cloudsecuritycam.com/csc/mobileapp/ajax.php",
				contentType: "application/json;charset=iso859-15",  
				dataType   : 'jsonp',  
				data       : {action: 'LOAD_CAMERAS'},
				success    : function(rep) {
					setTimeout('$(\'#footerDiv\').html(\'<br><center><table><tr><td align=center valign=center width=35><img src="glyphish-icons/01-refresh.png"></img></td><td align=center valign=center><div id="footerTxt">Rafraichir la liste</div></td></tr></table></center></br>\');',500);
					//$('#footerDiv').removeClass("blink");
					if(rep.status=='ERROR'){
						alert('Status: '+rep.status+"\nMessage: "+rep.errorMsg);
					}else{
						cnt=0;
						for(var i in rep.camera){
							Cname=rep.camera[i]['name'];
							Cfunction=rep.camera[i]['function'];
							Cprotocol=rep.camera[i]['protocol'];
							Ccontrollable=rep.camera[i]['controllable'];
							Cwidth=rep.camera[i]['width'];
							Cheight=rep.camera[i]['height'];
							Chost=rep.camera[i]['host'];
							Cport=rep.camera[i]['port'];
							Cpath=rep.camera[i]['path'];
							Cpath2=rep.camera[i]['path2'];
							Clogin=rep.camera[i]['login'];
							Cpassword=rep.camera[i]['password'];
							Ccontroldevice=rep.camera[i]['controldevice'];
							Cdevicepicture=rep.camera[i]['devicepicture'];
							Cid=rep.camera[i]['id'];
							Cevents=rep.camera[i]['events'];
							Cpreset=rep.camera[i]['preset'];
							CalertesSupport=rep.camera[i]['alertesSupport'];
							CalertesPlaning=rep.camera[i]['alertesPlaning'];
							//on incremente le compteur
								cnt++;
							//on creer l'objet
								tabCameras[Cid]=new Camera(Cid,Cname,Cfunction,Clogin,Cpassword,Cprotocol,Ccontrollable,Ccontroldevice,Cdevicepicture,Cwidth,Cheight,Chost,Cport,Cpath,Cpath2,Cpreset,Cevents,CalertesSupport,CalertesPlaning);
								//alert('Camera: '+Cname+' créé');
						}
						nbCameras=cnt;
						//on tri les actif et les non actifs
							enableTab=new Array();
							disableTab=new Array();
							enableCnt=0;
							disableCnt=0;
							for(var key in tabCameras){
								if(tabCameras[key].getFunction()=='disable'){
									disableTab[disableCnt]=key;
									disableCnt++;
								}else{
									enableTab[enableCnt]=key;
									enableCnt++;
								}
								//console.log('Camera id: '+key+' - '+tabCameras[key].getOnlineStatus());
							}
						//on lance l'affichage des cameras
							for(var key in enableTab){
								tabCameras[enableTab[key]].create('camerasListe');
							}
							for(var key in disableTab){
								tabCameras[disableTab[key]].create('camerasListe2');
							}
						//
						if(nbCameras==0){
							$('#camerasListe').html('<center>Aucune caméras');
						}
					}
				}
			});
			return false;
		}
	
		function goState(){
			showLoader('Changement d\'état en cours ...');
			state=$('#runState').val();
			datas='view=none&action=state&runState='+state;
			$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded;charset=iso859-1",
				url: "../zm/index.php",
				data: datas,
				success: function (rep) {
					hideLoader();
					$( "#popupRunstate" ).popup( "close" );
				}
			});
		}
		
		function include(fileName){
			document.write("<script type='text/javascript' src='"+fileName+"'></script>" );
		}
		function toast(popupdiv,msg,duree){
			$('#'+popupdiv).html(msg);
			$('#'+popupdiv).popup("open");
			setTimeout("$('#"+popupdiv+"').popup('close');",duree);
		}
		function showLoader(msg){
			$.mobile.loading( 'show', {
				text: msg,
				textVisible: true,
				theme: 'a',
				html: ''
			});
		}
		function hideLoader(){
			$.mobile.loading( 'hide' );
		}
		function checkScreen(){
			return false;
			if($(window).width()<$(window).height()){
				//portrait
				//$("#viewport").attr("content","width=device-width; initial-scale=0.7; maximum-scale=1.0; user-scalable=yes;");
				$('#fullview').css('width','100%');
				$('#fullview').css('height','auto');
			}else{
				//paysage
				$('#fullview').css('height',$(window).height()-80);
				$('#fullview').css('width','auto');
			}
		}
		function clearMenu(){
			$('#navbar li a').each(function() {	
				$(this).removeClass('ui-btn-active');
			});
		}
		function viewInfos(){
			setTimeout("$('#popupInfos').popup('open')",500);
		}
		
		window.onorientationchange=function(){	//detection du mode paysage
			wheight = $(window).height();
            changepush();
			checkScreen();
		}
		
		jQuery.fn.extend({
			findPos: function () {
				obj = jQuery(this).get(0);
				var a = obj.offsetLeft || 0;
				var b = obj.offsetTop || 0;
				while (obj = obj.offsetParent) {
					a += obj.offsetLeft;
					b += obj.offsetTop
				}
				return {
					x: a,
					y: b
				}
			}
		});
		jQuery.fn.center = function () {
			this.css("position", "fixed");
			this.css("top", ($(window).height() - this.height()) / 2 + "px");
			this.css("left", ($(window).width() - this.width()) / 2 + "px");
			return this
		}
		
		//CAMERAS PAGE
		$( '#camlistPage' ).live( 'pageshow',function(event){
			console.log("camlistPage - stop flux - getuserinfos - loadcameras");
			//$('#footerDiv').removeClass("blink");
			window.stop();
			play="off";
			currentCamera=new Array();
			getUserInfos();
			loadCameras();
		});
		function refreshCamerasListe(){
			getUserInfos();
			loadCameras();
		}
		
		//VIEW PAGE
		$( '#viewPage' ).live( 'pageshow',function(event){
			//alert(currentCamera['link']);
			console.log("viewPage - start flux - showpanel");
			//$('#fullview').attr('src',currentCamera['link']);
			$('#camName').html(currentCamera['name'].toUpperCase());
            wheight = $(window).height();
			changepush();
			//checkScreen();
			play='on';
			motion(currentCamera['link']);
			showPanel();
		});
		
		$( '#viewPage' ).live( 'pagecreate',function(event){
		  ctx = document.getElementById('fullview').getContext('2d');
		  tabCameras[currentCamera['id']].getPanel('panel');
		  //
			$( "#viewPage" ).on( 'swiperight', swiperightHandler );
			// Callback 
			function swiperightHandler( event ) {
				console.log('Swipe = back');
				$.mobile.changePage( "cameras.html", { transition: "slide", reverse: true} );
			}
		  //PANEL
			$( "#panel" ).on( 'swipedown', swipedownHandler );
			// Callback 
			function swipedownHandler( event ) {
				console.log('Panel swipe');
				tooglePanel();
			}
		});
		function motion(flux){
			if(play=="on"){
				randomNum=Date.now();
				tmpUrl=flux+'&time='+randomNum;
				tmpIMG=new Image();
				tmpIMG.src=tmpUrl;
				console.log("load image: ("+flux+")");
				tmpIMG.onload= function(){
					console.log("image loaded: ("+tmpIMG.src+")");
					if(play=="on"){
						ratio=this.width/this.height;
						largeur=Math.ceil(window.innerWidth-(window.innerWidth/10));	//largeur ecran -10%
						hauteur=Math.ceil(largeur/ratio);
						$('#fullview').attr('width',largeur);
						$('#fullview').attr('height',hauteur);
						//$('#fullview').attr('src',tmpUrl);
						console.log('source: '+this.width+'x'+this.height+' - destination: '+largeur+'x'+hauteur);
						ctx.drawImage(tmpIMG, 0, 0, this.width, this.height, 0, 0, largeur, hauteur);
						setTimeout("motion('"+flux+"')",1000);
						//motion();
					}
				};
				tmpIMG.onerror=function(){
					console.log("load error: ("+tmpIMG.src+")");
					if(play=="on"){
						setTimeout("motion('"+flux+"')",1000);
					}
				};
			}
		}
		
		//LOGIN PAGE
		$( '#loginPage' ).live( 'pageshow',function(event){
			var value1 = window.localStorage.getItem("CSC-LOGIN");
			var value2 = window.localStorage.getItem("CSC-PWD");
			//alert(value1+' , '+value2);
			if(value1){
				$('#username').val(value1);
				$('#password').val(value2);
			}
			$('#loginForm').submit(function() {
				$('#loginOK').hide();
				$('#loginBAD').hide();
				$('#loginLOAD').show();
				//$('#output').toast('show');
						$.ajax({
							type       : "GET",
							url        : "http://www.cloudsecuritycam.com/csc/mobileapp/ajax.php",
							contentType: "application/json;charset=iso859-15",  
							dataType   : 'jsonp',  
							data       : {action: 'LOGIN', login: escape($('#username').val()), password: escape($('#password').val())},
							success    : function(response) {
								$('#loginLOAD').hide();
								if(response.result=='OK'){
									$('#loginOK').show();
									window.localStorage.setItem("CSC-LOGIN", $('#username').val());
									window.localStorage.setItem("CSC-PWD", $('#password').val());
									//$('#output').html('Connexion réussie !');
									//$('#output').toast('show');
									setTimeout('$.mobile.changePage( "cameras.html", { transition: "slideup"} );',1000);
								}else{
									//$('#output').html('Connexion échouée !');
									//$('#output').toast('show');
									$('#loginBAD').show();
								}
							},
							error      : function() {
								//console.error("error");
								$('#output').html('Impossible de contacter le serveur !');
								$('#output').toast('show');							
							}
						});
						return false;
				
			});
		});
		
		function tooglePanel(){
			if($('#panel').css('opacity')==1){
				hidePanel();
			}else{
				showPanel();
			}
		}
		function showPanel(){
			$('#panel').show();
			$('#panel').animate({opacity:1},{
				duration:200,
				queue:false,
				complete:function(){
					
				}
			});
		}
		function hidePanel(){
			$('#panel').animate({opacity:0},{
				duration:200,
				queue:false,
				complete:function(){
					$('#panel').hide();
				}
			});
		}
       
        var wheight = 0;
        $(window).resize(function(){
            wheight = $(window).height();
            changepush();
			//checkScreen();
        });

        function changepush(){
            $('#contentbody').height(wheight-80);
        }
		
		function controlCamera(id,cmd){
			tabCameras[id].control(cmd);
		}
			
		function mydump(arr,level) {
			var dumped_text = "";
			if(!level) level = 0;

			var level_padding = "";
			for(var j=0;j<level+1;j++) level_padding += "    ";

			if(typeof(arr) == 'object') {  
				for(var item in arr) {
					var value = arr[item];

					if(typeof(value) == 'object') { 
						dumped_text += level_padding + "'" + item + "' ...\n";
						dumped_text += mydump(value,level+1);
					} else {
						dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
					}
				}
			} else { 
				dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
			}
			return dumped_text;
		}

		function exitApp(){
			if(navigator.app){
				console.log('navigator.app.exitApp');
      			navigator.app.exitApp();
			}else if(navigator.device){
				console.log('navigator.device.exitApp');
    		    navigator.device.exitApp();
			}
		};
		
		// SWIPEUP/DOWN
	(function() {
// initializes touch and scroll events
        var supportTouch = $.support.touch,
                scrollEvent = "touchmove scroll",
                touchStartEvent = supportTouch ? "touchstart" : "mousedown",
                touchStopEvent = supportTouch ? "touchend" : "mouseup",
                touchMoveEvent = supportTouch ? "touchmove" : "mousemove";

 // handles swipeup and swipedown
        $.event.special.swipeupdown = {
            setup: function() {
                var thisObject = this;
                var $this = $(thisObject);

                $this.bind(touchStartEvent, function(event) {
                    var data = event.originalEvent.touches ?
                            event.originalEvent.touches[ 0 ] :
                            event,
                            start = {
                                time: (new Date).getTime(),
                                coords: [ data.pageX, data.pageY ],
                                origin: $(event.target)
                            },
                            stop;

                    function moveHandler(event) {
                        if (!start) {
                            return;
                        }

                        var data = event.originalEvent.touches ?
                                event.originalEvent.touches[ 0 ] :
                                event;
                        stop = {
                            time: (new Date).getTime(),
                            coords: [ data.pageX, data.pageY ]
                        };

                        // prevent scrolling
                        if (Math.abs(start.coords[1] - stop.coords[1]) > 10) {
                            event.preventDefault();
                        }
                    }

                    $this
                            .bind(touchMoveEvent, moveHandler)
                            .one(touchStopEvent, function(event) {
                        $this.unbind(touchMoveEvent, moveHandler);
                        if (start && stop) {
                            if (stop.time - start.time < 1000 &&
                                    Math.abs(start.coords[1] - stop.coords[1]) > 30 &&
                                    Math.abs(start.coords[0] - stop.coords[0]) < 75) {
                                start.origin
                                        .trigger("swipeupdown")
                                        .trigger(start.coords[1] > stop.coords[1] ? "swipeup" : "swipedown");
                            }
                        }
                        start = stop = undefined;
                    });
                });
            }
        };

//Adds the events to the jQuery events special collection
        $.each({
            swipedown: "swipeupdown",
            swipeup: "swipeupdown"
        }, function(event, sourceEvent){
            $.event.special[event] = {
                setup: function(){
                    $(this).bind(sourceEvent, $.noop);
                }
            };
        });

    })();