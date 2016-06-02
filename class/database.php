<?php
/**
 * Chronolabs REST GeoSpatial Places API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @package         places
 * @since           1.0.2
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @version         $Id: functions.php 1000 2013-06-07 01:20:22Z mynamesnot $
 * @subpackage		api
 * @description		REST GeoSpatial Places API
 */

/**
 * Abstract base class for Database access classes
 *
 * @abstract
 * @author     Simon Roberts <wishcraft@users.sourceforge.net>
 * @package    places
 * @subpackage database
 */
abstract class TrackerDatabase
{

	
    /** 
     * Database connection
     *
     * @var resource
     */
    public $conn;

    /** 
     * Prefix for tables in the database
     *
     * @var string
     */
    public $prefix = '';

    /** setPrefix()
	 *  Sets the Database Prefix
	 *
     * set the prefix for tables in the database
     *
     * @param string $value table prefix
     */
    public function setPrefix($value)
    {
        $this->prefix = $value;
    }

    /** prefix()
     * attach the prefix.'_' to a given tablename
     * if tablename is empty, only prefix will be returned
     *
     * @param string $tablename tablename
     *
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

    /** connect()
	 *  Connects to a Database
	 *
     * @abstract
     *
     * @param bool $selectdb
     *
     * @return void
     */
    abstract function connect($selectdb = true);

    /** genId()
	 *  Generates AUTO INCREMENT Field Type Zero-Code
	 *
     * @param $sequence
     *
     * @abstract
     */
    abstract function genId($sequence);

    /** fetchRow()
	 *  Fetches a Field Row
	 *
     * @param $result
     *
     * @abstract
     */
    abstract function fetchRow($result);

    /** fetchArray()
	 *  Fetches a Row of Columns of a Database Record as an Array in Result
	 *
     * @param $result
     *
     * @return array
     * @abstract
     */
    abstract function fetchArray($result);

    /** fetchBoth()
	 *  Fetches a Row of Columns of a Database Record as an Array & Object in Result
	 *
     * @param $result
     *
     * @abstract
     */
    abstract function fetchBoth($result);

    /** fetchObject()
	 *  Fetches a Row of Columns of a Database Record as an Object in Result
	 *
     * @param $result
     *
     * @abstract
     */
    abstract function fetchObject($result);

    /** getInsertId()
	 *  Gets the Last AUTO INCREMENT Inserted ID in Result
	 *
     * @abstract
     */
    abstract function getInsertId();

    /** getRowsNum()
	 *  Get number of Rows in Result
	 *
     * @param $result
     *
     * @abstract
     */
    abstract function getRowsNum($result);

    /** getAffectedRows()
	 *  Get number of Affected Rows in Result
	 *
     * @abstract
     */
    abstract function getAffectedRows();

    /** close()
	 *  Closes the Connection to the Databaase
	 *
     * @abstract
     */
    abstract function close();

    /** freeRecordSet()
	 *  Commits a Transaction with the Database
	 *
     * @param $result
     *
     * @abstract
     */
    abstract function freeRecordSet($result);

    /** error()
	 *  Error Array
	 *
     * @abstract
     */
    abstract function error();

    /** errno()
	 *  Error Code Number on last Transaction
	 *
     * @abstract
     */
    abstract function errno();

    /** quoteString()
	 *  Inserts SQL Quotation Marks String
	 *
     * @param $str
     *
     * @abstract
     */
    abstract function quoteString($str);


    /** quoteString()
	 *  Inserts SQL Quotation Marks String
	 *
     * @param $string
     *
     * @abstract
     */
    abstract function quote($string);


    /** quote()
	 *  Inserts SQL Quotation Marks
	 *
     * @param     $sql
     * @param int $limit
     * @param int $start
     *
     * @abstract
     */
    abstract function queryF($sql, $limit = 0, $start = 0);

    /** queryF()
	 *  Executes and Open Query
	 *
     * @param     $sql
     * @param int $limit
     * @param int $start
     *
     * @abstract
     */
    abstract function query($sql, $limit = 0, $start = 0);

    /** query()
	 *  Executes a Select Query
	 *
     * @param $result
     * @param $offset
     *
     * @abstract
     */
    abstract function getFieldName($result, $offset);

    /** getFieldName()
	 *  Gets the name of fields
	 *
     * @param $result
     * @param $offset
     *
     * @abstract
     */
    abstract function getFieldType($result, $offset);

    /** getFieldsNum()
	 *  Gets the number of fields
	 *
     * @param $result
     *
     * @abstract
     */
    abstract function getFieldsNum($result);
}