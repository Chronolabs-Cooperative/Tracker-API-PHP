<?php
	header('Origin: *');
	header('Access-Control-Allow-Origin: *');
	header("Content-type: text/css");
	?>/**
 * Variables for $_GET if specified
 *
 * $_GET['weights'] = "000||111||010" ~ Minimum on Range||Maximum on Range||Has to mt_rant() Greater than for CSS Update 
 * $_GET['nodes'] = "html||h1, h2, h3, h4, h5||input||textarea||select" ~ Css Object, ID and HTML Tag classes to define individual shadows 
 * $_GET['elements'] = "box||text" ~ Shadow types only box or text or both
 * $_GET['reds'] = "21||198" ~ Range for mt_rand() of Minimal Red in RGB and Maximum
 * $_GET['greens'] = "31||168" ~ Range for mt_rand() of Minimal Green in RGB and Maximum
 * $_GET['blues'] = "42||238" ~ Range for mt_rand() of Minimal Blues in RGB and Maximum
 * $_GET['ranges'] = "0||3" ~ Range for mt_rand() of Minimal Shadow Depth in pixels and Maximum --- Across the board not 1st, 2nd etc. Setting
 * $_GET['firsts'] = "0||3" ~ Range for mt_rand() of Minimal Shadow First Basis Measuring Depth in pixels and Maximum 
 * $_GET['seconds'] = "0||3" ~ Range for mt_rand() of Minimal Shadow Seconds Basis Measuring Depth in pixels and Maximum 
 * $_GET['thirds'] = "0||3" ~ Range for mt_rand() of Minimal Shadow Third Basis Measuring Depth in pixels and Maximum 
 * $_GET['forths'] = "0||3" ~ Range for mt_rand() of Minimal Shadow Forth Basis Measuring Depth in pixels and Maximum 
 * $_GET['opacities'] = "41||99||72||91" ~ Range for mt_rand() / mt_rand() of Transparency of Shadowing in percentage
 */ 

<?php

	error_reporting(0);
	ini_set('display_errors', false);
	ini_set('log_errors', false);

	$url = 'http://localhost'.$_SERVER['REQUEST_URI'];
	parse_str(parse_url($url, PHP_URL_QUERY), $_GET);

	if (isset($_GET['sessionid']) && !empty($_GET['sessionid']))
		$sesshid = sha1($_GET['sessionid']);
	else
		$sesshid = sha1(serialize($_SERVER).json_encode($_GET));
	$weights = (!isset($_GET['weights']) && !strpos($_GET['weights'], '||') ? array(-80, 35, -11) : explode('||', $_GET['weights']));
	$nodes = (!isset($_GET['nodes']) && !strpos($_GET['nodes'], '||') ? array('html', 'h1', 'input', 'button', 'iframe, embed', 'textarea', 'select', 'checkbox', 'radio') : explode('||', $_GET['nodes']));
	$elements = (!isset($_GET['elements']) && (!strpos($_GET['elements'], 'box') || !strpos($_GET['elements'], 'text')) ? array('text', 'box') : explode('||', $_GET['elements']));
	$reds = (!isset($_GET['reds']) && !strpos($_GET['reds'], '||') ? array('21', '198') : explode('||', $_GET['reds']));
	$greens = (!isset($_GET['greens']) && !strpos($_GET['greens'], '||') ? array('31', '168') : explode('||', $_GET['greens']));
	$blues = (!isset($_GET['blues']) && !strpos($_GET['blues'], '||') ? array('42', '238') : explode('||', $_GET['blues']));
	$firsts = $seconds = $thirds = $forths = (!isset($_GET['ranges']) && !strpos($_GET['ranges'], '||') ? array('0', '3') : explode('||', $_GET['ranges']));
	$firsts = (!isset($_GET['firsts']) && !strpos($_GET['firsts'], '||') ? $firsts : explode('||', $_GET['firsts']));
	$seconds = (!isset($_GET['seconds']) && !strpos($_GET['seconds'], '||') ? $seconds : explode('||', $_GET['seconds']));
	$thirds = (!isset($_GET['thirds']) && !strpos($_GET['thirds'], '||') ? $thirds : explode('||', $_GET['ranges']));
	$forths = (!isset($_GET['forths']) && !strpos($_GET['forths'], '||') ? $forths : explode('||', $_GET['forths']));
	$opacities = (!isset($_GET['opacities']) && !strpos($_GET['opacities'], '||') ? array('41', '99', '72', '91') : explode('||', $_GET['opacities']));
	$token = sha1($_SERVER['HTTP_REFERER'] . md5($_SERVER['HTTP_USER_AGENT']) . sha1($_SERVER['REMOTE_ADDR'])  . sha1($_SERVER['REQUEST_URI']) . $_SERVER['REMOTE_HOST'] . $_REQUEST['token']);

	if (!isset($_SESSION[basename(dirname(__FILE__))]['timers'][$token]))
		$_SESSION[basename(dirname(__FILE__))]['timers'][$token] = microtime(true) + mt_rand(360, 8500);
	elseif ($_SESSION[basename(dirname(__FILE__))]['timers'][$token] < microtime(true))
	{
		unset($_SESSION[basename(dirname(__FILE__))][$token]);
		$_SESSION[basename(dirname(__FILE__))]['timers'][$token] = microtime(true) + mt_rand(360, 8500);
	}
	if (isset($_SESSION[basename(dirname(__FILE__))][$token]) && count($_SESSION[basename(dirname(__FILE__))][$token])==0)
		unset($_SESSION[basename(dirname(__FILE__))][$token]);
	if (!isset($_SESSION[basename(dirname(__FILE__))][$token]) || !is_array($_SESSION[basename(dirname(__FILE__))][$token]) || isset($_REQUEST['reset']) || is_string($_SESSION[basename(dirname(__FILE__))][$token]) || !isset($_SESSION[basename(dirname(__FILE__))][$token]) || mt_rand($weights[0], $weights[1]) >= $weights[2]) {
		$_SESSION[basename(dirname(__FILE__))][$token] = array();
		foreach($elements as $key)
		{ 
			mt_srand(mt_rand(-microtime(true), microtime(true)));
		    	mt_srand(mt_rand(-microtime(true), microtime(true)));
		    	mt_srand(mt_rand(-microtime(true), microtime(true)));
			$decimals = explode('', str_replace(array(' ', '.'), '', microtime(false)));
			shuffle($decimals);
			$decalpha = implode("", $decimals);
			shuffle($decimals);
			$decomega = implode("", $decimals);
		    	$points = $colour = array();
		    	foreach($nodes as $node) {
		    		$colour[$key]['red'] = (float)mt_rand((float)$reds[0].(!strpos($reds[0], '.')?".$decomega":""), (float)$reds[1].(!strpos($reds[1], '.')?".$decalpha":""));
		    		$colour[$key]['green'] = (float)mt_rand((float)$greens[0].(!strpos($greens[0], '.')?".$decomega":""), (float)$greens[1].(!strpos($greens[1], '.')?".$decalpha":""));
		    		$colour[$key]['blue'] = (float)mt_rand((float)$blues[0].(!strpos($blues[0], '.')?".$decomega":""), (float)$blues[1].(!strpos($blues[1], '.')?".$decalpha":""));
		    		$points[$key]['1st'] = (float)mt_rand((float)$firsts[0].(!strpos($firsts[0], '.')?".$decomega":""), (float)$firsts[1].(!strpos($firsts[1], '.')?".$decalpha":""));
				$points[$key]['2nd'] = (float)mt_rand((float)$seconds[0].(!strpos($seconds[0], '.')?".$decomega":""), (float)$seconds[1].(!strpos($seconds[1], '.')?".$decalpha":""));
				$points[$key]['3rd'] = (float)mt_rand((float)$thirds[0].(!strpos($thirds[0], '.')?".$decomega":""), (float)$thirds[1].(!strpos($thirds[1], '.')?".$decalpha":""));
				$points[$key]['4th'] = (float)mt_rand((float)$forths[0].(!strpos($forths[0], '.')?".$decomega":""), (float)$forths[1].(!strpos($forths[1], '.')?".$decalpha":""));
				$colour[$key]['opacity'] = (float)round(mt_rand((float)$opacities[0].(!strpos($opacities[0], '.')?".$decomega":""), (float)$opacities[1].(!strpos($opacities[1], '.')?".$decalpha":"")) / mt_rand((float)$opacities[2].(!strpos($opacities[2], '.')?".$decomega":""), (float)$opacities[3].(!strpos($opacities[3], '.')?".$decalpha":"")), 11);   

				switch ($key){
					case 'box':
						if (strpos($node, '%s')>0 || in_array(substr($node, 0, 1), array('.', '#')))
						{ 	
			$_SESSION[basename(dirname(__FILE__))][$token][$key][$node] = sprintf($node, $key) . ' {
	-webkit-box-shadow: ' . $points['box']['1st'] . 'px ' . $points['box']['2nd'] . 'px ' . $points['box']['3rd'] . 'px ' . $points['box']['4th'] . 'px rgba(' . $colour['box']['red'] . ', ' . $colour['box']['green'] . ', ' . $colour['box']['blue'] . ', ' . $colour['box']['opacity'] . '); 
	-moz-box-shadow: ' . $points['box']['1st'] . 'px ' . $points['box']['2nd'] . 'px ' . $points['box']['3rd'] . 'px ' . $points['box']['4th'] . 'px rgba(' . $colour['box']['red'] . ', ' . $colour['box']['green'] . ', ' . $colour['box']['blue'] . ', ' . $colour['box']['opacity'] . ');
	box-shadow:  ' . $points['box']['1st'] . 'px ' . $points['box']['2nd'] . 'px ' . $points['box']['3rd'] . 'px ' . $points['box']['4th'] . 'px rgba(' . $colour['box']['red'] . ', ' . $colour['box']['green'] . ', ' . $colour['box']['blue'] . ', ' . $colour['box']['opacity'] . ');
}

';
						} else {
			$_SESSION[basename(dirname(__FILE__))][$token][$key][$node] = $node . ' {
	-webkit-box-shadow: ' . $points['box']['1st'] . 'px ' . $points['box']['2nd'] . 'px ' . $points['box']['3rd'] . 'px ' . $points['box']['4th'] . 'px rgba(' . $colour['box']['red'] . ', ' . $colour['box']['green'] . ', ' . $colour['box']['blue'] . ', ' . $colour['box']['opacity'] . '); 
	-moz-box-shadow: ' . $points['box']['1st'] . 'px ' . $points['box']['2nd'] . 'px ' . $points['box']['3rd'] . 'px ' . $points['box']['4th'] . 'px rgba(' . $colour['box']['red'] . ', ' . $colour['box']['green'] . ', ' . $colour['box']['blue'] . ', ' . $colour['box']['opacity'] . ');
	box-shadow:  ' . $points['box']['1st'] . 'px ' . $points['box']['2nd'] . 'px ' . $points['box']['3rd'] . 'px ' . $points['box']['4th'] . 'px rgba(' . $colour['box']['red'] . ', ' . $colour['box']['green'] . ', ' . $colour['box']['blue'] . ', ' . $colour['box']['opacity'] . ');
}

';
						}
					break;					
					case 'text':
						if (strpos($node, '%s')>0 || in_array(substr($node, 0, 1), array('.', '#')))
						{ 
			$_SESSION[basename(dirname(__FILE__))][$token][$key][$node] = sprintf($node, $key) . ' {
	text-shadow:  ' . $points['text']['1st'] . 'px ' . $points['text']['2nd'] . 'px ' . $points['text']['3rd'] . 'px rgba(' . $colour['text']['red'] . ', ' . $colour['text']['green'] . ', ' . $colour['text']['blue'] . ', ' . $colour['text']['opacity'] . ');
}

';
						} else {
			$_SESSION[basename(dirname(__FILE__))][$token][$key][$node] = $node . ' {
	text-shadow:  ' . $points['text']['1st'] . 'px ' . $points['text']['2nd'] . 'px ' . $points['text']['3rd'] . 'px rgba(' . $colour['text']['red'] . ', ' . $colour['text']['green'] . ', ' . $colour['text']['blue'] . ', ' . $colour['text']['opacity'] . ');
}

';
						}
					break;	
				}
			}
		}
	}
	foreach(array_keys($_SESSION[basename(dirname(__FILE__))][$token]) as $key) 
		echo implode("", $_SESSION[basename(dirname(__FILE__))][$token][$key]);
	exit(0);
?>
