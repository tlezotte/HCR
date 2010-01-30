<?php
	$hr_role = ($_SESSION['hcr_groups'] == 'hr') ? true : false;
	$access1 = ($_SESSION['hcr_access'] == 1) ? true : false;
	$access2 = ($_SESSION['hcr_access'] == 2) ? true : false;
	$access3 = ($_SESSION['hcr_access'] == 3) ? true : false;
?>
<link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/reset-fonts-grids/reset-fonts-grids.css" /> 		<!-- CSS Grid -->
<script src="/Common/Javascript/yahoo/yahoo-dom-event/yahoo-dom-event.js" type="text/javascript"></script>
<script src="/Common/Javascript/yahoo/animation/animation-min.js" type="text/javascript"></script>
<script src="/Common/Javascript/yahoo/container/container_core-min.js" type="text/javascript"></script>
<script src="/Common/Javascript/yahoo/menu/menu-min.js" type="text/javascript"></script>

<script type="text/javascript">
	// Initialize and render the menu bar when it is available in the DOM
	YAHOO.util.Event.onContentReady("mainmenu", function () {
		// Animation object
		var oAnim;

		// "beforeshow" event handler for each submenu of the menu bar
		function onMenuBeforeShow(p_sType, p_sArgs) {

			var oBody, oShadow, oUL;
		
			if (this.parent) {
				oShadow = this.element.lastChild;
				oShadow.style.height = "0px";
			
				if (oAnim && oAnim.isAnimated()) {                        
					oAnim.stop();
					oAnim = null;                       
				}

				oBody = this.body;
				oUL = oBody.getElementsByTagName("ul")[0];

				YAHOO.util.Dom.setStyle(oBody, "overflow", "hidden");
				YAHOO.util.Dom.setStyle(oUL, "marginTop", ("-" + oUL.offsetHeight + "px"));                    
			}
		}

		function onTween(p_sType, p_aArgs, p_oShadow) {
			if (this.cfg.getProperty("iframe")) {                    
				this.syncIframe();                
			}                
			if (p_oShadow) {                
				p_oShadow.style.height = this.element.offsetHeight + "px";                    
			}                
		}

		function onAnimationComplete(p_sType, p_aArgs, p_oShadow) {
			var oBody = this.body,
				oUL = oBody.getElementsByTagName("ul")[0];

			if (p_oShadow) {                    
				p_oShadow.style.height = this.element.offsetHeight + "px";                   
			}

			YAHOO.util.Dom.setStyle(oUL, "marginTop", "auto");
			YAHOO.util.Dom.setStyle(oBody, "overflow", "visible");
			
			if (YAHOO.env.ua.ie) {                    
				YAHOO.util.Dom.setStyle(oBody, "zoom", "1");                    
			}                    
		}

		// "show" event handler for each submenu of the menu bar
		function onMenuShow(p_sType, p_sArgs) {
			var oElement,
				oShadow,
				oUL;
		
			if (this.parent) {
				oElement = this.element;
				oShadow = oElement.lastChild;
				oUL = this.body.getElementsByTagName("ul")[0];
			
				oAnim = new YAHOO.util.Anim(oUL, 
					{ marginTop: { to: 0 } },
					.5, YAHOO.util.Easing.easeOut);

				oAnim.onStart.subscribe(function () {        
					oShadow.style.height = "100%";                       
				});

				oAnim.animate();

				/*
					 Refire the event handler for the "iframe" 
					 configuration property with each tween so that the  
					 size and position of the iframe shim remain in sync 
					 with the menu.
				*/   
				if (YAHOO.env.ua.ie) {                            
					oShadow.style.height = oElement.offsetHeight + "px";
					oAnim.onTween.subscribe(onTween, oShadow, this);    
				}    
				oAnim.onComplete.subscribe(onAnimationComplete, oShadow, this);                   
			}                
		}

		// "beforerender" event handler for the menu bar
		function onMenuBeforeRender(p_sType, p_sArgs) {
			var oSubmenuData = {                    
				"Open Position": [                        
					{ text: "New Position", url: "<?= $default['url_home']; ?>/Requests/index.php" },
					{ text: "My Open Positions", url: "<?= $default['url_home']; ?>/Requests/list.php?action=my&access=0" },
					<?php if ($hr_role) { ?>
					{ text: "All Open Positions", url: "<?= $default['url_home']; ?>/Requests/list.php" }
					<?php } ?>           
				],
				<?php if ($hr_role) { ?>
				"Wage Adjustment": [   
					{ text: "New Wage Adjustment", url: "<?= $default['url_home']; ?>/Requests/_index.php?type=adjustment" },
					{ text: "My Wage Adjustments", url: "<?= $default['url_home']; ?>/Requests/list.php?type=adjustment&status=N&my=true" },
					{ text: "All Wage Adjustments", url: "<?= $default['url_home']; ?>/Requests/list.php?type=adjustment&status=N" }  
				],                        
				"Transfer": [  
					{ text: "New Transfer", url: "<?= $default['url_home']; ?>/Requests/_index.php?type=transfer" },
					{ text: "My Transfers", url: "<?= $default['url_home']; ?>/Requests/list.php?type=transfer&status=N&my=true" },
					{ text: "All Transfers", url: "<?= $default['url_home']; ?>/Requests/list.php?type=transfer&status=N" }
				], 
				"Conversion": [  
					{ text: "New Conversion", url: "<?= $default['url_home']; ?>/Requests/_index.php?type=conversion" },
					{ text: "My Conversions", url: "<?= $default['url_home']; ?>/Requests/list.php?type=conversion&status=N&my=true" },
					{ text: "All Conversions", url: "<?= $default['url_home']; ?>/Requests/list.php?type=conversion&status=N" }
				],  
				"Promotion": [  
					{ text: "New Promotion", url: "<?= $default['url_home']; ?>/Requests/_index.php?type=promotion" },
					{ text: "My Promotions", url: "<?= $default['url_home']; ?>/Requests/list.php?type=promotion&status=N&my=true" },
					{ text: "All Promotions", url: "<?= $default['url_home']; ?>/Requests/list.php?type=promotion&status=N" }
				],
				"All": [  
					{ text: "My HCR", url: "<?= $default['url_home']; ?>/Requests/list.php?type=all&status=N&my=true" },
					{ text: "All HCR", url: "<?= $default['url_home']; ?>/Requests/list.php?type=all&status=N" }
				],				
				<?php } ?>											                      
				"Administration": [ 
					<?php if ($access1 OR $access2 OR $access3) { ?> 
					{ text: "Information", url: "<?= $default['url_home']; ?>/Administration/user_information.php" },
					{ text: "Change Password", url: "<?= $default['url_home']; ?>/Administration/user_information.php#password" },
					{ text: "--------------------------", url: "<?= $default['url_home']; ?>/Administration/index.php" },					
					{ text: "Home", url: "<?= $default['url_home']; ?>/Administration/index.php" },
					{ text: "Users", url: "<?= $default['url_home']; ?>/Administration/users.php" },
					{ text: "Settings", url: "<?= $default['url_home']; ?>/Administration/settings.php" },
					{ text: "Databases", submenu: { id: "databases", itemdata: [

							{ text: "Position Title", url: "<?= $default['url_home']; ?>/Administration/db/positionTitle.php" },
							{ text: "Contract Agency", url: "<?= $default['url_home']; ?>/Administration/db/contractAgency.php" },
							{ text: "Verbiage", url: "<?= $default['url_home']; ?>/Administration/dbRequests.php" }

						] }
					}, 
					{ text: "Utilities", submenu: { id: "utilities", itemdata: [

							{ text: "Notify Users by Webs", url: "<?= $default['url_home']; ?>/Administration/notify_web.php" },
							{ text: "Send Test Email", url: "<?= $default['url_home']; ?>/Administration/testemail.php" },
							{ text: "Usage Summary", url: "<?= $default['url_home']; ?>/Administration/summary.php" },
							{ text: "Comments", url: "<?= $default['url_home']; ?>/Administration/comments.php" },
							{ text: "Conversion", url: "<?= $default['url_home']; ?>/Administration/conversion.php" },
							{ text: "Update Calendar", url: "<?= $default['url_home']; ?>/Administration/updateCalendar.php" },
							{ text: "Update RSS", url: "<?= $default['url_home']; ?>/Administration/updateRSS.php" }

						] }
					}					
					<?php } else { ?>     
					{ text: "Information", url: "<?= $default['url_home']; ?>/Administration/user_information.php" },
					{ text: "Change Password", url: "<?= $default['url_home']; ?>/Administration/user_information.php#password" }
					<?php } ?>							                
				]					          
			};

			// Add a submenu to each of the menu items in the menu bar
			this.getItem(0).cfg.setProperty("submenu", { id: "position", itemdata: oSubmenuData["Open Position"] });
			<?php if ($hr_role) { ?>
			this.getItem(1).cfg.setProperty("submenu", { id: "adjustment", itemdata: oSubmenuData["Wage Adjustment"] });
			this.getItem(2).cfg.setProperty("submenu", { id: "transfer", itemdata: oSubmenuData["Transfer"] });
			this.getItem(3).cfg.setProperty("submenu", { id: "conversion", itemdata: oSubmenuData["Conversion"] });
			this.getItem(4).cfg.setProperty("submenu", { id: "promotion", itemdata: oSubmenuData["Promotion"] });
			this.getItem(5).cfg.setProperty("submenu", { id: "all", itemdata: oSubmenuData["All"] });
			this.getItem(6).cfg.setProperty("submenu", { id: "administration", itemdata: oSubmenuData["Administration"] });
			<?php } else { ?>
			this.getItem(1).cfg.setProperty("submenu", { id: "administration", itemdata: oSubmenuData["Administration"] });
			<?php } ?>
			
			this.subscribe("beforeShow", onMenuBeforeShow);
			this.subscribe("show", onMenuShow);
		}

		// Initialize the root menu bar
		var oMenuBar = new YAHOO.widget.MenuBar("mainmenu", { autosubmenudisplay: true, hidedelay: 750, lazyload: true });

		// Subscribe to the "beforerender" event
		oMenuBar.beforeRenderEvent.subscribe(onMenuBeforeRender);

		/*
			 Call the "render" method with no arguments since the markup for 
			 this menu already exists in the DOM.
		*/
		oMenuBar.render();                       
	});
</script>  
<?php 
//include($default['FS_HOME'].'/Administration/include/detail.php');
//include($default['FS_HOME'].'/Administration/include/sql_debug.php'); 
?>