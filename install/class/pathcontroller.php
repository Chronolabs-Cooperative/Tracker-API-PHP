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
 **/
class PathStuffController
{
    public $apiPath = array(
        'root' => '',
        'lib'  => '',
        'tmp' => '');

    public $apiPathDefault = array(
        'lib'  => 'tmp',
        'tmp' => 'caches');

    public $tmpPath = array(
        'caches' => array(
            'api_cache'),
        'configs');

    public $path_lookup = array(
        'root' => 'ROOT_PATH',
        'tmp' => 'VAR_PATH',
        'lib'  => 'PATH');

    public $apiUrl = '';
    public $apiCookieDomain = '';

    public $validPath = array(
        'root' => 0,
        'tmp' => 0,
        'lib'  => 0);

    public $validUrl = false;

    public $permErrors = array(
        'root' => null,
        'tmp' => null);

    /**
     * @param $apiPathDefault
     * @param $tmpPath
     */
    public function __construct($apiPathDefault, $tmpPath)
    {
        $this->apiPathDefault = $apiPathDefault;
        $this->tmpPath         = $tmpPath;

        if (isset($_SESSION['settings']['ROOT_PATH'])) {
            foreach ($this->path_lookup as $req => $sess) {
                $this->apiPath[$req] = $_SESSION['settings'][$sess];
            }
        } else {
            $path = str_replace("\\", '/', realpath('../'));
            if (substr($path, -1) === '/') {
                $path = substr($path, 0, -1);
            }
            if (file_exists("$path/apiconfig.php")) {
                $this->apiPath['root'] = $path;
            }
            // Firstly, locate API lib folder out of API root folder
            $this->apiPath['lib'] = dirname($path) . '/' . $this->apiPathDefault['lib'];
            // If the folder is not created, re-locate API lib folder inside API root folder
            if (!is_dir($this->apiPath['lib'] . '/')) {
                $this->apiPath['lib'] = $path . '/' . $this->apiPathDefault['lib'];
            }
            // Firstly, locate API tmp folder out of API root folder
            $this->apiPath['tmp'] = '/tmp';
            // If the folder is not created, re-locate API tmp folder inside API root folder
            if (!is_dir($this->apiPath['tmp'] . '/')) {
                $this->apiPath['tmp'] = $path . '/' . $this->apiPathDefault['tmp'];
            }
        }
        if (isset($_SESSION['settings']['URL'])) {
            $this->apiUrl = $_SESSION['settings']['URL'];
        } else {
            $path           = $GLOBALS['wizard']->baseLocation();
            $this->apiUrl = substr($path, 0, strrpos($path, '/'));
        }
        if (isset($_SESSION['settings']['COOKIE_DOMAIN'])) {
            $this->apiCookieDomain = $_SESSION['settings']['COOKIE_DOMAIN'];
        } else {
            $this->apiCookieDomain = api_getBaseDomain($this->apiUrl);
        }
    }

    public function execute()
    {
        $this->readRequest();
        $valid = $this->validate();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($this->path_lookup as $req => $sess) {
                $_SESSION['settings'][$sess] = $this->apiPath[$req];
            }
            $_SESSION['settings']['URL'] = $this->apiUrl;
            $_SESSION['settings']['COOKIE_DOMAIN'] = $this->apiCookieDomain;
            if ($valid) {
                $GLOBALS['wizard']->redirectToPage('+1');
            } else {
                $GLOBALS['wizard']->redirectToPage('+0');
            }
        }
    }

    public function readRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = $_POST;
            foreach ($this->path_lookup as $req => $sess) {
                if (isset($request[$req])) {
                    $request[$req] = str_replace("\\", '/', trim($request[$req]));
                    if (substr($request[$req], -1) === '/') {
                        $request[$req] = substr($request[$req], 0, -1);
                    }
                    $this->apiPath[$req] = $request[$req];
                }
            }
            if (isset($request['URL'])) {
                $request['URL'] = trim($request['URL']);
                if (substr($request['URL'], -1) === '/') {
                    $request['URL'] = substr($request['URL'], 0, -1);
                }
                $this->apiUrl = $request['URL'];
            }
            if (isset($request['COOKIE_DOMAIN'])) {
                $tempCookieDomain = trim($request['COOKIE_DOMAIN']);
                $tempParts = parse_url($tempCookieDomain);
                if (!empty($tempParts['host'])) {
                    $tempCookieDomain = $tempParts['host'];
                }
                $request['COOKIE_DOMAIN'] = $tempCookieDomain;
                $this->apiCookieDomain = $tempCookieDomain;;
            }
        }
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach (array_keys($this->apiPath) as $path) {
            if ($this->checkPath($path)) {
                $this->checkPermissions($path);
            }
        }
        $this->validUrl = !empty($this->apiUrl);
        $validPaths     = (array_sum(array_values($this->validPath)) == count(array_keys($this->validPath))) ? 1 : 0;
        $validPerms     = true;
        foreach ($this->permErrors as $key => $errs) {
            if (empty($errs)) {
                continue;
            }
            foreach ($errs as $path => $status) {
                if (empty($status)) {
                    $validPerms = false;
                    break;
                }
            }
        }

        return ($validPaths && $this->validUrl && $validPerms);
    }

    /**
     * @param string $PATH
     *
     * @return int
     */
    public function checkPath($PATH = '')
    {
        $ret = 1;
        if ($PATH === 'root' || empty($PATH)) {
            $path = 'root';
            if (is_dir($this->apiPath[$path]) && is_readable($this->apiPath[$path])) {
                @include_once "{$this->apiPath[$path]}/include/version.php";
                if (file_exists("{$this->apiPath[$path]}/apiconfig.php") && defined('API_VERSION')) {
                    $this->validPath[$path] = 1;
                }
            }
            $ret *= $this->validPath[$path];
        }
        if ($PATH === 'lib' || empty($PATH)) {
            $path = 'lib';
            if (is_dir($this->apiPath[$path]) && is_readable($this->apiPath[$path])) {
                $this->validPath[$path] = 1;
            }
            $ret *= $this->validPath[$path];
        }
        if ($PATH === 'tmp' || empty($PATH)) {
            $path = 'tmp';
            if (is_dir($this->apiPath[$path]) && is_readable($this->apiPath[$path])) {
                $this->validPath[$path] = 1;
            }
            $ret *= $this->validPath[$path];
        }

        return $ret;
    }

    /**
     * @param $parent
     * @param $path
     * @param $error
     * @return null
     */
    public function setPermission($parent, $path, &$error)
    {
        if (is_array($path)) {
            foreach (array_keys($path) as $item) {
                if (is_string($item)) {
                    $error[$parent . '/' . $item] = $this->makeWritable($parent . '/' . $item);
                    if (empty($path[$item])) {
                        continue;
                    }
                    foreach ($path[$item] as $child) {
                        $this->setPermission($parent . '/' . $item, $child, $error);
                    }
                } else {
                    $error[$parent . '/' . $path[$item]] = $this->makeWritable($parent . '/' . $path[$item]);
                }
            }
        } else {
            $error[$parent . '/' . $path] = $this->makeWritable($parent . '/' . $path);
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function checkPermissions($path)
    {
        $paths  = array(
            'root' => array('mainfile.php','owner.php','dbconfig.php'),
            'tmp' => $this->tmpPath);
        $errors = array(
            'root' => null,
            'tmp' => null);

        if (!isset($this->apiPath[$path])) {
            return false;
        }
        if (!isset($errors[$path])) {
            return true;
        }
        $this->setPermission($this->apiPath[$path], $paths[$path], $errors[$path]);
        if (in_array(false, $errors[$path])) {
            $this->permErrors[$path] = $errors[$path];
        }

        return true;
    }

    /**
     * Write-enable the specified folder
     *
     * @param string $path
     * @param bool   $create
     *
     * @internal param bool $recurse
     * @return false on failure, method (u-ser,g-roup,w-orld) on success
     */
    public function makeWritable($path, $create = true)
    {
        $mode = intval('0777', 8);
        if (!file_exists($path)) {
            if (!$create) {
                return false;
            } else {
                mkdir($path, $mode);
            }
        }
        if (!is_writable($path)) {
            chmod($path, $mode);
        }
        clearstatcache();
        if (is_writable($path)) {
            $info = stat($path);
            if ($info['mode'] & 0002) {
                return 'w';
            } elseif ($info['mode'] & 0020) {
                return 'g';
            }

            return 'u';
        }

        return false;
    }
}
