<?php
/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 API Project (www.api.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@api.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frapi.org>
 */

if (!defined('API_INSTALL')) {
    die('API Custom Installation die');
}

$configs = array();

// setup config site info
$configs['db_types'] = array('mysql' => 'mysqli');

// setup config site info
$configs['conf_names'] = array(
);

// languages config files
$configs['language_files'] = array(
    'global');

// extension_loaded
$configs['extensions'] = array(
    'mbstring' => array('MBString', sprintf(PHP_EXTENSION, CHAR_ENCODING)),
    'geoip'     => array('GeoIP', sprintf(PHP_EXTENSION, GEOIP_SUPPORT)),
//  'iconv'    => array('Iconv', sprintf(PHP_EXTENSION, ICONV_CONVERSION)),
    'xml'      => array('XML', sprintf(PHP_EXTENSION, XML_PARSING)),
    'curl'     => array('Curl', sprintf(PHP_EXTENSION, CURL_HTTP)),
);

// Writable files and directories
$configs['writable'] = array(
    'mainfile.php',
    'uploads/',
    'data/',
    'include/',
    'include/license.php',
    'include/dbconfig.php',
    'include/constants.php',
    );


// GeoIP Resource data files default paths
$configs['api'] = array(
    'url_callback' => 'http://'.$_SERVER["HTTP_HOST"],
    'url_tracker'  => 'http://'.$_SERVER["HTTP_HOST"] .':' . $_SERVER["REMOTE_PORT"] . '/announce',
    'root_node' => 'http://tracker.snails.email'
    );

// Modules to be installed by default
$configs['modules'] = array();

// api_lib, api_tmp directories
$configs['apiPathDefault'] = array(
    'lib'  => 'data');

// writable api_lib, api_tmp directories
$configs['tmpPath'] = array(
    'caches'  => __DIR__ . '/caches',
    'includes' => __DIR__ . '/include',
    'tmp'    => '/tmp');
