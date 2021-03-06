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
 * Starts up Colibri.
 */

/**
 * The path to the Colibri sys folder. This contains the Colibri core. Must
 * end with a slash !
 */
define('COLIBRI_SYS_PATH', '../sys/');

/**
 * Autoload
 */
require_once(COLIBRI_SYS_PATH . 'autoload.php');

/**
 * And away we go !
 */
$colibri = new Colibri\Application(COLIBRI_SYS_PATH . '../app/conf.php');
$colibri->run()->respond();
