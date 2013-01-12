<?php
namespace Core;

/**
 * Handles all of the request based aspects of the system.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Request
{
	/**
	 * The URL that the user has visited.
	 *
	 * @access private
	 * @var    string
	 */
	private static $_url;

	/**
	 * A single entry point to the $_GET superglobal
	 * 
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_get;

	/**
	 * A single entry point to the $_POST superglobal
	 * 
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_post;

	/**
	 * A single entry point to the $_SERVER superglobal.
	 *
	 * @access private
	 * @var    array
	 * @static
	 */
	private static $_server;

	/**
	 * Return a breakdown of the URL into their sections.
	 *
	 * @access public
	 * @return array
     * @static
	 */
	public static function getUrlBreakdown() {
		// Set the URL
		self::$_url = trim($_SERVER['REQUEST_URI'], '/');
		// We do not want the start and the end slash, explode on separators, and filter
		$urlBreakdown = explode('/', self::$_url);
		$urlBreakdown = array_filter($urlBreakdown);

		// Start to piece back together and create a nice, usable, array
		$url = array(
			'controller' => isset($urlBreakdown[0]) ? $urlBreakdown[0] : 'Index',
			'action'     => isset($urlBreakdown[1]) ? $urlBreakdown[1] : 'index'
		);

		// Chunk them into variable->value
		$urlBreakdown = array_chunk($urlBreakdown, 2);

		// The first index will be the controller/action
		// We have already set this so just ignore
		unset($urlBreakdown[0]);

		// Loop over the remaining array, these are GET variables
		foreach ($urlBreakdown as $urlSegment) {
			// If there is no value, set it to true
			$url[$urlSegment[0]] = isset($urlSegment[1])
				? $urlSegment[1]
				: true;
		}

		// Put this into the GET string so we no never have to call this method again
		$_GET = array_merge($_GET, $url);

		// We want all requests to the GET and POST to be through this class, so we need
		// .. unset the superglobals (after storing a copy).
		self::$_get    = $_GET;
		self::$_post   = $_POST;
		self::$_server = $_SERVER;
		unset($_GET, $_POST, $_SERVER);
	}

	/**
	 * Return the URL that the user has visited.
	 *
	 * @access public
	 * @param  string $section        A specific part of the URL.
	 * @param  string $replaceSlashes What to replace '/' with.
	 * @return string
	 * @static
	 *
	 * @todo   Add the parse_str() function here.
	 */
	public static function getUrl($section = null, $replaceSlashes = null) {
		return ! empty(self::$_url)
			? str_replace('/', $replaceSlashes, self::$_url)
			: 'index';
	}

	/**
	 * Get a single GET variable.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to return.
	 * @param  mixed  $default  If the variable is not found, this is returned.
	 * @return mixed
	 * @static
	 */
	public static function get($variable, $default = null) {
		return isset(self::$_get[$variable])
			? self::$_get[$variable]
			: $default;
	}

	/**
	 * Get a single POST variable.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to return.
	 * @param  mixed  $default  If the variable is not found, this is returned.
	 * @return mixed
	 * @static
	 */
	public static function post($variable, $default = null) {
		return isset(self::$_post[$variable])
			? self::$_get[$variable]
			: $default;
	}

	/**
	 * Get a single SERVER variable.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to return.
	 * @param  mixed  $default  If the variable is not found, this is returned.
	 * @return mixed
	 * @static
	 */
	public static function server($variable, $default = null) {
		return isset(self::$_server[$variable])
			? self::$_server[$variable]
			: $default;
	}
}