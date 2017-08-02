<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="maincontentcontainer">
 *
 * @package Quark
 * @since Quark 1.0
 */
?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->


<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php
	$bo_yandex_verif_id = of_get_option('bo_yandex_verif_id', '');
	if (!empty($bo_yandex_verif_id)) {
		echo '<meta name=\'yandex-verification\' content=\''.$bo_yandex_verif_id.'\'>';
	}
?>

<meta http-equiv="cleartype" content="on">

<!-- Responsive and mobile friendly stuff -->
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
<?php
    //Вставляем Favicon`ку.
    //http://htmlbook.ru/faq/kak-dobavit-ikonku-sayta-v-adresnuyu-stroku-brauzera
	$favicon_url = of_get_option('bo_favicon', '');
    if (!$favicon_url) {
	    $favicon_url = of_get_option('bo_toppanel_logo_url', '');
	}
	$favicon_ext = pathinfo($favicon_url, PATHINFO_EXTENSION);
	
	if ($favicon_ext == 'ico') {
	    echo '<link rel="shortcut icon" href="'.$favicon_url.'" type="image/x-icon" />';
	} elseif ($favicon_ext == 'gif') {
	    echo '<link rel="shortcut icon" href="'.$favicon_url.'" type="image/gif" />';
	} elseif ($favicon_ext == 'png') {
	    echo '<link rel="shortcut icon" href="'.$favicon_url.'" type="image/png" />';
	}
?>

<?php
    wp_head();
?>
</head>

<?php
$body_class_addit = '';

if (is_category()) {
    global $current_cat_id;
    global $current_cat_type_id;
    global $current_cat_template_id;
    global $current_cat_sidebar;
    global $current_cat_sidebar_pos;
    global $cat_header;
    
    $current_cat_id = get_query_var('cat');
    $current_cat_type_id = of_get_option('bo_cat_'.$current_cat_id.'_type', 'usual');
    $current_cat_template_id = of_get_option('bo_cat_'.$current_cat_id.'_template', '1');
    $current_cat_sidebar = sanitize_text_field(of_get_option('bo_cat_template_'.$current_cat_template_id.'_sidebar', 'none'));
    $current_cat_sidebar_pos = sanitize_text_field(of_get_option('bo_cat_template_'.$current_cat_template_id.'_sidebar_pos', 'left'));    
    $cat_header = sanitize_text_field(of_get_option('bo_cat_'.$current_cat_id.'_header', ''));
    
    $body_class_addit = 'template-'.$current_cat_template_id; 

} elseif (is_single()) {
    global $bo_post_type;
    
    $bo_post_type = bo_get_field($post->ID, 'bo_cf_post_type', of_get_option('bo_post_type_default', 'post'));
    $body_class_addit = 'post-'.$bo_post_type;
} elseif (is_page() || is_home()) {
    global $page_id;
    
    $page_id = get_the_ID();
    $body_class_addit = 'page-'.$page_id;
}

global $lazyload_image;
global $lazyload_loader_gif;

$lazyload_image = of_get_option('bo_image_lazyload', '0');
if ($lazyload_image) {
    $lazyload_loader_gif = of_get_option('bo_image_lazyload_loader_gif', '');
}
?>

<body <?php body_class($body_class_addit); ?>>

<div id="wrapper" class="hfeed site">

	<div id="headercontainer">
	    <header>
<?php
		    if (of_get_option('bo_topbar', '0')) {
?>
	            <div id="topbar">
	                <div class="site-header row"></div>
	            </div>
<?php
		    }

		    if (of_get_option('bo_topbar2', '0')) {
?>
	            <div id="topbar2">
	                <div class="site-header row"></div>
	            </div>
<?php
		    }
		    
		    if (of_get_option('bo_toppanel', '0')) {
?>
	            <div id="toppanel">
	                <div class="site-header row">
<?php
                        foreach (array(1,2,3) as $block_inc) {
							
                            $block_type = of_get_option('bo_toppanel_block'.$block_inc, 'none');
							
                            if ($block_type !== 'none') {
?>
                                <div class="col <?php echo of_get_option('bo_toppanel_block'.$block_inc.'_grid', 'grid_4_of_12'); ?>">
<?php
                                    if ($block_type == 'logo') {
?>
                                        <div class="<?php echo 'block'.$block_inc; ?> logo">
                                            <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?>" rel="home">
                                                <img src="<?php echo of_get_option('bo_toppanel_logo_url', ''); ?>" alt="" />
                                            </a>
                                        </div> <!-- /.logo -->
<?php
                                    }
?>
                                </div> <!-- /.col.grid_*_of_12 -->
<?php
                            }
                        }
?>
	                </div>
	            </div>
<?php
		    }
		    
?>
	        
	        
    	    
            <div id="topmenu">
        		<div id="topmenu-fix" class="site-header row">
<?php
        		    if (of_get_option('bo_topmenu_minilogo', '1') && of_get_option('bo_toppanel_logo_url', '')) {
?>
            		    <span class="logo">
            				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home">
            					<img src="<?php echo of_get_option('bo_toppanel_logo_url', ''); ?>" alt="" />
            				</a>
            			</span>
<?php
        			}
        			
        			if (1>2) {
?>
            			<div class="social-media-icons">
            				<?php echo quark_get_social_media(); ?>
            			</div>
<?php
        			}
        			
?>
        			<nav id="site-navigation" class="main-navigation align-<?php echo of_get_option('bo_topmenu_align', 'left'); ?>" role="navigation">
<?php
                        //Префикс top- обязателен для маркировки верхнего меню. Это нужно для Theme Options.
        			    //wp_nav_menu(array('theme_location' => 'top-left', 'menu_class' => 'nav-menu', 'walker' => new BO_Walker_Top_Nav_Menu)); //Отображается только в десктопном представлении.
        			    //wp_nav_menu(array('theme_location' => 'top-right', 'menu_class' => 'nav-menu', 'walker' => new BO_Walker_Top_Nav_Menu)); //Отображается только в десктопном представлении.
        			    //wp_nav_menu(array('theme_location' => 'top-mobile', 'menu_class' => 'nav-menu', 'walker' => new BO_Walker_Top_Nav_Menu)); //Отображается только в мобильном представлении.
        			    
        			    $temp = wp_nav_menu(array('echo' => false, 'fallback_cb' => '__return_false', 'theme_location' => 'top-left', 'container_class' => 'menu-top-left-container', 'menu_class' => 'nav-menu')); //Отображается только в десктопном представлении.
        			    if (!empty($temp)) {
        			        echo $temp;
        			    }
        			    
        			    $temp = wp_nav_menu(array('echo' => false, 'fallback_cb' => '__return_false', 'theme_location' => 'top-right', 'container_class' => 'menu-top-right-container', 'menu_class' => 'nav-menu')); //Отображается только в десктопном представлении.
        			    if (!empty($temp)) {
        			        echo $temp;
        			    }
        			    
        			    $temp = wp_nav_menu(array('echo' => false, 'fallback_cb' => '__return_false', 'theme_location' => 'top-mobile', 'container_class' => 'menu-top-mobile-container', /*'menu_id' => 'menu-top-mobile', */'menu_class' => 'nav-menu')); //Отображается только в мобильном представлении.
        			    if (!empty($temp)) {
        			        echo $temp;
        			    }
?>
        			</nav> <!-- /.site-navigation.main-navigation -->
        		
        		</div> <!-- /.site-header.row -->
            </div>
        </header>
	</div> <!-- /#headercontainer -->

	<div id="maincontentcontainer">
		<?php	do_action( 'quark_before_woocommerce' ); ?>