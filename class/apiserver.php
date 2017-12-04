<?php
/**
 * Chronolabs REST Whois API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         whois
 * @since           1.0.2
 * @author          Simon Roberts <meshy@labs.coop>
 * @version         $Id: apiserver.php 1000 2013-06-07 01:20:22Z mynamesnot $
 * @subpackage		api
 * @description		Whois API Service REST
 */
	/**
	 * API Server Class Factory
	 *
	 * @author     Simon Roberts <meshy@labs.coop>
	 * @package    whois
	 * @subpackage api
	 */
	class apiserver {
		
		/**
		 *  __construct()
		 *  Constructor
		 */
		function __construct() {
			if (strlen(session_id())==0) {
				session_name(__CLASS__);
				session_id(md5(sha1($this->getIP())));
				session_start();
			}
		}
		
		
		/**
		 *  __destruct()
		 *  Destructor
		 */
		function __destruct() {
			session_commit();
		}
		/**
		 * parseToArray()
		 * Parses Whois Services Text/Data into an array
		 * 
		 * @param string $data
		 * @param string $item
		 * @param string $function
		 * @param string $class
		 * @param string $output
		 * @return array
		 */
		function parseToArray($data, $item, $function, $class, $output = 'html') {
			$ret = array();
			$nsfound = false;
			$legalfinished=false;
			$sep = ':';
			switch ($class) {
				case 'whois':
					if ($function == 'lookupDomain') {
						$parts = explode("\n", $data);
						$ret = array();
						foreach($parts as $line => $result) {
							foreach (array('..'=>'.', '  '=>' ', '--'=>'-', '::'=>':', ': '=>':', '. '=>':') as $search => $replace)
								while(strpos($result, $search)) {
								$result = str_replace($search, $replace, $result);
							}
							if (strlen(trim($result))) {
								if ($legalfinished==false) {
									if (strpos(strtolower($result), ' id:')>0 || strpos(strtolower(' '.$result), $item)>0) {
										$legalfinished=true;
										$parts = explode($sep, $result);
										$fields = explode(' ', strtolower($parts[0]));
										$fielda = $fields[0];
										unset($fields[0]);
										if ($output=='xml') {
											$fieldb = implode('-', $fields);
											$parts[1] = htmlspecialchars($parts[1]);
										} else
											$fieldb = implode('-', $fields);
										if (strlen($parts[1])&&!isset($ret[$fielda][$fieldb]))
											$ret[$fielda][$fieldb] = $parts[1];
										elseif (strlen($parts[1])&&!is_array($ret[$fielda][$fieldb])) {
											$value = $ret[$fielda][$fieldb];
											unset($ret[$fielda][$fieldb]);
											$ret[$fielda][$fieldb]['node-'.(sizeof($ret[$fielda][$fieldb])+1)] = $value;
											$ret[$fielda][$fieldb]['node-'.(sizeof($ret[$fielda][$fieldb])+1)] = $parts[1];
										} elseif ( is_array($ret[$fielda][$fieldb]) ) {
											$ret[$fielda][$fieldb]['node-'.(sizeof($ret[$fielda][$fieldb])+1)] = $parts[1];
										}
									} else {
										if ($output=='xml') {
											$result = htmlspecialchars($result);
											$ret['legal']['legal-'.(sizeof($ret['legal'])+1)] = $result;
										} else {
											$ret['legal'][] = $result;
										}
									}
								} else {
									if ($nsfound == true && !strpos(strtolower($result),'ame server')) {
										if ($output=='xml') {
											$result = htmlspecialchars($result);
											$ret['legal']['legal-'.(sizeof($ret['legal'])+1)] = $result;
										} else {
											$ret['legal'][] = $result;
										}
									} else {
										$parts = explode($sep, $result);
										if (strpos(strtolower($parts[0]),'name server'))
											$nsfound = true;
										$fields = explode(' ', strtolower($parts[0]));
										$fielda = $fields[0];
										unset($fields[0]);
										if ($output=='xml') {
											$fieldb = implode('-', $fields);
											$parts[1] = htmlspecialchars($parts[1]);
										} else
											$fieldb = implode('-', $fields);
										if (strlen($parts[1])&&!isset($ret[$fielda][$fieldb]))
											$ret[$fielda][$fieldb] = $parts[1];
										elseif (strlen($parts[1])&&!is_array($ret[$fielda][$fieldb])) {
											$value = $ret[$fielda][$fieldb];
											unset($ret[$fielda][$fieldb]);
											$ret[$fielda][$fieldb]['node-'.(sizeof($ret[$fielda][$fieldb])+1)] = $value;
											$ret[$fielda][$fieldb]['node-'.(sizeof($ret[$fielda][$fieldb])+1)] = $parts[1];
										} elseif ( is_array($ret[$fielda][$fieldb]) ) {
											$ret[$fielda][$fieldb]['node-'.(sizeof($ret[$fielda][$fieldb])+1)] = $parts[1];
										}
									}
								}
							}
						}
					} elseif ($function == 'lookupIP') {
						$parts = explode("\n", $data);
						$ret = array();
						$sep = ': ';
						foreach($parts as $line => $result) {
							foreach (array('..'=>'.', '  '=>' ', '--'=>'-', '::'=>':') as $search => $replace)
								while(strpos($result, $search)) {
								$result = str_replace($search, $replace, $result);
							}
							if (strlen(trim($result))) {
								$parts = explode($sep, $result);
								if (strlen($parts[1])) {
									if ($output=='xml') {
										$parts[1] = htmlspecialchars($parts[1]);
										if (isset($ret[$parts[0]])&&!is_array($ret[$parts[0]])) {
											$value = $ret[$parts[0]];
											unset($ret[$parts[0]]);
											$ret[$parts[0]][$parts[0].'-'.(sizeof($ret[$parts[0]])+1)] = $value;
											$ret[$parts[0]][$parts[0].'-'.(sizeof($ret[$parts[0]])+1)] = $parts[1];
										} elseif (isset($ret[$parts[0]])&&is_array($ret[$parts[0]])) {
											$ret[$parts[0]][$parts[0].'-'.(sizeof($ret[$parts[0]])+1)] = $parts[1];
										} else {
											$ret[$parts[0]] = $parts[1];
										}
									} else {
										if (isset($ret[$parts[0]])&&!is_array($ret[$parts[0]])) {
											$value = $ret[$parts[0]];
											unset($ret[$parts[0]]);
											$ret[$parts[0]][] = $value;
											$ret[$parts[0]][] = $parts[1];
										} elseif (isset($ret[$parts[0]])&&is_array($ret[$parts[0]])) {
											$ret[$parts[0]][] = $parts[1];
										} else {
											$ret[$parts[0]] = $parts[1];
										}
									}
								}
							}
						}						
					}
					break;
				default:
					return explode("\n", $data);
			}
			return $ret;
		}
		/**
		 * getIP()
		 * Gets Users IP Address
		 *
		 * @return string
		 */
		function getIP() {
			$ip = $_SERVER['REMOTE_ADDR'];
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			return $ip;
		}
		
		/**
		 * extractURLS()
		 * Pulls URLs from String Data
		 * 
		 * @param string $data
		 * @param array $ret
		 * @return array
		 */
		function extractURLS($data, $ret) {
			$valid_chars = "a-z0-9\/\-_+=.~!%@?#&;:\,$\|";
			$end_chars   = "a-z0-9\/\-_+=~!%@?#&;:\,$\|";
			$ret = $this->findWebsites($ret, $ret);
			$patterns   = array();
			$patterns[]     = "/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([{$valid_chars}]+[{$end_chars}])/ei";
			$patterns[]     = "/(^|[^]_a-z0-9-=\"'\/:\.])www\.((([a-zA-Z0-9\-]*\.){1,}){1}([a-zA-Z]{2,6}){1})((\/([a-zA-Z0-9\-\._\?\,\'\/\\+&%\$#\=~])*)*)/ei";
			$patterns[]     = "/((([a-zA-Z0-9\-]*\.){1,}){1}([a-zA-Z]{2,6}){1})((\/([a-zA-Z0-9\-\._\?\,\'\/\\+&%\$#\=~])*)*)/ei";
			$patterns[]     = "/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.([{$valid_chars}]+[{$end_chars}])/ei";
			$match = array();
			foreach($patterns as $id => $pattern) {
       			preg_match($pattern, $data, $result);
       			$match[$id] = $result;
       		}
       		foreach($match as $id => $urls) {
       		    if (isset($urls[0]) && is_string($urls[0])) {
	       			$ret['urls'][$this->getBaseDomain($urls[0])]['url-'.(sizeof($ret['urls'][$this->getBaseDomain($urls[0])])+1)] = $urls[0];
	       			$ret['domain'][$this->getBaseDomain($urls[0])] = $this->getBaseDomain($urls[0]);
       			}
       		}
       		return $ret;		
		}
		
		/**
		 * extractEmails()
		 * Pulls Emails from String Data
		 * 
		 * @param string $data
		 * @param array $ret
		 * @return array
		 */
		function extractEmails($data, $ret) {
			$valid_chars = "a-z0-9\/\-_+=.~!%@?#&;:,$\|";
			$end_chars   = "a-z0-9\/\-_+=~!%@?#&;:,$\|";
			$ret = $this->findEmails($ret, $ret);		
			$patterns   = array();
			$patterns[]     = "/(^|[^]_a-z0-9-=\"'\/:\.]+)([-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+)@((?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?)/ei";
			$patterns[]		= "^[_a-zA-Z0-9-\-_]+(\.[_a-zA-Z0-9-\-_]+)*@[a-zA-Z0-9-\-]+(\.[a-zA-Z0-9-\-]+)*(\.([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|mobi|asia|museum|name))$";
       		$match = array();
       		foreach($patterns as $id => $pattern) {
       			preg_match($pattern, $data, $result);
       			$match[$id] = $result;
       		}
       		foreach($match as $id => $email) {
       			if (isset($email[0])) {
	       			$ret['email'][$email[3]][$email[2]] = trim($email[0]);
	       			$ret['domains'][$email[3]] = $email[3];
       			}
       		}
       		return $ret;		
		}
		
		/**
		 * extractEmails()
		 * Pulls Emails from String Data
		 *
		 * @param string $data
		 * @param array $ret
		 * @return array
		 */
		function sortArray($data = array(), $dir = SORT_DESC) {
		    $string = $array = false;
		    foreach($data as $key => $values) {
		        if (is_string($values)) {
		            $string = true;
		        } elseif (is_array($values)) {
		            $array = true;
		        }
		    }
		    if ($array==true)
		    {
		        foreach($data as $key => $values) {
		            if (is_array($values)) {
		                $data[$key] = self::sortArray($values, $dir);
		            }
		        }
		    }
		    if ($string==true)
		    {
		        $ret = $quekeys = $queue = array();
		        foreach($data as $key => $values) {
		            if (is_string($values)) {
		                $queue[$key] = $values;
		                $quekeys[$key] = $key;
		                unset($data[$key]);
		            }
		        }
		        foreach(array_keys($data) as $key)
		            $quekeys[$key] = $key;
		        sort($quekeys, $dir);
		        
		        foreach($quekeys as $key) {
		            $ret[$key] = (isset($queue[$key])?$queue[$key]:$data[$key]);
		        }
		        return $ret;
		    }
		    return $data;
		}
		
		/**
		 * extractEmails()
		 * Pulls Emails from String Data
		 *
		 * @param string $data
		 * @param array $ret
		 * @return array
		 */
		function cleanEmails($data = array(), &$ret = array()) {
		    foreach($data as $key => $values) {
		        if (is_string($values)) {
        		    if (strpos($values, ' ')) {
        		       foreach(explode(" ", $values) as $value)
        		       {
        		           if (checkEmail($value) == $value)
        		               return $value;
        		       }
        		    } elseif (checkEmail($values) == $values) {
        		       return $values;
        		    }
    		    } elseif (is_array($values)) {
    		        $ret[$key] = $data[$key] = self::cleanEmails($values, $ret);
    		    }
		    }
            return $data;
		}
		/**
		 * findEmails()
		 * Extracts Email Addresses from Array Data
		 * 
		 * @param array $ret
		 * @param string $where
		 * @param string $key
		 * @return array
		 */
		function findEmails($ret, $where, $key) {
			if ($key == 'email'&& !is_array($where)) {
				$address = explode('@', $where);
				$ret['email'][$address[1]][$address[0]] = $where;
			} elseif (is_array($where)) {
				foreach($where as $key => $value) {
					$ret = $this->findEmails($ret, $where[$key], $key);
				}
			}
			return $ret;
		}
		/**
		 * findWebsites()
		 * Extracts Websites from Array Data
		 * 
		 * @param array $ret
		 * @param string $where
		 * @param string $key
		 * @return array
		 */
		function findWebsites($ret, $where, $key) {
			if (in_array($key, array('website','domain','site')) && !is_array($where)) {
				$ret['urls'][$this->getBaseDomain($where)]['url-'.(sizeof($ret['urls'][$this->getBaseDomain($where)])+1)] = $where;
	       		$ret['domains'][$this->getBaseDomain($where)] = $this->getBaseDomain($where);
			} elseif (is_array($where)) {
				foreach($where as $key => $value) {
					$ret = $this->findWebsites($ret, $where[$key], $key);
				}
			}
			return $ret;
		}
		
		/**
		 * validateEmail()
		 * Validates an Email Address
		 * 
		 * @param string $email
		 * @return boolean
		 */
		function validateEmail($email) {
			if(preg_match("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|mobi|asia|museum|name))$", $email)) {
				return true;
			} else {
				return false;
			}
		}
		/**
		 * validateDomain()
		 * Validates a Domain Name
		 *
		 * @param string $domain
		 * @return boolean
		 */
		static function validateDomain($domain) {
			if(!preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {
				return false;
			}
			return $domain;
		}
		
		/**
		 * validateIPv4()
		 * Validates and IPv6 Address
		 * 
		 * @param string $ip
		 * @return boolean
		 */
		static function validateIPv4($ip) {
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === FALSE) // returns IP is valid
			{
				return false;
			} else {
				return true;
			}
		}
		
		/**
		 * validateIPv6()
		 * Validates and IPv6 Address
		 * 
		 * @param string $ip
		 * @return boolean
		 */
		static function validateIPv6($ip) {
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE) // returns IP is valid
			{
				return false;
			} else {
				return true;
			}
		}
		
		/**
		 * getBaseDomain()
		 * Removes Subdomains from Domain String
		 * 
		 * @param string $url
		 * @param boolean $debug
		 * @return string
		 */
		function getBaseDomain($url, $debug = 0)
		{
			$url = strtolower($url);
			$full_domain = parse_url($url, PHP_URL_HOST);
		
			// break up domain, reverse
			$domain = explode('.', $full_domain);
			$domain = array_reverse($domain);
			// first check for ip address
			if (count($domain) == 4 && is_numeric($domain[0]) && is_numeric($domain[1]) && is_numeric($domain[2]) && is_numeric($domain[3])) {
				return $full_domain;
			}
		
			// if only 2 domain parts, that must be our domain
			if (count($domain) <= 2) {
				return $full_domain;
			}
		
			/*
			 *	finally, with 3+ domain parts: obviously D0 is tld now,
			*	if D0 = ctld and D1 = gtld, we might have something like com.uk so,
			*	if D0 = ctld && D1 = gtld && D2 != 'www', domain = D2.D1.D0 else if D0 = ctld && D1 = gtld && D2 == 'www',
			*	domain = D1.D0 else domain = D1.D0 - these rules are simplified below.
			*/
			if (in_array($domain[0], $this->c_tld) && in_array($domain[1], $this->g_tld) && $domain[2] != 'www') {
				$full_domain = $domain[2] . '.' . $domain[1] . '.' . $domain[0];
			} else {
				$full_domain = $domain[1] . '.' . $domain[0];
			}
			// did we succeed?
			return $full_domain;
		}
	}