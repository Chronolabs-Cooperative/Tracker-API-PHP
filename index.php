<?php
/**
 * Chronolabs Torrent Tracker REST API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       	Chronolabs Cooperative http://labs.coop
 * @license         	General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @package         	tracker
 * @since           	2.1.9
 * @author          	Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		api
 * @description		Torrent Tracker REST API
 * @link				http://sourceforge.net/projects/chronolabsapis
 * @link				http://cipher.labs.coop
 */

if (!defined("API_DEBUG"))
    define('API_DEBUG', false);

include_once './apiconfig.php';
include_once './header.php';

/**
 * URI Path Finding of API URL Source Locality
 * @var unknown_type
 */
$odds = $inner = array();
foreach($inner as $key => $values) {
    if (!isset($inner[$key])) {
        $inner[$key] = $values;
    } elseif (!in_array(!is_array($values) ? $values : md5(json_encode($values, true)), array_keys($odds[$key]))) {
        if (is_array($values)) {
            $odds[$key][md5(json_encode($inner[$key] = $values, true))] = $values;
        } else {
            $odds[$key][$inner[$key] = $values] = "$values--$key";
        }
    }
}

foreach($_POST as $key => $values) {
    if (!isset($inner[$key])) {
        $inner[$key] = $values;
    } elseif (!in_array(!is_array($values) ? $values : md5(json_encode($values, true)), array_keys($odds[$key]))) {
        if (is_array($values)) {
            $odds[$key][md5(json_encode($inner[$key] = $values, true))] = $values;
        } else {
            $odds[$key][$inner[$key] = $values] = "$values--$key";
        }
    }
}

foreach(parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'], '?')?'&':'?').$_SERVER['QUERY_STRING'], PHP_URL_QUERY) as $key => $values) {
    if (!isset($inner[$key])) {
        $inner[$key] = $values;
    } elseif (!in_array(!is_array($values) ? $values : md5(json_encode($values, true)), array_keys($odds[$key]))) {
        if (is_array($values)) {
            $odds[$key][md5(json_encode($inner[$key] = $values, true))] = $values;
        } else {
            $odds[$key][$inner[$key] = $values] = "$values--$key";
        }
    }
}
if (isset($inner['output']) || !empty($inner['output'])) {
	$version = isset($inner['version'])?(string)$inner['version']:'v2';
	$output = isset($inner['output'])?(string)$inner['output']:'';
	$name = isset($inner['name'])?(string)$inner['name']:'';
	$clause = isset($inner['clause'])?(string)$inner['clause']:'';
	$callback = isset($_REQUEST['callback'])?(string)$_REQUEST['callback']:'';
	$mode = isset($inner['mode'])?(string)$inner['mode']:'';
	$state = isset($inner['state'])?(string)$inner['state']:'';
	switch($output)
	{
		default:
		case "raw":
		case "html":
		case "serial":
		case "json":
		case "xml":
			if (in_array($mode, array('torrents', 'peers', 'seeds', 'files', 'trackers', 'network')))
				$help=false;
			break;		
	}
} else {
	$help=true;
}

if ($help==true) {
	if (function_exists('http_response_code'))
		http_response_code(400);
	include dirname(__FILE__).'/help.php';
	exit;
}
/*
session_start();
if (!in_array(whitelistGetIP(true), whitelistGetIPAddy())) {
	if (isset($_SESSION['reset']) && $_SESSION['reset']<microtime(true))
		$_SESSION['hits'] = 0;
	if ($_SESSION['hits']<=MAXIMUM_QUERIES) {
		if (!isset($_SESSION['hits']) || $_SESSION['hits'] = 0)
			$_SESSION['reset'] = microtime(true) + 3600;
		$_SESSION['hits']++;
	} else {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
}
*/

if (function_exists('http_response_code'))
	http_response_code(200);

switch ($output) {
	default:
		header('Content-type: text/plain');
		echo implode("",getAPIDataArray($mode, $clause, $state, $name, $output, $version));
		break;
	case 'html':
		echo '<h1>' . $country . ' - ' . $place . ' (Places data)</h1>';
		echo '<pre style="entities-family: \'Courier New\', Courier, Terminal; entities-size: 0.77em;">';
		echo implode("\n", getAPIDataArray($mode, $clause, $state, $name, $output, $version));
		echo '</pre>';
		break;
	case 'raw':
		echo '<?php return ' . var_export(getAPIDataArray($mode, $clause, $state, $name, $output, $version)) . "; ?>";
		break;
	case 'json':
		header('Content-type: application/json');
		echo json_encode(getAPIDataArray($mode, $clause, $state, $name, $output, $version));
		break;
	case 'serial':
		header('Content-type: text/plain');
		echo serialize(getAPIDataArray($mode, $clause, $state, $name, $output, $version));
		break;
	case 'benc':
		benc_resp_raw(benc_api(getAPIDataArray($mode, $clause, $state, $name, $output, $version), $mode));
		break;
	case 'xml':
		header('Content-type: application/xml');
		$dom = new XmlDomConstruct('1.0', 'utf-8');
		$dom->fromMixed(array('root'=>getAPIDataArray($mode, $clause, $state, $name, $output, $version)));
		echo $dom->saveXML();
		break;
}
?>		