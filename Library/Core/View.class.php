<?php
namespace Core;

/**
 * Handles the main functionality of the view including the parsing,
 * caching, variable storage.
 *
 * Also controls which layout will be shown and provides the means for
 * View Helpers to be called.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class View extends ViewHelper
{
	/**
	 * Which layout we are going to use for this view.
	 *
	 * @access public
	 * @var    string
	 */
	public $layout = 'default';

	/**
	 * The variables that we want to pass to this view.
	 *
	 * @access public
	 * @var    array
	 */
	public $_variables = array();

	/**
	 * Add a variable to the view.
	 *
	 * These variables will be made available to the view. Any variable that has already
	 * been defined will be overwritten.
	 *
	 * @access public
	 * @param  string $variable The variable we wish to add to the view.
	 * @param  string $value    The value of the variable.
	 */
	public function addVariable($variable, $value) {
		$this->_variables[$variable] = $value;
	}

	/**
	 * Returns a set variable if it exists.
	 *
	 * @access public
	 * @param  string $variable The variable that we wish to retrieve from the view.
	 * @return mixed
	 */
	public function getVariable($variable) {
		return isset($this->_variables[$variable])
			? $this->_variables[$variable]
			: false;
	}

	/**
	 * Render the page.
	 *
	 * @access public
	 * @throws Exception If the view does not exist.
	 */
	public function render() {
		// Can we use a cache to speed things up?
		// If the cache object exists then it means the controller wants to use caching
		// However, the action might have disabled it
		if (Cache::has(Request::getUrl(null, '_'))) {
			// The cache is enabled and there is an instance of the file in cache
			$this->_variables['viewContent'] = Cache::get(Request::getUrl(null, '_'));
		}

		// Nope, there is no cache
		else {
			// Set the action location we need to run
			$templateUrlAction = Config::get('path', 'base') . Config::get('path', 'view_script')
				. $this->controller . '/' . $this->action . '.phtml';

			// Does the view file exist?
			if (! file_exists($templateUrlAction)) {
				throw new \Exception('The view ' . $this->action . ' does not exist in ' . $this->controller);
			}

			// And parse the action's script
			$this->_variables['viewContent'] = $this->parse(
				$templateUrlAction,
				$this->_variables,
				Request::getUrl(null, '_')
			);
		}

		// Now start to wrap the view content in the layout
		// Set the action location we need to run
		$templateUrlLayout = Config::get('path', 'base') . Config::get('path', 'layout')
			. $this->layout . '.phtml';

		// And parse the action's script
		$template = $this->parse(
			$templateUrlLayout,
			$this->_variables
		);

		// Inform the bootstrap that we are about to shutdown
		Bootstrap::initShutdown($this->controller, $this->action);

		// And now, the journey ends
		// We die so that we do not call other action's render()
		die($template);
	}

	/**
	 * Parse a template, also caching if desired.
	 * 
	 * @param  string $template  The full path of the template file.
	 * @param  array  $variables The variables we wish to replace.
	 * @param  string $cacheName What to call the cached file.
	 * @return string
	 */
	public function parse($template, $variables, $cacheName = null) {
		// The view exists
		// Extract the variables that have been set
		if ($this->_variables) {
			extract($this->_variables);
		}

		// Enable object buffering
		ob_start();

		// And include the file for parsing
		include $template;

		// Get the content of the view after parsing, and dispose of the buffer
		$content = ob_get_contents();
		ob_end_clean();

		// If we are using the cache then save it
		if (Config::get('cache', 'enable') && $cacheName) {
			Cache::put(
				$cacheName,
				$content . ' <!-- Cached: ' .  date('r'). ' //-->'
			);
		}

		// And return the result of this parse
		return $content;
	}
}