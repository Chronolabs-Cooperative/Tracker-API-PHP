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
 * @subpackage			api
 * @description			Torrent Tracker REST API
 * @link				http://sourceforge.net/projects/chronolabsapis
 * @link				http://cipher.labs.coop
 */


	require_once  __DIR__ . DIRECTORY_SEPARATOR . "header.php";

	$sql = "SELECT * FROM `apis` WHERE `id` LIKE '%s'";
	if ($GLOBALS['trackerDB']->getRowsNum($results = $GLOBALS['trackerDB']->queryF(sprintf($sql, mysql_escape_string($GLOBALS['apiid']))))==1)
	{
		$api = $GLOBALS['trackerDB']->fetchArray($results);
	}
	
	$mode = !isset($_REQUEST['mode'])?md5(NULL):$_REQUEST['mode'];
	
	switch ($mode)
	{
		case "register":
			$required = array('api-id', 'api-url', 'version', 'callback', 'polinating');
			foreach($required as $field)
				if (!in_array($field, array_keys($_POST)))
					die("Field \$_POST[$field] is required to operate this function!");
			
			$sql = "INSERT INTO `apis` (`id`, `api-url`, `version`, `callback`, `polinating`, `created`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')";
			if ($GLOBALS['trackerDB']->queryF(sprintf($sql, mysql_escape_string($_POST['api-id']), mysql_escape_string($_POST['api-url']), mysql_escape_string($_POST['version']), mysql_escape_string($_POST['callback']), ($_POST['polinating']==true?'Yes':'No'), time())))
			{
				if ($_POST['polinating']==true)
				{
					@setCallBackURI(sprintf($_POST['callback'], $mode), 145, 145, array('api-id'=>$api['id'], 'api-url' => $api['api-url'], 'version' => $api['version'], 'callback' => $api['callback'], 'polinating' => ($api['polinating']=='Yes'?true:false)));
					if (API_URL === API_ROOT_NODE)
					{
						$sql = "SELECT * FROM `apis` WHERE `id` NOT LIKE '%s' AND  `id` NOT LIKE '%s' AND `polinating` = 'Yes'";
						if ($GLOBALS['trackerDB']->getRowsNum($results = $GLOBALS['trackerDB']->queryF(sprintf($sql, mysql_escape_string($GLOBALS['apiid']), mysql_escape_string($_POST['id']))))>=1)
						{
							while($other = $GLOBALS['trackerDB']->fetchArray($results))
							{
								@setCallBackURI(sprintf($other['callback'], $mode), 145, 145, array('api-id'=>$_POST['api-id'], 'api-url' => $_POST['api-url'],  'version' => $_POST['version'], 'callback' => $_POST['callback'], 'polinating' => $_POST['polinating']));
							}
						}
					}
				}
				
			}
			break;
		case "peers":
			
			foreach (array("info_hash","peer_id","port","downloaded","uploaded","left") as $x)
				if (!isset($_POST['peer-data'][$x]))
					err("Missing key: $x");
	
			foreach (array("info_hash","peer_id") as $x)
				if (strlen($_POST['peer-data'][$x]) != 20) {
					err("Invalid $x (" . strlen($_POST['peer-data'][$x]) . " - " . urlencode($_POST['peer-data'][$x]) . ")");
				}
	
			$ipid = getIPIdentity($_POST['ip-addy']);
			$infohash = bin2hex($_POST['peer-data']['info_hash']);
			$peerid = bin2hex($_POST['peer-data']['peer_id']);
			$port = 0 + $_POST['peer-data']['port'];
			$downloaded = 0 + $_POST['peer-data']['downloaded'];
			$uploaded = 0 + $_POST['peer-data']['uploaded'];
			$left = 0 + $_POST['peer-data']['left'];
			$apiid = $_POST['api-id'];
			
			$rsize = 50;
			foreach(array("num want", "numwant", "num_want") as $k)
			{
				if (isset($_POST['peer-data'][$k]))
				{
					$rsize = 0 + $_POST['peer-data'][$k];
					break;
				}
			}
			
			if (!isset($_POST['peer-data']['event']))
				$event = "";
			else
				$event = $_POST['peer-data']['event'];
	
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

			if($_POST['peer-data']['compact'] != 1)
			{
				$compact = 'no';
			} else {
				$compact = 'yes';
			}

			if($_POST['peer-data']['supportcrypto'] != 1)
			{
				$crypto = 'no';
			} else
				$crypto = 'yes';

			$updateset = array();
			if ($event == "stopped")
			{
				$GLOBALS['trackerDB']->queryF("DELETE FROM `peers` WHERE `torrentid` = $torrentid AND `peerid` = '$peerid' AND `apiid` = '$apiid'");
				if (mysql_affected_rows())
				{
					if ($self["seeder"] == "yes")
						$updateset[] = "seeders = seeders - 1";
						else
							$updateset[] = "leechers = leechers - 1";
				}
			} else {
				if ($event == "completed")
					$updateset[] = "times_completed = times_completed + 1";

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

					$ret = $GLOBALS['trackerDB']->queryF($sql = "INSERT INTO `peers` (`apiid`,`connectable`, `torrentid`, `peerid`, `ipid`, `port`, `uploaded`, `downloaded`, `left`, `started`, `event`, `seeder`, `agent`, `key`, `compact`, `supportcrypto`) VALUES ('".$GLOBALS['apiid'] ."', '$connectable', $torrentid, '$peerid', '$ipid', $port, $uploaded, $downloaded, $left, ".time().", '$event', '$seeder', '" . mysql_escape_string($agent) . "','" . mysql_escape_string($_POST['peer-data']['key']) . "', '$compact', '$crypto')");
					
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

			if ($seeder == "yes")
			{
				if ($torrent["banned"] != "yes")
					$updateset[] = "visible = 'yes'";
					$updateset[] = "last_action = ".time();
			}
			
			if (count($updateset))
				$GLOBALS['trackerDB']->queryF("UPDATE `torrents` SET " . implode(",", $updateset) . " WHERE id = $torrentid");
					
		default:
			
			break;
	}
	exit(0);
?>