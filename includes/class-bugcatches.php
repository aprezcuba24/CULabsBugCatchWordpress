<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://bugcatches.com/
 * @since      1.0.0
 *
 * @package    Bugcatches
 * @subpackage Bugcatches/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bugcatches
 * @subpackage Bugcatches/includes
 * @author     Duvan Monsalve <duvanmonsa@gmail.com>
 */



class Bugcatches {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bugcatches_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	/**
	 * Notifications active
	 */
	protected $active;
	/**
	 * Bugcatches project key
	 */
	protected $api_key;
	/**
	 * Bugcatches client
	 */
	protected $client;

	protected $feedback;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'bugcatches';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bugcatches_Loader. Orchestrates the hooks of the plugin.
	 * - Bugcatches_i18n. Defines internationalization functionality.
	 * - Bugcatches_Admin. Defines all hooks for the admin area.
	 * - Bugcatches_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bugcatches-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bugcatches-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bugcatches-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bugcatches-public.php';

		require_once  plugin_dir_path( dirname( __FILE__ ) ) .'bugcatch-php/Client/ClientFactory.php';
		require_once  plugin_dir_path( dirname( __FILE__ ) ) .'bugcatch-php/ErrorHandler/ErrorHandler.php';
		require_once  plugin_dir_path( dirname( __FILE__ ) ) .'bugcatch-php/ErrorHandler/FlattenException.php';
		require_once  plugin_dir_path( dirname( __FILE__ ) ) .'bugcatch-php/Client/Client.php';


		$this->loader = new \Bugcatches_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bugcatches_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new \Bugcatches_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new \Bugcatches_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		// Add menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

        // Add Settings link to the plugin
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
		// Save/Update our plugin options
		$this->loader->add_action('admin_init', $plugin_admin, 'options_update');


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new \Bugcatches_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		if( $this->getFeedback()) 	{
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'add_feedback_button');
		}
		// Hook up automatic error handling
		set_error_handler(array($this, 'errorHandler'));
//		set_exception_handler(array($this, 'exceptionHandler'));

	}

	/**
	 * Hooks the errors.
	 *
	 * @since    1.0.0
	 */
	function errorHandler($error,$errstr,$errfile, $errline)
	{
		if($this->getActive())
		{
			switch ($error) {
				case E_PARSE:
				case E_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					if($this->isTypeActive('error'))
					{
						$this->reportToBugcatch($errstr,$error,$errfile,$errline);

					}
					break;
				case E_WARNING:
				case E_USER_WARNING:
				case E_COMPILE_WARNING:
				case E_RECOVERABLE_ERROR:
					if($this->isTypeActive('warning'))
					{
						$this->reportToBugcatch($errstr,$error,$errfile,$errline);

					}
					break;
				case E_NOTICE:
				case E_USER_NOTICE:
					if($this->isTypeActive('notice'))
					{
						$this->reportToBugcatch($errstr,$error,$errfile,$errline);

					}
					break;
				case E_STRICT:
					if($this->isTypeActive('strict'))
					{
						$this->reportToBugcatch($errstr,$error,$errfile,$errline);

					}
					break;
				case E_DEPRECATED:
				case E_USER_DEPRECATED:
					if($this->isTypeActive('deprecate'))
					{
						$this->reportToBugcatch($errstr,$error,$errfile,$errline);

					}
					break;
				default :
					if($this->isTypeActive('unknown'))
					{
						$this->reportToBugcatch($errstr,$error,$errfile,$errline);
					}
					break;
			}



		}
	}

	function reportToBugcatch($errstr,$error,$errfile,$errline)
	{
		$errorHandler = $this->getErrorHandler();
		$errorHandler->setFiles($_FILES);
		$errorHandler->setGet($_GET);
		$errorHandler->setPost($_POST);
		$errorHandler->setUserData(array());
		$errorHandler->setUserData(array());
		$exception = new ErrorException($errstr,$error,1,$errfile,$errline);
		$errorHandler->notifyException($exception);
	}
	/**
	 * Hooks the exceptions.
	 *
	 * @since    1.0.0
	 */
	function exceptionHandler() {

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Bugcatches_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	protected function getFeedback()
	{
		$options = get_option($this->plugin_name);
		return $options['feedback'];
	}

	protected function getActive()
	{
		$options = get_option($this->plugin_name);
		return $options['bugcatches_active'];
	}


	/**
	 * @param mixed $active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}

	/**
	 * @return mixed
	 */
	public function getApiKey()
	{
		$options = get_option($this->plugin_name);
		return $options['bugcatches_key'];
	}

	public function isTypeActive($type)
	{
		$options = get_option($this->plugin_name);
		return $options['bugcatches_'.$type];
	}

	/**
	 * @param mixed $api_key
	 */
	public function setApiKey($api_key)
	{
		$this->api_key = $api_key;
	}

	protected function getErrorHandler()
	{
		$clientFactory = new \CULabs\BugCatch\Client\ClientFactory($this->getApiKey());
		return new \CULabs\BugCatch\ErrorHandler\ErrorHandler($clientFactory->getClient(),$this->getActive());

	}

}
