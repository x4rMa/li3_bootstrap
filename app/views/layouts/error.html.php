<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This layout is used to render error pages in both development and production. It is recommended
 * that you maintain a separate, simplified layout for rendering errors that does not involve any
 * complex logic or dynamic data, which could potentially trigger recursive errors.
 */
?>
<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Unhandled Exception</title>
	<?php echo $this->html->style(array('bootstrap')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body>
    
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/">Error</a>
          </div>
        </div>
    </div>    
	
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="well">
                <h1>An unhandled exception was thrown</h1>
                <h3>Configuration</h3>
                <p>
                    This layout can be changed by modifying
                    <code><?php
                        echo realpath(LITHIUM_APP_PATH . '/views/layouts/error.html.php');
                    ?></code>
                </p><p>
                    To modify your error-handling configuration, see
                    <code><?php
                        echo realpath(LITHIUM_APP_PATH . '/config/bootstrap/errors.php');
                    ?></code>
                </p>
            </div>
        </div>
        
		<div class="row-fluid">
			<?php echo $this->content(); ?>
		</div>

    </div>
    
</body>
</html>