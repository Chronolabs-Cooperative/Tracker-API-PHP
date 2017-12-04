<?php
/**
 * MySQL access using MySQLi extension
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
 * @subpackage          database
 * @since               1.0.0
 * @author              Kazumi Ono <onokazu@api.org>
 * @author              Rodney Fulk <redheadedrod@hotmail.com>
 */
defined('API_ROOT_PATH') || die('Restricted access');

include_once API_ROOT_PATH . '/class/database/database.php';

/**
 * connection to a MySQL database using MySQLi extension
 *
 * @abstract
 * @author              Kazumi Ono <onokazu@api.org>
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @package             class
 * @subpackage          database
 */
class APIMySQLDatabase extends APIDatabase
{
    /**
     * Database connection
     *
     * @var APIDatabase|MySQLi
     */
    public $conn;

    /**
     * connect to the database
     *
     * @param bool $selectdb select the database now?
     * @return bool successful?
     */
    public function connect($selectdb = true)
    {
        if (!extension_loaded('MySQLi')) {
            trigger_error('notrace:MySQLi extension not loaded', E_USER_ERROR);

            return false;
        }

        $this->allowWebChanges = ($_SERVER['REQUEST_METHOD'] !== 'GET');

        if ($selectdb) {
            $dbname = constant('API_DB_NAME');
        } else {
            $dbname = '';
        }
        if (API_DB_PCONNECT == 1) {
            $this->conn = new MySQLi('p:' . API_DB_HOST, API_DB_USER, API_DB_PASS, $dbname);
        } else {
            $this->conn = new MySQLi(API_DB_HOST, API_DB_USER, API_DB_PASS, $dbname);
        }

        // errno is 0 if connect was successful
        if (0 !== $this->conn->connect_errno) {
            return false;
        }

        if (defined('API_DB_CHARSET') && ('' !== API_DB_CHARSET)) {
            // $this->queryF("SET NAMES '" . API_DB_CHARSET . "'");
            $this->conn->set_charset(API_DB_CHARSET);
        }
        $this->queryF('SET SQL_BIG_SELECTS = 1');

        return true;
    }

    /**
     * generate an ID for a new row
     *
     * This is for compatibility only. Will always return 0, because MySQL supports
     * autoincrement for primary keys.
     *
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int always 0, because MySQL has support for autoincrement
     */
    public function genId($sequence)
    {
        return 0; // will use auto_increment
    }

    /**
     * Get a result row as an enumerated array
     *
     * @param MySQLi_result $result
     *
     * @return array|false false on end of data
     */
    public function fetchRow($result)
    {
        $row = @MySQLi_fetch_row($result);
        return (null === $row) ? false : $row;
    }

    /**
     * Fetch a result row as an associative array
     *
     * @param MySQLi_result $result
     *
     * @return array|false false on end of data
     */
    public function fetchArray($result)
    {
        $row = @MySQLi_fetch_assoc($result);
        return (null === $row) ? false : $row;

    }

    /**
     * Fetch a result row as an associative array
     *
     * @param MySQLi_result $result
     *
     * @return array|false false on end of data
     */
    public function fetchBoth($result)
    {
        $row = @MySQLi_fetch_array($result, MySQLI_BOTH);
        return (null === $row) ? false : $row;
    }

    /**
     * APIMySQLiDatabase::fetchObjected()
     *
     * @param mixed $result
     * @return stdClass|false false on end of data
     */
    public function fetchObject($result)
    {
        $row = @MySQLi_fetch_object($result);
        return (null === $row) ? false : $row;
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int
     */
    public function getInsertId()
    {
        return MySQLi_insert_id($this->conn);
    }

    /**
     * Get number of rows in result
     *
     * @param MySQLi_result $result
     *
     * @return int
     */
    public function getRowsNum($result)
    {
        return @MySQLi_num_rows($result);
    }

    /**
     * Get number of affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return MySQLi_affected_rows($this->conn);
    }

    /**
     * Close MySQL connection
     *
     * @return void
     */
    public function close()
    {
        MySQLi_close($this->conn);
    }

    /**
     * will free all memory associated with the result identifier result.
     *
     * @param MySQLi_result $result result
     *
     * @return void
     */
    public function freeRecordSet($result)
    {
        MySQLi_free_result($result);
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     *
     * @return string Returns the error text from the last MySQL function, or '' (the empty string) if no error occurred.
     */
    public function error()
    {
        return @MySQLi_error($this->conn);
    }

    /**
     * Returns the numerical value of the error message from previous MySQL operation
     *
     * @return int Returns the error number from the last MySQL function, or 0 (zero) if no error occurred.
     */
    public function errno()
    {
        return @MySQLi_errno($this->conn);
    }

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     *
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    public function quoteString($str)
    {
        return $this->quote($str);
    }

    /**
     * Quotes a string for use in a query.
     *
     * @param string $string string to quote/escape for use in query
     *
     * @return string
     */
    public function quote($string)
    {
        $quoted = $this->escape($string);
        return "'{$quoted}'";
    }

    /**
     * Escapes a string for use in a query. Does not add surrounding quotes.
     *
     * @param string $string string to escape
     *
     * @return string
     */
    public function escape($string)
    {
        return MySQLi_real_escape_string($this->conn, $string);
    }

    /**
     * perform a query on the database
     *
     * @param string $sql   a valid MySQL query
     * @param int    $limit number of records to return
     * @param int    $start offset of first record to return
     * @return MySQLi_result|bool query result or FALSE if successful
     *                      or TRUE if successful and no result
     */
    public function queryF($sql, $limit = 0, $start = 0)
    {
        if (!empty($limit)) {
            if (empty($start)) {
                $start = 0;
            }
            $sql = $sql . ' LIMIT ' . (int)$start . ', ' . (int)$limit;
        }
        $this->logger->startTime('query_time');
        $result = MySQLi_query($this->conn, $sql);
        $this->logger->stopTime('query_time');
        $query_time = $this->logger->dumpTime('query_time', true);
        if ($result) {
            $this->logger->addQuery($sql, null, null, $query_time);

            return $result;
        } else {
            $this->logger->addQuery($sql, $this->error(), $this->errno(), $query_time);

            return false;
        }
    }

    /**
     * perform a query
     *
     * This method is empty and does nothing! It should therefore only be
     * used if nothing is exactly what you want done! ;-)
     *
     * @param string $sql   a valid MySQL query
     * @param int    $limit number of records to return
     * @param int    $start offset of first record to return
     * @abstract
     */
    public function query($sql, $limit = 0, $start = 0)
    {
    }

    /**
     * perform queries from SQL dump file in a batch
     *
     * @param string $file file path to an SQL dump file
     * @return bool FALSE if failed reading SQL file or TRUE if the file has been read and queries executed
     */
    public function queryFromFile($file)
    {
        if (false !== ($fp = fopen($file, 'r'))) {
            include_once API_ROOT_PATH . '/class/database/sqlutility.php';
            $sql_queries = trim(fread($fp, filesize($file)));
            SqlUtility::splitMySQLFile($pieces, $sql_queries);
            foreach ($pieces as $query) {
                // [0] contains the prefixed query
                // [4] contains unprefixed table name
                $prefixed_query = SqlUtility::prefixQuery(trim($query), $this->prefix());
                if ($prefixed_query != false) {
                    $this->query($prefixed_query[0]);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get field name
     *
     * @param MySQLi_result $result query result
     * @param int           $offset numerical field index
     *
     * @return string
     */
    public function getFieldName($result, $offset)
    {
        return $result->fetch_field_direct($offset)->name;
    }

    /**
     * Get field type
     *
     * @param MySQLi_result $result query result
     * @param int           $offset numerical field index
     *
     * @return string
     */
    public function getFieldType($result, $offset)
    {
        $typecode = $result->fetch_field_direct($offset)->type;
        switch ($typecode) {
            case MySQLI_TYPE_DECIMAL:
            case MySQLI_TYPE_NEWDECIMAL:
                $type = 'decimal';
                break;
            case MySQLI_TYPE_BIT:
                $type = 'bit';
                break;
            case MySQLI_TYPE_TINY:
            case MySQLI_TYPE_CHAR:
                $type = 'tinyint';
                break;
            case MySQLI_TYPE_SHORT:
                $type = 'smallint';
                break;
            case MySQLI_TYPE_LONG:
                $type = 'int';
                break;
            case MySQLI_TYPE_FLOAT:
                $type = 'float';
                break;
            case MySQLI_TYPE_DOUBLE:
                $type = 'double';
                break;
            case MySQLI_TYPE_NULL:
                $type = 'NULL';
                break;
            case MySQLI_TYPE_TIMESTAMP:
                $type = 'timestamp';
                break;
            case MySQLI_TYPE_LONGLONG:
                $type = 'bigint';
                break;
            case MySQLI_TYPE_INT24:
                $type = 'mediumint';
                break;
            case MySQLI_TYPE_NEWDATE:
            case MySQLI_TYPE_DATE:
                $type = 'date';
                break;
            case MySQLI_TYPE_TIME:
                $type = 'time';
                break;
            case MySQLI_TYPE_DATETIME:
                $type = 'datetime';
                break;
            case MySQLI_TYPE_YEAR:
                $type = 'year';
                break;
            case MySQLI_TYPE_INTERVAL:
                $type = 'interval';
                break;
            case MySQLI_TYPE_ENUM:
                $type = 'enum';
                break;
            case MySQLI_TYPE_SET:
                $type = 'set';
                break;
            case MySQLI_TYPE_TINY_BLOB:
                $type = 'tinyblob';
                break;
            case MySQLI_TYPE_MEDIUM_BLOB:
                $type = 'mediumblob';
                break;
            case MySQLI_TYPE_LONG_BLOB:
                $type = 'longblob';
                break;
            case MySQLI_TYPE_BLOB:
                $type = 'blob';
                break;
            case MySQLI_TYPE_VAR_STRING:
                $type = 'varchar';
                break;
            case MySQLI_TYPE_STRING:
                $type = 'char';
                break;
            case MySQLI_TYPE_GEOMETRY:
                $type = 'geometry';
                break;
            default:
                $type = 'unknown';
                break;
        }

        return $type;
    }

    /**
     * Get number of fields in result
     *
     * @param MySQLi_result $result query result
     *
     * @return int
     */
    public function getFieldsNum($result)
    {
        return MySQLi_num_fields($result);
    }

    /**
     * getServerVersion get version of the MySQL server
     *
     * @return string
     */
    public function getServerVersion()
    {
        return MySQLi_get_server_info($this->conn);
    }
}

/**
 * Safe Connection to a MySQL database.
 *
 * @author              Kazumi Ono <onokazu@api.org>
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @package             kernel
 * @subpackage          database
 */
class APIMySQLDatabaseSafe extends APIMySQLDatabase
{
    /**
     * perform a query on the database
     *
     * @param string $sql   a valid MySQL query
     * @param int    $limit number of records to return
     * @param int    $start offset of first record to return
     * @return resource query result or FALSE if successful
     *                      or TRUE if successful and no result
     */
    public function query($sql, $limit = 0, $start = 0)
    {
        return $this->queryF($sql, $limit, $start);
    }
}

/**
 * Read-Only connection to a MySQL database.
 *
 * This class allows only SELECT queries to be performed through its
 * {@link query()} method for security reasons.
 *
 * @author              Kazumi Ono <onokazu@api.org>
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @package             class
 * @subpackage          database
 */
class APIMySQLDatabaseProxy extends APIMySQLDatabase
{
    /**
     * perform a query on the database
     *
     * this method allows only SELECT queries for safety.
     *
     * @param string $sql   a valid MySQL query
     * @param int    $limit number of records to return
     * @param int    $start offset of first record to return
     * @return resource query result or FALSE if unsuccessful
     */
    public function query($sql, $limit = 0, $start = 0)
    {
        $sql = ltrim($sql);
        if (!$this->allowWebChanges && strtolower(substr($sql, 0, 6)) !== 'select') {
            trigger_error('Database updates are not allowed during processing of a GET request', E_USER_WARNING);

            return false;
        }

        return $this->queryF($sql, $limit, $start);
    }
}
