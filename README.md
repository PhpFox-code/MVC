<h2>How to use this MVC</h2>

<h3>Directory structure</h3>

<pre>
web
|-- cache
|-- libs
|   |-- Controller
|   |   `-- IndexController.php
|   |
|   |-- Core
|   |   |-- Cache.class.php
|   |   |-- Controller.class.php
|   |   |-- Format.class.php
|   |   |-- Router.class.php
|   |   |-- Snippet.class.php
|   |   |-- Url.class.php
|   |   |-- View.class.php
|   |   `-- ViewHelper.class.php
|   |
|   |-- Layout
|   |   `-- default.phtml
|   |
|   |-- Model
|   |
|   |-- View
|   |   |-- Index
|   |   |   |-- index.phtml
|   |   |   `-- error.phmtl
|   |   |
|   |   `-- Snippet
|   |
|   |-- autoloader.php
|   |-- config.php
|   `-- global.php
|
|-- .htaccess
`-- index.php</pre>

<hr />

<h3>Setup</h3>

<p>The only configurations you will need to make are:</p>

<ol>
  <li><code>index.php</code>: change location to the <code>/libs/global.php</code> if not in the root.</li>
	<li>All of the directory paths are stored in <code>/libs/config.php</code>, should only need to change <code>PATH_WEB</code> & <code>PATH_BASE</code>.</li>
</ol>

<hr />

<h3>Routing</h3>

<p>We parse URI's such as <code>/index/hello/my/variables/go/here/foobar</code> and place it into the GET array. Dumping <code>$_GET</code> will give you:

<pre>array(
	'controller' => 'index',
	'action'     => 'hello',
	'my'         => 'variables',
	'go'         => 'here',
	'foobar'     => true
)</pre>

<p>This will forward the request onto the <code>index</code> controller and into the <code>hello</code> action.</p>

<hr />

<h3>Controllers</h3>

<p>Controllers are created in <code>/libs/Controller</code>, the file name begins with an uppercase letter and ends in a <code>.class.php</code> extension, so <code>index</code> would be called <code>Index.class.php</code>.</p>

<h4>Actions</h4>

<p>Actions are named the same as specified in the URI, are lowercase, and end in <code>Action</code>. So the <code>index</code> action will be named <code>indexAction()</code>.</p>

<h4>Caching</h4>

<p>You can enable the caching on a per controller basis, by setting a public <code>$enableCache</code> variable and specifying a time in seconds through the <code>$cacheLife</code> public variable. You can disable/enable the cache on a per action basis by calling the <code>$this->view->cache->setCache(true)</code> method. You can also specify the life of the cached file by calling the <code>$this->view->cache->setCacheLife(30)</code> method. All cached files are stored in <code>/cache</code>.</p>

<h4>Forwarding</h4>

<p>You can forward to another action (or controller) via the <code>$this->forward('action', 'controller')</code> command in a controller's action.</p>

<h4>Layouts</h4>

<p>You can change the layout (layouts are stored in <code>/libs/Layout</code>, are lowercase, and end with a <code>.phtml</code> extension) that will wrap the View by calling the <code>$this->setLayout('layout')</code> method in a controllers action.</p>

<h4>Example</h4>

<pre>&lt;?php
class IndexController extends Core_Controller
{
	public $enableCache = true;
	public $cacheLife   = 30;

	public function indexAction() {
		$this->setLayout('new-layout');
		$this->forward('hello');
	}

	public function helloAction() {
		// This is the function that will be rendered to the browser
	}
}</pre>

<hr />

<h3>Views</h3>

<p>Views are stored in the <code>/libs/View</code> directory, and each controller has their own directory. So the <code>Index</code> controller's views will be stored in <code>/libs/Views/Index</code>. Each of the controllers actions have a separate view, so the <code>Index</code> controller's <code>hello</code> action will be stored in <code>/libs/Views/Index/hello.phtml</code>.</p>

<h4>URL generation</h4>

<p>The view has a built in method to generate URL's. You can specify the controller, action and any variables. You can also state whether you want to retain the current pages URL variables (disabled by default). This is called via:</p>

<pre>echo $this->url(
	array(
		'controller'      => 'index',
		'action'          => 'hello',
		'variables'       => array('foo' => 'bar'),
		'variable_retain' => true
	)
);</pre>

<h4>Safe HTML</h4>

<p>You can output HTML to the browser safely by using the <code>$this->safe('evil string')</code> method.</p>