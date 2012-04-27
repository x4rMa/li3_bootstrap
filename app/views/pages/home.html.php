<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\core\Libraries;
use lithium\data\Connections;

$this->title('Home');

$self = $this;

$notify = function($status, $message, $solution = null) {
	$html  = '<div class="alert alert-block alert-'.$status.'"><h4 class="alert-heading">'.$message.'</h4>';
	$html .= '<div class="prettyprint">'.$solution.'</div>';
    $html .= '</div>';
	return $html;
};

$support = function($classes) {
	$result = '';

	foreach ($classes as $class => $enabled) {
		$name = substr($class, strrpos($class, '\\') + 1);
		$url = 'http://lithify.me/docs/' . str_replace('\\', '/', $class);
		$status = $enabled ? '&#x2714;' : '&#x2718;';
		$enabled = $enabled ? 'success' : 'danger';

		$item = '<a  href="'.$url.'" class="btn btn-'.$enabled.'">'.$status;
		$item .= $name.'</a> ';
		$result .= $item;
	}
	return $result;
};

$checks = array(
	'resourcesWritable' => function() use ($notify) {
		if (is_writable($path = Libraries::get(true, 'resources'))) {
			return $notify('success', 'Resources directory is writable.');
		}
		$path = str_replace(dirname(LITHIUM_APP_PATH) . '/', null, $path);
		$solution = null;

		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			$solution  = 'To fix this, run the following from the command line: ';
			$solution .= "<code>$ chmod -R 0777 {$path}</code>.";
		}
		return $notify(
			'fail',
			'Your resource path is not writeable.',
			$solution
		);
	},
	'database' => function() use ($notify) {
		$config = Connections::config();

		if (!empty($config)) {
			return $notify('success', 'Database connection/s configured.');
		}
		return $notify(
			'info',
			'No database connection defined.',
			"To create a database connection:
			<ol>
				<li>Edit the file <code>config/bootstrap.php</code>.</li>
				<li>
					Uncomment the line having
					<code>require __DIR__ . '/bootstrap/connections.php';</code>.
				</li>
				<li>Edit the file <code>config/bootstrap/connections.php</code>.</li>
			</ol>"
		);
	},
	'magicQuotes' => function() use ($notify) {
		if (get_magic_quotes_gpc() === 0) {
			return;
		}
		return $notify(
			'error',
			'Magic quotes are enabled in your PHP configuration.',
			'Please set <code>magic_quotes_gpc = Off</code> in your <code>php.ini</code> settings.'
		);
	},
	'registerGlobals' => function() use ($notify) {
		if (!ini_get('register_globals')) {
			return;
		}
		return $notify(
			'error',
			'Register globals is enabled in your PHP configuration.',
			'Please set <code>register_globals = Off</code> in your <code>php.ini</code> settings.'
		);
	},
	'change' => function() use ($notify, $self) {
		$template = $self->html->link('template', 'http://lithify.me/docs/lithium/template');

		return $notify(
			'info',
			"You're using the application's default home page.",
			"To change this {$template}, edit the file
			<code>views/pages/home.html.php</code>.
			To change the layout,
			(that is what's wrapping content)
			edit the file <code>views/layouts/default.html.php</code>."
		);
	},
	'routing' => function() use ($notify, $self) {
		$routing = $self->html->link('routing', 'http://lithify.me/docs/lithium/net/http/Router');

		return $notify(
			'info',
			'Use custom routing.',
			"To change the {$routing} edit the file <code>config/routes.php</code>."
		);
	},
	'tests' => function() use ($notify, $self) {
		$tests = $self->html->link('run all tests', '/test/all');
		$dashboard = $self->html->link('test dashboard', '/test');
		$ticket = $self->html->link('file a ticket', 'http://dev.lithify.me/lithium/tickets');

		return $notify(
			'info',
			'Run the tests.',
			"Check the builtin {$dashboard} or {$tests} now to ensure Lithium
			is working as expected. Do not hesitate to {$ticket} in case a test fails."
		);
	},
	'dbSupport' => function() use ($notify, $support) {
		$paths = array('data.source', 'adapter.data.source.database', 'adapter.data.source.http');
		$list = array();

		foreach ($paths as $path) {
			$list = array_merge($list, Libraries::locate($path, null, array('recursive' => false)));
		}
		$list = array_filter($list, function($class) { return method_exists($class, 'enabled'); });
		$map = array_combine($list, array_map(function($c) { return $c::enabled(); }, $list));

		return $notify(
			'info',
			'Database support',
			'<div class="test-result solution">' . $support($map) . '</div>'
		);
	},
	'cacheSupport' => function() use ($notify, $support) {
		$list = Libraries::locate('adapter.storage.cache', null, array('recursive' => false));
		$list = array_filter($list, function($class) { return method_exists($class, 'enabled'); });
		$map = array_combine($list, array_map(function($c) { return $c::enabled(); }, $list));

		return $notify(
			'info',
			'Cache support',
			'<div class="test-result solution">' . $support($map) . '</div>'
		);
	}
);

?>


<div class="hero-unit">
    <h1>Welcome to your new App</h1>
    <p>It's frech installed with some nice bootstrap flavour and some nifty libraries for you to get your app up and runnig fast!</p>
</div>

<div class="row-fluid">
    <div class="span8">

        <h2>Getting Started</h2>
        <?php foreach ($checks as $check): ?>
            <?php echo $check(); ?>
        <?php endforeach; ?>
    </div>

    <div class="span4 well">
        <h3>Additional Resources</h3>
        <ul class="additional-resources">
            <li><?php echo $this->html->link('Documentation (Draft)', 'http://dev.lithify.me/drafts/source/en'); ?></li>
            <li><?php echo $this->html->link('API Documentation', 'http://lithify.me/docs/lithium'); ?></li>
            <li>
                Development <?php echo $this->html->link('Wiki', 'http://dev.lithify.me/lithium/wiki'); ?>
                and <?php echo $this->html->link('Timeline', 'http://dev.lithify.me/lithium/timeline'); ?>
            </li>
            <li>
                <?php echo $this->html->link('#li3 general support', 'irc://irc.freenode.net/#li3'); ?>
                and
                <?php echo $this->html->link('#li3-core core discussion', 'irc://irc.freenode.net/#li3-core'); ?>
                IRC channels
                (<?php echo $this->html->link('logs', 'http://lithify.me/bot/logs'); ?>)
            </li>
        </ul>
    </div>
    
    <div class="span4 well">
        <h3>Documentation</h3>
        <ul class="additional-resources">
            <li><?php echo $this->html->link('Documentation', '/docs'); ?></li>
        </ul>
    </div>

</div>