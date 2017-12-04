<?php
/**
 *  API Form Class Elements
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
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('API_ROOT_PATH') || exit('Restricted access');

/**
 * APIUserUtility
 *
 * @package Kernel
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 */
class APIUserUtility
{
    /**
     * APIUserUtility::sendWelcome
     *
     * @param mixed $user
     *
     * @return bool
     */
    public static function sendWelcome($user)
    {
        global $apiConfigUser, $apiConfig;

        if (empty($apiConfigUser)) {
            /* @var $config_handler APIConfigHandler  */
            $config_handler  = api_getHandler('config');
            $apiConfigUser = $config_handler->getConfigsByCat(API_CONF_USER);
        }
        if (empty($apiConfigUser['welcome_type'])) {
            return true;
        }

        if (!empty($user) && !is_object($user)) {
            /* @var $member_handler APIMemberHandler */
            $member_handler = api_getHandler('member');
            $user           = $member_handler->getUser($user);
        }
        if (!is_object($user)) {
            return false;
        }

        api_loadLanguage('user');
        $apiMailer = api_getMailer();
        if ($apiConfigUser['welcome_type'] == 1 || $apiConfigUser['welcome_type'] == 3) {
            $apiMailer->useMail();
        }
        if ($apiConfigUser['welcome_type'] == 2 || $apiConfigUser['welcome_type'] == 3) {
            $apiMailer->usePM();
        }
        $apiMailer->setTemplate('welcome.tpl');
        $apiMailer->setSubject(sprintf(_US_WELCOME_SUBJECT, $apiConfig['sitename']));
        $apiMailer->setToUsers($user);
        if ($apiConfigUser['reg_dispdsclmr'] && $apiConfigUser['reg_disclaimer']) {
            $apiMailer->assign('TERMSOFUSE', $apiConfigUser['reg_disclaimer']);
        } else {
            $apiMailer->assign('TERMSOFUSE', '');
        }

        return $apiMailer->send();
    }
    /**
     * $uname, $email, $pass = null, $vpass = null
     */
    /**
     * APIUserUtility::validate
     *
     * @return bool|string
     */
    public static function validate()
    {
        global $apiUser;

        $args     = func_get_args();
        $args_num = func_num_args();

        $user  = null;
        $uname = null;
        $email = null;
        $pass  = null;
        $vpass = null;

        switch ($args_num) {
            case 1:
                $user = $args[0];
                break;
            case 2:
                list($uname, $email) = $args;
                break;
            case 3:
                list($user, $pass, $vpass) = $args;
                break;
            case 4:
                list($uname, $email, $pass, $vpass) = $args;
                break;
            default:
                return false;
        }
        if (is_object($user)) {
            $uname = $user->getVar('uname', 'n');
            $email = $user->getVar('email', 'n');
        }
        /* @var $config_handler APIConfigHandler  */
        $config_handler  = api_getHandler('config');
        $apiConfigUser = $config_handler->getConfigsByCat(API_CONF_USER);

        api_loadLanguage('user');
        $myts = MyTextSanitizer::getInstance();

        $apiUser_isAdmin = is_object($apiUser) && $apiUser->isAdmin();
        $stop              = '';
        // Invalid email address
        if (!checkEmail($email)) {
            $stop .= _US_INVALIDMAIL . '<br>';
        }
        if (strrpos($email, ' ') > 0) {
            $stop .= _US_EMAILNOSPACES . '<br>';
        }
        // Check forbidden email address if current operator is not an administrator
        if (!$apiUser_isAdmin) {
            foreach ($apiConfigUser['bad_emails'] as $be) {
                if (!empty($be) && preg_match('/' . $be . '/i', $email)) {
                    $stop .= _US_INVALIDMAIL . '<br>';
                    break;
                }
            }
        }
        $uname = api_trim($uname);
        switch ($apiConfigUser['uname_test_level']) {
            case 0:
                // strict
                $restriction = '/[^a-zA-Z0-9\_\-]/';
                break;
            case 1:
                // medium
                $restriction = '/[^a-zA-Z0-9\_\-\<\>\,\.\$\%\#\@\!\\\'\']/';
                break;
            case 2:
                // loose
                $restriction = '/[\000-\040]/';
                break;
        }
        if (empty($uname) || preg_match($restriction, $uname)) {
            $stop .= _US_INVALIDNICKNAME . '<br>';
        }
        // Check uname settings if current operator is not an administrator
        if (!$apiUser_isAdmin) {
            if (strlen($uname) > $apiConfigUser['maxuname']) {
                $stop .= sprintf(_US_NICKNAMETOOLONG, $apiConfigUser['maxuname']) . '<br>';
            }
            if (strlen($uname) < $apiConfigUser['minuname']) {
                $stop .= sprintf(_US_NICKNAMETOOSHORT, $apiConfigUser['minuname']) . '<br>';
            }
            foreach ($apiConfigUser['bad_unames'] as $bu) {
                if (!empty($bu) && preg_match('/' . $bu . '/i', $uname)) {
                    $stop .= _US_NAMERESERVED . '<br>';
                    break;
                }
            }
            /**
             * if (strrpos($uname, ' ') > 0) {
             * $stop .= _US_NICKNAMENOSPACES . '<br>';
             * }
             */
        }
        $apiDB = APIDatabaseFactory::getDatabaseConnection();
        // Check if uname/email already exists if the user is a new one
        $uid    = is_object($user) ? $user->getVar('uid') : 0;
        $sql    = 'SELECT COUNT(*) FROM `' . $apiDB->prefix('users') . '` WHERE `uname` = ' . $apiDB->quote(addslashes($uname)) . (($uid > 0) ? " AND `uid` <> {$uid}" : '');
        $result = $apiDB->query($sql);
        list($count) = $apiDB->fetchRow($result);
        if ($count > 0) {
            $stop .= _US_NICKNAMETAKEN . '<br>';
        }
        $sql    = 'SELECT COUNT(*) FROM `' . $apiDB->prefix('users') . '` WHERE `email` = ' . $apiDB->quote(addslashes($email)) . (($uid > 0) ? " AND `uid` <> {$uid}" : '');
        $result = $apiDB->query($sql);
        list($count) = $apiDB->fetchRow($result);
        if ($count > 0) {
            $stop .= _US_EMAILTAKEN . '<br>';
        }
        // If password is not set, skip password validation
        if ($pass === null && $vpass === null) {
            return $stop;
        }

        if (!isset($pass) || $pass == '' || !isset($vpass) || $vpass == '') {
            $stop .= _US_ENTERPWD . '<br>';
        }
        if (isset($pass) && ($pass != $vpass)) {
            $stop .= _US_PASSNOTSAME . '<br>';
        } elseif (($pass != '') && (strlen($pass) < $apiConfigUser['minpass'])) {
            $stop .= sprintf(_US_PWDTOOSHORT, $apiConfigUser['minpass']) . '<br>';
        }

        return $stop;
    }

    /**
     * Get client IP
     *
     * Adapted from PMA_getIp() [phpmyadmin project]
     *
     * @param  bool $asString requiring integer or dotted string
     * @return mixed string or integer value for the IP
     */
    public static function getIP($asString = false)
    {
        // Gets the proxy ip sent by the user
        $proxy_ip = '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_VIA'])) {
            $proxy_ip = $_SERVER['HTTP_VIA'];
        } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
        } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
        }
        if (!empty($proxy_ip)) {
            $ip = new \Xmf\IPAddress($proxy_ip);
            if (false === $ip->asReadable()) {
                $ip = \Xmf\IPAddress::fromRequest();
            }
        } else {
            $ip = \Xmf\IPAddress::fromRequest();
        }

        // this really should return $ip->asBinary() instead of ip2long, but for IPv6, this will
        // return false when the ip2long() fails. Callers are not expecting binary strings.
        $the_IP = $asString ? $ip->asReadable() : ip2long($ip->asReadable());

        return $the_IP;
    }

    /**
     * APIUserUtility::getUnameFromIds()
     *
     * @param  mixed $uid
     * @param  mixed $usereal
     * @param  mixed $linked
     * @return array
     */
    public static function getUnameFromIds($uid, $usereal = false, $linked = false)
    {
        if (!is_array($uid)) {
            $uid = array($uid);
        }
        $userid = array_map('intval', array_filter($uid));

        $myts  = MyTextSanitizer::getInstance();
        $users = array();
        if (count($userid) > 0) {
            $apiDB = APIDatabaseFactory::getDatabaseConnection();
            $sql     = 'SELECT uid, uname, name FROM ' . $apiDB->prefix('users') . ' WHERE level > 0 AND uid IN(' . implode(',', array_unique($userid)) . ')';
            if (!$result = $apiDB->query($sql)) {
                return $users;
            }
            while ($row = $apiDB->fetchArray($result)) {
                $uid = $row['uid'];
                if ($usereal && $row['name']) {
                    $users[$uid] = $myts->htmlSpecialChars($row['name']);
                } else {
                    $users[$uid] = $myts->htmlSpecialChars($row['uname']);
                }
                if ($linked) {
                    $users[$uid] = '<a href="' . API_URL . '/userinfo.php?uid=' . $uid . '" title="' . $users[$uid] . '">' . $users[$uid] . '</a>';
                }
            }
        }
        if (in_array(0, $users, true)) {
            $users[0] = $myts->htmlSpecialChars($GLOBALS['apiConfig']['anonymous']);
        }

        return $users;
    }

    /**
     * APIUserUtility::getUnameFromId()
     *
     * @param  mixed $userid
     * @param  mixed $usereal
     * @param  mixed $linked
     * @return string
     */
    public static function getUnameFromId($userid, $usereal = false, $linked = false)
    {
        $myts     = MyTextSanitizer::getInstance();
        $userid   = (int)$userid;
        $username = '';
        if ($userid > 0) {
            /* @var $member_handler APIMemberHandler */
            $member_handler = api_getHandler('member');
            $user           = $member_handler->getUser($userid);
            if (is_object($user)) {
                if ($usereal && $user->getVar('name')) {
                    $username = $user->getVar('name');
                } else {
                    $username = $user->getVar('uname');
                }
                if (!empty($linked)) {
                    $username = '<a href="' . API_URL . '/userinfo.php?uid=' . $userid . '" title="' . $username . '">' . $username . '</a>';
                }
            }
        }
        if (empty($username)) {
            $username = $myts->htmlSpecialChars($GLOBALS['apiConfig']['anonymous']);
        }

        return $username;
    }
}
