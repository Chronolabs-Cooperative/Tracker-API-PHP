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

	$infohash = getRandomInfoHash();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta property="og:title" content="<?php echo API_VERSION; ?>"/>
<meta property="og:type" content="api<?php echo API_TYPE; ?>"/>
<meta property="og:image" content="<?php echo API_URL; ?>/assets/images/logo_500x500.png"/>
<meta property="og:url" content="<?php echo (isset($_SERVER["HTTPS"])?"https://":"http://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?>" />
<meta property="og:site_name" content="<?php echo API_VERSION; ?> - <?php echo API_LICENSE_COMPANY; ?>"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="rating" content="general" />
<meta http-equiv="author" content="wishcraft@users.sourceforge.net" />
<meta http-equiv="copyright" content="<?php echo API_LICENSE_COMPANY; ?> &copy; <?php echo date("Y"); ?>" />
<meta http-equiv="generator" content="Chronolabs Cooperative (<?php echo $place['iso3']; ?>)" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo API_VERSION; ?> || <?php echo API_LICENSE_COMPANY; ?></title>
<!-- AddThis Smart Layers BEGIN -->
<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50f9a1c208996c1d"></script>
<script type="text/javascript">
  addthis.layers({
	'theme' : 'transparent',
	'share' : {
	  'position' : 'right',
	  'numPreferredServices' : 6
	}, 
	'follow' : {
	  'services' : [
		{'service': 'facebook', 'id': 'Chronolabs'},
		{'service': 'twitter', 'id': 'JohnRingwould'},
		{'service': 'twitter', 'id': 'ChronolabsCoop'},
		{'service': 'twitter', 'id': 'Cipherhouse'},
		{'service': 'twitter', 'id': 'OpenRend'},
	  ]
	},  
	'whatsnext' : {},  
	'recommended' : {
	  'title': 'Recommended for you:'
	} 
  });
</script>
<!-- AddThis Smart Layers END -->
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/style.css" type="text/css" />
<!-- Custom Fonts -->
<link href="<?php echo API_URL; ?>/assets/media/Labtop/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Bold Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Superwide Boldish/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Thin/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Unicase/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/LHF Matthews Thin/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Life BT Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Life BT Bold Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Normal/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/gradients.php" type="text/css" />
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/shadowing.php" type="text/css" />

</head>
<body>
<div class="main">
	<img style="float: right; margin: 11px; width: auto; height: auto; clear: none;" src="<?php echo API_URL; ?>/assets/images/logo_350x350.png" />
    <h1><?php echo API_VERSION; ?> -- <?php echo API_LICENSE_COMPANY; ?></h1>
    <p style="text-align: justify; font-size: 169.2356897%; font-weight: 400">This is an API Service for providing torrent tracker to your website, just below this is the URL for the tracker to be entered into the torrent to support trackers, it also has a scrape the URL for this torrent tracker is:~</p>
    <p style="text-align: center; font-size: 299.784623%; font-weight: bold"><?php echo API_URL . '/announce'; ?></p>
    <h2>Code API Documentation</h2>
    <p>You can find the phpDocumentor code API documentation at the following path :: <a href="<?php echo API_URL; ?>/docs/" target="_blank"><?php echo API_URL; ?>/docs/</a>. These should outline the source code core functions and classes for the API to function!</p>   
    <h2>Benc Document Output</h2>
    <p>This is done with the <em>benc.api</em> extension at the end of the url, this is for the functions for entities on the API!</p>
    <blockquote>
        <font color="#001201">This is for a list of all torrents for the API</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/torrents/benc.api" target="_blank"><?php echo API_URL; ?>/v2/torrents/benc.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of peers with the torrent bin2hex(InfoHash) benc stripping, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/benc.api" target="_blank"><?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/benc.api</a></strong></em><br /><br />
		<font color="#001201">Produces a list of torrent seed's with the torrent bin2hex(InfoHash) benc stripping, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/benc.api" target="_blank"><?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/benc.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of files with the torrent bin2hex(InfoHash) benc stripping, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/benc.api" target="_blank"><?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/benc.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of trackers with the torrent bin2hex(InfoHash) benc stripping, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/benc.api" target="_blank"><?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/benc.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of IP Address with Network Information associated with the torrent bin2hex(InfoHash) benc stripping, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/benc.api" target="_blank"><?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/benc.api</a></strong></em><br /><br />
	</blockquote>
    <h2>Serialisation Document Output</h2>
    <p>This is done with the <em>serial.api</em> extension at the end of the url, this is for the functions for entities on the API!</p>
    <blockquote>
        <font color="#001201">This is for a list of all torrents for the API</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/torrents/serial.api" target="_blank"><?php echo API_URL; ?>/v2/torrents/serial.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of peers with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/serial.api" target="_blank"><?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/serial.api</a></strong></em><br /><br />
		<font color="#001201">Produces a list of torrent seed's with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/serial.api" target="_blank"><?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/serial.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of files with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/serial.api" target="_blank"><?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/serial.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of trackers with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/serial.api" target="_blank"><?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/serial.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of IP Address with Network Information associated with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/serial.api" target="_blank"><?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/serial.api</a></strong></em><br /><br />
	</blockquote>
    <h2>XML Document Output</h2>
    <p>This is done with the <em>xml.api</em> extension at the end of the url, this is for the functions for entities on the API!</p>
    <blockquote>
        <font color="#001201">This is for a list of all torrents for the API</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/torrents/xml.api" target="_blank"><?php echo API_URL; ?>/v2/torrents/xml.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of peers with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/xml.api" target="_blank"><?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/xml.api</a></strong></em><br /><br />
		<font color="#001201">Produces a list of torrent seed's with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/xml.api" target="_blank"><?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/xml.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of files with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/xml.api" target="_blank"><?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/xml.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of trackers with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/xml.api" target="_blank"><?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/xml.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of IP Address with Network Information associated with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/xml.api" target="_blank"><?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/xml.api</a></strong></em><br /><br />
	</blockquote>
    <h2>JSON Document Output</h2>
    <p>This is done with the <em>json.api</em> extension at the end of the url, you replace the address with either a domain, an IPv4 or IPv6 address the following example is of calls to the api</p>
    <blockquote>
        <font color="#001201">This is for a list of all torrents for the API</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/torrents/json.api" target="_blank"><?php echo API_URL; ?>/v2/torrents/json.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of peers with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/peers/<?php echo $infohash; ?>/json.api</a></strong></em><br /><br />
		<font color="#001201">Produces a list of torrent seed's with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/seeds/<?php echo $infohash; ?>/json.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of files with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/files/<?php echo $infohash; ?>/json.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of trackers with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/trackers/<?php echo $infohash; ?>/json.api</a></strong></em><br /><br />
        <font color="#001201">Produces a list of IP Address with Network Information associated with the torrent bin2hex(InfoHash) serial number, if in the database!</font><br/>
        <em><strong><a href="<?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/network/<?php echo $infohash; ?>/json.api</a></strong></em><br /><br />
	</blockquote>
  	<?php if (file_exists($fionf = __DIR__ .  DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'apis-localhost.html')) {
    	readfile($fionf);
    }?>	
    <?php if (!in_array(whitelistGetIP(true), whitelistGetIPAddy())) { ?>
    <h2>Limits</h2>
    <p>There is a limit of <?php echo MAXIMUM_QUERIES; ?> queries per hour. You can add yourself to the whitelist by using the following form API <a href="http://whitelist.<?php echo domain; ?>/">Whitelisting form (whitelist.<?php echo domain; ?>)</a>. This is only so this service isn't abused!!</p>
    <?php } ?>
    <h2>The Author</h2>
    <p>This was developed by Simon Roberts in 2019 and is part of the Chronolabs System and api's.<br/><br/>This is open source which you can download from <a href="https://github.com/Chronolabs-Cooperative/Tracker-API-PHP">https://github.com/Chronolabs-Cooperative/Tracker-API-PHP/</a> contact the scribe  <a href="mailto:wishcraft@users.sourceforge.net">wishcraft@users.sourceforge.net</a></p></body>
</div>
</html>
<?php 
