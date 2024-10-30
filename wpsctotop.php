<?php
/*
  Plugin Name: CRUDLab Scroll to Top
  Description: CRUDLab Scroll to Top plugin allows you to easily scroll back to top of your page.
  Author: <a href="http://crudlab.com/">CRUDLab</a>
  Version: 1.0.1
 */
error_reporting(0);
require_once( ABSPATH . "wp-includes/pluggable.php" );

register_deactivation_hook(__FILE__, 'wpsctotop_uninstall_hook');
register_activation_hook(__FILE__, 'wpsctotop_install_hook');
register_activation_hook(__FILE__, 'wpsctotop_install_data');

function wpsctotop_plugin_setup_menu() {

    global $wpdb;
    $table = $wpdb->prefix . 'wpsctotop';
    $myrows = $wpdb->get_results("SELECT * FROM $table WHERE id = 1");
    if ($myrows[0]->status == 0) {
        add_menu_page('Scroll To Top', 'Scroll To Top <span id="wpsctotop_circ" class="update-plugins count-1" style="background:#F00"><span class="plugin-count">&nbsp&nbsp</span></span>', 'manage_options', 'scroll-to-top-button', 'wpsctotop_init', plugins_url("/img/ico.png", __FILE__));
    } else {
        add_menu_page('Scroll To Top', 'Scroll To Top <span id="wpsctotop_circ" class="update-plugins count-1" style="background:#0F0"><span class="plugin-count">&nbsp&nbsp</span></span>', 'manage_options', 'scroll-to-top-button', 'wpsctotop_init', plugins_url("/img/ico.png", __FILE__));
    }
}

add_filter('wp_footer', 'wpsctotop');

function wpsctotop_settings_link($links) { 
  $settings_link = '<a href="admin.php?page=scroll-to-top-button&edit=1">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wpsctotop_settings_link' );

function wpsctotop(){
    global $wpdb;
    $table = $wpdb->prefix . 'wpsctotop';
    $myrows = $wpdb->get_results("SELECT * FROM $table WHERE id = 1");
    $status = $myrows[0]->status;
    $display = $myrows[0]->display;
    if($status == 1 && (($display & 2 && is_page() && !defined('is_front_page')) || ($display & 1 && is_front_page()) || ($display & 4 && is_single()))){
        $icon_position = "right";
        $button = $myrows[0]->button;
        $text = $myrows[0]->text;
        $width = $myrows[0]->width;
        $height = $myrows[0]->height;
        $textcolor = $myrows[0]->textcolor;
        $bgcolor = $myrows[0]->bgcolor;
        $placement = $myrows[0]->placement;
        $active = $myrows[0]->active;
        $icon_path = plugins_url("/img/icon_style$button.png", __FILE__);
        $icon_position = $placement;
        list($width, $height, $type, $attr) = getimagesize($icon_path);
        echo '<article><a href="#" class="scrollup">Scroll</a></article>';
        echo '<style>.scrollup{width:'.$width.'px;height:'.$height.'px;position:fixed;bottom:50px;'.$icon_position.':100px;display:none;text-indent:-9999px;z-index:99;background:url('.$icon_path.') no-repeat}</style>';
        echo '<script>jQuery(document).ready(function(){jQuery(window).scroll(function(){jQuery(this).scrollTop()>100?jQuery(".scrollup").fadeIn():jQuery(".scrollup").fadeOut()}),jQuery(".scrollup").click(function(){return jQuery("html, body").animate({scrollTop:0},600),!1})});</script>';
    }
}

if (isset($_REQUEST['wpsctotop_switchonoff'])) {
    global $wpdb;
    $val = $_REQUEST['wpsctotop_switchonoff'];
    $data = array(
        'status' => $val
    );
    $table = $wpdb->prefix . 'wpsctotop';
    if ($wpdb->update($table, $data, array('id' => 1))) {
        echo $val;
    } else {
        echo 'error';
    };
    die;
}


global $wpfblike_db_version;
$wpfblike_db_version = '1.0';


if (isset($_REQUEST['update_wpscrolltotop'])) {

    $display = 0;
    $table_name = $wpdb->prefix . 'wpsctotop';
    foreach ($_REQUEST['display'] as $value){
        $display+=intval($value);
    }
    $data['display'] =$display;
    $data['button'] =$_REQUEST['button'];
    $data['text'] =$_REQUEST['box_text'];
    $data['width'] =$_REQUEST['box_width'];
    $data['height'] =$_REQUEST['box_height'];
    $data['textcolor'] =$_REQUEST['text_color'];
    $data['bgcolor'] =$_REQUEST['background_color'];
    $data['placement'] =$_REQUEST['button_placement'];
    $active =0;
    if(isset($_REQUEST['active'])){
        $active=1;
    }
    
    $data['active'] =$active;   
    
    if ($wpdb->update($table_name, $data, array('id' => 1))) {
        //echo $val;
    } else {
        //echo 'error';
    };
    
   // exit;
}

function wpsctotop_install_hook() {
    global $wpdb;
    global $wpfblike_db_version;

    $table_name = $wpdb->prefix . 'wpsctotop';

    $charset_collate = $wpdb->get_charset_collate();

    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$table_name");

    // status: 1=active, 0 unactive
    // display: 1=all other page, 2= home page, 3=all pages

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
                display int,
                button int,
                text varchar(255),
                width int,
                height int,
                textcolor varchar(25),
                bgcolor varchar(25),
                placement varchar(25),
                active int,
                status int,
		created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		last_modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $check = dbDelta($sql);


    add_option('wpfblike_db_version', $wpfblike_db_version);
}

function wpsctotop_install_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpsctotop';


    $table = $wpdb->prefix . 'wpsctotop';
    $myrows = $wpdb->get_results("SELECT * FROM $table WHERE id = 1");
    if ($myrows == NULL) {
        $wpdb->insert(
                $table_name, array(
            'created' => current_time('mysql'),
            'last_modified' => current_time('mysql'),
            'active' => 1,
            'status' => 1,
            'display' => 3,
            'button' => 1,
            'width' => 35,
            'text' => 'Go to Top',
            'height' => 35,
            'textcolor' => '#238DB1',
            'bgcolor' => '#FFFFFF',
            'placement' => 'right'
                )
        );
    }
}

function wpsctotop_uninstall_hook() {

    global $wpdb;

    $thetable = $wpdb->prefix . "wpsctotop";

    $wpdb->query("DROP TABLE IF EXISTS $thetable");
}

function wpsctotop_init() {

    if (!isset($_REQUEST['edit'])) {
        echo '<script>location = location+"&edit=1"</script>';
    }
    global $wpdb;

    $check = array();

    $table = $wpdb->prefix . 'wpsctotop';
    if (!isset($_REQUEST['edit'])) {
        header('Location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&edit=1');
    }
    if (!(isset($_REQUEST['new']) || isset($_REQUEST['edit']))) {
        $myrows = $wpdb->get_results("SELECT * FROM $table WHERE id = 1");
    } else if (isset($_REQUEST['edit'])) {
        $edit_id = $_REQUEST['edit'];
        $str = "SELECT * FROM $table WHERE id = 1";
        $myrows = $wpdb->get_results($str);
    }
  
    $data = '';
    $data_array = array();
    if ($myrows[0]->display & 1) {
        $display[1] = 'checked';
    };
    if ($myrows[0]->display & 2) {
        $display[2] = 'checked';
    };
    if ($myrows[0]->display & 4) {
        $display[4] = 'checked';
    };
    if ($myrows[0]->placement =='right') {
        $placemetn[1] = 'checked';
    };
    if ($myrows[0]->placement =='left') {
        $placemetn[2] = 'checked';
    };
    
    
    ?>
    <div class="crd-scrolltop">
        <form method="post" id="save_scroll_to_top_preferences">
            <div class="well">
                <div class="col-md-4">
                    Where would you like to display?
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="checkbox" for="checkbox1">
                            <input type="checkbox" name="display[]" <?php echo @$display['1']; ?> data-toggle="checkbox"  id="checkbox1" value="1">
                            Homepage
                        </label>
                        <label class="checkbox" for="checkbox2">
                            <input type="checkbox" name="display[]" <?php echo @$display['2']; ?> data-toggle="checkbox"  id="checkbox2" value="2">
                            All Pages
                        </label>
                        <label class="checkbox" for="checkbox3">
                            <input type="checkbox" name="display[]" <?php echo @$display['4']; ?> data-toggle="checkbox"  id="checkbox3" value="4">
                            All posts
                        </label>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="well">
                <div class="col-md-12">
                    Choose Button:
                </div>
                <div class="crd-icons-wrap col-md-12">
                    <input type="hidden" value="<?php echo $myrows[0]->button;?>" id="icon_id" name="button">
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='1'?'selected':'';?>" data-id="1">
                            <img src="<?php echo plugins_url('img/icon_style1.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='2'?'selected':'';?>" data-id="2">
                            <img src="<?php echo plugins_url('img/icon_style2.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='3'?'selected':'';?>" data-id="3">
                            <img src="<?php echo plugins_url('img/icon_style3.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='4'?'selected':'';?>" data-id="4">
                            <img src="<?php echo plugins_url('img/icon_style4.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='5'?'selected':'';?>" data-id="5">
                            <img src="<?php echo plugins_url('img/icon_style5.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='6'?'selected':'';?>" data-id="6">
                            <img src="<?php echo plugins_url('img/icon_style6.png', __FILE__); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="crd-icons-wrap col-md-12">
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='7'?'selected':'';?>" data-id="7">
                            <img src="<?php echo plugins_url('img/icon_style7.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='8'?'selected':'';?>" data-id="8">
                            <img src="<?php echo plugins_url('img/icon_style8.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='9'?'selected':'';?>" data-id="9">
                            <img src="<?php echo plugins_url('img/icon_style9.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='10'?'selected':'';?>" data-id="10">
                            <img src="<?php echo plugins_url('img/icon_style10.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='11'?'selected':'';?>" data-id="11">
                            <img src="<?php echo plugins_url('img/icon_style11.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='12'?'selected':'';?>" data-id="12">
                            <img src="<?php echo plugins_url('img/icon_style12.png', __FILE__); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="crd-icons-wrap col-md-12">
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='13'?'selected':'';?>" data-id="13">
                            <img src="<?php echo plugins_url('img/icon_style13.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='14'?'selected':'';?>" data-id="14">
                            <img src="<?php echo plugins_url('img/icon_style14.png', __FILE__); ?>"/>
                        </div>
                    </div>
                    <div class="inline-block">
                        <div class="crd-icons <?php echo $myrows[0]->button=='15'?'selected':'';?>" data-id="15">
                            <img src="<?php echo plugins_url('img/icon_style15.png', __FILE__); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>            
            <div class="well">
                <div class="">
                    Button Placement:
                </div>
                <div class="pull-left">
                    <label class="radio">
                        <input type="radio" name="button_placement" id="optionsRadios5" value="right" data-toggle="radio" <?php echo @$placemetn['1']; ?>>
                        Bottom Right
                    </label>
                </div>
                <div class="pull-left" style="margin-left: 15px;">
                    <label class="radio">
                        <input type="radio" name="button_placement" id="optionsRadios6" value="left" data-toggle="radio"  <?php echo @$placemetn['2']; ?>>
                        Bottom Left
                    </label>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="well">
                <div class="pull-left">
                    <input type="submit" class="btn btn-block btn-lg btn-success col-md-2" name="update_wpscrolltotop" value="Save Settings"/>

                </div>
                <div class="pull-right">
                    <input type="checkbox" data-toggle="switch" id="switchonoff" name="status" value="" <?php echo ($myrows[0]->status == 1)?"checked":""?> />
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'wpsctotop_plugin_setup_menu');

//-------------------------------------------------------------------- Adding css JS
function wpsctotop_my_enqueue($hook) {
    if (isset($_GET['page']) && $_GET['page'] == 'scroll-to-top-button') {
        //only for our special plugin admin page
        wp_register_style('wpsctotop_bootstrap_min', plugins_url('/dist/css/vendor/bootstrap.min.css', __FILE__));
        wp_enqueue_style('wpsctotop_bootstrap_min');
        wp_register_style('wpsctotop_flat_ui', plugins_url('/dist/css/flat-ui.css', __FILE__));
        wp_enqueue_style('wpsctotop_flat_ui');
        wp_register_style('wpsctotop_scroll_top', plugins_url('/dist/css/scroll-top.css', __FILE__));
        wp_enqueue_style('wpsctotop_scroll_top');
    }
}

add_action('admin_enqueue_scripts', 'wpsctotop_my_enqueue');
add_action('admin_enqueue_scripts', 'wpsctotop_my_admin_scripts');

function wpsctotop_my_admin_scripts() {
    if (isset($_GET['page']) && $_GET['page'] == 'scroll-to-top-button') {
        wp_enqueue_media();
        wp_register_script('radio_check', plugins_url('/js/radiocheck.js', __FILE__), array('jquery'));
        wp_enqueue_script('radio_check');

        wp_register_script('switch_js', plugins_url('/js/bootstrap-switch.min.js', __FILE__), array('jquery'));
        wp_enqueue_script('switch_js');

        wp_register_script('custom_js', plugins_url('/js/custom.js', __FILE__), array('jquery'));
        wp_enqueue_script('custom_js');

        wp_enqueue_script('jquery-ui-tooltip');
    }
}
