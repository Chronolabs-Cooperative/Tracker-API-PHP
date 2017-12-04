<?php
/**
 * API common initialization file
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 API Project (www.API.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 */
defined('API_MAINFILE_INCLUDED') || die('Restricted access');

/**
 * YOU SHOULD NEVER USE THE FOLLOWING TO CONSTANTS, THEY WILL BE REMOVED
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('NWLINE') or define('NWLINE', "\n");

/**
 * Include files with definitions
 */
include_once __DIR__ . DS . 'functions.php';
include_once __DIR__ . DS . 'version.php';
include_once __DIR__ . DS . 'license.php';
include_once __DIR__ . DS . 'constants.php';

/**
 * Include APILoad
 */
require_once API_ROOT_PATH . DS . 'class' . DS . 'apiload.php';
require_once API_ROOT_PATH . DS . 'class' . DS . 'preload.php';

/**
 * Create Instance of apiSecurity Object and check Supergolbals
 */
APILoad::load('apisecurity');
$apiSecurity = new APISecurity();
$apiSecurity->checkSuperglobals();

/**
 * Create Instantance APILogger Object
 */
APILoad::load('apilogger');
$apiLogger       = APILogger::getInstance();
$apiErrorHandler = APILogger::getInstance();
$apiLogger->startTime();
$apiLogger->startTime('XOOPS Boot');

/**
 * Include Required Files
 */
include_once API_ROOT_PATH . DS . 'class' . DS . 'criteria.php';
include_once API_ROOT_PATH . DS . 'class' . DS . 'module.textsanitizer.php';
include_once API_ROOT_PATH . DS . 'include' . DS . 'functions.php';
/**
 * Get database for making it global
 * Requires APILogger, API_DB_PROXY;
 */
require_once API_ROOT_PATH . DS . 'include' . DS . 'dbconfig.php';
require_once API_ROOT_PATH . DS . 'class' . DS . 'database' . DS . 'databasefactory.php';
$GLOBALS['APIDB'] = APIDatabaseFactory::getDatabaseConnection();
