<?php
/**
 * Abstract base class for API Database access classes
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
 * @subpackage          database
 * @since               1.0.0
 * @author              Kazumi Ono <onokazu@api.org>
 */

defined('API_ROOT_PATH') || exit('Restricted access');

/**
 * make sure this is only included once!
 */
if (defined('API_C_DATABASE_INCLUDED')) {
    return null;
}

define('API_C_DATABASE_INCLUDED', 1);

/**
 * Abstract base class for Database access classes
 *
 * @abstract
 * @author     Kazumi Ono <onokazu@api.org>
 * @package    kernel
 * @subpackage database
 */
abstract class APIDatabase
{
    /**
     * Prefix for tables in the database
     *
     * @var string
     */
    public $prefix = '';

    /**
     * reference to a {@link APILogger} object
     *
     * @see APILogger
     * @var object APILogger
     */
    public $logger;

    /**
     * If statements that modify the database are selected
     *
     * @var boolean
     */
    public $allowWebChanges = false;

    /**
     * APIDatabase constructor.
     */
    public function __construct()
    {
        // exit('Cannot instantiate this class directly');
    }

    /**
     * assign a {@link APILogger} object to the database
     *
     * @see APILogger
     * @param APILogger $logger reference to a {@link APILogger} object
     */

    public function setLogger(APILogger $logger)
    {
        $this->logger = &$logger;
    }

    /**
     * set the prefix for tables in the database
     *
     * @param string $value table prefix
     */
    public function setPrefix($value)
    {
        $this->prefix = $value;
    }

    /**
     * attach the prefix.'_' to a given tablename
     *
     * if tablename is empty, only prefix will be returned
     *
     * @param  string $tablename tablename
     * @return string prefixed tablename, just prefix if tablename is empty
     */
    public function prefix($tablename = '')
    {
        if ($tablename != '') {
            return $this->prefix . '_' . $tablename;
        } else {
            return $this->prefix;
        }
    }
}

/**
 * Only for backward compatibility
 *
 * @deprecated
 */
class Database
{
    /**
     * @return object
     */
    public function getInstance()
    {
        if (is_object($GLOBALS['apiLogger'])) {
            $GLOBALS['apiLogger']->addDeprecated("'Database::getInstance();' is deprecated since API 2.5.4, please use 'APIDatabaseFactory::getDatabaseConnection();' instead.");
        }
        $inst = APIDatabaseFactory::getDatabaseConnection();

        return $inst;
    }
}
