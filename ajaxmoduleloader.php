<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Override Joomla Views and Layouts
 *
 * @package     Plugin
 * @subpackage  System
 * @since       2.5
 */
class plgSystemAjaxmoduleloader extends JPlugin
{
	/**
	 * Name or short description
	 *
	 * Full description (multiline)
	 *
	 * @author Gruz <arygroup@gmail.com>
	 * @param	type	$name	Description
	 * @return	type			Description
	 */
	function onAfterRender() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) { return; }


		$buffer = JResponse::getBody();
		preg_match_all('~{ajaxmoduleloader\|([^}]*)}~Ui',$buffer,$matches);
		foreach ($matches[1] as $k=>$placeholder) {
			$periodical=null;
			$interval=null;
			$chrome = null;
			$position = null;
			$style = null;
			$starttime = null;
			$placeholder = explode ('|',$placeholder);

			foreach ($placeholder as $parameter) {
				$parameter = explode ('=',$parameter);
				switch ($parameter[0]) {
					case 'position':
						$position = $parameter[1];
						break;
					case 'chrome':
						$chrome = $parameter[1];
						break;
					case 'interval':
						$interval = 'id="'.$parameter[1].'"';
						break;
					case 'style':
						$style = 'style='.$parameter[1].'';
						break;
					case 'periodical':
						$periodical = 'periodical';
						break;
					case 'data-start-time':
						$starttime = 'data-start-time="'.$parameter[1].'"';
						break;
					default :

						break;
				}
			}
			$html = '<div rel="'.$position.'" alt="'.$chrome.'" class="delayedmodule '.$periodical.'" '.$interval.' '.$style.' '.$starttime.'><img src="plugins/system/ajaxmoduleloader/images/ajax-loader.gif" ></div>';

			$buffer = str_replace ($matches[0][$k],$html,$buffer);
		}

		JResponse::setBody($buffer);
	}


	 /**
	  * Name or short description
	  *
	  * Full description (multiline)
	  *
	  * @author Gruz <arygroup@gmail.com>
	  * @param	type	$name	Description
	  * @return	type			Description
	  */
	public function onBeforeRender() {

		$app = JFactory::getApplication();
		if ($app->isAdmin()) { return; }

		$tmpl = 	'ajaxmoduleloader';

		//Docs here: http://gruz.org.ua/en/about-joomla/25-faq/50-joomla-15-ta-17-jak-zavantazhuvaty-moduli-za-dopomogoju-ajax.html
		$jsframework = $this->params->get('use_jquery_or_mootools', 'jquery');
		$usejQuery = false;
		if ($jsframework == 'jquery') {
			$usejQuery = true;
		}
		if (!$usejQuery) {
			if (version_compare(JVERSION,'3.0','ge')) {
				JHtml::_('behavior.framework');

			}
			else {
				JHTML::_( 'behavior.mootools' );
			}
		}
		$loader_link = "plugins/system/ajaxmoduleloader/images/ajax-loader.gif";
		$document = JFactory::getDocument();
		if ($usejQuery)  {
			$ajaxfunction = "
					\$j.ajax({
						url: url+'&tto='+(new Date().getTime()),
						context: document.body,
						beforeSend: function () { element.innerHTML = '<img src=\"".$loader_link."\" >' },
						success: function(data){ element.innerHTML = data; },
					});
			";
		}
		else {
			 $ajaxfunction = "
								  var a = new Request.HTML({
										url: url+'&tto='+(new Date().getTime()),
										method: 'get',
										//start: element.innerHTML = '<img src=\"".$loader_link."\" >',
										onRequest: function() {element.set('html','<img src=\"".$loader_link."\" >');},
										update: element
								  }).send();
			 ";
		}

		if ($usejQuery)  {
			$scr ="
			//jQuery.noConflict();
			var \$j = jQuery;
			(function($){
			\$j(window).load(function() {
				//get elements to use ajax load
				var elements = \$j('.delayedmodule');
			";
		}
		else {
			$scr ="
			window.addEvent('load', function() {
				//get elements to use ajax load
				var elements = $$('.delayedmodule');
			";
		}
		$scr .= "
			 //loop the elements
			 for (var i=0; i<elements.length; i++) {
				//get info to fetch modules
				modulepos = elements[i].getAttribute('rel');
				modulestyle = elements[i].getAttribute('alt');
				timereload = elements[i].id;


				//set default time for periodical reload in miliseconds
				if (!timereload) {
					timereload = 5000;
				}
				if (modulepos != undefined) {
					if (modulestyle == undefined) {
						 modulestyle = '';
					}
					// the url is used to fecth the correct module

					//url = '".JURI::base()."?option=com_content&view=article&format=module&moduleposition='+modulepos+'&modulestyle='+modulestyle;
					url = '".JURI::base()."?tmpl=".$tmpl."&moduleposition='+modulepos+'&modulestyle='+modulestyle;
					//define loader function and pass current iteration element and url to it
					//defining the function in such a way is a MUST, because of iteration nature in JS
					//otherwise the ajax loading will be applied not to each loop element, but to the last one
					var loader = function (element, url){
						return  function () {
							".$ajaxfunction."
						}
					}(elements[i],url);
					var loaderR = function (element,url,timereload){
						return  function () {
						".$ajaxfunction."
							setInterval(function(){	".$ajaxfunction." },timereload);
						}
					}(elements[i],url,timereload);



					//use periodical reload or no
					if (typeof \$j !='undefined' && \$j == jQuery) {
						var hasClass =  $(elements[i]).hasClass('periodical');
					}
					else {
						var hasClass = elements[i].hasClass('periodical') ;
					}
					if (hasClass) {
						loader();


						starttimeEdges = elements[i].getAttribute('data-start-time');
						startTime = false;
						if (starttimeEdges) {
							starttimeEdges = starttimeEdges.split(';');
							var now = new Date();
							var edge = false;
							if (starttimeEdges.length == 1) {
								edge = starttimeEdges[0];

							}
							else {
								for (var g=0; g<starttimeEdges.length; g++) {
									edge = starttimeEdges[g];
									if (now.getMinutes() == starttimeEdges[g]) {
										break;
									}
									if (now.getMinutes() < starttimeEdges[g]) {
										break;
									}
									if (now.getMinutes() > starttimeEdges[g]) {
										edge = starttimeEdges[0];
									}
								}
							}
							var now = new Date();
							if (edge == 0) {
								if (now.getMinutes() != 0) {
									var startTime = new Date(now.getFullYear(),now.getMonth(),now.getDate(),now.getHours()+1,0,0);
								}
							}
							else if(now.getMinutes() < edge) {
								var startTime = new Date(now.getFullYear(),now.getMonth(),now.getDate(),now.getHours(),edge,0);
							}
							else if(now.getMinutes() > edge) {
								var startTime = new Date(now.getFullYear(),now.getMonth(),now.getDate(),now.getHours()+1,edge,0);
							}
						}
						var now = new Date();
						if (startTime) {
							var timeOutMiliSec = startTime-now;
						}
						else {
							timeOutMiliSec = 1;
						}
						setTimeout(loaderR,timeOutMiliSec);
						//setInterval(loader,timereload);
					}
					else {
						loader();
					}
				}
			 }
		});
		";
		if ($usejQuery)  {
			$scr .= "})(jQuery);    //Passing the jQuery object as a first argument";
		}
		$document->addScriptDeclaration($scr);

	 }

}
