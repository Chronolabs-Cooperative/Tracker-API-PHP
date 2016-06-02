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


	/**
	 *
	 * @var string
	 */
	define('API_VERSION', '2.0.4');
	define('API_URL', 'http://tracker.labs.coop');
	define('API_URL_CALLBACK', 'http://tracker.labs.coop/v2/%s/callback.api');
	define('API_URL_TRACKER', 'http://tracker.labs.coop:80/announce');
	define('API_USER_AGENT', 'Chronolabs Torrent Tracker API + Robot ~ Version '.API_VERSION.' ~ Auto Mounting Torrent Tracker (PHP ' . PHP_VERSION . ')');
	define('API_POLINATING', (strpos(API_URL, 'localhost')?false:true));
	
	/**
	 * 
	 * @var string
	 */
	
	/******* DO NOT CHANGE THIS VARIABLE ****
	 * @var string
	 */
	define('API_ROOT_NODE', 'http://tracker.labs.coop');
	
?>