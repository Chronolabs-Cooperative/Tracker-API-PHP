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


if ( !isset($_REQUEST['info_hash']) OR (strlen($_REQUEST['info_hash']) != 20) )
	err('Invalid hash');
$torrentid = getTorrentIdentity($infohash);
$res = @$GLOBALS['trackerDB']->queryF( "SELECT info_hash, seeders, leechers, times_completed FROM `torrents` WHERE id = $torrentid");
if( !mysql_num_rows($res) )
	err('No torrent with that hash found');
$benc = 'd5:files';
while ($row = $GLOBALS['trackerDB']->fetchArray($res))
{
	$benc .= 'd20:'.pack('H*', $row['info_hash'])."d8:completei{$row['seeders']}e10:downloadedi{$row['times_completed']}e10:incompletei{$row['leechers']}eee";
}
$benc .= 'ed5:flagsd20:min_request_intervali1800eee';

header('Content-Type: text/plain; charset=UTF-8');
header('Pragma: no-cache');
die($benc);
?>