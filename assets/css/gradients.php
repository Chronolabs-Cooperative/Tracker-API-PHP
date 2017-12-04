<?php
	header('Origin: *');
	header('Access-Control-Allow-Origin: *');
	header("Content-type: text/css");
	error_reporting(0);
	ini_set('display_errors', false);?>/**
 * Variables for $_GET if specified
 *
 * $_GET['weights'] = "000--111--010" ~ Minimum on Range||Maximum on Range||Has to mt_rant() Greater than for CSS Update 
 * $_GET['nodes'] = "html--h1, h2, h3, h4, h5--input--textarea--select" ~ Css HTML Tag classes to define individual grandients 
 * $_GET['elements'] = "#gradomega, .gradomega--#gradbeta, .gradbeta--#gradcharley, .gradcharley--#graddelta, .graddelta--.boxingsmall" ~ Css Identity and Classing Tags to define individual grandients
 * $_GET['reds'] = "21--198" ~ Range for mt_rand() of Minimal Red in RGB and Maximum
 * $_GET['greens'] = "31--168" ~ Range for mt_rand() of Minimal Green in RGB and Maximum
 * $_GET['blues'] = "42--238" ~ Range for mt_rand() of Minimal Blues in RGB and Maximum
 * $_GET['heats'] = "41--99--72--91" ~ Range for mt_rand() / mt_rand() of Heats of Grandienting in percentage
 * $_GET['opacities'] = "41--99--72--91" ~ Range for mt_rand() / mt_rand() of Transparency of Grandienting in percentage
 */ 


<?php
	
	function rgb2html($r, $g=-1, $b=-1)
	{
	    if (is_array($r) && sizeof($r) == 3)
		list($r, $g, $b) = $r;

	    $r = intval($r); $g = intval($g);
	    $b = intval($b);

	    $r = dechex($r<0?0:($r>255?255:$r));
	    $g = dechex($g<0?0:($g>255?255:$g));
	    $b = dechex($b<0?0:($b>255?255:$b));

	    $color = (strlen($r) < 2?'0':'').$r;
	    $color .= (strlen($g) < 2?'0':'').$g;
	    $color .= (strlen($b) < 2?'0':'').$b;
	    return '#'.$color;
	}

	error_reporting(0);
	ini_set('display_errors', false);
	ini_set('log_errors', false);

	if (isset($_GET['sessionid']) && !empty($_GET['sessionid']))
		$sesshid = sha1($_GET['sessionid']);
	else
		$sesshid = sha1(serialize($_SERVER).json_encode($_GET));

	session_id($sesshid);
	session_start();
	$weights = (!isset($_GET['weights']) && !strpos($_GET['weights'], '--') ? array(-80, 35, -11) : explode('--', $_GET['weights']));
	$nodes = (!isset($_GET['nodes']) &&  empty($_GET['nodes']) ? array('html', 'blockquote, pre, code',  'button', 'iframe, embed', 'form') : explode('--', $_GET['nodes']));
	$elements = (!isset($_GET['elements']) && empty($_GET['elements']) ? array('#header, .header', '#main, .main', '#content, .content', '#footer, .footer', '.item, #item', '.odd, #odd', '.even, #even') : explode('--', $_GET['elements']));
	$reds = (!isset($_GET['reds']) && !strpos($_GET['reds'], '--') ? array(mt_rand(67,127), mt_rand(128,255)) : explode('--', $_GET['reds']));
	$greens = (!isset($_GET['greens']) && !strpos($_GET['greens'], '--') ? array(mt_rand(57,127), mt_rand(128,255)) : explode('--', $_GET['greens']));
	$blues = (!isset($_GET['blues']) && !strpos($_GET['blues'], '--') ? array(mt_rand(61,127), mt_rand(128,255)) : explode('--', $_GET['blues']));
	$heats = (!isset($_GET['heats']) && !strpos($_GET['heats'], '--') ? array(mt_rand(0,49), mt_rand(45,99), mt_rand(0,49), mt_rand(45,99)) : explode('--', $_GET['heats']));
	$opacities = (!isset($_GET['opacities']) && !strpos($_GET['opacities'], '--') ? array(mt_rand(0,49), mt_rand(45,99), mt_rand(0,49), mt_rand(45,99)) : explode('--', $_GET['opacities']));
	$modes = array();
	$modes[] = array("-moz-linear-gradient" => "top", '-webkit-gradient' => "left top, left bottom",'-webkit-linear-gradient' => "top",  '-o-linear-gradient'=> "top",  "-ms-linear-gradient"=> "top",'linear-gradient'=>'to bottom');
	$modes[] = array("-moz-linear-gradient" => "left", '-webkit-gradient' => "left top, right top",'-webkit-linear-gradient' => "left",  '-o-linear-gradient'=> "left",  "-ms-linear-gradient"=> "left",'linear-gradient'=>'to right');
	$modes[] = array("-moz-linear-gradient" => "-45deg", '-webkit-gradient' => "left top, right bottom",'-webkit-linear-gradient' => "-45deg",  '-o-linear-gradient'=> "-45deg",  "-ms-linear-gradient"=> "-45deg",'linear-gradient'=>'135deg');
	$modes[] = array("-moz-linear-gradient" => "45deg", '-webkit-gradient' => "left bottom, right top",'-webkit-linear-gradient' => "45deg",  '-o-linear-gradient'=> "45deg",  "-ms-linear-gradient"=> "45deg",'linear-gradient'=>'45deg');
	$modeskeys = array_keys($modes);
	$token = sha1($_SERVER['HTTP_REFERER'] . md5($_SERVER['HTTP_USER_AGENT']) . sha1($_SERVER['REMOTE_ADDR'])  . sha1($_SERVER['REQUEST_URI']) . $_SERVER['REMOTE_HOST'] . $_REQUEST['token']);
	if (!isset($_SESSION[basename(dirname(__FILE__))]['timers'][$token]))
		$_SESSION[basename(dirname(__FILE__))]['timers'][$token] = microtime(true) + mt_rand(360, 8500);
	elseif ($_SESSION[basename(dirname(__FILE__))]['timers'][$token] < microtime(true))
	{
		unset($_SESSION[basename(dirname(__FILE__))][$token]);
		$_SESSION[basename(dirname(__FILE__))]['timers'][$token] = microtime(true) + mt_rand(360, 8500);
	}

	if (!is_array($_SESSION[basename(dirname(__FILE__))][$token]) || isset($_REQUEST['reset']) || is_string($_SESSION[basename(dirname(__FILE__))][$token]) || !isset($_SESSION[basename(dirname(__FILE__))][$token]) || mt_rand($weights[0], $weights[1]) >= $weights[2]) {
		$_SESSION[basename(dirname(__FILE__))][$token] = array('reset' => implode(', ', $nodes) . ', ' . implode(', ', $elements) . ' { 
		    background-color: transparent;
		}');

		foreach(array_merge($nodes, $elements) as $node) 
		{ 
			mt_srand(mt_rand(-microtime(true), microtime(true)));
		    	mt_srand(mt_rand(-microtime(true), microtime(true)));
		    	mt_srand(mt_rand(-microtime(true), microtime(true)));
			$decimals = explode('', str_replace(array(' ', '.'), '', microtime(false)));
			shuffle($decimals);
			$decalpha = implode("", $decimals);
			shuffle($decimals);
			$decomega = implode("", $decimals);		    	
		    	$colour = array();
		    	for($rt = 1; $rt<=mt_rand(3,9); $rt++) {
				$colour[$rt]['red'] = mt_rand($reds[0], $reds[1]);
		    		$colour[$rt]['green'] = mt_rand($greens[0], $greens[1]);
		    		$colour[$rt]['blue'] = mt_rand($blues[0], $blues[1]);
		    		$colour[$rt]['heat'] = (mt_rand($heats[0], $heats[1]) / mt_rand($heats[2], $heats[3])) * 58.69;   
				$colour[$rt]['opacity'] = mt_rand($opacities[0], $opacities[1]) / mt_rand($opacities[2], $opacities[3]);   
		    	}
			$state = $modes[mt_rand(0, count($modes)-1)];
			$colorstyle = array();
			$colorstop = array();
			$kieyes = array_keys($colour);
			shuffle($kieyes);
			foreach($kieyes as $mkey)
			{
				$colorstyle[$mkey] = "rgba(".$colour[$mkey]['red'] . ", " . $colour[$mkey]['green'] . ", " . $colour[$mkey]['blue'] . ", " . $colour[$mkey]['opacity'] . ") ". $colour[$mkey]['heat'].'%';
				$colorstop[$mkey] = 'color-stop('. $colour[$mkey]['heat'].'%, rgba('.$colour[$mkey]['red'] . ", " . $colour[$mkey]['green'] . ", " . $colour[$mkey]['blue'] . ", " . $colour[$mkey]['opacity'] . "))";
			}
			$colorstyle = implode(", ", $colorstyle);
			$colorstop  = implode(", ", $colorstop);
			$_SESSION[basename(dirname(__FILE__))][$token][$key][$node] = "$node " . ' {
	background: -moz-linear-gradient('.$state['-moz-linear-gradient'].', '.$colorstyle .') !important; /* FF3.6+ */
	background: -webkit-gradient(linear, '.$state['-webkit-gradient'].', '. $colorstop . ') !important; /* Chrome,Safari4+ */
	background: -webkit-linear-gradient('.$state['-webkit-linear-gradient'].', '.$colorstyle .') !important;  /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient('.$state['-o-linear-gradient'].', '.$colorstyle .') !important; /* Opera 11.10+ */
	background: -ms-linear-gradient('.$state['-ms-linear-gradient'].', '.$colorstyle .') !important; /* IE10+ */
	background: linear-gradient('.$state['linear-gradient'].', '.$colorstyle .') !important; /* W3C */
}

';
		}
	}

	foreach(array_keys($_SESSION[basename(dirname(__FILE__))][$token]) as $key) 
		echo implode("", $_SESSION[basename(dirname(__FILE__))][$token][$key]);
	exit(0);
?>
