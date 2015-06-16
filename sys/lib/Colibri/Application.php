<?php
/**
 * Colibri, the tiny PHP framework
 *
 * Copyright (c) 2012 Wouter Admiraal (http://github.com/wadmiraal)
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 */

/**
 * @file
 * Defines the Colibri application core class. Bootstraps the entire application.
 */

namespace Colibri;

use Colibri\ApplicationInterface;

/**
 * Log function for system errors.
 *
 * @param string $message
 *        The error message.
 * @param bool $kill = FALSE
 *        (optional) flag setting wether to kill the application. If TRUE, will
 *        route to an HTTP 400 error page.
 */
function sys_error($message, $kill = FALSE) {
  error_log($message);

  if ($kill) {
    try {
      // Prevent a redirection loop
      //  @todo
      if (segment(0) != '400') {
        Router::kill();
      }
    }
    catch(ErrorException $e) {
      die("Colibri encountered an unrecoverable error !");
    }
  }
}

// Register composer autoloader
require_once(COLIBRI_SYS_PATH . 'vendor/autoload.php');

// Require helper functions
require_once(COLIBRI_SYS_PATH . 'functions.php');


class Application implements ApplicationInterface {

  /**
   * The Router instance.
   */
  protected $router;

  /**
   * The response that will eventually be outputted.
   */
  protected $response;

  /**
   * The response headers.
   */
  protected $headers;

  /**
   * Constructor...
   */
  public function __construct($conf_path) {
    // Include configuration file
    require_once($conf_path);
  }

  /**
   * @inheritDoc
   */
  public function get_router() {
    return $this->router;
  }

  /**
   * @inheritDoc
   */
  public function get_response() {
    return $this->response;
  }

  /**
   * @inheritDoc
   */
  public function get_headers() {
    return $this->headers;
  }

  /**
   * @inheritDoc
   */
  public function run() {
    // Autoload
    $autoload = conf('autoload', array());

    foreach (array('Router', 'Controller', 'View') as $file) {
      $autoload[] = COLIBRI_SYS_PATH . 'lib/Colibri/' . $file . '.php';
    }

    foreach($autoload as $file) {
      require_once($file);
    }

    $RouterClass = conf('router_class', conf('i18n_enabled', FALSE) ? 'Colibri\\I18nRouter' : 'Colibri\\Router');

    // Initialize router
    $this->router = new $RouterClass();

    // Route the current request
    $this->router->route();

    $Class  = $this->router->get_class();
    $method = $this->router->get_method();
    $args   = $this->router->get_arguments();

    // Check the controller class definition
    if (empty($Class) || empty($method) || !file_exists(conf('dir_controllers') . $Class . conf('class_extension'))) {
      // Add method and class as arguments for the error handler
      array_unshift($args, $method);
      array_unshift($args, $Class);

      // Get the error handler and generate a 404 error
      $ErrorClass = conf('404_handler');

      if (!isset($ErrorClass) || !file_exists(conf('dir_controllers') . $ErrorClass . conf('class_extension'))) {
        $Class = 'Colibri\Error';
      }
      else {
        $Class = $ErrorClass;
      }

      $method = 'error404';
    }
    else {
      require_once(conf('dir_controllers') . $Class . conf('class_extension'));
    }

    $controller = new $Class();
    
    if( method_exists( $controller,$method ) != false ) {
    	call_user_func_array( array( $controller, $method ), $args );
    	
    }
		else {
			$controller = new \Colibri\Error();
			$controller->error404();
			
		}

    $this->response = $controller->render();
    $this->headers  = $controller->view->get_headers();

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function respond() {
    foreach ($this->headers as $key => $value) {
      header("$key:$value");
    }

    echo $this->response;
  }
}
