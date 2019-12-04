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
 * @copyright       	Chronolabs Cooperative http://snails.email
 * @license         	General Public License version 3 (http://snails.email/briefs/legal/general-public-licence/13,3.html)
 * @package         	tracker
 * @since           	2.1.9
 * @author          	Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		api
 * @description		Torrent Tracker REST API
 * @link				http://sourceforge.net/projects/chronolabsapis
 * @link				http://cipher.snails.email
 */

require_once __DIR__.'/common.php';


if (!function_exists("bcmod")) {
/**
* my_bcmod - get modulus (substitute for bcmod)
* string my_bcmod ( string left_operand, int modulus )
* left_operand can be really big, but be carefull with modulus :(
* by Andrius Baranauskas and Laurynas Butkus :) Vilnius, Lithuania
**/
function bcmod( $x, $y )
{
    // how many numbers to take at once? carefull not to exceed (int)
    $take = 5;    
    $mod = '';

    do
    {
        $a = (int)$mod.substr( $x, 0, $take );
        $x = substr( $x, $take );
        $mod = $a % $y;   
    }
    while ( strlen($x) );

    return (int)$mod;
}
}

if (!function_exists("setUserAgentID")) {
	function setUserAgentID($useragent) {
		return bcmod(base_convert(sha1($useragent), 16, 10), 1234567890);
	}


}

if (!function_exists("setCallBackURI")) {

	/* function getURIData()
	 *
	 * 	cURL Routine
	 * @author 		Simon Roberts (Chronolabs) simon@snails.email
	 *
	 * @return 		float()
	 */
	function setCallBackURI($uri = '', $timeout = 65, $connectout = 65, $data = array(), $queries = array())
	{
		list($when) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT `when` from `" . $GLOBALS['APIDB']->prefix('callbacks'). "` ORDER BY `when` DESC LIMIT 1"));
		if ($when<time())
			$when = $time();
			$when = $when + mt_rand(3, 14);
		return $GLOBALS['APIDB']->queryF("INSERT INTO `" . $GLOBALS['APIDB']->prefix('callbacks'). "` (`when`, `uri`, `timeout`, `connection`, `data`, `queries`) VALUES(\"$when\", \"$uri\", \"$timeout\", \"$connectout\", \"" . mysqli_real_escape_string(json_encode($data)) . "\",\"" . mysqli_real_escape_string(json_encode($queries)) . "\")");
	}
}

if (!function_exists("getAPIDataArray")) {

	/* function getURIData()
	 *
	 * 	cURL Routine
	 * @author 		Simon Roberts (Chronolabs) simon@snails.email
	 *
	 * @return 		float()
	 */
	function getAPIDataArray($mode = '', $clause = '', $state = '', $name = '', $output = '', $version = 'v2')
	{
		$return = array();
		switch($mode)
		{
			case "torrents":
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('torrents') . "` ORDER BY `last_action` DESC");
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return[md5($row['id'].API_URL)] = $row;
					unset($return[md5($row['id'].API_URL)]['id']);
				}
				break;
			case "peers":
				list($torrentid) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT `id` from `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE `info_hash` = '$clause' ORDER BY RAND() DESC LIMIT 1"));
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('peers') . "` WHERE torrentid = '$torrentid' AND `seeder` = 'no' ORDER BY RAND()");
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return[$hash = md5($row['id'].API_URL.$row['apiid'].$row['peerid'])] = $row;
					unset($return[$hash]['torrentid']);
					unset($return[$hash]['id']);
				}
				break;
			case "seeds":
				list($torrentid) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT `id` from `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE `info_hash` = '$clause' ORDER BY RAND() DESC LIMIT 1"));
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('peers') . "` WHERE torrentid = '$torrentid' AND `seeder` = 'yes' ORDER BY RAND()");
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return[$hash = md5($row['id'].API_URL.$row['apiid'].$row['peerid'])] = $row;
					unset($return[$hash]['torrentid']);
					unset($return[$hash]['id']);
				}
				break;
			case "files":
				list($torrentid) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT `id` from `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE `info_hash` = '$clause' ORDER BY RAND() DESC LIMIT 1"));
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('files') . "` WHERE torrentid = '$torrentid'");
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return[$hash = md5($row['id'].API_URL.$row['filename'].$row['size'])] = $row;
					unset($return[$hash]['torrentid']);
					unset($return[$hash]['id']);
				}
				break;
			case "trackers":
				list($torrentid) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT `id` from `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE `info_hash` = '$clause' ORDER BY RAND() DESC LIMIT 1"));
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('trackers_to_torrents') . "` WHERE torrentid = '$torrentid' AND `seeder` = 'yes' ORDER BY RAND()");
				$trackerids = array();
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return['statistical'][$hash = md5($row['trackerid'].API_URL.$row['torrentid'])] = $row;
					$trackerids[$row['trackerid']] = $row['trackerid'];
					$return['statistical'][$hash]['trackerid'] = md5(API_URL.$return['statistical'][$hash]['trackerid']);
					unset($return['statistical'][$hash]['torrentid']);
					unset($return['statistical'][$hash]['id']);
				}
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('trackers'). "` WHERE `id` IN (".implode(", ", $trackerids).")");
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return['trackers'][$hash = md5(API_URL.$row['id'])] = $row;
					$trackerids[$row['trackerid']] = $row['trackerid'];
					$return['trackers'][$hash]['id'] = md5(API_URL.$return['trackers'][$hash]['id']);
				}
				break;
			case "network":
				list($torrentid) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT `id` from `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE `info_hash` = '$clause' ORDER BY RAND() DESC LIMIT 1"));
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('networking_to_torrents') . "` WHERE torrentid = '$torrentid' AND `seeder` = 'yes' ORDER BY RAND()");
				$ipids = array();
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return['statistical'][$hash = md5($row['ipid'].API_URL.$row['torrentid'])] = $row;
					$ipids[$row['ipid']] = $row['ipid'];
					unset($return['statistical'][$hash]['torrentid']);
				}
				$result = $GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('trackers'). "` WHERE `id` IN ('".implode("', '", $ipids)."')");
				while($row = $GLOBALS['APIDB']->fetchArray($result))
				{
					$return['networking'][$hash = md5(API_URL.$row['ipid'])] = $row;
				}
				break;
				
		}
		return $return;
	}
}

if (!function_exists("err")) {
	/**
	 * Benc Error Output
	 * 
	 * @param string $msg
	 */
	function err($msg)
	{
		benc_resp(array('failure reason' => array('type' => 'string', 'value' => $msg)));
	
		exit();
	}
}

if (!function_exists("benc_api")) {
	/**
	 * Benc Response
	 *
	 * @param string $d
	 */
	function benc_api($d, $state = '')
	{
		return benc_state($d, $state);
	}
}


if (!function_exists("benc_encoder")) {
	/**
	 * Benc Response
	 *
	 * @param string $d
	 */
	function benc_encoder($d, $ret = '')
	{
	
		foreach($d as $key => $value)
		{
			if (is_numeric($value))
				$type = 'integer';
			elseif(is_string($value))
				$type = 'string';
			elseif(is_array($value))
				$type = 'list';
			switch ($type) {
				case "string":
					$ret .= benc_str($key) . benc_str($value);
					break;
				case "integer":
					$ret .= benc_str($key) . benc_int($value);
					break;
				case "list":
					$ret .= benc_str($key) . benc_list($value);
					break;
				default:
					$ret .= '';
			}
		}
		return $ret;
	}
}

if (!function_exists("benc_resp")) {
	/**
	 * Benc Response
	 * 
	 * @param string $d
	 */
	function benc_resp($d)
	{
		benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
	}
}

if (!function_exists("benc_resp_raw")) {
	/**
	 * Benc Response Raw
	 * 
	 * @param string $x
	 */
	function benc_resp_raw($x)
	{
		header( "Content-Type: text/plain" );
		header( "Pragma: no-cache" );
	
		if ( $_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip' )
		{
			header( "Content-Encoding: gzip" );
			die(gzencode( $x, 9, FORCE_GZIP ));
		}
		else
			die($x) ;
	}
}

if (!function_exists("benc")) {
	/**
	 * Benc Codec
	 * 
	 * @param array $obj
	 */
	function benc($obj) {
		if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
			return;
			$c = $obj["value"];
			switch ($obj["type"]) {
				case "string":
					return benc_str($c);
				case "integer":
					return benc_int($c);
				case "list":
					return benc_list($c);
				case "dictionary":
					return benc_dict($c);
				default:
					return;
			}
	}
}

if (!function_exists("benc_str")) {
	/**
	 * Benc String Encoder
	 * 
	 * @param string $s
	 * @return string
	 */
	function benc_str($s) {
		return strlen($s) . ":$s";
	}
}

if (!function_exists("benc_int")) {
	/**
	 * Benc Integer Encoder
	 * 
	 * @param integer $i
	 * @return string
	 */
	function benc_int($i) {
		return "i" . $i . "e";
	}
}

if (!function_exists("benc_list")) {
	/**
	 * Benc List Encoder
	 * 
	 * @param array $a
	 */
	function benc_list($a) {
		$s = "l";
		foreach ($a as $key => $e) {
			if (!is_array($e["value"]))
				$s .= benc($e);
			else 
				$s .= benc_str($key) . benc_encoder($e["value"]);
		}
		$s .= "e";
		return $s;
	}
}

if (!function_exists("benc_state")) {
	/**
	 * Benc Dictionary Encoder
	 *
	 * @param array $d
	 */
	function benc_state($d, $state = '') {
		$s = substr($state, 0, 1);
		$keys = array_keys($d);
		sort($keys);
		foreach ($keys as $k) {
			$v = $d[$k];
			$s .= benc_str($k);
			$s .= benc_encoder($v);
		}
		$s .= "e";
		return $s;
	}
}

if (!function_exists("benc_dict")) {
	/**
	 * Benc Dictionary Encoder
	 * 
	 * @param array $d
	 */
	function benc_dict($d) {
		$s = "d";
		$keys = array_keys($d);
		sort($keys);
		foreach ($keys as $k) {
			$v = $d[$k];
			$s .= benc_str($k);
			$s .= benc($v);
		}
		$s .= "e";
		return $s;
	}
}

if (!function_exists("portblacklisted")) {
	/**
	 * Torrent Port Blacklistings
	 * 
	 * @param integer $port
	 */
	function portblacklisted($port)
	{
		// direct connect
		if ($port >= 411 && $port <= 413) return true;
	
		// bittorrent
		if ($port >= 6881 && $port <= 6889) return true;
	
		// kazaa
		if ($port == 1214) return true;
	
		// gnutella
		if ($port >= 6346 && $port <= 6347) return true;
	
		// emule
		if ($port == 4662) return true;
	
		// winmx
		if ($port == 6699) return true;
	
		return false;
	}
}



if (!function_exists("getRandomInfoHash")) {
	/**
	 *
	 * @param string $url
	 * @param string $version
	 * @param string $callback
	 * @param string $polinating
	 * @param string $root
	 */
	function getRandomInfoHash( ) {
		$i = -10;
		$sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE 1 = 1 ";
		$results = $GLOBALS['APIDB']->queryF($sql);
		list($count) = $GLOBALS['APIDB']->fetchRow($results);
		if ($count < 2)
			return '0x0x0x0x0x0x0x0x0x0x0x0';

		while(strlen($torrent['info_hash']) == 0 || $i < 1)
		{
			$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('torrents') . "` ORDER BY RAND() LIMIT 1";
			if ($GLOBALS['APIDB']->getRowsNum($results = $GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($infohash))))==1)
			{
				$torrent = $GLOBALS['APIDB']->fetchArray($results);
			} else 
				continue;
			$i++;
		}
		return strlen($torrent['info_hash']) == 0? sha1(NULL):$torrent['info_hash'];
	}
}

if (!function_exists("getNetworkingArray")) {
	/**
	 *
	 * @param string $url
	 * @param string $version
	 * @param string $callback
	 * @param string $polinating
	 * @param string $root
	 */
	function getNetworkingArray( $ipid = '' ) {

		$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('networking'). "` WHERE `id` LIKE '%s'";
		if ($GLOBALS['APIDB']->getRowsNum($results = $GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($infohash))))==1)
		{
			$network = $GLOBALS['APIDB']->fetchArray($results);
			return $network;
		} else {
			return array();
		}
	}
}

if (!function_exists("getTorrentIdentity")) {
	/**
	 *
	 * @param string $url
	 * @param string $version
	 * @param string $callback
	 * @param string $polinating
	 * @param string $root
	 */
	function getTorrentIdentity( $infohash = '' ) {

		$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE `info_hash` LIKE '%s'";
		if ($GLOBALS['APIDB']->getRowsNum($results = $GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($infohash))))==1)
		{
			$torrent = $GLOBALS['APIDB']->fetchArray($results);
			return $torrent['id'];
		} else {
			$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('torrents') . "` (`info_hash`, `added`, `owner_ipid`) VALUES ('%s', unix_timestamp(), '%s')";
			if ($GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($infohash), mysqli_real_escape_string($GLOBALS['ipid']))))
			{
				$torrentid = $GLOBALS['APIDB']->getInsertID();
				$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('networking_to_torrents') . "` (`ipid`, `torrentid`, `start`) VALUES('%s','%s','%s')";
				$GLOBALS['APIDB']->queryF(sprintf($sql, $GLOBALS['ipid'],$torrentid,time()));
				return $torrentid;
			}
			err("Creating Torrent Caused Tracking Issues!");
		}
	}
}


if (!function_exists("getAPIIdentity")) {
	/**
	 * 
	 * @param string $url
	 * @param string $version
	 * @param string $callback
	 * @param string $polinating
	 * @param string $root
	 */
	function getAPIIdentity( $url,  $version, $callback, $polinating = true, $root = "http://tracker.snails.email" ) {
		
	    if (!is_object($GLOBALS['APIDB']))
	       return false;
	    
		$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('apis') . "` WHERE `api-url` LIKE '%s'";
		if ($GLOBALS['APIDB']->getRowsNum($results = $GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($url))))==1)
		{
			$api = $GLOBALS['APIDB']->fetchArray($results);
			return $api['id'];
		} else {
			if (strpos($url, 'localhost')>0 || $url = $root)
				$polinating = false;
			$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('peers') . "` (`peer-id`, `api-url`, `version`, `callback`, `created`) VALUES ('%s', '%s', '%s', '%s', '%s')";
			$apiid = md5($url.$version.$callback.$polinating.$root.microtime(true));
			if ($GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($apiid), mysqli_real_escape_string($url), mysqli_real_escape_string($version), mysqli_real_escape_string($callback), time())))
			{
				if ($polinating==true)
					@getURIData($root . "/v2/register/callback.api", 25, 35, array('api-id'=>$apiid, 'api-url' => $url, 'version' => $version, 'callback' => $callback, 'polinating' => $polinating));
			}
			return $apiid;
		}
		
	}
}



if (!function_exists("getCountryID")) {
	/**
	 *
	 * @param array $array
	 */
	function getCountryID($country = '')
	{
		static $countries = array();
		if (empty($countries))
			$countries = json_decode(getURIData('http://places.snails.email/v1/list/list/json.api', 20, 30), true);
		foreach($countries['countries'] as $name => $values)
			if (strtolower($name)==strtolower($country))
				return $values['key'];
		return $country;
	}
}


if (!function_exists("getPlaceID")) {
	/**
	 *
	 * @param array $array
	 */
	function getPlaceID($countryid = '', $name = '', $countriessupported = array())
	{
		if (empty($countryid))
			return '';
		static $countries = array();
		if (empty($countries))
			$countries = json_decode(getURIData('http://places.snails.email/v1/list/list/json.api', 20, 30), true);
		if (count($countriessupported))
			foreach($countries['countries'] as $country => $values)
				if (!in_array($values['key'], $countriessupported))
					unset($countries['countries'][$country]);
		foreach($countries['countries'] as $country => $values)
			if (strtolower($countryid)==strtolower($values['key']) || strtolower($name)==strtolower($countryid))
				$count = strtolower(str_replace(" ", "", $name));
		if (!empty($count))
			$place = json_decode(getURIData('http://places.snails.email/v1/$count/$name/json.api', 20, 30), true);
		else
			foreach($countries['countries'] as $country => $values)
			{
				$country = strtolower(str_replace(" ", "", $country));
				$place = json_decode(getURIData('http://places.snails.email/v1/$country/$name/json.api', 20, 30), true);
				if (isset($place['country']['place']['RegionName']) && !empty($place['country']['place']['RegionName']))
					return $place['country']['place']['key'];
			}
		if (isset($place['country']['place']['RegionName']) && !empty($place['country']['place']['RegionName']))
			return $place['country']['place']['key'];
		return $name;
	}
}

if (!function_exists("getPlaceArray")) {
	/**
	 *
	 * @param array $array
	 */
	function getPlaceArray($countryid = '', $name = '', $countriessupported = array())
	{
		if (empty($countryid))
			return array();
		static $countries = array();
		if (empty($countries))
			$countries = json_decode(getURIData('http://places.snails.email/v1/list/list/json.api', 20, 30), true);
			if (count($countriessupported))
				foreach($countries['countries'] as $country => $values)
					if (!in_array($values['key'], $countriessupported))
						unset($countries['countries'][$country]);
			foreach($countries['countries'] as $country => $values)
				if (strtolower($countryid)==strtolower($values['key']) || strtolower($name)==strtolower($countryid))
					$count = strtolower(str_replace(" ", "", $country));
		if (!empty($count))
			$place = json_decode(getURIData('http://places.snails.email/v1/$count/$name/json.api', 20, 30), true);
		else
			foreach($countries['countries'] as $country => $values)
			{
				$country = strtolower(str_replace(" ", "", $country));
				$place = json_decode(getURIData('http://places.snails.email/v1/$country/$name/json.api', 20, 30), true);
				if (isset($place['country']['place']['RegionName']) && !empty($place['country']['place']['RegionName']))
					return $place['country']['place']['key'];
			}
		if (isset($place['country']['place']['RegionName']) && !empty($place['country']['place']['RegionName']))
			return $place['country']['place'];
		return array();
	}
}


if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIPAddy()
	 *
	* 	provides an associative array of whitelisted IP Addresses
	* @author 		Simon Roberts (Chronolabs) simon@snails.email
	*
	* @return 		array
	*/
	function whitelistGetIPAddy() {
		return array_merge(whitelistGetNetBIOSIP(), file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist.txt'));
	}
}

if (!function_exists("whitelistGetNetBIOSIP")) {

	/* function whitelistGetNetBIOSIP()
	 *
	* 	provides an associative array of whitelisted IP Addresses base on TLD and NetBIOS Addresses
	* @author 		Simon Roberts (Chronolabs) simon@snails.email
	*
	* @return 		array
	*/
	function whitelistGetNetBIOSIP() {
		$ret = array();
		foreach(file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist-domains.txt') as $domain) {
			$ip = gethostbyname($domain);
			$ret[$ip] = $ip;
		}
		return $ret;
	}
}

if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIP()
	 *
	* 	get the True IPv4/IPv6 address of the client using the API
	* @author 		Simon Roberts (Chronolabs) simon@snails.email
	*
	* @param		$asString	boolean		Whether to return an address or network long integer
	*
	* @return 		mixed
	*/
	function whitelistGetIP($asString = true){
		// Gets the proxy ip sent by the user
		$proxy_ip = '';
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else
		if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
			$proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
		} else
		if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
			$proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
		} else
		if (!empty($_SERVER['HTTP_FORWARDED'])) {
			$proxy_ip = $_SERVER['HTTP_FORWARDED'];
		} else
		if (!empty($_SERVER['HTTP_VIA'])) {
			$proxy_ip = $_SERVER['HTTP_VIA'];
		} else
		if (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
			$proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
		} else
		if (!empty($_SERVER['HTTP_COMING_FROM'])) {
			$proxy_ip = $_SERVER['HTTP_COMING_FROM'];
		}
		if (!empty($proxy_ip) && $is_ip = preg_match('/^([0-9]{1,3}.){3,3}[0-9]{1,3}/', $proxy_ip, $regs) && count($regs) > 0)  {
			$the_IP = $regs[0];
		} else {
			$the_IP = $_SERVER['REMOTE_ADDR'];
		}
			
		$the_IP = ($asString) ? $the_IP : ip2long($the_IP);
		return $the_IP;
	}
}


if (!function_exists("getIPIdentity")) {
	/**
	 *
	 * @param string $ip
	 * @return string
	 */
	function getIPIdentity($ip = '', $sarray = false)
	{
		$sql = array();
		
		if (empty($ip))
			$ip = whitelistGetIP(true);
		
		if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
			$sql['selecta'] = "SELECT * from `" . $GLOBALS['APIDB']->prefix('networking'). "` WHERE `ipaddy` LIKE '" . $ip . "' AND `type` = 'ipv6'";
		else
			$sql['selecta'] = "SELECT * from `" . $GLOBALS['APIDB']->prefix('networking'). "` WHERE `ipaddy` LIKE '" . $ip . "' AND `type` = 'ipv4'";
		if (!$row = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql['selecta'])))
			if (($ipaddypart[0] ===  $serverpart[0] && $ipaddypart[1] ===  $serverpart[1]) )
			{
				$uris = cleanWhitespaces(file($file = __DIR__ . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "lookups.diz"));
				shuffle($uris); shuffle($uris); shuffle($uris); shuffle($uris);
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE || FILTER_FLAG_NO_RES_RANGE) === false)
				{
					$data = array();
					foreach($uris as $uri)
					{
						if ($data['ip']==$ip || $data['country']['iso'] == "-" || empty($data))
							$data = json_decode(getURIData(sprintf($uri, 'myself', 'json'), 20, 30), true);
						if (count($data) > 0 &&  $data['country']['iso'] != "-")
							continue;
					}
				} else{
					foreach($uris as $uri)
					{
						if ($data['ip']!=$ip || $data['country']['iso'] == "-" || empty($data))
							$data = json_decode(getURIData(sprintf($uri, $ip, 'json'), 20, 30), true);
						if (count($data) > 0 &&  $data['country']['iso'] != "-")
							continue;
					}
				}
				if (!isset($data['ip']) && empty($data['ip']))
					$data['ip'] = $ip;
				if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
					$sql['selectb'] = "SELECT * from `" . $GLOBALS['APIDB']->prefix('networking'). "` WHERE `ipaddy` LIKE '" . $data['ip'] . "' AND `type` = 'ipv6'";
				else
					$sql['selectb'] = "SELECT * from `" . $GLOBALS['APIDB']->prefix('networking'). "` WHERE `ipaddy` LIKE '" . $data['ip'] . "' AND `type` = 'ipv4'";
				if (!$row = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql['selectb'])))
				{
					$row = array();
					$row['ipaddy'] = $data['ip'];
					if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
						$row['type'] = 'ipv6';
					else 
						$row['type'] = 'ipv4';
					$row['netbios'] = gethostbyaddr($row['ipaddy']);
					$row['data'] = array('ipstack' => gethostbynamel($row['netbios']));
					$row['domain'] = getBaseDomain("http://".$row['netbios']);
					$row['country'] = $data['country']['iso'];
					$row['region'] = $data['location']['region'];
					$row['city'] = $data['location']['city'];
					$row['postcode'] = $data['location']['postcode'];
					$row['timezone'] = "GMT " . $data['location']['gmt'];
					$row['longitude'] = $data['location']['coordinates']['longitude'];
					$row['latitude'] = $data['location']['coordinates']['latitude'];
					$row['last'] = $row['created'] = time();
					$row['whois'] = array();
					$whoisuris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "whois.diz"));
					shuffle($whoisuris); shuffle($whoisuris); shuffle($whoisuris); shuffle($whoisuris);
					foreach($whoisuris as $uri)
					{
						if (empty($row['whois'][$row['type']]) || !isset($row['whois'][$row['type']]))
						{
							$row['whois'][$row['type']] = json_decode(getURIData(sprintf($uri, $row['ipaddy'], 'json'), 20, 30), true);
						} elseif (empty($row['whois']['domain']) || !isset($row['whois']['domain']))
						{
							$row['whois']['domain'] = json_decode(getURIData(sprintf($uri, $row['domain'], 'json'), 20, 30), true);
						} else
							continue;
					}
					$row['id'] = md5(json_encode($row));
					$data = array();
					foreach($row as $key => $value)
						if (is_array($value))
							$data[$key] = mysqli_real_escape_string(json_encode($value));
						else
							$data[$key] = mysqli_real_escape_string($value);
					$sql['inserta'] = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('networking'). "` (`" . implode("`, `", array_keys($data)) . "`) VALUES ('" . implode("', '", $data) . "')";
					$GLOBALS['APIDB']->queryF($sql['inserta']);
				} 
			} 
		$sql['updatea'] = "UPDATE `" . $GLOBALS['APIDB']->prefix('networking'). "` SET `last` = '". time() . '\' WHERE `id` = "' . $row['id'] .'"';
		$GLOBALS['APIDB']->queryF($sql['updatea']);
		if ($sarray == false)
			return $row['id'];
		else
			return $row;
	}
}


if (!function_exists("getBaseDomain")) {
	/**
	 * getBaseDomain
	 *
	 * @param string $url
	 * @return string|unknown
	 */
	function getBaseDomain($url)
	{

		static $fallout, $stratauris, $classes;

		if (empty($classes))
		{
			if (empty($stratauris)) {
				$stratauris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "stratas.diz"));
				shuffle($stratauris); shuffle($stratauris); shuffle($stratauris); shuffle($stratauris);
			}
			shuffle($stratauris);
			$attempts = 0;
			while(empty($classes) || $attempts <= (count($stratauris) * 1.65))
			{
				$attempts++;
				$classes = array_keys(unserialize(getURIData($stratauris[mt_rand(0, count($stratauris)-1)] ."/v1/strata/serial.api", 20, 30)));
			}
		}
		if (empty($fallout))
		{
			if (empty($stratauris)) {
				$stratauris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "stratas.diz"));
				shuffle($stratauris); shuffle($stratauris); shuffle($stratauris); shuffle($stratauris);
			}
			shuffle($stratauris);
			$attempts = 0;
			while(empty($fallout) || $attempts <= (count($stratauris) * 1.65))
			{
				$attempts++;
				$fallout = array_keys(unserialize(getURIData($stratauris[mt_rand(0, count($stratauris)-1)] ."/v1/fallout/serial.api", 20, 30)));
			}
		}
		
		// Get Full Hostname
		$url = strtolower($url);
		$hostname = parse_url($url, PHP_URL_HOST);
		if (!filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 || FILTER_FLAG_IPV4) === false)
			return $hostname;

		// break up domain, reverse
		$elements = explode('.', $hostname);
		$elements = array_reverse($elements);

		// Returns Base Domain
		if (in_array($elements[0], $classes))
			return $elements[1] . '.' . $elements[0];
		elseif (in_array($elements[0], $fallout) && in_array($elements[1], $classes))
			return $elements[2] . '.' . $elements[1] . '.' . $elements[0];
		elseif (in_array($elements[0], $fallout))
			return  $elements[1] . '.' . $elements[0];
		else
			return  $elements[1] . '.' . $elements[0];
	}
}

if (!function_exists("mkdirSecure")) {
	/**
	 *
	 * @param unknown_type $path
	 * @param unknown_type $perm
	 * @param unknown_type $secure
	 */
	function mkdirSecure($path = '', $perm = 0777, $secure = true)
	{
		if (!is_dir($path))
		{
			mkdir($path, $perm, true);
			if ($secure == true)
			{
				writeRawFile($path . DIRECTORY_SEPARATOR . '.htaccess', "<Files ~ \"^.*$\">\n\tdeny from all\n</Files>");
			}
			return true;
		}
		return false;
	}
}

if (!function_exists("cleanWhitespaces")) {
	/**
	 *
	 * @param array $array
	 */
	function cleanWhitespaces($array = array())
	{
		foreach($array as $key => $value)
		{
			if (is_array($value))
				$array[$key] = cleanWhitespaces($value);
			else {
				$array[$key] = trim(str_replace(array("\n", "\r", "\t"), "", $value));
			}
		}
		return $array;
	}
}


if (!function_exists("setTimeLimit")) {

	/* function getURIData()
	 *
	 * 	cURL Routine
	 * @author 		Simon Roberts (Chronolabs) simon@snails.email
	 *
	 * @return 		float()
	 */
	function setTimeLimit($seconds = '480')
	{
		static $limit = 45;
		$limit = $limit + $seconds;
		set_time_limit($limit);
		return $limit;
	}
}

if (!function_exists("getURIData")) {

	/* function getURIData()
	 *
	* 	cURL Routine
	* @author 		Simon Roberts (Chronolabs) simon@snails.email
	*
	* @return 		float()
	*/
	function getURIData($uri = '', $timeout = 65, $connectout = 65, $post_data = array())
	{
		if (!function_exists("curl_init"))
		{
			return file_get_contents($uri);
		}
		if (!$btt = curl_init($uri)) {
			return false;
		}
		setTimeLimit($timeout+$connectout);
		curl_setopt($btt, CURLOPT_HEADER, 0);
		curl_setopt($btt, CURLOPT_POST, (count($posts)==0?false:true));
		if (count($posts)!=0)
			curl_setopt($btt, CURLOPT_POSTFIELDS, http_build_query($post_data));
		curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
		curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($btt, CURLOPT_VERBOSE, false);
		curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($btt);
		curl_close($btt);
		return $data;
	}
}

if (!function_exists("writeRawFile")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function writeRawFile($file = '', $data = '')
	{
		$lineBreak = "\n";
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			$lineBreak = "\r\n";
		}
		if (!is_dir(dirname($file)))
			if (strpos(' '.$file, ENTITIES_CACHE))
			mkdirSecure(dirname($file), 0777, true);
		else
			mkdir(dirname($file), 0777, true);
		elseif (strpos(' '.$file, ENTITIES_CACHE) && !file_exists(ENTITIES_CACHE . DIRECTORY_SEPARATOR . '.htaccess'))
		writeRawFile(ENTITIES_CACHE . DIRECTORY_SEPARATOR . '.htaccess', "<Files ~ \"^.*$\">\n\tdeny from all\n</Files>");
		if (is_file($file))
			unlink($file);
		$data = str_replace("\n", $lineBreak, $data);
		$ff = fopen($file, 'w');
		fwrite($ff, $data, strlen($data));
		fclose($ff);
	}
}

if (!function_exists('sef'))
{

	/**
	 * Safe encoded paths elements
	 *
	 * @param unknown $datab
	 * @param string $char
	 * @return string
	 */
	function sef($value = '', $stripe ='-')
	{
		$value = str_replace('&', 'and', $value);
		$value = str_replace(array("'", '"', "`"), 'tick', $value);
		$replacement_chars = array();
		$accepted = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","m","o","p","q",
				"r","s","t","u","v","w","x","y","z","0","9","8","7","6","5","4","3","2","1");
		for($i=0;$i<256;$i++){
			if (!in_array(strtolower(chr($i)),$accepted))
				$replacement_chars[] = chr($i);
		}
		$result = (str_replace($replacement_chars, $stripe, ($value)));
		while(substr($result, 0, strlen($stripe)) == $stripe)
			$result = substr($result, strlen($stripe), strlen($result) - strlen($stripe));
		while(substr($result, strlen($result) - strlen($stripe), strlen($stripe)) == $stripe)
			$result = substr($result, 0, strlen($result) - strlen($stripe));
		while(strpos($result, $stripe . $stripe))
			$result = str_replace($stripe . $stripe, $stripe, $result);
		return(strtolower($result));
	}
}

if (!function_exists("redirect")) {
	/**
	 * checkEmail()
	 *
	 * @param mixed $email
	 * @return bool|mixed
	 */
	function redirect($url = '', $seconds = 9, $message = '')
	{
		$GLOBALS['url'] = $url;
		$GLOBALS['time'] = $seconds;
		$GLOBALS['message'] = $message;
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'redirect.php';
		exit(-1000);
	}
}

if (!function_exists("checkEmail")) {
	/**
	 * checkEmail()
	 *
	 * @param mixed $email
	 * @return bool|mixed
	 */
	function checkEmail($email)
	{
		if (!$email || !preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
			return false;
		}
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/\=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/\=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
				return false;
			}
		}
		if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
					return false;
				}
			}
		}
		return $email;
	}
}

if (!function_exists("writeRawFile")) {
	function writeRawFile($file = '', $data = '')
	{
		if (!is_dir(dirname($file)))
			mkdir(dirname($file), 0777, true);
		if (is_file($file))
			unlink($file);
		$ff = fopen($file, 'w');
		fwrite($ff, $data, strlen($data));
		fclose($ff);
		if (!strpos($file, 'caches-files-sessioning.serial') && strpos($file, 'serial'))
		{
			if (file_exists(ENTITIES_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial'))
				$sessions = unserialize(file_get_contents(ENTITIES_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial'));
			else
				$sessions = array();
			if (!isset($sessions[basename($file)]))
				$sessions[basename($file)] = array('file' => $file, 'till' =>microtime(true) + mt_rand(3600*24*7.35,3600*24*14*8.75));
			foreach($sessions as $file => $values)
				if ($values['till']<time() && isset($values['till']))
				{
					if (file_exists($values['file']))
						unlink($values['file'])	;
					unset($sessions[$file]);
				}
			writeRawFile(ENTITIES_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial', serialize($sessions));
		}
	}
}

if (!function_exists("getCompleteDirListAsArray")) {
	function getCompleteDirListAsArray($dirname, $result = array())
	{
		foreach(getDirListAsArray($dirname) as $path)
		{
			$result[$dirname . DIRECTORY_SEPARATOR . $path] = $dirname . DIRECTORY_SEPARATOR . $path;
			$result = getCompleteDirListAsArray($dirname . DIRECTORY_SEPARATOR . $path, $result);
		}
		$result[$dirname] = $dirname;
		return $result;
	}
	
}

if (!function_exists("getDirListAsArray")) {
        function getDirListAsArray($dirname)
        {
            $ignored = array(
                'cvs' ,
                '_darcs');
            $list = array();
            if (substr($dirname, - 1) != '/') {
                $dirname .= '/';
            }
            if ($handle = opendir($dirname)) {
                while ($file = readdir($handle)) {
                    if (substr($file, 0, 1) == '.' || in_array(strtolower($file), $ignored))
                        continue;
                    if (is_dir($dirname . $file)) {
                        $list[$file] = $file;
                    }
                }
                closedir($handle);
                asort($list);
                reset($list);
            }

            return $list;
        }
}

if (!function_exists("getFileListAsArray")) {
        function getFileListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if (substr($dirname, - 1) == '/') {
                $dirname = substr($dirname, 0, - 1);
            }
            if (is_dir($dirname) && $handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (! preg_match('/^[\.]{1,2}$/', $file) && is_file($dirname . '/' . $file)) {
                        $file = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }
}

if (!class_exists("XmlDomConstruct")) {
	/**
	 * class XmlDomConstruct
	 *
	 * 	Extends the DOMDocument to implement personal (utility) methods.
	 *
	 * @author 		Simon Roberts (Chronolabs) simon@snails.email
	 */
	class XmlDomConstruct extends DOMDocument {

		/**
		 * Constructs elements and texts from an array or string.
		 * The array can contain an element's name in the index part
		 * and an element's text in the value part.
		 *
		 * It can also creates an xml with the same element tagName on the same
		 * level.
		 *
		 * ex:
		 * <nodes>
		 *   <node>text</node>
		 *   <node>
		 *     <field>hello</field>
		 *     <field>world</field>
		 *   </node>
		 * </nodes>
		 *
		 * Array should then look like:
		 *
		 * Array (
		 *   "nodes" => Array (
		 *     "node" => Array (
		 *       0 => "text"
		 *       1 => Array (
		 *         "field" => Array (
		 *           0 => "hello"
		 *           1 => "world"
		 *         )
		 *       )
		 *     )
		 *   )
		 * )
		 *
		 * @param mixed $mixed An array or string.
		 *
		 * @param DOMElement[optional] $domElement Then element
		 * from where the array will be construct to.
		 *
		 * @author 		Simon Roberts (Chronolabs) simon@snails.email
		 *
		 */
		public function fromMixed($mixed, DOMElement $domElement = null) {

			$domElement = is_null($domElement) ? $this : $domElement;

			if (is_array($mixed)) {
				foreach( $mixed as $index => $mixedElement ) {

					if ( is_int($index) ) {
						if ( $index == 0 ) {
							$node = $domElement;
						} else {
							$node = $this->createElement($domElement->tagName);
							$domElement->parentNode->appendChild($node);
						}
					}

					else {
						$node = $this->createElement($index);
						$domElement->appendChild($node);
					}

					$this->fromMixed($mixedElement, $node);

				}
			} else {
				$domElement->appendChild($this->createTextNode($mixed));
			}

		}
			
	}
}


/**
 * Populates Peer Data
 * @var string
 */
if (!isset($GLOBALS['apiid']))
	$GLOBALS['apiid'] = getAPIIdentity(API_URL, API_VERSION, API_URL_CALLBACK, API_POLINATING, API_URL);

?>
