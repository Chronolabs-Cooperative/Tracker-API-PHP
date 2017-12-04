<?php
/**
 * API legacy logger
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          logger
 * @since               2.0.0
 * @deprecated
 */

defined('API_ROOT_PATH') || exit('Restricted access');

/**
 * this file is for backward compatibility only
 * @package    kernel
 * @subpackage logger
 **/
/**
 * Load the new APILogger class
 **/
require_once $GLOBALS['api']->path('class/logger/apilogger.php');
trigger_error('Instance of ' . __FILE__ . " file is deprecated, check 'APILogger' in class/logger/apilogger.php");
