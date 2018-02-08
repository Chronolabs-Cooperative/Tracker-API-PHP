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

	$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('apis') . "` WHERE `id` LIKE '%s'";
	if ($GLOBALS['APIDB']->getRowsNum($results = $GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($GLOBALS['apiid']))))==1)
	{
		$api = $GLOBALS['APIDB']->fetchArray($results);
	}
	
	$mode = !isset($_REQUEST['mode'])?md5(NULL):$_REQUEST['mode'];
	
	switch ($mode)
	{
		case "register":
			$required = array('api-id', 'api-url', 'version', 'callback', 'polinating');
			foreach($required as $field)
				if (!in_array($field, array_keys($inner)))
					die("Field \$inner[$field] is required to operate this function!");
			
			$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('apis') . "` (`id`, `api-url`, `version`, `callback`, `polinating`, `created`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')";
			if ($GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($inner['api-id']), mysqli_real_escape_string($inner['api-url']), mysqli_real_escape_string($inner['version']), mysqli_real_escape_string($inner['callback']), ($inner['polinating']==true?'Yes':'No'), time())))
			{
				if ($inner['polinating']==true)
				{
					@setCallBackURI(sprintf($inner['callback'], $mode), 145, 145, array('api-id'=>$api['id'], 'api-url' => $api['api-url'], 'version' => $api['version'], 'callback' => $api['callback'], 'polinating' => ($api['polinating']=='Yes'?true:false)));
					if (API_URL === API_ROOT_NODE)
					{
						$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('apis') . "` WHERE `id` NOT LIKE '%s' AND  `id` NOT LIKE '%s' AND `polinating` = 'Yes'";
						if ($GLOBALS['APIDB']->getRowsNum($results = $GLOBALS['APIDB']->queryF(sprintf($sql, mysqli_real_escape_string($GLOBALS['apiid']), mysqli_real_escape_string($inner['id']))))>=1)
						{
							while($other = $GLOBALS['APIDB']->fetchArray($results))
							{
								@setCallBackURI(sprintf($other['callback'], $mode), 145, 145, array('api-id'=>$inner['api-id'], 'api-url' => $inner['api-url'],  'version' => $inner['version'], 'callback' => $inner['callback'], 'polinating' => $inner['polinating']));
							}
						}
					}
				}
				
			}
			break;
		case "peers":
			
			foreach (array("info_hash","peer_id","port","downloaded","uploaded","left") as $x)
				if (!isset($inner['peer-data'][$x]))
					err("Missing key: $x");
	
			foreach (array("info_hash","peer_id") as $x)
				if (strlen($inner['peer-data'][$x]) != 20) {
					err("Invalid $x (" . strlen($inner['peer-data'][$x]) . " - " . urlencode($inner['peer-data'][$x]) . ")");
				}
	
			$ipid = getIPIdentity($inner['ip-addy']);
			$infohash = bin2hex($inner['peer-data']['info_hash']);
			$peerid = bin2hex($inner['peer-data']['peer_id']);
			$port = 0 + $inner['peer-data']['port'];
			$downloaded = 0 + $inner['peer-data']['downloaded'];
			$uploaded = 0 + $inner['peer-data']['uploaded'];
			$left = 0 + $inner['peer-data']['left'];
			$apiid = $inner['api-id'];
			
			$rsize = 50;
			foreach(array("num want", "numwant", "num_want") as $k)
			{
				if (isset($inner['peer-data'][$k]))
				{
					$rsize = 0 + $inner['peer-data'][$k];
					break;
				}
			}
			
			if (!isset($inner['peer-data']['event']))
				$event = "";
			else
				$event = $inner['peer-data']['event'];
	
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

			if($inner['peer-data']['compact'] != 1)
			{
				$compact = 'no';
			} else {
				$compact = 'yes';
			}

			if($inner['peer-data']['supportcrypto'] != 1)
			{
				$crypto = 'no';
			} else
				$crypto = 'yes';

			$updateset = array();
			if ($event == "stopped")
			{
				$GLOBALS['APIDB']->queryF("DELETE FROM `" . $GLOBALS['APIDB']->prefix('peers') . "` WHERE `torrentid` = $torrentid AND `peerid` = '$peerid' AND `apiid` = '$apiid'");
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

					$ret = $GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('peers') . "` (`apiid`,`connectable`, `torrentid`, `peerid`, `ipid`, `port`, `uploaded`, `downloaded`, `left`, `started`, `event`, `seeder`, `agent`, `key`, `compact`, `supportcrypto`) VALUES ('".$GLOBALS['apiid'] ."', '$connectable', $torrentid, '$peerid', '$ipid', $port, $uploaded, $downloaded, $left, ".time().", '$event', '$seeder', '" . mysqli_real_escape_string($agent) . "','" . mysqli_real_escape_string($inner['peer-data']['key']) . "', '$compact', '$crypto')");
					
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

			if ($seeder == "yes")
			{
				if ($torrent["banned"] != "yes")
					$updateset[] = "visible = 'yes'";
					$updateset[] = "last_action = ".time();
			}
			
			if (count($updateset))
				$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('torrents') . "` SET " . implode(",", $updateset) . " WHERE id = $torrentid");
					
		default:
			
			break;
	}
	exit(0);
?>