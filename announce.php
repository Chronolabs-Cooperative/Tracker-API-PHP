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
	
global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source, $ipid, $apiid;

require_once 'header.php';

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
$agent = $_SERVER["HTTP_USER_AGENT"];

// Deny access made with a browser...
if (
    ereg("^Mozilla\\/", $agent) || 
    ereg("^Opera\\/", $agent) || 
    ereg("^Links ", $agent) || 
    ereg("^Lynx\\/", $agent) || 
    isset($_SERVER['HTTP_COOKIE']) || 
    isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || 
    isset($_SERVER['HTTP_ACCEPT_CHARSET'])
    )
    err("Agents/HTTP Settings Banned - Tracker Disabled");

/////////////////////// FUNCTION DEFS END ///////////////////////////////

foreach (array("info_hash","peer_id","port","downloaded","uploaded","left") as $x)
	if (!isset($inner[$x])) 
		err("Missing key: $x");

foreach (array("info_hash","peer_id") as $x)
	if (strlen($inner[$x]) != 20) {
		err("Invalid $x (" . strlen($inner[$x]) . " - " . urlencode($inner[$x]) . ")");
	}

$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('apis') . "` WHERE `id` NOT LIKE '%s' AND  `id` NOT LIKE '%s' AND `polinating` = 'Yes'";
if ($GLOBALS['APIDB']->getRowsNum($results = $GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($GLOBALS['apiid']))))>=1)
{
	while($other = $GLOBALS['APIDB']->fetchArray($results))
	{
		@setCallBackURI(sprintf($other['callback'], 'peers'), 145, 145, array('api-id'=>$GLOBALS['apiid'], 'ip-addy' => whitelistGetIP(true),  'peer-data' => $inner));
	}
}


$infohash = bin2hex($inner['info_hash']);
$peerid = bin2hex($inner['peer_id']);
$port = 0 + $inner['port'];
$downloaded = 0 + $inner['downloaded'];
$uploaded = 0 + $inner['uploaded'];
$left = 0 + $inner['left'];

$rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k)
{
	if (isset($inner[$k]))
	{
		$rsize = 0 + $inner[$k];
		break;
	}
}

if (!isset($inner['event']))
	$event = "";
else 
	$event = $inner['event'];

$seeder = ($left == 0) ? "yes" : "no";

$torrentid = getTorrentIdentity($infohash);
$res = $GLOBALS['APIDB']->queryF("SELECT id, seeders + leechers AS numpeers, added AS ts FROM `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE `banned` = 'no' AND id = " . $torrentid);
$torrent = $GLOBALS['APIDB']->fetchArray($res);
if (!$torrent)
	err("torrent not registered with this tracker!");

$fields = "seeder, peerid, ipid, port, uploaded, downloaded, apiid";

$numpeers = $torrent["numpeers"];
$limit = "";
if ($numpeers > $rsize)
	$limit = "ORDER BY RAND() LIMIT $rsize";
$res = $GLOBALS['APIDB']->queryF("SELECT $fields FROM `" . $GLOBALS['APIDB']->prefix('peers') . "` WHERE torrentid = $torrentid AND connectable = 'yes' $limit");

if($inner['compact'] != 1)
{
	$compact = 'no';
	$resp = "d" . benc_str("interval") . "i" . API_TRACKER_ANNOUNCE_INTERVAL . "e" . benc_str("peers") . "l";
} else {
	$resp = "d" . benc_str("interval") . "i" . API_TRACKER_ANNOUNCE_INTERVAL ."e" . benc_str("min interval") . "i" . 300 ."e5:"."peers" ;
	$compact = 'yes';
}

if($inner['supportcrypto'] != 1)
{
	$crypto = 'no';
} else 
	$crypto = 'yes';

$peer = array();
$peer_num = 0;
while ($row = $GLOBALS['APIDB']->fetchArray($res))
{
    if($inner['compact'] != 1)
    {
		$row["peer_id"] = str_pad($row["peer_id"], 20);
		if ($row["peer_id"] === $peerid)
		{
			$self = $row;
			continue;
		}
		$resp .= "d" .
		benc_str("ip") . benc_str($row["ip"]);
		if (!$inner['no_peer_id']) {
			$resp .= benc_str("peer id") . benc_str($row["peer_id"]);
		}
		$resp .= benc_str("port") . "i" . $row["port"] . "e" . "e";
	} else {
		$ipident = getNetworkingArray($row["ipid"]);
		if ($ipident['type']=='ipv4')
		{
			$peer_ip = explode('.', $ipident['ipaddy']);
			$peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
		} else {
			$peer_ip = explode(':', $ipident['ipaddy']);
			$peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3], $peer_ip[4], $peer_ip[5]);
		}
	}
	$peer_port = pack("n*", (int)$row["port"]);
	$time = intval((time() % 7680) / 60);
	if($inner['left'] == 0)
	{
		$time += 128;
	}
	$time = pack("C", $time);
	$peer[] = $time . $peer_ip . $peer_port;
	$peer_num++;
}

if ($inner['compact']!=1)
	$resp .= "ee";
else {
	$o = "";
	for($i=0;$i<$peer_num;$i++)
	{
		$o .= substr($peer[$i], 1, 6);
	}
	$resp .= strlen($o) . ':' . $o . 'e';
}

$updateset = array();
if ($event == "stopped")
{
	if (isset($self))
	{
		$GLOBALS['APIDB']->queryF("DELETE FROM `" . $GLOBALS['APIDB']->prefix('peers') . "` WHERE `torrentid` = $torrentid AND `peerid` = '$peerid' AND `apiid` = '$apiid'");
		if (mysql_affected_rows())
		{
			if ($self["seeder"] == "yes")
				$updateset[] = "seeders = seeders - 1";
			else
				$updateset[] = "leechers = leechers - 1";
		}
	}
}
else
{
	if ($event == "completed")
		$updateset[] = "times_completed = times_completed + 1";

	if (isset($self))
	{
		$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('peers') . "` SET `uploaded` = $uploaded, `downloaded` = $downloaded, `left` = $left, `lastaction` = ".time().", `seeder` = '$seeder'"
			. ($seeder == "yes" && $self["seeder"] != $seeder ? ", finished = " . time() : "") . " WHERE  `torrentid` = $torrentid AND `peerid` = '$peerid' AND `apiid` = '$apiid'");
		if (mysql_affected_rows() && $self["seeder"] != $seeder)
		{
			if ($seeder == "yes")
			{
				$updateset[] = "seeders = seeders + 1";
				$updateset[] = "leechers = leechers - 1";
				$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('networking_to_torrents') . "` SET `finished` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['APIDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
			}
			else
			{
				$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('networking_to_torrents') . "` SET `last` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['APIDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
				$updateset[] = "seeders = seeders - 1";
				$updateset[] = "leechers = leechers + 1";
			}
		}
		
	}
	else
	{
		if (portblacklisted($port))
		{
			err("Port $port is blacklisted.");
		}
		$connectable = 'yes';
		$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
		if (!$sockres)
			$connectable = "no";
		else
		{
			$connectable = "yes";
			@fclose($sockres);
		}
		
		$ret = $GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('peers') . "` (`apiid`,`connectable`, `torrentid`, `peerid`, `ipid`, `port`, `uploaded`, `downloaded`, `left`, `started`, `event`, `seeder`, `agent`, `key`, `compact`, `supportcrypto`) VALUES ('".$GLOBALS['apiid'] ."', '$connectable', $torrentid, '$peerid', '$ipid', $port, $uploaded, $downloaded, $left, ".time().", '$event', '$seeder', '" . mysqli_real_escape_string($agent) . "','" . mysqli_real_escape_string($inner['key']) . "', '$compact', '$crypto')");
		
		if ($ret)
		{
			if ($seeder == "yes")
			{
				$updateset[] = "seeders = seeders + 1";
				$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('networking_to_torrents') . "` SET `finished` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['APIDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
			} else {
				$updateset[] = "leechers = leechers + 1";
				$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('networking_to_torrents') . "` SET `last` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['APIDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
			}
		}
	}
}

if ($seeder == "yes")
{
	if ($torrent["banned"] != "yes")
		$updateset[] = "visible = 'yes'";
	$updateset[] = "last_action = ".time();
}

if (count($updateset))
	$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('torrents') . "` SET " . implode(",", $updateset) . " WHERE id = $torrentid");

benc_resp_raw($resp);


?>
?>