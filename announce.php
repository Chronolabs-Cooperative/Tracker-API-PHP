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
	if (!isset($_REQUEST[$x])) 
		err("Missing key: $x");

foreach (array("info_hash","peer_id") as $x)
	if (strlen($_REQUEST[$x]) != 20) {
		err("Invalid $x (" . strlen($_REQUEST[$x]) . " - " . urlencode($_REQUEST[$x]) . ")");
	}

$sql = "SELECT * FROM `apis` WHERE `id` NOT LIKE '%s' AND  `id` NOT LIKE '%s' AND `polinating` = 'Yes'";
if ($GLOBALS['trackerDB']->getRowsNum($results = $GLOBALS['trackerDB']->queryF(sprintf($sql, mysql_escape_string($GLOBALS['apiid']))))>=1)
{
	while($other = $GLOBALS['trackerDB']->fetchArray($results))
	{
		@setCallBackURI(sprintf($other['callback'], 'peers'), 145, 145, array('api-id'=>$GLOBALS['apiid'], 'ip-addy' => whitelistGetIP(true),  'peer-data' => $_REQUEST));
	}
}


$infohash = bin2hex($_REQUEST['info_hash']);
$peerid = bin2hex($_REQUEST['peer_id']);
$port = 0 + $_REQUEST['port'];
$downloaded = 0 + $_REQUEST['downloaded'];
$uploaded = 0 + $_REQUEST['uploaded'];
$left = 0 + $_REQUEST['left'];

$rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k)
{
	if (isset($_REQUEST[$k]))
	{
		$rsize = 0 + $_REQUEST[$k];
		break;
	}
}

if (!isset($_REQUEST['event']))
	$event = "";
else 
	$event = $_REQUEST['event'];

$seeder = ($left == 0) ? "yes" : "no";

$torrentid = getTorrentIdentity($infohash);
$res = $GLOBALS['trackerDB']->queryF("SELECT id, seeders + leechers AS numpeers, added AS ts FROM `torrents` WHERE `banned` = 'no' AND id = " . $torrentid);
$torrent = $GLOBALS['trackerDB']->fetchArray($res);
if (!$torrent)
	err("torrent not registered with this tracker!");

$fields = "seeder, peerid, ipid, port, uploaded, downloaded, apiid";

$numpeers = $torrent["numpeers"];
$limit = "";
if ($numpeers > $rsize)
	$limit = "ORDER BY RAND() LIMIT $rsize";
$res = $GLOBALS['trackerDB']->queryF("SELECT $fields FROM `peers` WHERE torrentid = $torrentid AND connectable = 'yes' $limit");

if($_REQUEST['compact'] != 1)
{
	$compact = 'no';
	$resp = "d" . benc_str("interval") . "i" . API_TRACKER_ANNOUNCE_INTERVAL . "e" . benc_str("peers") . "l";
} else {
	$resp = "d" . benc_str("interval") . "i" . API_TRACKER_ANNOUNCE_INTERVAL ."e" . benc_str("min interval") . "i" . 300 ."e5:"."peers" ;
	$compact = 'yes';
}

if($_REQUEST['supportcrypto'] != 1)
{
	$crypto = 'no';
} else 
	$crypto = 'yes';

$peer = array();
$peer_num = 0;
while ($row = $GLOBALS['trackerDB']->fetchArray($res))
{
    if($_REQUEST['compact'] != 1)
    {
		$row["peer_id"] = str_pad($row["peer_id"], 20);
		if ($row["peer_id"] === $peerid)
		{
			$self = $row;
			continue;
		}
		$resp .= "d" .
		benc_str("ip") . benc_str($row["ip"]);
		if (!$_REQUEST['no_peer_id']) {
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
	if($_REQUEST['left'] == 0)
	{
		$time += 128;
	}
	$time = pack("C", $time);
	$peer[] = $time . $peer_ip . $peer_port;
	$peer_num++;
}

if ($_REQUEST['compact']!=1)
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
		$GLOBALS['trackerDB']->queryF("DELETE FROM `peers` WHERE `torrentid` = $torrentid AND `peerid` = '$peerid' AND `apiid` = '$apiid'");
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
		$GLOBALS['trackerDB']->queryF("UPDATE `peers` SET `uploaded` = $uploaded, `downloaded` = $downloaded, `left` = $left, `lastaction` = ".time().", `seeder` = '$seeder'"
			. ($seeder == "yes" && $self["seeder"] != $seeder ? ", finished = " . time() : "") . " WHERE  `torrentid` = $torrentid AND `peerid` = '$peerid' AND `apiid` = '$apiid'");
		if (mysql_affected_rows() && $self["seeder"] != $seeder)
		{
			if ($seeder == "yes")
			{
				$updateset[] = "seeders = seeders + 1";
				$updateset[] = "leechers = leechers - 1";
				$sql = "UPDATE `networking_to_torrents` SET `finished` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['trackerDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
			}
			else
			{
				$sql = "UPDATE `networking_to_torrents` SET `last` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['trackerDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
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
		
		$ret = $GLOBALS['trackerDB']->queryF($sql = "INSERT INTO `peers` (`apiid`,`connectable`, `torrentid`, `peerid`, `ipid`, `port`, `uploaded`, `downloaded`, `left`, `started`, `event`, `seeder`, `agent`, `key`, `compact`, `supportcrypto`) VALUES ('".$GLOBALS['apiid'] ."', '$connectable', $torrentid, '$peerid', '$ipid', $port, $uploaded, $downloaded, $left, ".time().", '$event', '$seeder', '" . mysql_escape_string($agent) . "','" . mysql_escape_string($_REQUEST['key']) . "', '$compact', '$crypto')");
		
		if ($ret)
		{
			if ($seeder == "yes")
			{
				$updateset[] = "seeders = seeders + 1";
				$sql = "UPDATE `networking_to_torrents` SET `finished` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['trackerDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
			} else {
				$updateset[] = "leechers = leechers + 1";
				$sql = "UPDATE `networking_to_torrents` SET `last` = '%s', `left` = '%s' WHERE `ipid` = '%s' AND `torrentid` = '%s'";
				$GLOBALS['trackerDB']->queryF(sprintf($sql, time(),0,$GLOBALS['ipid'],$torrentid));
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
	$GLOBALS['trackerDB']->queryF("UPDATE `torrents` SET " . implode(",", $updateset) . " WHERE id = $torrentid");

benc_resp_raw($resp);


?>
?>