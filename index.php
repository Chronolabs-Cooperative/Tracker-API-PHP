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

	global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source;
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';
	
	$help=true;
	if (isset($_GET['output']) || !empty($_GET['output'])) {
		$version = isset($_GET['version'])?(string)$_GET['version']:'v2';
		$output = isset($_GET['output'])?(string)$_GET['output']:'';
		$name = isset($_GET['name'])?(string)$_GET['name']:'';
		$clause = isset($_GET['clause'])?(string)$_GET['clause']:'';
		$callback = isset($_REQUEST['callback'])?(string)$_REQUEST['callback']:'';
		$mode = isset($_GET['mode'])?(string)$_GET['mode']:'';
		$state = isset($_GET['state'])?(string)$_GET['state']:'';
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
			echo implode("} | {", getAPIDataArray($mode, $clause, $state, $name, $output, $version));
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