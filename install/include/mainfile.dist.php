<?php
/**
 * API main configuration file
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
 */

if (!defined('API_MAINFILE_INCLUDED')) {
    define('API_MAINFILE_INCLUDED', 1);

    // API Physical Paths

    // Physical path to the API documents (served) directory WITHOUT trailing slash
    define('API_ROOT_PATH', '');

    // For forward compatibility
    // Physical path to the API library directory WITHOUT trailing slash
    define('API_PATH', '');
    
    // Physical path to the API datafiles (writable) directory WITHOUT trailing slash
    define('API_VAR_PATH', '');
    
    // Alias of API_PATH, for compatibility, temporary solution
    define('API_TRUST_PATH', API_PATH);

    // URL Association for SSL and Protocol Compatibility
    $http = 'http://';
    if (!empty($_SERVER['HTTPS'])) {
        $http = ($_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    }
    define('API_PROT', $http);

    // API Virtual Path (URL)
    // Virtual path to your main API directory WITHOUT trailing slash
    // Example: define("API_URL", "http://url_to_API_directory");
    define('API_URL', 'http://');

    // API Cookie Domain to specify when creating cookies. May be blank (i.e. for IP address host),
    // full host from API_URL (i.e. www.example.com) or just the registered domain (i.e. example.com)
    // to share cookies across multiple subdomains (i.e. www.example.com and blog.example.com)
    define('API_COOKIE_DOMAIN', '');

    // Shall be handled later, don't forget!
    define('API_CHECK_PATH', 0);
    
    // Protect against external scripts execution if safe mode is not enabled
    if (API_CHECK_PATH && !@ini_get('safe_mode')) {
        if (function_exists('debug_backtrace')) {
            $APIScriptPath = debug_backtrace();
            if (!count($APIScriptPath)) {
                die('API path check: this file cannot be requested directly');
            }
            $APIScriptPath = $APIScriptPath[0]['file'];
        } else {
            $APIScriptPath = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            // IIS6 may double the \ chars
            $APIScriptPath = str_replace(strpos($APIScriptPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $APIScriptPath);
        }
        if (strcasecmp(substr($APIScriptPath, 0, strlen(API_ROOT_PATH)), str_replace(DIRECTORY_SEPARATOR, '/', API_ROOT_PATH))) {
            exit('API path check: Script is not inside API_ROOT_PATH and cannot run.');
        }
    }

    // Secure file
    require API_ROOT_PATH . '/include/dbconfig.php';

    if (!isset($APIOption['nocommon']) && API_ROOT_PATH != '') {
        include API_ROOT_PATH . '/include/common.php';
    }
}
