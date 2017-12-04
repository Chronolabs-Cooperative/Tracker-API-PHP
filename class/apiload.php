<?php
/**
 * API Autoload class
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
 * @package             class
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @todo                For PHP 5 compliant
 */
defined('API_ROOT_PATH') || exit('Restricted access');

/**
 * Class APILoad
 */
class APILoad
{
    //static  $loaded;
    //static  $configs;

    /**
     * @param        $name
     * @param string $type
     *
     * @return bool
     */
    public static function load($name, $type = 'core')
    {
        static $loaded;
        static $deprecated;

        if (!isset($deprecated)) {
            $deprecated = array(
                'uploader'    => 'apimediauploader',
                'utility'     => 'apiutility',
                'captcha'     => 'apicaptcha',
                'cache'       => 'apicache',
                'file'        => 'apifile',
                'model'       => 'apimodelfactory',
                'calendar'    => 'apicalendar',
                'userutility' => 'apiuserutility');
        }
        $name = strtolower($name);
        if (in_array($type, array('core', 'class')) && array_key_exists($name, $deprecated)) {
            if (isset($GLOBALS['apiLogger'])) {
                $GLOBALS['apiLogger']->addDeprecated("api_load('{$name}') is deprecated, use api_load('{$deprecated[$name]}')");
            } else {
                trigger_error("api_load('{$name}') is deprecated, use api_load('{$deprecated[$name]}')", E_USER_WARNING);
            }
            $name = $deprecated[$name];
        }

        $type = empty($type) ? 'core' : $type;
        if (isset($loaded[$type][$name])) {
            return $loaded[$type][$name];
        }

        if (class_exists($name, false)) {
            $loaded[$type][$name] = true;

            return true;
        }
        $isloaded = false;
        switch ($type) {
            case 'framework':
                $isloaded = APILoad::loadFramework($name);
                break;
            case 'class':
            case 'core':
                $type     = 'core';
                $isloaded = APILoad::loadCore($name);
                break;
            default:
                $isloaded = APILoad::loadModule($name, $type);
                break;
        }
        $loaded[$type][$name] = $isloaded;

        return $loaded[$type][$name];
    }

    /**
     * Load core class
     *
     * @access private
     * @param $name
     * @return bool|string
     */
    public static function loadCore($name)
    {
        static $configs;

        if (!isset($configs)) {
            $configs = APILoad::loadCoreConfig();
        }
        if (isset($configs[$name])) {
            require_once $configs[$name];
            if (class_exists($name) && method_exists($name, '__autoload')) {
                call_user_func(array($name, '__autoload'));
            }

            return true;
        } elseif (file_exists($file = API_ROOT_PATH . '/class/' . $name . '.php')) {
            include_once $file;
            $class = 'API' . ucfirst($name);
            if (class_exists($class)) {
                return $class;
            } else {
                trigger_error('Class ' . $name . ' not found in file ' . __FILE__ . 'at line ' . __LINE__, E_USER_WARNING);
            }
        }

        return false;
    }

    /**
     * Load Framework class
     *
     * @access private
     * @param $name
     * @return bool|string
     */
    public static function loadFramework($name)
    {
        if (!file_exists($file = API_ROOT_PATH . '/Frameworks/' . $name . '/api' . $name . '.php')) {
            trigger_error('File ' . str_replace(API_ROOT_PATH, '', $file) . ' not found in file ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);

            return false;
        }
        include_once $file;
        $class = 'API' . ucfirst($name);
        if (class_exists($class)) {
            return $class;
        }
        return null;
    }

    /**
     * Load module class
     *
     * @access private
     * @param  string      $name    class file name
     * @param  string|null $dirname module directory name
     * @return bool
     */
    public static function loadModule($name, $dirname = null)
    {
        if (empty($dirname)) {
            return false;
        }
        if (file_exists($file = API_ROOT_PATH . '/modules/' . $dirname . '/class/' . $name . '.php')) {
            include_once $file;
            if (class_exists(ucfirst($dirname) . ucfirst($name))) {
                return true;
            }
        }

        return false;
    }

    /**
     * APILoad::loadCoreConfig()
     *
     * @return array
     */
    public static function loadCoreConfig()
    {
        return $configs = array(
            'apiuserutility'           => API_ROOT_PATH . '/class/userutility.php',
            'apimediauploader'         => API_ROOT_PATH . '/class/uploader.php',
            'apiutility'               => API_ROOT_PATH . '/class/utility/apiutility.php',
            'apicaptcha'               => API_ROOT_PATH . '/class/captcha/apicaptcha.php',
            'apicache'                 => API_ROOT_PATH . '/class/cache/apicache.php',
            'apifile'                  => API_ROOT_PATH . '/class/file/apifile.php',
            'apimodelfactory'          => API_ROOT_PATH . '/class/model/apimodel.php',
            'apicalendar'              => API_ROOT_PATH . '/class/calendar/apicalendar.php',
            'apikernel'                => API_ROOT_PATH . '/class/apikernel.php',
            'apisecurity'              => API_ROOT_PATH . '/class/apisecurity.php',
            'apilogger'                => API_ROOT_PATH . '/class/logger/apilogger.php',
            'apipagenav'               => API_ROOT_PATH . '/class/pagenav.php',
            'apilists'                 => API_ROOT_PATH . '/class/apilists.php',
            'apilocal'                 => API_ROOT_PATH . '/include/apilocal.php',
            'apilocalabstract'         => API_ROOT_PATH . '/class/apilocal.php',
            'apieditor'                => API_ROOT_PATH . '/class/apieditor/apieditor.php',
            'apieditorhandler'         => API_ROOT_PATH . '/class/apieditor/apieditor.php',
            'apiformloader'            => API_ROOT_PATH . '/class/apiformloader.php',
            'apiformelement'           => API_ROOT_PATH . '/class/apiform/formelement.php',
            'apiform'                  => API_ROOT_PATH . '/class/apiform/form.php',
            'apiformlabel'             => API_ROOT_PATH . '/class/apiform/formlabel.php',
            'apiformselect'            => API_ROOT_PATH . '/class/apiform/formselect.php',
            'apiformpassword'          => API_ROOT_PATH . '/class/apiform/formpassword.php',
            'apiformbutton'            => API_ROOT_PATH . '/class/apiform/formbutton.php',
            'apiformbuttontray'        => API_ROOT_PATH . '/class/apiform/formbuttontray.php',
            'apiformcheckbox'          => API_ROOT_PATH . '/class/apiform/formcheckbox.php',
            'apiformselectcheckgroup'  => API_ROOT_PATH . '/class/apiform/formselectcheckgroup.php',
            'apiformhidden'            => API_ROOT_PATH . '/class/apiform/formhidden.php',
            'apiformfile'              => API_ROOT_PATH . '/class/apiform/formfile.php',
            'apiformradio'             => API_ROOT_PATH . '/class/apiform/formradio.php',
            'apiformradioyn'           => API_ROOT_PATH . '/class/apiform/formradioyn.php',
            'apiformselectcountry'     => API_ROOT_PATH . '/class/apiform/formselectcountry.php',
            'apiformselecttimezone'    => API_ROOT_PATH . '/class/apiform/formselecttimezone.php',
            'apiformselectlang'        => API_ROOT_PATH . '/class/apiform/formselectlang.php',
            'apiformselectgroup'       => API_ROOT_PATH . '/class/apiform/formselectgroup.php',
            'apiformselectuser'        => API_ROOT_PATH . '/class/apiform/formselectuser.php',
            'apiformselecttheme'       => API_ROOT_PATH . '/class/apiform/formselecttheme.php',
            'apiformselectmatchoption' => API_ROOT_PATH . '/class/apiform/formselectmatchoption.php',
            'apiformtext'              => API_ROOT_PATH . '/class/apiform/formtext.php',
            'apiformtextarea'          => API_ROOT_PATH . '/class/apiform/formtextarea.php',
            'apiformdhtmltextarea'     => API_ROOT_PATH . '/class/apiform/formdhtmltextarea.php',
            'apiformelementtray'       => API_ROOT_PATH . '/class/apiform/formelementtray.php',
            'apithemeform'             => API_ROOT_PATH . '/class/apiform/themeform.php',
            'apisimpleform'            => API_ROOT_PATH . '/class/apiform/simpleform.php',
            'apiformtextdateselect'    => API_ROOT_PATH . '/class/apiform/formtextdateselect.php',
            'apiformdatetime'          => API_ROOT_PATH . '/class/apiform/formdatetime.php',
            'apiformhiddentoken'       => API_ROOT_PATH . '/class/apiform/formhiddentoken.php',
            'apiformcolorpicker'       => API_ROOT_PATH . '/class/apiform/formcolorpicker.php',
            'apiformcaptcha'           => API_ROOT_PATH . '/class/apiform/formcaptcha.php',
            'apiformeditor'            => API_ROOT_PATH . '/class/apiform/formeditor.php',
            'apiformselecteditor'      => API_ROOT_PATH . '/class/apiform/formselecteditor.php',
            'apiformcalendar'          => API_ROOT_PATH . '/class/apiform/formcalendar.php',
            'apiformrenderer'          => API_ROOT_PATH . '/class/apiform/renderer/APIFormRenderer.php',
            'apiformrendererinterface' => API_ROOT_PATH . '/class/apiform/renderer/APIFormRendererInterface.php',
            'apiformrendererlegacy'    => API_ROOT_PATH . '/class/apiform/renderer/APIFormRendererLegacy.php',
            'apiformrendererbootstrap3'=> API_ROOT_PATH . '/class/apiform/renderer/APIFormRendererBootstrap3.php',
            'apifilterinput'           => API_ROOT_PATH . '/class/apifilterinput.php',
            'apirequest'               => API_ROOT_PATH . '/class/apirequest.php');
    }

    /**
     * APILoad::loadConfig()
     *
     * @param mixed $data
     *
     * @return array|bool
     */
    public function loadConfig($data = null)
    {
        if (is_array($data)) {
            $configs = $data;
        } else {
            if (!empty($data)) {
                $dirname = $data;
            } elseif (is_object($GLOBALS['apiModule'])) {
                $dirname = $GLOBALS['apiModule']->getVar('dirname', 'n');
            } else {
                return false;
            }
            if (file_exists($file = API_ROOT_PATH . '/modules/' . $dirname . '/include/autoload.php')) {
                if (!$configs = include $file) {
                    return false;
                }
            }
        }

        return $configs = array_merge(APILoad::loadCoreConfig(), $configs);
    }
}
// To be enabled in API 3.0
// spl_autoload_register(array('APILoad', 'load'));

/**
 * XMF libraries
 */
include_once API_ROOT_PATH . '/class/libraries/vendor/autoload.php';
