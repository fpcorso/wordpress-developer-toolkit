<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
  * Class Description
  *
  * @since 0.1.0
  */
class WPDT_Plugin_Dev_Page
{
  /**
   * Page Tabs Array
   *
   * @since 1.0.0
   */
   public $page_tabs = array();

    /**
      * Main Construct Function
      *
      * Call functions within class
      *
      * @since 0.1.0
      * @uses WPDT_Plugin_Dev_Page::load_dependencies() Loads required filed
      * @uses WPDT_Plugin_Dev_Page::add_hooks() Adds actions to hooks and filters
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
      //Insert code
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
      add_action('admin_menu', array( $this, 'add_plugin_dev_submenu_page'), 11);
      add_action( 'plugins_loaded', array( $this, 'load_tabs' ) );
    }

    /**
     * Adds Plugin Dev Page To Menu
     *
     * @since 1.0.0
     * @return void
     */
    public function add_plugin_dev_submenu_page() {
      add_submenu_page('wpdevtool', __('Plugin Dev', 'wordpress-developer-toolkit'), __('Plugin Dev', 'wordpress-developer-toolkit'), 'moderate_comments', 'wpdt_plugin_dev', array($this,'generate_page'));
    }

    /**
     * Generates Content For Plugin Dev Page
     *
     * @since 1.0.0
     * @return void
     */
    public function generate_page() {
      if ( !current_user_can('moderate_comments') ) {
    		return;
    	}
      wp_enqueue_style( 'wpdt_admin_style', plugins_url( '../css/admin.css' , __FILE__ ) );
      wp_enqueue_script( 'wpdt_admin_script', plugins_url( '../js/admin.js' , __FILE__ ) );
    	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'readme-validator';
    	$tab_array = $this->get_tabs();
    	?>
    	<div class="wrap">
    		<h2><?php _e( "Plugin Development", 'wordpress-developer-toolkit' ) ?></h2>
    		<h2 class="nav-tab-wrapper">
    			<?php
    			foreach($tab_array as $tab)
    			{
    				$active_class = '';
    				if ($active_tab == $tab['slug'])
    				{
    					$active_class = 'nav-tab-active';
    				}
    				echo "<a href=\"?page=wpdt_plugin_dev&tab=".$tab['slug']."\" class=\"nav-tab $active_class\">".$tab['title']."</a>";
    			}
    			?>
    		</h2>
    		<div class="wpdt-plugin-dev-tab">
    		<?php
    			foreach($tab_array as $tab)
    			{
    				if ($active_tab == $tab['slug'])
    				{
    					call_user_func($tab['function']);
    				}
    			}
    		?>
    		</div>
    	</div>
    	<?php
    }

    /**
     * Register Tabs In Plugins loaded
     *
     * @since 1.0.0
     */
    public function load_tabs() {
      $this->register_tabs( __( "Readme Validator", 'wordpress-developer-toolkit' ), array( $this, 'readme_validator_tab' ) );
      //$this->register_tabs( __("You do not have proper authority to access this page",'wordpress-developer-toolkit'), '' );
    }

    /**
     *
     */
    public function readme_validator_tab() {
      if ( isset( $_POST["readme"] ) ) {
        include "readme_validation/readme_validator.php";
        echo wpdt_validate_readme( stripslashes($_POST["readme"]) );
      }
      ?>
      <h3><?php _e( "Paste your readme.txt in the box below:", 'wordpress-developer-toolkit' ); ?></h3>
      <form action="" method="post">
        <textarea name="readme" class="wpdt-readme"></textarea><br>
        <button class="button-primary"><?php _e( "Validate Readme", 'wordpress-developer-toolkit' ); ?></button>
      </form>
      <?php
    }

    /**
	  * Registers Page Tabs
	  *
	  * Registers a new tab on the this page
	  *
	  * @since 4.0.0
		* @param string $title The name of the tab
		* @param string $function The function that displays the tab's content
		* @return void
	  */
	public function register_tabs($title, $function) {
		$slug = strtolower(str_replace( " ", "-", $title));
		$new_tab = array(
			'title' => $title,
			'function' => $function,
			'slug' => $slug
		);
		$this->page_tabs[] = $new_tab;
	}

	/**
	  * Retrieves Page Tab Array
	  *
	  * Retrieves the array of titles and functions of the registered tabs
	  *
	  * @since 4.0.0
		* @return array The array of registered tabs
	  */
	public function get_tabs() {
		return $this->page_tabs;
	}
}
$wpdt_plugin_dev_page = new WPDT_Plugin_Dev_Page();
?>
