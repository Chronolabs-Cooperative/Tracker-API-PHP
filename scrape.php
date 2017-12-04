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

if ( !isset($inner['info_hash']) OR (strlen($inner['info_hash']) != 20) )
	err('Invalid hash');
$torrentid = getTorrentIdentity($infohash);
$res = @$GLOBALS['APIDB']->queryF( "SELECT info_hash, seeders, leechers, times_completed FROM `" . $GLOBALS['APIDB']->prefix('torrents') . "` WHERE id = $torrentid");
if( !mysql_num_rows($res) )
	err('No torrent with that hash found');
$benc = 'd5:files';
while ($row = $GLOBALS['APIDB']->fetchArray($res))
{
	$benc .= 'd20:'.pack('H*', $row['info_hash'])."d8:completei{$row['seeders']}e10:downloadedi{$row['times_completed']}e10:incompletei{$row['leechers']}eee";
}
$benc .= 'ed5:flagsd20:min_request_intervali1800eee';

header('Content-Type: text/plain; charset=UTF-8');
header('Pragma: no-cache');
die($benc);
?>