<?php

/**
 * This is an example class of a controller in Colibri. You can safely delete
 * this class or modify it.
 */

class Home extends Colibri\Controller {

  public function __construct() {
    // Do stuff necessary for all methods
    // ...

    // If you override the constructor, do not forget to call the parent
    // constructor as well !
    parent::__construct();
  }

  public function index() {
    // Could define another layout if needed. Defaults to 'default'. Do not
    // add the extension.
    $this->view->layout('default');

    // Could define another view. Defaults to 'default'. This is usually
    // different per controller. Do not add the extension. You can add a directory
    // structure if you need to
    $this->view->view('my_dir/my_view');

    // Define any variables you want for the views and layouts. These variables
    // are shared between the layout and the views, except for:
    //  - view: in the layout, will be the rendered view
    //  - stylesheets
    //  - scripts
    // You can set these variables for your views however. The values will just
    // be different for the layouts
    $this->view->set('head_title', "You've successfully installed Colibri !")
               ->set('my_title', 'Home page')
               ->set('my_custom_var', 'This is some default text.');

    // This variable will be available in a view, but not in a layout. Notice
    // the url() helper function
    $this->view->set('view', '<p><strong>This is some more text and <a href="' . Colibri\url('Home', 'json_page', array('world', '<script>alert("evil !")</script>')) . '">a link</a>.</strong></p>');

    // Can add stylesheets and scripts easily
    $this->view->add_js('js/my-file.js')
               ->add_css('css/my-file.css');
  }

  public function json_page($name = '') {
    // Set the render mode to JSON
    $this->view->json();

    $this->view->set('look_mum', "I do JSON !")->set('my_title', 'JSON page');

    // Careful ! URL params are not sanitized ! That's your job !
    $this->view->set('my_url_param', $name);
  }
}
