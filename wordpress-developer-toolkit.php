<?php
/**
 * Plugin Name: WordPress Developer Toolkit
 * Plugin URI: http://mylocalwebstop.com
 * Description:
 * Author: Frank Corso
 * Author URI: http://mylocalwebstop.com
 * Version: 0.2.1
 * Text Domain: wordpress-developer-toolkit
 * Domain Path: /languages
 *
 * Disclaimer of Warranties
 * The plugin is provided "as is". My Local Webstop and its suppliers and licensors hereby disclaim all warranties of any kind,
 * express or implied, including, without limitation, the warranties of merchantability, fitness for a particular purpose and non-infringement.
 * Neither My Local Webstop nor its suppliers and licensors, makes any warranty that the plugin will be error free or that access thereto will be continuous or uninterrupted.
 * You understand that you install, operate, and uninstall the plugin at your own discretion and risk.
 *
 * @author Frank Corso
 * @version 0.2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/**
  * This class is the main class of the plugin
  *
  * When loaded, it loads the included plugin files and add functions to hooks or filters. The class also handles the admin menu
  *
  * @since 0.1.0
  */
class MLWWPDeveloperToolkit
{
    /**
  	 * Cron Manager Object
  	 *
  	 * @var object
  	 * @since 0.1.0
  	 */
  	public $cronManager;

    /**
     * WPDT Version Number
     *
     * @var string
     * @since 0.1.0
     */
    public $version = '0.2.0';

    /**
  	  * Main Construct Function
  	  *
  	  * Call functions within class
  	  *
  	  * @since 0.1.0
  	  * @uses MLWWPDeveloperToolkit::load_dependencies() Loads required filed
  	  * @uses MLWWPDeveloperToolkit::add_hooks() Adds actions to hooks and filters
  	  * @return void
  	  */
    function __construct()
    {
      $this->load_dependencies();
      $this->add_hooks();
    }

    /**
  	  * Load File Dependencies
  	  *
  	  * @since 0.1.0
  	  * @return void
  	  */
    public function load_dependencies()
    {
      include("php/wpdt_plugins_page.php");
      include("php/wpdt_stats_page.php");
      include("php/wpdt_about_page.php");
      include("php/wpdt_shortcodes.php");
      include("php/wpdt_update.php");
      include("php/wpdt_cron.php");
      include("php/wpdt_refresh.php");
      include("php/wpdt-plugin-dev-page.php");

      $this->cronManager = new WPDTCron();
    }

    /**
  	  * Add Hooks
  	  *
  	  * Adds functions to relavent hooks and filters
  	  *
  	  * @since 0.1.0
  	  * @return void
  	  */
    public function add_hooks()
    {
        add_action('admin_menu', array( $this, 'setup_admin_menu'));
        add_action('init', array( $this, 'register_post_types'));
        add_action('plugins_loaded',  array( $this, 'setup_translations'));
        add_action('admin_head', array( $this, 'admin_head'), 900);
        add_action('admin_init','wpdt_update');
    }

    /**
     * Creates Custom Post Types
     *
     * Creates custom post type for plugins
     *
     * @since 0.1.0
     */
    public function register_post_types()
    {
      $labels = array(
  			'name'               => 'Plugins',
  			'singular_name'      => 'Plugin',
  			'menu_name'          => 'Plugin',
  			'name_admin_bar'     => 'Plugin',
  			'add_new'            => 'Add New',
  			'add_new_item'       => 'Add New Plugin',
  			'new_item'           => 'New Plugin',
  			'edit_item'          => 'Edit Plugin',
  			'view_item'          => 'View Plugin',
  			'all_items'          => 'All Plugins',
  			'search_items'       => 'Search Plugins',
  			'parent_item_colon'  => 'Parent Plugin:',
  			'not_found'          => 'No Plugin Found',
  			'not_found_in_trash' => 'No Plugin Found In Trash'
  		);

      $args = array(
  			'show_ui' => false,
  			'show_in_nav_menus' => false,
  			'labels' => $labels,
  			'publicly_queryable' => false,
  			'exclude_from_search' => true,
  			'label'  => 'Plugins',
  			'rewrite' => array('slug' => 'plugin'),
  			'has_archive'        => false,
  			'supports'           => array( 'title', 'editor', 'author' )
  		);

  		register_post_type( 'plugin', $args );
    }

    /**
  	  * Setup Admin Menu
  	  *
  	  * Creates the admin menu and pages for the plugin and attaches functions to them
  	  *
  	  * @since 0.1.0
  	  * @return void
  	  */
  	public function setup_admin_menu()
  	{
  		if (function_exists('add_menu_page'))
  		{
        add_menu_page('WP Dev Toolkit', 'WP Dev Toolkit', 'moderate_comments', 'wpdevtool', array('WPDTPluginPage','generate_page'), 'dashicons-flag');
        add_submenu_page('wpdevtool', __('Stats', 'wordpress-developer-toolkit'), __('Stats', 'wordpress-developer-toolkit'), 'moderate_comments', 'wpdt_stats', array('WPDTStatsPage','generate_page'));
      }
      add_dashboard_page(
				__( 'WPDT About', 'wordpress-developer-toolkit' ),
				__( 'WPDT About', 'wordpress-developer-toolkit' ),
				'manage_options',
				'wpdt_about',
				array('WPDTAboutPage', 'generate_page')
			);
    }

    /**
  	 * Removes Unnecessary Admin Page
  	 *
  	 * Removes the update, quiz settings, and quiz results pages from the Quiz Menu
  	 *
  	 * @since 4.1.0
  	 * @return void
  	 */
  	public function admin_head()
  	{
  		remove_submenu_page( 'index.php', 'wpdt_about' );
  	}

    /**
  	  * Loads the plugin language files
  	  *
  	  * @since 0.1.0
  	  * @return void
  	  */
  	public function setup_translations()
  	{
  		load_plugin_textdomain( 'wordpress-developer-toolkit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  	}
}
$mlwWPDeveloperToolkit = new MLWWPDeveloperToolkit();
?>
