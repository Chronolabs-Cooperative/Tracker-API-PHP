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
 * @param string $hash
 * @return bool
 */

function install_acceptUser($hash = '')
{
    $GLOBALS['apiUser'] = null;
    $assertClaims = array(
        'sub' => 'apiinstall',
    );
    if (false === $claims || empty($claims->uname)) {
        return false;
    }
    $uname = $claims->uname;
    /* @var $memberHandler APIMemberHandler */
    $memberHandler = api_getHandler('member');
    $user = array_pop($memberHandler->getUsers(new Criteria('uname', $uname)));

    if (is_object($GLOBALS['api']) && method_exists($GLOBALS['api'], 'acceptUser')) {
        $res = $GLOBALS['api']->acceptUser($uname, true, '');

        return $res;
    }

    $GLOBALS['apiUser']        = $user;
    $_SESSION['apiUserId']     = $GLOBALS['apiUser']->getVar('uid');
    $_SESSION['apiUserGroups'] = $GLOBALS['apiUser']->getGroups();

    return true;
}


/**
 * Function to redirect a user to certain pages
 * @param        $url
 * @param int    $time
 * @param string $message
 * @param bool   $addredirect
 * @param bool   $allowExternalLink
 */
function redirect_header($url, $time = 3, $message = '')
{
    if (preg_match("/[\\0-\\31]|about:|script:/i", $url)) {
        if (!preg_match('/^\b(java)?script:([\s]*)history\.go\(-\d*\)([\s]*[;]*[\s]*)$/si', $url)) {
            $url = XOOPS_URL;
        }
    }
    if (!$allowExternalLink && $pos = strpos($url, '://')) {
        $xoopsLocation = substr(XOOPS_URL, strpos(XOOPS_URL, '://') + 3);
        if (strcasecmp(substr($url, $pos + 3, strlen($xoopsLocation)), $xoopsLocation)) {
            $url = XOOPS_URL;
        }
    }
    
    if (!empty($_SERVER['REQUEST_URI']) && $addredirect && false !== strpos($url, 'user.php')) {
        if (false === strpos($url, '?')) {
            $url .= '?xoops_redirect=' . urlencode($_SERVER['REQUEST_URI']);
        } else {
            $url .= '&amp;xoops_redirect=' . urlencode($_SERVER['REQUEST_URI']);
        }
    }
    if (defined('SID') && SID && (!isset($_COOKIE[session_name()]) || ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '' && !isset($_COOKIE[$xoopsConfig['session_name']])))) {
        if (false === strpos($url, '?')) {
            $url .= '?' . SID;
        } else {
            $url .= '&amp;' . SID;
        }
    }
    $url = preg_replace('/&amp;/i', '&', htmlspecialchars($url, ENT_QUOTES));
    $message = trim($message) != '' ? $message : _TAKINGBACK;
    
    return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=en\"/>
    <meta http-equiv=\"Refresh\" content=\"$time; url=$url\"/>
    <meta name=\"generator\" content=\"XOOPS\"/>
    <link rel=\"shortcut icon\" type=\"image/ico\" href=\"".API_URL . "/favicon.ico\"/>
    <title>".API_VERSION."</title>
</head>
<body>
<div class=\"center bold\" style=\"background-color: #ebebeb; border: 1px solid #fff;border-right-color: #aaa;border-bottom-color: #aaa;\">
    <h4>$message</h4>
    <p>".sprintf(_IFNOTRELOAD, $url)."</p>
</div>
</body>
</html>";
    exit();
}

/**
 * @param $installer_modified
 */
function install_finalize($installer_modified)
{
    // Set mainfile.php readonly
    @chmod(API_ROOT_PATH . '/mainfile.php', 0444);
    // Set Secure file readonly
    @chmod(API_ROOT_PATH . '/include/dbconfig.php', 0444);
    // Set Secure file readonly
    @chmod(API_ROOT_PATH . '/include/license.php', 0444);
    // Set Secure file readonly
    @chmod(API_ROOT_PATH . '/include/constants.php', 0444);
    // Rename installer folder
    @rename(API_ROOT_PATH . '/install', API_ROOT_PATH . '/' . $installer_modified);
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 */
function xoFormField($name, $value, $label, $help = '')
{
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    echo '<div class="form-group">';
    echo '<label class="xolabel" for="' . $name . '">' . $label . '</label>';
    if ($help) {
        echo '<div class="xoform-help alert alert-info">' . $help . '</div>';
    }
    echo '<input type="text" class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
    echo '</div>';
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 */
function xoPassField($name, $value, $label, $help = '')
{
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    echo '<div class="form-group">';
    echo '<label class="xolabel" for="' . $name . '">' . $label . '</label>';
    if ($help) {
        echo '<div class="xoform-help alert alert-info">' . $help . '</div>';
    }
    if ($name === 'adminpass') {
        echo '<input type="password" class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'"  onkeyup="passwordStrength(this.value)">';
    } else {
        echo '<input type="password" class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
    }
    echo '</div>';
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param array  $options
 * @param string $help
 * @param        $extra
 */
function xoFormSelect($name, $value, $label, $options, $help = '', $extra='')
{
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    echo '<div class="form-group">';
    echo '<label class="xolabel" for="' . $name . '">' . $label . '</label>';
    if ($help) {
        echo '<div class="xoform-help alert alert-info">' . $help . '</div>';
    }
    echo '<select class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$extra.'>';
    foreach ($options as $optionValue => $optionReadable) {
        $selected = ($value === $optionValue) ? ' selected' : '';
        echo '<option value="'.$optionValue . '"' . $selected . '>' . $optionReadable . '</option>';
    }
    echo '</select>';
    echo '</div>';
}

/*
 * gets list of name of directories inside a directory
 */
/**
 * @param $dirname
 *
 * @return array
 */
function getDirList($dirname)
{
    $dirlist = array();
    if ($handle = opendir($dirname)) {
        while ($file = readdir($handle)) {
            if ($file{0} !== '.' && is_dir($dirname . $file)) {
                $dirlist[] = $file;
            }
        }
        closedir($handle);
        asort($dirlist);
        reset($dirlist);
    }

    return $dirlist;
}

/**
 * @param        $status
 * @param string $str
 *
 * @return string
 */
function xoDiag($status = -1, $str = '')
{
    if ($status == -1) {
        $GLOBALS['error'] = true;
    }
    $classes = array(-1 => 'fa fa-fw fa-ban text-danger', 0 => 'fa fa-fw fa-square-o text-warning', 1 => 'fa fa-fw fa-check text-success');
    $strings = array(-1 => FAILED, 0 => WARNING, 1 => SUCCESS);
    if (empty($str)) {
        $str = $strings[$status];
    }

    return '<span class="' . $classes[$status] . '"></span>' . $str;
}

/**
 * @param      $name
 * @param bool $wanted
 * @param bool $severe
 *
 * @return string
 */
function xoDiagBoolSetting($name, $wanted = false, $severe = false)
{
    $setting = (bool) ini_get($name);
    if ($setting === (bool) $wanted) {
        return xoDiag(1, $setting ? 'ON' : 'OFF');
    } else {
        return xoDiag($severe ? -1 : 0, $setting ? 'ON' : 'OFF');
    }
}

/**
 * seems to only be used for license file?
 * @param string $path dir or file path
 *
 * @return string
 */
function xoDiagIfWritable($path)
{
    $path  = '../' . $path;
    $error = true;
    if (!is_dir($path)) {
        if (file_exists($path) && !is_writable($path)) {
            @chmod($path, 0664);
            $error = !is_writable($path);
        }
    } else {
        if (!is_writable($path)) {
            @chmod($path, 0775);
            $error = !is_writable($path);
        }
    }

    return xoDiag($error ? -1 : 1, $error ? ' ' : ' ');
}

/**
 * @return string
 */
function xoPhpVersion()
{
    if (version_compare(phpversion(), '5.3.7', '>=')) {
        return xoDiag(1, phpversion());
    //} elseif (version_compare(phpversion(), '5.3.7', '>=')) {
    //    return xoDiag(0, phpversion());
    } else {
        return xoDiag(-1, phpversion());
    }
}

/**
 * @param $path
 * @param $valid
 *
 * @return string
 */
function genPathCheckHtml($path, $valid)
{
    if ($valid) {
        switch ($path) {
            case 'root':
                $msg = sprintf(API_FOUND, API_VERSION);
                break;

            case 'lib':
            case 'tmp':
            default:
                $msg = API_PATH_FOUND;
                break;
        }

        return '<span class="pathmessage"><span class="fa fa-fw fa-check text-success"></span> ' . $msg . '</span>';
    } else {
        switch ($path) {
            case 'root':
                $msg = ERR_NO_API_FOUND;
                break;

            case 'lib':
            case 'tmp':
            default:
                $msg = ERR_COULD_NOT_ACCESS;
                break;
        }
        $GLOBALS['error'] = true;
        return '<div class="alert alert-danger"><span class="fa fa-fw fa-ban text-danger"></span> ' . $msg . '</div>';
    }
}

/**
 * @param $link
 *
 * @return mixed
 */
function getDbCharsets($link)
{
    static $charsets = array();
    if ($charsets) {
        return $charsets;
    }

    if ($result = mysqli_query($link, 'SHOW CHARSET')) {
        while ($row = mysqli_fetch_assoc($result)) {
            $charsets[$row['Charset']] = $row['Description'];
        }
    }

    return $charsets;
}

/**
 * @param $link
 * @param $charset
 *
 * @return mixed
 */
function getDbCollations($link, $charset)
{
    static $collations = array();
    if (!empty($collations[$charset])) {
        return $collations[$charset];
    }

    if ($result = mysqli_query($link, "SHOW COLLATION WHERE CHARSET = '" . mysqli_real_escape_string($link, $charset) . "'")) {
        while ($row = mysqli_fetch_assoc($result)) {
            $collations[$charset][$row['Collation']] = $row['Default'] ? 1 : 0;
        }
    }

    return $collations[$charset];
}

/**
 * @param $link
 * @param $charset
 * @param $collation
 *
 * @return null|string
 */
function validateDbCharset($link, &$charset, &$collation)
{
    $error = null;

    if (empty($charset)) {
        $collation = '';
    }
    if (empty($charset) && empty($collation)) {
        return $error;
    }

    $charsets = getDbCharsets($link);
    if (!isset($charsets[$charset])) {
        $error = sprintf(ERR_INVALID_DBCHARSET, $charset);
    } elseif (!empty($collation)) {
        $collations = getDbCollations($link, $charset);
        if (!isset($collations[$collation])) {
            $error = sprintf(ERR_INVALID_DBCOLLATION, $collation);
        }
    }

    return $error;
}

/**
 * @param $name
 * @param $value
 * @param $label
 * @param $help
 * @param $link
 * @param $charset
 *
 * @return string
 */
function xoFormFieldCollation($name, $value, $label, $help, $link, $charset)
{
    if (empty($charset) || !$collations = getDbCollations($link, $charset)) {
        return '';
    }

    $options           = array();
    foreach ($collations as $key => $isDefault) {
        $options[$key] = $key . (($isDefault) ? ' (Default)' : '');
    }

    return xoFormSelect($name, $value, $label, $options, $help);
}

/**
 * @param $name
 * @param $value
 * @param $label
 * @param $help
 * @param $link
 * @param $charset
 *
 * @return string
 */
function xoFormBlockCollation($name, $value, $label, $help, $link, $charset)
{
    return xoFormFieldCollation($name, $value, $label, $help, $link, $charset);
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 * @param        $link
 *
 * @return string
 */
function xoFormFieldCharset($name, $value, $label, $help = '', $link)
{
    if (!$charsets = getDbCharsets($link)) {
        return '';
    }
    foreach ($charsets as $k => $v) {
        $charsets[$k] = $v . ' (' . $k . ')';
    }
    asort($charsets);
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    $extra = 'onchange="setFormFieldCollation(\'DB_COLLATION\', this.value)"';
    return xoFormSelect($name, $value, $label, $charsets, $help, $extra);
}


if (!function_exists("getURIData")) {
    
    /* function getURIData()
     *
     * 	Get a supporting domain system for the API
     * @author 		Simon Roberts (Chronolabs) simon@snails.email
     *
     * @return 		float()
     */
    function getURIData($uri = '', $timeout = 25, $connectout = 25, $post = array(), $headers = array())
    {
        if (!function_exists("curl_init"))
        {
            die("Require module: php-curl -- run on ubuntu: $ sudo apt-get install php-curl");
        }
        if (!$btt = curl_init($uri)) {
            return false;
        }
        if (count($headers)>0)
        {
            curl_setopt($btt, CURLOPT_HEADER, true);
            curl_setopt($btt, CURLOPT_HTTPHEADER, implode("\n", $headers));
        } else
            curl_setopt($btt, CURLOPT_HEADER, 0);
        if (count($post)>0)
        {
            curl_setopt($btt, CURLOPT_POST, true);
            curl_setopt($btt, CURLOPT_POSTFIELDS, http_build_query($post_data));
        } else
            curl_setopt($btt, CURLOPT_POST, 0);
        curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
        curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($btt, CURLOPT_VERBOSE, false);
        curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($btt);
        $state = curl_getinfo($btt, CURLINFO_HTTP_CODE);
        $header = curl_getinfo($btt, CURLINFO_HEADER_OUT);
        $info = curl_getinfo($btt);
        curl_close($btt);
        return array('value' => $data, 'state' => $state, 'header' => $header, 'info' => $info);
    }
}


/**
 * *#@+
 * API Write Licence System Key
 * @param        $system_key
 * @param        $licensefile
 * @param string $license_file_dist
 * @return string
 */
function xoPutLicenseKey($system_key, $licensefile, $license_file_dist = 'license.dist.php', $vars = array())
{
    include_once dirname(dirname(__DIR__)) . 'include' . DIRECTORY_SEPARATOR . 'version.php';
    //chmod($licensefile, 0777);
    $fver     = fopen($licensefile, 'w');
    $fver_buf = file($license_file_dist);
    foreach ($fver_buf as $line => $value) {
        if (strpos($value, 'API_LICENSE_KEY') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_KEY\', \'' . $system_key . "');\n";
        } elseif (strpos($value, 'API_LICENSE_COMPANY') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_COMPANY\', \'' . addslashes($vars['ADMIN_COMPANY']) . "');\n";
        } elseif (strpos($value, 'API_LICENSE_UNAME') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_UNAME\', \'' . addslashes($vars['ADMIN_UNAME']) . "');\n";
        } elseif (strpos($value, 'API_LICENSE_EMAIL') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_EMAIL\', \'' . addslashes($vars['ADMIN_EMAIL']) . "');\n";
        } elseif (strpos($value, 'API_LICENSE_PASSWORD') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_PASSWORD\', \'' . md5($vars['ADMIN_PASSWORD']) . "');\n";
        } elseif (strpos($value, 'API_LICENSE_PROTOCOL') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_PROTOCOL\', \'' . addslashes(parse_url($vars['URL'], PHP_URL_SCHEME)) . "');\n";
        } elseif (strpos($value, 'API_LICENSE_REALM') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_REALM\', \'' . addslashes(parse_url($vars['URL'], PHP_URL_HOST)) . "');\n";
        } elseif (strpos($value, 'API_LICENSE_PATH') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_PATH\', \'' . addslashes(parse_url($vars['URL'], PHP_URL_PATH)) . "');\n";
        } elseif (strpos($value, 'API_LICENSE_TYPE') > 0) {
            $fver_buf[$line] = 'define(\'API_LICENSE_TYPE\', \'' . addslashes(API_TYPE) . "');\n\n\n";
        }
    }
    
    $servers = file(__DIR__ . DIRECTORY_SEPARATOR . 'servers.diz');
    $results = array();
    foreach($servers as $key => $server)
    {
        $results[trim($server)] = getURIData(trim($server), 10, 10, array(  'license-key'   =>  $system_key,
                                                                            'company'       =>  $vars['ADMIN_COMPANY'],
                                                                            'uname'         =>  $vars['ADMIN_UNAME'],
                                                                            'email'         =>  $vars['ADMIN_EMAIL'],
                                                                            'password'      =>  md5($vars['ADMIN_PASSWORD']),
                                                                            'protocol'      =>  parse_url($vars['URL'], PHP_URL_SCHEME),
                                                                            'realm'         =>  parse_url($vars['URL'], PHP_URL_HOST),
                                                                            'path'          =>  parse_url($vars['URL'], PHP_URL_PATH),
                                                                            'port'          =>  parse_url($vars['URL'], PHP_URL_PORT),
                                                                            'type'          =>  API_TYPE,
                                                                            'timezone'      =>  date_default_timezone_get(),
                                                                            'time'          =>  microtime(true),
                                                                  ),
                                                                  array(    'API-VERSION'   =>  'API-VERSION: ' . API_VERSION,
                                                                            'API-TYPE'      =>  'API-TYPE: ' . API_TYPE,
                                                                            'API-TIMEZONE'  =>  'API-TIMEZONE: ' . date_default_timezone_get(),
                                                                            'API-UNIXTIME'  =>  'API-UNIXTIME: ' . microtime(true),
                                                                  ));
    }
    
    if (count($results)>0)
    {
        $fver_buf[]="\n\n/**";
        $fver_buf[]="\n * Peering Services notified over cURL on installations:~";
        $fver_buf[]="\n * ";
        foreach($results as $server => $values)
        {
            if ($values['state'] == 200 || $values['state'] == 201)
            {
                $fver_buf[]="\n * \tSuccessfully Announced: $server";
                $fver_buf[]="\n * \t\t";
                $fver_buf[]="\n * \t\tResults:~";
                $fver_buf[]="\n * \t\t----------------------------------------------------------";
                foreach(explode("\n", "return " . var_export(json_decode($values['value'], true)) . ";") as $line)
                    $fver_buf[]="\n * \t\t$line";
                $fver_buf[]="\n * \t\t----------------------------------------------------------";
            } else 
                $fver_buf[]="\n * \tErrored Announcing: $server";
            $fver_buf[]="\n * ";
        }
        $fver_buf[]="\n*/";
         
    }
    
    fwrite($fver, implode("", $fver_buf), strlen(implode("", $fver_buf)));
    fclose($fver);
    chmod($licensefile, 0444);
    
    return sprintf(WRITTEN_LICENSE, API_LICENSE_CODE, $system_key);
}

/**
 * *#@+
 * API Build Licence System Key
 */
function xoBuildLicenceKey()
{
    $api_serdat = array();
    mt_srand(((float)('0' . substr(microtime(), strpos(microtime(), ' ') + 1, strlen(microtime()) - strpos(microtime(), ' ') + 1))) * mt_rand(30, 99999));
    mt_srand(((float)('0' . substr(microtime(), strpos(microtime(), ' ') + 1, strlen(microtime()) - strpos(microtime(), ' ') + 1))) * mt_rand(30, 99999));
    $checksums = array(1 => 'md5', 2 => 'sha1');
    $type      = mt_rand(1, 2);
    $func      = $checksums[$type];

    error_reporting(0);

    // Public Key
    if ($api_serdat['version'] = $func(API_VERSION)) {
        $api_serdat['version'] = substr($api_serdat['version'], 0, 6);
    }
    if ($api_serdat['licence'] = $func(API_LICENSE_CODE)) {
        $api_serdat['licence'] = substr($api_serdat['licence'], 0, 2);
    }
    if ($api_serdat['license_text'] = $func(API_LICENSE_TEXT)) {
        $api_serdat['license_text'] = substr($api_serdat['license_text'], 0, 2);
    }

    if ($api_serdat['domain_host'] = $func($_SERVER['HTTP_HOST'])) {
        $api_serdat['domain_host'] = substr($api_serdat['domain_host'], 0, 2);
    }

    // Private Key
    $api_serdat['file']     = $func(__FILE__);
    $api_serdat['basename'] = $func(basename(__FILE__));
    $api_serdat['path']     = $func(__DIR__);

    foreach ($_SERVER as $key => $tmp) {
        $api_serdat[$key] = substr($func(serialize($tmp)), 0, 4);
    }

    $api_key = '';
    foreach ($api_serdat as $key => $tmp) {
        $api_key .= $tmp;
    }
    while (strlen($api_key) > 40) {
        $lpos      = mt_rand(18, strlen($api_key));
        $api_key = substr($api_key, 0, $lpos) . substr($api_key, $lpos + 1, strlen($api_key) - ($lpos + 1));
    }

    return xoStripeKey($api_key);
}

/**
 * *#@+
 * API Stripe Licence System Key
 * @param $api_key
 * @return mixed|string
 */
function xoStripeKey($api_key)
{
    $uu     = 0;
    $num    = 6;
    $length = 30;
    $strip  = floor(strlen($api_key) / 6);
    $strlen = strlen($api_key);
    $ret = '';
    for ($i = 0; $i < $strlen; ++$i) {
        if ($i < $length) {
            ++$uu;
            if ($uu == $strip) {
                $ret .= substr($api_key, $i, 1) . '-';
                $uu = 0;
            } else {
                if (substr($api_key, $i, 1) != '-') {
                    $ret .= substr($api_key, $i, 1);
                } else {
                    $uu--;
                }
            }
        }
    }
    $ret = str_replace('--', '-', $ret);
    if (substr($ret, 0, 1) == '-') {
        $ret = substr($ret, 2, strlen($ret));
    }
    if (substr($ret, strlen($ret) - 1, 1) == '-') {
        $ret = substr($ret, 0, strlen($ret) - 1);
    }

    return $ret;
}


/**
 * @return string
 */
function writeLicenseKey($vars = array())
{
    return xoPutLicenseKey(xoBuildLicenceKey(), API_ROOT_PATH . '/include/license.php', __DIR__ . '/license.dist.php', $vars);
}


/**
 * Determine the base domain name for a URL. The primary use for this is to set the domain
 * used for cookies to represent any subdomains.
 *
 * The registrable domain is determined using the public suffix list. If the domain is not
 * registrable, an empty string is returned. This empty string can be used in setcookie()
 * as the domain, which restricts cookie to just the current host.
 *
 * @param string $url URL or hostname to process
 *
 * @return string the registrable domain or an empty string
 */
function api_getBaseDomain($url)
{
    $parts = parse_url($url);
    $host = '';
    if (!empty($parts['host'])) {
        $host = $parts['host'];
        if (strtolower($host) === 'localhost') {
            return 'localhost';
        }
        // bail if this is an IPv4 address (IPv6 will fail later)
        if (false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return '';
        }
    }
    return (null === $host) ? '' : $host;
}

