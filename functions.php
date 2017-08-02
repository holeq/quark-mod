<?php

//Подключен для отработки is_plugin_active().
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

/**
 * Quark functions and definitions
 *
 * @package Quark
 * @since Quark 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Quark 1.0
 */
if ( ! isset( $content_width ) )
	$content_width = 790; /* Default the embedded content width to 790px */


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since Quark 1.0
 *
 * @return void
 */
if ( ! function_exists( 'quark_setup' ) ) {
	function quark_setup() {
		global $content_width;

		/**
		 * Make theme available for translation
		 * Translations can be filed in the /languages/ directory
		 * If you're building a theme based on Quark, use a find and replace
		 * to change 'quark' to the name of your theme in all the template files
		 */
		load_theme_textdomain('quark', trailingslashit(get_template_directory()).'languages');

		//Подключаем стили из editor-style.css для редактора постов.
		add_editor_style();

		// Add default posts and comments RSS feed links to head
		add_theme_support('automatic-feed-links');

		// Enable support for Post Thumbnails
		add_theme_support('post-thumbnails');

		//Добавляем для загружаемых изображений еще форматы (помимо базовых из Настройки->Медиафайлы).
		//Не забываем после запускать плагин Regenerate Thumbnails для нарезки новых добавленных форматов.
		//http://codex.wordpress.org/Function_Reference/add_image_size
		add_image_size('post_feature_full_width', 792, 300, true); //формат 792х300 с названием post_feature_full_width
        add_image_size('post_feature_square', 792, 400, true);
        
		// This theme uses wp_nav_menu() in one location
		register_nav_menus(array(
			'top-left' => 'Top Left Menu',
			'top-right' => 'Top Right Menu',
			'top-mobile' => 'Top Mobile Menu', //Появляется только в мобильном варианте.
		));

		// This theme supports a variety of post formats
		add_theme_support('post-formats', array('aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'));

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// Enable support for WooCommerce
		add_theme_support( 'woocommerce' );

		//Подключаем функционал Theme Options.
		//http://wptheming.com/options-framework-theme/
		if ( !function_exists( 'optionsframework_init' ) ) {
			define( 'OPTIONS_FRAMEWORK_DIRECTORY', trailingslashit( get_template_directory_uri() ) . 'inc/' );
			require_once trailingslashit( dirname( __FILE__ ) ) . 'inc/options-framework.php';

			//Загружаем кастомный каркас настроек из options.php в папке темы.
			$optionsfile = locate_template('options.php');
			load_template( $optionsfile );
		}

		// If WooCommerce is running, check if we should be displaying the Breadcrumbs
		if( quark_is_woocommerce_active() && !of_get_option( 'woocommerce_breadcrumbs', '1' ) ) {
			add_action( 'init', 'quark_remove_woocommerce_breadcrumbs' );
		}
		
		//Подключаем сторонний хелпер к Piklist.
		//https://github.com/JasonTheAdams/PiklistHelper
        /* Пока отключен за ненадобностью (09.02.2015)
        if (!class_exists('PiklistHelper')) {
            require_once trailingslashit(dirname(__FILE__)).'piklist/PiklistHelper.php'; 
            PiklistHelper::Initiate();
        }
        */
        
        //Отключаем показ административной панели.
        //if (!current_user_can('administrator') && !is_admin()) {
		if (!of_get_option('bo_admin_bar', '0')) {
			show_admin_bar(false);
		}
        //}
        
	}
}
add_action('after_setup_theme', 'quark_setup');


/**
 * Enable backwards compatability for title-tag support
 *
 * @since Quark 1.3
 *
 * @return void
 */
if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function quark_slug_render_title() { ?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php }
	add_action( 'wp_head', 'quark_slug_render_title' );
}


/**
 * Returns the Google font stylesheet URL, if available.
 *
 * The use of PT Sans and Arvo by default is localized. For languages that use characters not supported by the fonts, the fonts can be disabled.
 *
 * @since Quark 1.2.5
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function quark_fonts_url() {
	
	$fonts_url = '';
	
	$google_fonts = bo_google_fonts_normalize();
	
	if (!empty($google_fonts)) {
	
		$font_families = array();

		foreach ($google_fonts as $key => $value) {
			
			$font_name = $key;
			$font_name = str_ireplace(' ', '+', $font_name);
			$font_name = str_ireplace('"', '', $font_name);
			$font_families[] = $font_name.':400,700';	    
		}
		
		$subsets = 'latin,cyrillic';
		$query_args = array(
			'family' => implode('|', $font_families),
			'subset' => $subsets,
		);
		
		$protocol = is_ssl() ? 'https' : 'http';
		$fonts_url = add_query_arg($query_args, "$protocol://fonts.googleapis.com/css");
	}
	
	return $fonts_url;
}

function bo_google_fonts_normalize($for_options = false) { 

	$google_fonts_used = array();
	
	//Собираем Google шрифты выбранные для подключения в Theme Settings. В опции bo_fonts_google заведен список Google шрифтов.
	$google_fonts_list = of_get_option('bo_fonts_google', '');
	
	if ($google_fonts_list) {
		
    	foreach ($google_fonts_list as $key => $value) {
			
    	    if ($value) {
				
    	        $font_name = $key;
    	        $font_name = str_ireplace('pt_', 'PT_', $font_name);
    	        $font_name = str_ireplace('_2p', '_2P', $font_name);
    	        $font_name = ucwords(str_ireplace('_', ' ', $font_name));
    	        //$google_fonts_used['"'.$font_name.'"'] = $font_name.' (Google)'; //Есть проблемы с парсингом шрифтов в lessphp, поэтому передаем только по одному шрифту и строго в кавычках.
    	        
    	        //Если true, то форматируется key для подстановки в Theme Options, иначе вывод для запроса к Google Fonts.
    	        if ($for_options) { 
    	            $google_fonts_used['"'.$font_name.'", Tahoma, sans-serif'] = $font_name.' (Google)';
    	        } else {
    	            $google_fonts_used['"'.$font_name.'"'] = $font_name;
    	        }
    	    }
    	}
	}
	
	return $google_fonts_used;
}

/**
 * Adds additional stylesheets to the TinyMCE editor if needed.
 *
 * @since Quark 1.2.5
 *
 * @param string $mce_css CSS path to load in TinyMCE.
 * @return string The filtered CSS paths list.
 */
function quark_mce_css( $mce_css ) {
	
	$fonts_url = quark_fonts_url();

	if ( empty( $fonts_url ) ) {
		return $mce_css;
	}

	if ( !empty( $mce_css ) ) {
		$mce_css .= ',';
	}

	$mce_css .= esc_url_raw( str_replace( ',', '%2C', $fonts_url ) );

	return $mce_css;
}
add_filter( 'mce_css', 'quark_mce_css' );


//=== Регистируем сайдбары и области для виджетов.
function quark_widgets_init() {
    
    foreach (array(1,2,3,4,5,6) as $sidebar_inc) {
    	register_sidebar(array(
            'name' => 'Sidebar '.$sidebar_inc,
            'id' => 'sidebar-'.$sidebar_inc, //Префикс sidebar- сообщает Theme Settings о использовании этого сайдбара только в боковых панелях (т.е. сбоку).
            'description' => 'Страницы на которых выводится данный сайдбар задаются в Theme Options',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<div class="widget-title">',
            'after_title' => '</div>'
    	));
    }
    
    foreach (array(1,2,3,4,5,6) as $block_inc) {
    	register_sidebar(array(
            'name' => 'Under Post Block '.$block_inc,
            'id' => 'underpostblock-'.$block_inc, //Префикс underpostblock- сообщает Theme Settings о использовании этого сайдбара только под постами.
            'description' => 'Типы постов под которыми выводится данный блок задаются в Theme Options',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title">',
            'after_title' => '</div>'
    	));
    }
    
    foreach (array(1,2,3,4) as $area_inc) {
    	register_sidebar(array(
            'name' => 'Footer Area '.$area_inc,
            'id' => 'footerarea-'.$area_inc, //Префикс footerarea- сообщает Theme Settings о использовании этого сайдбара только в подвале.
            'description' => 'Область для подвала. В Theme Settings ничего указывать не нужно.',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title">',
            'after_title' => '</div>'
    	));
    }
}
add_action('widgets_init', 'quark_widgets_init');
//==============================================================

//=== Подключение всех файлов стилей и скриптов темы. Куски (не файлы) Javascript-кода выводятся через функцию quark_footer_scripts_code.
function quark_scripts_styles() {

	//Используем стили normalise в качестве сброса css к базовому представлению и позиционированию.
	wp_register_style('normalize', trailingslashit( get_template_directory_uri()).'css/normalize.css', array(), '3.0.2', 'all');
	wp_enqueue_style('normalize');

	//Загружаем набор иконок Font Awesome.
	//http://fortawesome.github.io/Font-Awesome
	wp_register_style('fontawesome', trailingslashit(get_template_directory_uri()).'css/font-awesome.min.css', array(), '4.2.0', 'all');
	wp_enqueue_style('fontawesome');

	//Загружаем грид-систему.
	wp_register_style('gridsystem', trailingslashit(get_template_directory_uri()).'css/grid.css', array(), '1.0.0', 'all');
	wp_enqueue_style('gridsystem');

	//Подключаем LESS к CSS. Должен быть установлен плагин BO Lessify Wordpress.
	//Подключать style.css не нужно - плагин сам возьмет style.less и переработает его в style.css
	//Переработка происходит на стороне сервера (скриптом lessphp), а не в браузере.
	//LESS-синтаксис https://ru.wikipedia.org/wiki/LESS_%28%D1%8F%D0%B7%D1%8B%D0%BA_%D1%81%D1%82%D0%B8%D0%BB%D0%B5%D0%B9%29
	wp_register_style('less-style', trailingslashit(get_template_directory_uri()).'style.less');
	wp_enqueue_style('less-style');
	//wp_enqueue_style('style', get_stylesheet_uri(), array(), '1.2.3', 'all');
	
	//Загружаем стили для AJAX подгрузки постов http://kenwheeler.github.io/slick/
	wp_register_style('slick', trailingslashit(get_template_directory_uri()).'css/slick.css', array(), '1.0.0', 'all');
	wp_register_style('slick-theme', trailingslashit(get_template_directory_uri()).'css/slick-theme.css', array(), '1.0.0', 'all');
	wp_enqueue_style('slick');
	wp_enqueue_style('slick-theme');
	
	//Стиль верхнего меню с адаптацией под мобаил http://slicknav.com/
	wp_register_style('slick-nav-style', trailingslashit(get_template_directory_uri()).'css/slicknav.css', array(), '1.0.0', 'all');
	wp_enqueue_style('slick-nav-style');
	 
	//Загружаем Гугл шрифты.
	$fonts_url = quark_fonts_url();
	
	if (!empty($fonts_url)) {
		wp_enqueue_style('quark-fonts', esc_url_raw($fonts_url), array(), null);
	}

	//Скрипт Modernizr для идентификации HTML5 элементов и фия. Необходимо загрузать в начале документа.
	wp_register_script('modernizr', trailingslashit(get_template_directory_uri()).'js/modernizr-2.8.3-min.js', array('jquery'), '2.8.3', false);
	wp_enqueue_script('modernizr');
	
	//Скрипт верхнего меню с адаптацией под мобаил http://slicknav.com/. Необходимо загрузать в начале документа.
	wp_register_script('slick-nav', trailingslashit(get_template_directory_uri()).'js/jquery.slicknav.min_.js', array('jquery'), '2.8.3', false);
	wp_enqueue_script('slick-nav');
    
	//Скрипт определения появления элемента на экране.
	//https://github.com/customd/jquery-visible
	wp_register_script('check-onscreen', trailingslashit(get_template_directory_uri()).'js/jquery.visible.min_.js', array('jquery'), '1.0.0', false); //false - в <head>, иначе перед </body>
	wp_enqueue_script('check-onscreen');
	
	//Скрипт прикрепления одного объекта к другому.
	//http://tether.io/
	//wp_register_script('tether', trailingslashit(get_template_directory_uri()).'js/tether.min_.js', array(), '0.2.9', false); //false - в <head>, иначе перед </body>
	//wp_enqueue_script('tether'); 
	
	//jQuery UI
	//http://jqueryui.com/
	//wp_register_script('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js', array(), '1.11.4', false); //false - в <head>, иначе перед </body>
	//wp_enqueue_script('jqueryui'); 
    //wp_enqueue_style('jqueryui-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');

	//Скрипты для формы комментирования для поддержки древовидных комментариев. Загрузка объявлена перед закрытием тега body.
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	//Скрипты валидации формы комментирования. Сообщения при валидации можно тут же задать. Загрузка объявлена перед закрытием тега body.
	if (is_singular() && comments_open()) {
		wp_register_script('validate', trailingslashit(get_template_directory_uri()).'js/jquery.validate.min.1.13.0.js', array('jquery'), '1.13.0', true);
		wp_register_script('commentvalidate', trailingslashit(get_template_directory_uri()).'js/comment-form-validation.js', array('jquery', 'validate'), '1.13.0', true);

		wp_enqueue_script('commentvalidate');
		wp_localize_script('commentvalidate', 'comments_object', array(
    			'req' => get_option('require_name_email'),
    			'author'  => 'Please enter your name',
    			'email'  => 'Please enter a valid email address',
    			'comment' => 'Please add a comment', 
			)
		);
	}
	
	//Подключаем jQuery-плагин создания попапов + базовый css к нему. Загрузка объявлена перед закрытием тега body.
	//http://plugins.jquery.com/magnific-popup/
	wp_register_script('magnific-popup', trailingslashit(get_template_directory_uri()).'js/jquery.magnific-popup.min_.js', array('jquery'), '1.0.0', true);
	wp_enqueue_script('magnific-popup');
	wp_enqueue_style('magnific-popup-style', trailingslashit(get_template_directory_uri()).'css/magnific-popup.css');
	
	//Подключаем jQuery-плагин фиксации элементов. Загрузка объявлена перед закрытием тега body.
	//https://github.com/bigspotteddog/ScrollToFixed
	wp_register_script('scroll-to-fixed', trailingslashit(get_template_directory_uri()).'js/jquery-scrolltofixed-min.js', array('jquery'), '1.0.0', true);
	wp_enqueue_script('scroll-to-fixed');
	
	/*
    //В WordPress masonry есть в комплекте по умолчанию, поэтому можно просто его подключить. Плюс подключаем собственные настройки масонри. Необходимо загрузать в начале документа.
    //http://masonry.desandro.com/options.html
	wp_enqueue_script('jquery-masonry');
	wp_register_script('masonry-settings', trailingslashit(get_template_directory_uri()).'js/masonry-settings.js', array('jquery'), '1.0.0', false);
	wp_enqueue_script('masonry-settings');
	*/

	//Плитки, масонри, фильтры, сортировки. Необходимо загрузать в начале документа.
	//http://isotope.metafizzy.co/
	wp_register_script('isotope', trailingslashit(get_template_directory_uri()).'js/isotope.pkgd_.min_.js', array('jquery'), '2.1.1', false);
	wp_enqueue_script('isotope');
	wp_register_script('isotope-settings', trailingslashit(get_template_directory_uri()).'js/isotope-settings.js', array('jquery'), '1.0.0', false);
	wp_enqueue_script('isotope-settings');
	
	//imagesLoaded для корректной отрисовки масонри. Необходимо загрузать в начале документа.
	wp_deregister_script('imagesloaded'); //Поскольку новые версии WP уже включают этот скрипт.
	wp_register_script('imagesloaded', trailingslashit(get_template_directory_uri()).'js/imagesloaded.pkgd_.min_.js', array('jquery'), '3.1.8', false);
	wp_enqueue_script('imagesloaded');
	
	/*
	//Масонри. Необходимо загрузать в начале документа.
	wp_register_script('freetile', trailingslashit(get_template_directory_uri()).'js/jquery.freetile.min_.js', array('jquery'), '0.3.1', false);
	wp_enqueue_script('freetile');	
	*/

    //=== AJAX подгрузка постов к категориям. URL остается тот же. Загрузка объявлена перед закрытием тега body.
    if (is_category()) { 
        //Здесь еще не видна глобальные переменные из header.php.
        $current_cat_id = get_query_var('cat');
        $current_cat_template_id = of_get_option('bo_cat_'.$current_cat_id.'_template', '1');
        
        if (of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pagin', '1') && 
            of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pagin_loadmore', '1')) {
        	wp_register_script('load-more-posts', trailingslashit(get_template_directory_uri()).'js/load-more-posts.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('load-more-posts');
        }
    }
    
	//jQuery слайдер. Необходимо загружать в начале документа.
	//http://kenwheeler.github.io/slick/
	wp_register_script('slick', trailingslashit(get_template_directory_uri()).'js/slick.min_.js', array('jquery'), '1.4.0', false);
	//wp_register_script('slick-settings', trailingslashit(get_template_directory_uri()).'js/slick-settings.js', array('jquery'), '1.0.0', false);
	wp_enqueue_script('slick');
	//wp_enqueue_script('slick-settings');
}
add_action('wp_enqueue_scripts', 'quark_scripts_styles');
//==============================================================

//=== Прямые выводы CSS в head.
function quark_theme_options_styles() {
    
	$output = '';
	$imagepath = trailingslashit(get_template_directory_uri()).'images/';

    //===
    /*
    //.main-navigation
	if ( of_get_option('bo_topmenu_margin', '0px')) {
		$output .= "\n.main-navigation { ";
		$output .= "margin: ".sanitize_text_field(of_get_option('bo_topmenu_margin', '0px')).";";
		$output .= " }";
	}

    //#footercontainer
	$footerColour = apply_filters( 'of_sanitize_color', of_get_option( 'footer_color', '#222222' ) );
	if ( !empty( $footerColour ) ) {
		$output .= "\n#footercontainer { ";
		$output .= "background-color: " . $footerColour . ";";
		$output .= " }";
	}
	
    //.smallprint
	if (of_get_option( 'footer_position', 'center' ) ) {
		$output .= "\n.smallprint { ";
		$output .= "text-align: " . sanitize_text_field( of_get_option( 'footer_position', 'center' ) ) . ";";
		$output .= " }";
	}
	*/
	//===

	if ($output != '') {
		$output = "\n<style>\n" . $output . "\n</style>\n";
		echo $output;
	}
}
add_action('wp_head', 'quark_theme_options_styles');
//==============================================================

//=== Прямые вывода JS-кода в header.
function quark_header_scripts_code() {
    return;
}
add_action('wp_head', 'quark_header_scripts_code');
//==============================================================

//=== Прямые вывода Javascript-кода перед закрывающим тегом body.
function quark_footer_scripts_code() {
    
    //Верхнее меню с адаптацией под мобаил http://slicknav.com/
    //Необходимо выводить в конце для корректного прицепления к элементам меню.
    //#menu-top-menu это ul с пунктами меню.
?>
    <script type='text/javascript'>
    jQuery('.menu-top-mobile-container ul.nav-menu').slicknav({
        label: 'МЕНЮ',
        prependTo: '#topmenu-fix'
    });
    </script>

<?php
    //Закрепление шапки.
	if (of_get_option('bo_topmenu_fixed', '0')) {
?>
        <script type='text/javascript'>
        jQuery(document).ready(function() {
            jQuery('#topmenu').scrollToFixed({
                top: 0, 
                limit: jQuery('#topmenu').offset().bottom,
            });
        });
        </script>
<?php
	}

	//Lazy Load изображений.
	//Не забываем про необходимость выстраивать изображения по горизонтали сверху вниз (img с display:block), иначе все одномоментно замеченные на экране каркасы изображения будут загружены.
	//Также помним, что плагин (.visible()) не умеет определять видимость элементов в скрытых областях, например, в табах.
	if (of_get_option('bo_image_lazyload', '0')) {
?>
        <script type='text/javascript'>
<?php
            //Функция-плагин для img для подстановки в атрибут src значения из атрибута data-src.
            //Дока по созданию плагина-функции.
            //http://jquery.page2page.ru/index.php5/%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5_%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%D0%B0_jQuery
?>
            (function($){
            jQuery.fn.boImagesShowing = function() {
                var make = function() {
                    if (jQuery(this).visible(true)) {
                    
                        jQuery(this).find('img[data-src]').each(function() {
                            jQuery(this).attr('src', jQuery(this).attr('data-src'));
                            jQuery(this).removeAttr('data-src');
                        });
                        
                        //jQuery(this).imagesLoaded(function() {
                        //    jQuery('.isotope').isotope('layout');
                        //});
                    };
                };

                jQuery(this).each(make);
            };
            })(jQuery);
<?php
            //Подгружаем lazy-изображения блоков article.isotope-item попавших на первый экран.
            //window.load для Firefox, поскольку для Firefox недостаточно только document.ready
?>
            jQuery(document).ready(function() {
                jQuery(document).find('article.isotope-item').boImagesShowing();
            });
            //jQuery(window).load(function() {
            //    jQuery('.isotope').isotope('layout');
            //});
<?php
            //Подгружаем lazy-изображения блоков article.isotope-item при событии скроллинга (мышкой и клавой) и при условии попадания этих блоков на экран.
?>
            jQuery(window).scroll(function() {
                jQuery(document).find('article.isotope-item').boImagesShowing();
            });
    	    </script>
<?php	    
	}
	
	//Repaint isotope-элементов.
?>
    <script type='text/javascript'>
        jQuery(document).imagesLoaded(function() {
            jQuery('.isotope').isotope('layout');
        });
        jQuery(window).load(function() {
            jQuery('.isotope').isotope('layout');
        });
        //jQuery(window).scroll(function() {
        //    jQuery('.isotope').isotope('layout');
        //});
        //$('.isotope').bind('DOMSubtreeModified', function() {
        //    jQuery('.isotope').isotope('layout');
        //});
        jQuery(window).resize(function() {
            jQuery('.isotope').isotope('layout');
        });
    </script>
<?php	    
	
	//Позиционирование OVER-маркеров.
	if (current_user_can('manage_options') && 1>2) {
?>    
    <script type='text/javascript'>
<?php
        //Функция-плагин позиционирует элементы .overblock относительно родительских тегов в которые эти элементы вложены.
?>
        (function($){
        jQuery.fn.boOvermarkersRepaint = function(to_selector, pos_coor) {
            var make = function() {
                //Ищем 1 ближайший из всех предыдущих элементов того же уровня что и позиционируемые + искомый элемент должен соответствовать селектору to_selector.
                jQuery(this).css("position", "absolute");
                
                //Определяем точку К которой будет цеплять позиционируемый элемент.
                arr_pos_coor = pos_coor.split('-');
                if (arr_pos_coor.length == 2) {
                    var to_pos1 = arr_pos_coor[0];
                    var to_pos2 = arr_pos_coor[1];
                } else {
                    var to_pos1 = 'top';
                    var to_pos2 = 'left';
                }
                
                var target_elem = jQuery(this).prevAll(to_selector+':first');
                var target_topleft = target_elem.position(); //.position всегда возвращает только top и left.
                
                if (to_pos1 == 'bottom') {
                    to_pos1_px = target_topleft.top + target_elem.height() - jQuery(this).height();
                } else { //т.е. для top и для всех остальных ошибочных параметров.
                    to_pos1_px = target_topleft.top;
                }

                if (to_pos2 == 'right') {
                    to_pos2_px = target_topleft.left + target_elem.width() - jQuery(this).width();
                } else { //т.е. для left и для всех остальных ошибочных параметров.
                    to_pos2_px = target_topleft.left;
                }                

                //Позиционируем.
                jQuery(this).css('top', to_pos1_px);
                jQuery(this).css('left', to_pos2_px);
            };

            jQuery(this).each(make);
        };
        })(jQuery);

        
        jQuery(document).imagesLoaded(function() {
            //jQuery(document).find('.overblock2').boOvermarkersRepaint('.wp-post-image', 'bottom-right');
            //jQuery('.max-zindex').css('z-index', '1000');
        });
        jQuery(window).load(function() {
            //jQuery(document).find('.overblock2').boOvermarkersRepaint('.wp-post-image', 'bottom-right');
        });
        jQuery(window).scroll(function() {
            //jQuery(document).find('.overblock2').boOvermarkersRepaint('.wp-post-image', 'bottom-right');
        });
        jQuery(window).resize(function() {
            //jQuery(document).find('.overblock2').boOvermarkersRepaint('.wp-post-image', 'bottom-right');
            /*setInterval(function() {
                jQuery(document).find('.overblock2').boOvermarkersRepaint('.wp-post-image', 'bottom-right');
            }, 2000);*/
        });
        
        
        
        
    </script>
<?php
	}
	
    //Вывод Javascript-кода соцкнопок "Поделиться" от UpToLike. Для всех страниц для того, чтобы в веб-панели UpToLike были нагляднее статы. Выводить нужно в body.
    echo of_get_option('bo_uptolike_share_js', '');
    
    //jQuery-код формирования формы "Подписаться на Email-рассылку".
    if (of_get_option('bo_unisender_signup_html', '')) {
?>
        <script type='text/javascript'>
            jQuery(document).ready(function() {
                jQuery('.unisender-signup-link').magnificPopup({
                    type: 'inline',
                    midClick: true,
                    closeOnBgClick: true,
                    closeBtnInside: true,
                    showCloseBtn: true,
                    enableEscapeKey: true,
                    removalDelay: 300,
                    mainClass: 'my-mfp-zoom-in',
                    //fixedContentPos: false,
                    //fixedBgPos: true,
                    //overflowY: 'auto',
                    //preloader: false,
                });
            });
        </script>
<?php
    }
    
    //jQuery-код размещения стикера при нажатии на который будет отображаться форма "Подписаться на Email-рассылку".
    //.offset().top это верхняя координата указанного элемента.
    //Для прижатия к верхнему краю - jQuery('.unisender-signup-sticker').scrollToFixed({top: 0, limit: jQuery('.unisender-signup-sticker').offset().bottom});
    //Для прижатия к нижнему краю - jQuery('.unisender-signup-sticker').scrollToFixed({bottom: 0, limit: jQuery('.unisender-signup-sticker').offset().top});
    if (of_get_option('bo_unisender_signup_widget_sticker', '0')) {
?>
        <script type='text/javascript'>
            jQuery(document).ready(function() {
                jQuery('.unisender-signup-sticker').scrollToFixed({
                    bottom: 0, 
                    limit: jQuery('.unisender-signup-sticker').offset().top,
                });
            });
        </script>
<?php
    }
	
    //Форма "Подписаться на Email-рассылку".
    if (of_get_option('bo_unisender_signup_html', '')) {
?>
        <noindex><div id="unisender-signup-popup" class="white-popup mfp-with-anim mfp-hide">
<?php
    	    echo of_get_option('bo_unisender_signup_html', '');
?>
        </div></noindex>
<?php
    }
}
add_action('wp_footer', 'quark_footer_scripts_code');
//==============================================================




//=== Вывод настроек Билдера страниц.
function quark_content_nav($nav_id) {
	global $wp_query;
	$big = 999999999; // need an unlikely integer

	$nav_class = 'site-navigation paging-navigation';
	if (is_single()) {
		$nav_class = 'site-navigation post-navigation nav-single';
	}
?>
	<nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">
<?php 
		if (is_single()) {
            previous_post_link('<div class="nav-previous">%link</div>', '<span class="meta-nav"><i class="fa fa-angle-left"></i></span> %title');
			next_post_link('<div class="nav-next">%link</div>', '%title <span class="meta-nav"><i class="fa fa-angle-right"></i></span>');
		
		//Если есть страницы пагинации.
		} elseif ($wp_query->max_num_pages > 1) {
		    
		    if (is_category) {
		        global $current_cat_template_id;
                echo $current_cat_template_id;
                
		        //=== AJAX подгрузка постов к категориям (URL остается тот же).
                if (of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pagin_loadmore', '1')) {
        
                    //http://www.problogdesign.com/wordpress/load-next-wordpress-posts-with-ajax/
                    global $wp_query;
                    $max = $wp_query->max_num_pages;
                    $paged = (get_query_var('paged') > 1) ? get_query_var('paged') : 1;
                    
                    //Формируем и передаем параметры для зарегестрированног js-скрипта load-more-posts.js
                    //http://codex.wordpress.org/Function_Reference/wp_localize_script
                    wp_localize_script(
                        'load-more-posts', //хендл зареганного js-скрипта к которому будем аттачить параметры.
                        'obj_params', //имя объекта который будет содержать передаваемые параметры.
                        array( //массив имя параметра => значение.
                            'start_page_num' => $paged,
                            'pages_count' => $max,
                            'next_link' => next_posts($max, false),
                            'but_caption_more' => of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pagin_loadmore_but_caption_more', 'Показать еще'),
                            'but_caption_nomore' => of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pagin_loadmore_but_caption_nomore', 'Больше нет'),
                            'but_caption_loading' => of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pagin_loadmore_but_caption_loading', 'Загрузка постов...'),
                        )
                    );
                    
                //Или обычная пагинация.
                } else {
		        
                    echo paginate_links(array(
        				'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        				'format' => '?paged=%#%',
        				'current' => max(1, get_query_var('paged')),
        				'total' => $wp_query->max_num_pages,
        				'type' => 'list',
        				'prev_text' => '<i class="fa fa-angle-left"></i> Пред.',
        				'next_text' => 'След. <i class="fa fa-angle-right"></i>',
        			));
                }
    			
		    } elseif (is_search()) {
                echo paginate_links(array(
    				'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
    				'format' => '?paged=%#%',
    				'current' => max(1, get_query_var('paged')),
    				'total' => $wp_query->max_num_pages,
    				'type' => 'list',
    				'prev_text' => '<i class="fa fa-angle-left"></i> Пред.',
    				'next_text' => 'След. <i class="fa fa-angle-right"></i>',
    			));
		    }
		}
?>
	</nav><!-- #<?php echo $nav_id; ?> -->
	
<?php
}
//==============================================================




//=== Вывод настроек Билдера страниц.
function quark_bo_page_builder($bo_id_prefix, $bo_term_id, $is_category = false) {

    //Собираем блоки в соответствии с их порядковыми номерами. Этот код во многом (если не полностью) дублируем код в options.php. Подумать как избавиться от дубля.
    $blocks_order = array();
    foreach (array(1,2,3,4,5,6) as $block_inc) {
        $cur_block = $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc;
        if (of_get_option($cur_block, 'none') == 'none') {
            $index = 200 + $block_inc;
        } else {
            $index = of_get_option($cur_block.'_order', '180') + $block_inc;
        }
        
        //Если вдруг индекс такой есть, то инкрементируем до тех пор, пока индекса не будет существовать.
        while (isset($blocks_order[$index])) {
            $index++;
        }
    
        $blocks_order[$index] = $block_inc;
    }
    ksort($blocks_order); //Сортируем по возрастанию ключа.
    //var_dump($blocks_order);

    //foreach (array(1,2,3,4,5,6) as $block_inc) {
    foreach ($blocks_order as $block_inc) {
        
        $block_type = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc, 'none');
        if ($block_type!='none') {
            $block_class = 'block-'.$block_inc;
            
            
            
            
            
            //Слайдер Slick.
            if ($block_type=='slider-slick' && of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_code', '')) {
?>
                <div class="<?php echo $block_class; ?> slider-slick">
<?php
                    $slider_pre = $slider_post = '';
                    if (!of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_wide', '1')) {
                        $slider_pre = '<div class="middle-block row">'; $slider_post = '</div> <!-- /.row -->';
                    }
                    
                    echo $slider_pre;
                    
                    if ($block_header = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_text', '')) {
?>
                        <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                    }
                    
                    echo of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_code', '');
                    echo $slider_post;        
?>
                </div> <!-- /.slider -->
<?php




            //Анонсы постов.
            } elseif ($block_type=='posts-list') {
    
                if ($is_category) {
                    global $wp_query;
                    $listing = $wp_query;
                } else {

        			//Выводим анонсы постов категорий.
                	$args = array(
                		'category_name'       => sanitize_text_field(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_cats', '')),
                		'order'               => 'DESC',
                		'orderby'             => 'date',
                		'post_type'           => explode(',', 'post'),
                		'posts_per_page'      => sanitize_text_field(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_count', '')),
                	);
        
                	$args['post_status'] = array('publish'); //'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any'
                
                	$listing = new WP_Query($args);
                }
                
            	if ($listing->have_posts()) {
?>
                    <div class="site-content row <?php echo $block_class; ?>">
                		<div class="row clearfix">
                		    <div class="col grid_12_of_12">
<?php
                                if ($block_header = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_text', '')) {
?>
                                    <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                                }
?>
                                <div id="posts-list" class="<?php echo $block_class; ?> posts isotope <?php echo sanitize_text_field(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_view', 'list')); ?>">
<?php
                                	while ($listing->have_posts()) {
                                	    $listing->the_post();
                                	    global $post;
                                		//get_template_part('content');
                                		
                                		if (2>1) {
?>
                                        <div id="post-<?php the_ID(); ?>" <?php post_class('isotope-item'); ?>>
                                            <div class="entry-header clearfix">
<?php
                                                if (has_post_thumbnail() && !is_search()) {
?>
                                                    <div class="entry-thumb">
                                        				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>">
<?php
                                                            //Поскольку анонсы постов могут выводится в виде масонри, то и применение Lazy Load отключено.
                                        					the_post_thumbnail(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_thumb_format', 'thumbnail'));
?>
                                        				</a>
                                    				</div>
<?php
                                                }
?>
                                				<div class="entry-title">
                                					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                                				</div>
                                            </div> <!-- /.entry-header -->
                                        </div> <!-- /#post -->
<?php
                                		}
                                	}
?>
                                </div>
                		    </div> <!-- /.col.grid_12_of_12 -->
                		</div> <!-- /.row clearfix -->
                	</div><!-- /.site-content.row -->
<?php
                    wp_reset_postdata();
            	}
            	
            	
            	
            	
            	
            //Поле ввода.
            } elseif ($block_type=='textarea') {
?>
                <div class="site-content row">
            		<div class="row clearfix">
            		    <div class="col grid_12_of_12">
            		        <div class="<?php echo $block_class; ?> textarea">
<?php
                                echo do_shortcode(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_textarea_text', ''));
?>
                            </div> <!-- /.textarea -->
            		    </div> <!-- /.col.grid_12_of_12 -->
            		</div> <!-- /.row clearfix -->
            	</div><!-- /.site-content.row -->
<?php




            //Баннер.
            } elseif ($block_type=='banner') {
                
            }
        }
    }
}
//==============================================================







//=== Билдер блоков.
function quark_blocks_builder($bb_id, $is_category = false) {

    //Собираем блоки в соответствии с их порядковыми номерами. Этот код во многом (если не полностью) дублируем код в options.php. Подумать как избавиться от дубля.
    $blocks_order = array();
    foreach (array(1,2,3,4,5,6) as $block_inc) {
        $cur_block = $bb_id.'_b'.$block_inc;
        if (of_get_option($cur_block, 'none') == 'none') {
            $index = 200 + $block_inc;
        } else {
            $index = of_get_option($cur_block.'_order', '180') + $block_inc;
        }
        
        //Если вдруг индекс такой есть, то инкрементируем до тех пор, пока индекса не будет существовать.
        while (isset($blocks_order[$index])) {
            $index++;
        }
    
        $blocks_order[$index] = $block_inc;
    }
    ksort($blocks_order); //Сортируем по возрастанию ключа.
    //var_dump($blocks_order);

    //foreach (array(1,2,3,4,5,6) as $block_inc) {
    foreach ($blocks_order as $block_inc) {
        
        $block_type = of_get_option($bb_id.'_b'.$block_inc, 'none');
        if ($block_type!='none') {
            $block_class = 'block-'.$block_inc;
            
            
            
            
            
            //=== Слайдер Slick.
            if ($block_type=='slider-slick' && of_get_option($bb_id.'_b'.$block_inc.'_slider_code', '')) {
?>
                <div class="<?php echo $bb_id.' '.$block_class; ?> slider-slick">
<?php
                    $slider_pre = $slider_post = '';
                    if (!of_get_option($bb_id.'_b'.$block_inc.'_slider_wide', '1')) {
                        $slider_pre = '<div class="middle-block row">'; $slider_post = '</div> <!-- /.row -->';
                    }
                    
                    echo $slider_pre;
                    
                    if ($block_header = of_get_option($bb_id.'_b'.$block_inc.'_h_text', '')) {
?>
                        <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                    }
                    
                    echo of_get_option($bb_id.'_b'.$block_inc.'_slider_code', '');
                    echo $slider_post;        
?>
                </div> <!-- /.slider -->
<?php




            //=== Анонсы постов.
            } elseif ($block_type=='posts-list') {
    
                if ($is_category) {
                    global $wp_query;
                    $listing = $wp_query;
                } else {

        			//Выводим анонсы постов категорий.
                	$args = array(
                		'category_name'       => sanitize_text_field(of_get_option($bb_id.'_b'.$block_inc.'_postsann_cats_slug', '')),
                		'order'               => 'DESC',
                		'orderby'             => 'date',
                		'post_type'           => explode(',', 'post'),
                		'posts_per_page'      => sanitize_text_field(of_get_option($bb_id.'_b'.$block_inc.'_postsann_count', '')),
                	);
        
                	$args['post_status'] = array('publish'); //'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any'
                
                	$listing = new WP_Query($args);
                }
                
            	if ($listing->have_posts()) {
?>
                    <div class="<?php echo $bb_id.' '.$block_class; ?> site-content row">
                		<div class="row clearfix">
                		    <div class="col grid_12_of_12">
<?php
                                if ($block_header = of_get_option($bb_id.'_b'.$block_inc.'_h_text', '')) {
?>
                                    <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                                }
?>
                                <div id="posts-list" class="<?php echo $block_class; ?> posts isotope <?php echo sanitize_text_field(of_get_option($bb_id.'_b'.$block_inc.'_postsann_view', 'list')); ?>">
<?php
                                	while ($listing->have_posts()) {
                                	    $listing->the_post();
                                	    global $post;
                                		//get_template_part('content');
                                		
                                		if (2>1) {
?>
                                        <div id="post-<?php the_ID(); ?>" <?php post_class('isotope-item'); ?>>
                                            <div class="entry-header clearfix">
<?php
                                                if (has_post_thumbnail() && !is_search()) {
?>
                                                    <div class="entry-thumb">
                                        				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>">
<?php
                                                            //Поскольку анонсы постов могут выводится в виде масонри, то и применение Lazy Load отключено.
                                        					the_post_thumbnail(of_get_option($bb_id.'_b'.$block_inc.'_postsann_thumb_format', 'thumbnail'));
?>
                                        				</a>
                                    				</div>
<?php
                                                }
?>
                                				<div class="entry-title">
                                					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                                				</div>
                                            </div> <!-- /.entry-header -->
                                        </div> <!-- /#post -->
<?php
                                		}
                                	}
?>
                                </div>
                		    </div> <!-- /.col.grid_12_of_12 -->
                		</div> <!-- /.row clearfix -->
                	</div><!-- /.site-content.row -->
<?php
                    wp_reset_postdata();
            	}
            	
            	
            	
            	
            	
            //=== Поле ввода.
            } elseif ($block_type=='textarea') {
?>
                <div class="<?php echo $bb_id.' '.$block_class; ?> site-content row">
            		<div class="row clearfix">
            		    <div class="col grid_12_of_12">
            		        <div class="<?php echo $block_class; ?> textarea">
<?php
                                echo do_shortcode(of_get_option($bb_id.'_b'.$block_inc.'_textarea_text', ''));
?>
                            </div> <!-- /.textarea -->
            		    </div> <!-- /.col.grid_12_of_12 -->
            		</div> <!-- /.row clearfix -->
            	</div><!-- /.site-content.row -->
<?php




            //=== Баннер.
            } elseif ($block_type=='banner') {
                
            }
        }
    }
}
//==============================================================







/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own quark_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 * (Note the lack of a trailing </li>. WordPress will add it itself once it's done listing any children and whatnot)
 *
 * @since Quark 1.0
 *
 * @param array Comment
 * @param array Arguments
 * @param integer Comment depth
 * @return void
 */
if ( ! function_exists( 'quark_comment' ) ) {
	function quark_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) {
		case 'pingback' :
		case 'trackback' :
			// Display trackbacks differently than normal comments ?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="pingback">
					<p><?php esc_html_e( 'Pingback:', 'quark' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'quark' ), '<span class="edit-link">', '</span>' ); ?></p>
				</article> <!-- #comment-##.pingback -->
			<?php
			break;
		default :
			// Proceed with normal comments.
			global $post; ?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="comment">
					<header class="comment-meta comment-author vcard">
						<?php
						echo get_avatar( $comment, 44 );
						printf( '<cite class="fn">%1$s %2$s</cite>',
							get_comment_author_link(),
							// If current post author is also comment author, make it known visually.
							( $comment->user_id === $post->post_author ) ? '<span> ' . esc_html__( 'Post author', 'quark' ) . '</span>' : '' );
						printf( '<a href="%1$s" title="Posted %2$s"><time itemprop="datePublished" datetime="%3$s">%4$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							sprintf( esc_html__( '%1$s @ %2$s', 'quark' ), esc_html( get_comment_date() ), esc_attr( get_comment_time() ) ),
							get_comment_time( 'c' ),
							/* Translators: 1: date, 2: time */
							sprintf( esc_html__( '%1$s at %2$s', 'quark' ), get_comment_date(), get_comment_time() )
						);
						?>
					</header> <!-- .comment-meta -->

					<?php if ( '0' == $comment->comment_approved ) { ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'quark' ); ?></p>
					<?php } ?>

					<section class="comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'quark' ), '<p class="edit-link">', '</p>' ); ?>
					</section> <!-- .comment-content -->

					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => wp_kses( __( 'Reply <span>&darr;</span>', 'quark' ), array( 'span' => array() ) ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div> <!-- .reply -->
				</article> <!-- #comment-## -->
			<?php
			break;
		} // end comment_type check
	}
}


/**
 * Update the Comments form so that the 'required' span is contained within the form label.
 *
 * @since Quark 1.0
 *
 * @param string Comment form fields html
 * @return string The updated comment form fields html
 */
/*
function quark_comment_form_default_fields($fields) {

	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? ' aria-required="true"' : "" );

	$fields[ 'author' ] = '<p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', 'quark' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';

	$fields[ 'email' ] =  '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'quark' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';

	$fields[ 'url' ] =  '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'quark' ) . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>';

	return $fields;

}
add_action('comment_form_default_fields', 'quark_comment_form_default_fields');
*/


//=== Мета-информация поста.
if (!function_exists('quark_posted_on')) {
    
	function quark_posted_on() {
	    
	    //Иконка для даты публикации поста.
		$post_icon = '';
		
		switch (get_post_format()) {
		    
			case 'aside':
				$post_icon = 'fa-file-o';
				break;
			case 'audio':
				$post_icon = 'fa-volume-up';
				break;
			case 'chat':
				$post_icon = 'fa-comment';
				break;
			case 'gallery':
				$post_icon = 'fa-camera';
				break;
			case 'image':
				$post_icon = 'fa-picture-o';
				break;
			case 'link':
				$post_icon = 'fa-link';
				break;
			case 'quote':
				$post_icon = 'fa-quote-left';
				break;
			case 'status':
				$post_icon = 'fa-user';
				break;
			case 'video':
				$post_icon = 'fa-video-camera';
				break;
			default:
				$post_icon = 'fa-calendar';
				break;
		}

		//1: Icon 2: Permalink 3: Post date and time 4: Publish date in ISO format 5: Post date
		$date = sprintf( '<i class="fa %1$s"></i> <a href="%2$s" title="Posted %3$s" rel="bookmark"><time class="entry-date" datetime="%4$s" itemprop="datePublished">%5$s</time></a>',
			$post_icon,
			esc_url( get_permalink() ),
			sprintf( esc_html__( '%1$s @ %2$s', 'quark' ), esc_html( get_the_date() ), esc_attr( get_the_time() ) ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		//1: Date link 2: Author link 3: Categories 4: No. of Comments
		/* bo отключил вывод автора.
		$author = sprintf( '<i class="fa fa-pencil"></i> <address class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></address>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( esc_html__( 'View all posts by %s', 'quark' ), get_the_author() ) ),
			get_the_author()
		);
		*/

		//Список категорий поста.
		$categories_list = get_the_category_list( esc_html__( ' ', 'quark' ) );

		//1: Permalink 2: Title 3: No. of Comments
		/* bo отключил в пользу варианта вывода чуть ниже.
		$comments = sprintf( '<span class="comments-link"><i class="fa fa-comment"></i> <a href="%1$s" title="%2$s">%3$s</a></span>',
			esc_url( get_comments_link() ),
			esc_attr( esc_html__( 'Comment on ' . the_title_attribute( 'echo=0' ) ) ),
			( get_comments_number() > 0 ? sprintf( _n( '%1$s Comment', '%1$s Comments', get_comments_number(), 'quark' ), get_comments_number() ) : esc_html__( 'No Comments', 'quark' ) )
		);
		*/
		
		if (get_comments_number() > 0) {
		    
    		$comments = sprintf('<span class="comments-link"><i class="fa fa-comment"></i> <a href="%1$s" title="%2$s">%3$s</a></span>',
    			esc_url(get_comments_link()),
    			esc_attr(esc_html__('Comment on ' . the_title_attribute('echo=0'))),
    		    get_comments_number()
    		);
		}

		//1: Date 2: Author 3: Categories 4: Comments
		printf(
		    wp_kses(
		        __('<div class="header-meta">%1$s%2$s<span class="post-categories">%3$s</span>%4$s</div>', 'quark'), 
    		    array( 
        			'div' => array(
        				'class' => array()),
        			'span' => array(
        				'class' => array())
        		)
    		),
			$date,
			$author,
			$categories_list,
			(is_search() ? '' : $comments)
		);
	}
}


/**
 * Prints HTML with meta information for current post: categories, tags, permalink
 *
 * @since Quark 1.0
 *
 * @return void
 */
if ( ! function_exists( 'quark_entry_meta' ) ) {
	function quark_entry_meta() {
		// Return the Tags as a list
		$tag_list = "";
		if ( get_the_tag_list() ) {
			$tag_list = get_the_tag_list( '<span class="post-tags">', esc_html__( ' ', 'quark' ), '</span>' );
		}

		// Translators: 1 is tag
		if ( $tag_list ) {
			printf( wp_kses( __( '<i class="fa fa-tag"></i> %1$s', 'quark' ), array( 'i' => array( 'class' => array() ) ) ), $tag_list );
		}
	}
}


/**
 * Adjusts content_width value for full-width templates and attachments
 *
 * @since Quark 1.0
 *
 * @return void
 */
function quark_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() ) {
		global $content_width;
		$content_width = 1200;
	}
}
add_action( 'template_redirect', 'quark_content_width' );


/**
 * Change the "read more..." link so it links to the top of the page rather than part way down
 *
 * @since Quark 1.0
 *
 * @param string The 'Read more' link
 * @return string The link to the post url without the more tag appended on the end
 */
function quark_remove_more_jump_link( $link ) {
	$offset = strpos( $link, '#more-' );
	if ( $offset ) {
		$end = strpos( $link, '"', $offset );
	}
	if ( $end ) {
		$link = substr_replace( $link, '', $offset, $end-$offset );
	}
	return $link;
}
add_filter( 'the_content_more_link', 'quark_remove_more_jump_link' );


/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Quark 1.0
 *
 * @return string The 'Continue reading' link
 */
function quark_continue_reading_link() {
	/*
	return '&hellip;<p><a class="more-link" href="'. esc_url( get_permalink() ) . '" title="' . esc_html__( 'Continue reading', 'quark' ) . ' &lsquo;' . get_the_title() . '&rsquo;">' . wp_kses( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'quark' ), array( 'span' => array( 
			'class' => array() ) ) ) . '</a></p>';
	*/
}


/**
 * Replaces "[...]" (appended to automatically generated excerpts) with the quark_continue_reading_link().
 *
 * @since Quark 1.0
 *
 * @param string Auto generated excerpt
 * @return string The filtered excerpt
 */
function quark_auto_excerpt_more( $more ) {
	return quark_continue_reading_link();
}
add_filter( 'excerpt_more', 'quark_auto_excerpt_more' );


/**
 * Extend the user contact methods to include Twitter, Facebook and Google+
 *
 * @since Quark 1.0
 *
 * @param array List of user contact methods
 * @return array The filtered list of updated user contact methods
 */
function quark_new_contactmethods( $contactmethods ) {
	// Add Twitter
	$contactmethods['twitter'] = 'Twitter';

	//add Facebook
	$contactmethods['facebook'] = 'Facebook';

	//add Google Plus
	$contactmethods['googleplus'] = 'Google+';

	return $contactmethods;
}
add_filter( 'user_contactmethods', 'quark_new_contactmethods', 10, 1 );


/**
 * Add a filter for wp_nav_menu to add an extra class for menu items that have children (ie. sub menus)
 * This allows us to perform some nicer styling on our menu items that have multiple levels (eg. dropdown menu arrows)
 *
 * @since Quark 1.0
 *
 * @param Menu items
 * @return array An extra css class is on menu items with children
 */
function quark_add_menu_parent_class($items) {
	$parents = array();
	foreach ( $items as $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parents[] = $item->menu_item_parent;
		}
	}

	foreach ( $items as $item ) {
		if ( in_array( $item->ID, $parents ) ) {
			$item->classes[] = 'menu-parent-item';
		}
	}

	return $items;
}
add_filter('wp_nav_menu_objects', 'quark_add_menu_parent_class');


/**
 * Add Filter to allow Shortcodes to work in the Sidebar
 *
 * @since Quark 1.0
 */
add_filter( 'widget_text', 'do_shortcode' );


/**
 * Return an unordered list of linked social media icons, based on the urls provided in the Theme Options
 *
 * @since Quark 1.0
 *
 * @return string Unordered list of linked social media icons
 */
if ( ! function_exists( 'quark_get_social_media' ) ) {
	function quark_get_social_media() {
		$output = '';
		$icons = array(
			array( 'url' => of_get_option( 'social_twitter', '' ), 'icon' => 'fa-twitter', 'title' => esc_html__( 'Follow me on Twitter', 'quark' ) ),
			array( 'url' => of_get_option( 'social_facebook', '' ), 'icon' => 'fa-facebook', 'title' => esc_html__( 'Friend me on Facebook', 'quark' ) ),
			array( 'url' => of_get_option( 'social_googleplus', '' ), 'icon' => 'fa-google-plus', 'title' => esc_html__( 'Connect with me on Google+', 'quark' ) ),
			array( 'url' => of_get_option( 'social_linkedin', '' ), 'icon' => 'fa-linkedin', 'title' => esc_html__( 'Connect with me on LinkedIn', 'quark' ) ),
			array( 'url' => of_get_option( 'social_slideshare', '' ), 'icon' => 'fa-slideshare', 'title' => esc_html__( 'Follow me on SlideShare', 'quark' ) ),
			array( 'url' => of_get_option( 'social_dribbble', '' ), 'icon' => 'fa-dribbble', 'title' => esc_html__( 'Follow me on Dribbble', 'quark' ) ),
			array( 'url' => of_get_option( 'social_tumblr', '' ), 'icon' => 'fa-tumblr', 'title' => esc_html__( 'Follow me on Tumblr', 'quark' ) ),
			array( 'url' => of_get_option( 'social_github', '' ), 'icon' => 'fa-github', 'title' => esc_html__( 'Fork me on GitHub', 'quark' ) ),
			array( 'url' => of_get_option( 'social_bitbucket', '' ), 'icon' => 'fa-bitbucket', 'title' => esc_html__( 'Fork me on Bitbucket', 'quark' ) ),
			array( 'url' => of_get_option( 'social_foursquare', '' ), 'icon' => 'fa-foursquare', 'title' => esc_html__( 'Follow me on Foursquare', 'quark' ) ),
			array( 'url' => of_get_option( 'social_youtube', '' ), 'icon' => 'fa-youtube', 'title' => esc_html__( 'Subscribe to me on YouTube', 'quark' ) ),
			array( 'url' => of_get_option( 'social_instagram', '' ), 'icon' => 'fa-instagram', 'title' => esc_html__( 'Follow me on Instagram', 'quark' ) ),
			array( 'url' => of_get_option( 'social_flickr', '' ), 'icon' => 'fa-flickr', 'title' => esc_html__( 'Connect with me on Flickr', 'quark' ) ),
			array( 'url' => of_get_option( 'social_pinterest', '' ), 'icon' => 'fa-pinterest', 'title' => esc_html__( 'Follow me on Pinterest', 'quark' ) ),
			array( 'url' => of_get_option( 'social_rss', '' ), 'icon' => 'fa-rss', 'title' => esc_html__( 'Subscribe to my RSS Feed', 'quark' ) )
		);

		foreach ( $icons as $key ) {
			$value = $key['url'];
			if ( !empty( $value ) ) {
				$output .= sprintf( '<li><a href="%1$s" title="%2$s"%3$s><span class="fa-stack fa-lg"><i class="fa fa-square fa-stack-2x"></i><i class="fa %4$s fa-stack-1x fa-inverse"></i></span></a></li>',
					esc_url( $value ),
					$key['title'],
					( !of_get_option( 'social_newtab', '0' ) ? '' : ' target="_blank"' ),
					$key['icon']
				);
			}
		}

		if ( !empty( $output ) ) {
			$output = '<ul>' . $output . '</ul>';
		}

		return $output;
	}
}

/**
 * Recreate the default filters on the_content
 * This will make it much easier to output the Theme Options Editor content with proper/expected formatting.
 * We don't include an add_filter for 'prepend_attachment' as it causes an image to appear in the content, on attachment pages.
 * Also, since the Theme Options editor doesn't allow you to add images anyway, no big deal.
 *
 * @since Quark 1.0
 */
add_filter( 'meta_content', 'wptexturize' );
add_filter( 'meta_content', 'convert_smilies' );
add_filter( 'meta_content', 'convert_chars'  );
add_filter( 'meta_content', 'wpautop' );
add_filter( 'meta_content', 'shortcode_unautop'  );

//Unhook the WooCommerce Wrappers
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

//Outputs the opening container div for WooCommerce
if (!function_exists( 'quark_before_woocommerce_wrapper')) {
	function quark_before_woocommerce_wrapper() {
		echo '<div id="primary" class="site-content row" role="main">';
	}
}

//Outputs the closing container div for WooCommerce
if (!function_exists( 'quark_after_woocommerce_wrapper')) {
	function quark_after_woocommerce_wrapper() {
		echo '</div> <!-- /#primary.site-content.row -->';
	}
}

//Check if WooCommerce is active
function quark_is_woocommerce_active() {
	if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
		return true;
	} else {
		return false;
	}
}

//Проверяем, активирован ли плагин Affiliate Egg.
function quark_is_affiliateegg_active() {
	if (is_plugin_active('affiliate-egg/affiliate-egg.php')) {
	/*if (in_array('affiliate-egg/index.php', apply_filters('active_plugins', get_option('active_plugins')))) {*/
		return true;
	} else {
		return false;
	}
}

//Check if WooCommerce is active and a WooCommerce template is in use and output the containing div
if (!function_exists('quark_setup_woocommerce_wrappers')) {
	function quark_setup_woocommerce_wrappers() {
		if (quark_is_woocommerce_active() && is_woocommerce()) {
			add_action('quark_before_woocommerce', 'quark_before_woocommerce_wrapper', 10, 0);
			add_action('quark_after_woocommerce', 'quark_after_woocommerce_wrapper', 10, 0);		
		}
	}
	add_action('template_redirect', 'quark_setup_woocommerce_wrappers', 9);
}

//Outputs the opening wrapper for the WooCommerce content
if (!function_exists('quark_woocommerce_before_main_content')) {
	function quark_woocommerce_before_main_content() {
		if((is_shop() && !of_get_option('woocommerce_shopsidebar', '1')) || (is_product() && !of_get_option('woocommerce_productsidebar', '1'))) {
			echo '<div class="col grid_12_of_12">';
		} else {
			echo '<div class="col grid_8_of_12">';
		}
	}
	add_action('woocommerce_before_main_content', 'quark_woocommerce_before_main_content', 10);
}

//Outputs the closing wrapper for the WooCommerce content
if (!function_exists('quark_woocommerce_after_main_content')) {
	function quark_woocommerce_after_main_content() {
		echo '</div>';
	}
	add_action('woocommerce_after_main_content', 'quark_woocommerce_after_main_content', 10);
}

//Remove the sidebar from the WooCommerce templates
if (!function_exists('quark_remove_woocommerce_sidebar')) {
	function quark_remove_woocommerce_sidebar(){
		if((is_shop() && !of_get_option('woocommerce_shopsidebar', '1')) || (is_product() && !of_get_option('woocommerce_productsidebar', '1'))) {
			remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
		}
	}
	add_action('woocommerce_before_main_content', 'quark_remove_woocommerce_sidebar');
}

//Remove the breadcrumbs from the WooCommerce pages
if (!function_exists('quark_remove_woocommerce_breadcrumbs')) {
	function quark_remove_woocommerce_breadcrumbs() {
		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	}
}

//=== Добавляем в футер формирование кастомной сессии по пользователю.
//=== Должен быть установлен плагин https://wordpress.org/plugins/wp-session-manager/
function init_custom_session () {
    global $wp_session;
    $wp_session = WP_Session::get_instance(); 
}
add_action('init', 'init_custom_session');

function create_custom_session() {
    global $wp_session;
    //print_r($wp_session);
    
    if (current_user_can('manage_options')) {
    //    phpinfo();
        //echo '<a href="http://hodex.ru/postback/?pub_hash=ffe604304251e518537dc6bcca122b27cd2a6f1f">GO</a>';
    }

    //Если user_hash не задан, значит зашел новый пользователь, поэтому формируем данные по нему.
    if (!isset($wp_session['user_hash'])) {
        //Получаем/сохраняем в данные сессии всю информацию по источнику перехода и пользователю.

        //Получаем текущий полный урл. Т.е. с учетом условия выше это будет урл входной страницы сайта.
        $wp_session['url'] = urldecode('http'.(isset($_SERVER['HTTPS']) ? 's' : '') .'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        
        //Собираем базовую информацию.
        //date_default_timezone_set('Europe/Moscow'); //Задаем временную зону для получения московской даты и времени.
        //$bo_session['date'] = date("d.m.Y H:i:s");        
        $wp_session['date'] = gmdate('d.m.Y H:i:s', time() + 3*3600); //Время по гринвичу + 3 часа. Более надежный способ получить нужное время.
        $wp_session['agent'] = $_SERVER['HTTP_USER_AGENT'];
        $wp_session['uri'] = $_SERVER['REQUEST_URI'];
        $wp_session['path'] = get_uri_path($wp_session['uri']);
        $wp_session['user'] = $_SERVER['PHP_AUTH_USER'];
        
        //Помнинм, что nginx может перекрывать IP посетителя айпишкой сервера, поэтому в nginx необходимо что-то настроить, если такое имеет место быть.
        //$wp_session['ip'] = $_SERVER['REMOTE_ADDR'];
    
        //Пытаемся определить корректный IP посетителя.
        //http://stackoverflow.com/questions/6097767/why-does-serverremote-addr-show-a-different-ip-than-my-external-ip
        foreach (array('HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $server_var) {
            if (!empty($_SERVER[$server_var])) {
                $wp_session['ip'] = $_SERVER[$server_var];
                break;
            }
        }

        $wp_session['ref'] = $_SERVER['HTTP_REFERER'];
        $wp_session['ref_domain'] = parse_url($wp_session['ref'], PHP_URL_HOST);  
        
        //Генерируем user_hash. Хеш обрезаем до 22 символов, чтобы небыло проблем с другими системами (например, с CityAds (там subid до 30 символов)).
        $sum = '';
        foreach(array('date','ip','agent','ref','url') as $param) {
            $sum = $sum.$wp_session[$param];              
        }
        $wp_session['user_hash'] = substr(md5($sum), 0, 22);
        
        //=== И в итоге записываем все собранные и сгенерированные данные в файл.
        $entry_line = '';
        foreach(array('date','ip','agent','user','ref_domain','ref','url','user_hash') as $param) {
            $entry_line = $entry_line.$wp_session[$param].'~|~';              
        }
        $entry_line = $entry_line."\r\n"; // \r\n - перевод строки для Windows.

        /*
    	$fp = fopen($_SERVER['DOCUMENT_ROOT']."/users_data.txt", "a");
    	fputs($fp, mb_convert_encoding($entry_line, "windows-1251", "auto")); //Кодируем в windows-1251 для просмотра в Excel.
    	fclose($fp);
        */
        
        $fp = fopen($_SERVER['DOCUMENT_ROOT']."/users_data.txt", "a");
        fputs($fp, $entry_line);
        fclose($fp);
    }
}
add_action('wp_footer', 'create_custom_session');
//==============================================================

//=== Определяем путь в URI без параметров. URI это путь+параметры, например, /basca12?bo_debug=1&bo_geo=123, а функция вернет только /basca12
function get_uri_path($uri) {
    $pos = stripos($uri, '?');
    if ($pos) {
        $res = substr($uri, 0, $pos);
    } else {
        $res = $uri;
    }
    return $res;   
}
//==============================================================

//=== В анонсах выводим не более 20 слов.
/*
function custom_excerpt_length( $length ) {
	return 20;
}
add_filter('excerpt_length', 'custom_excerpt_length', 999);
*/
//==============================================================

//=== В анонсах выводим 1 первое предложение. Если предложение длинное, то обрезаем его до 250 символов.
function excerpt_read_more_link($output) {
    global $post;
    $output = preg_replace('/(.*?[?!.](?=\s|$)).*/', '\\1', $output);
    if (mb_strlen($output) > 250) {
        $output = mb_substr($output, 0, 250);
    }
    //return $output.'<a href="'.get_permalink($post->ID).'"> Читать полностью...</a>';
    return $output;
}
add_filter('the_excerpt', 'excerpt_read_more_link');
//==============================================================

//=== Функция получения форматов и размеров изображений из настроек Вордпресс.
//=== Функция взята из http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
function get_image_sizes($size = '') {
    global $_wp_additional_image_sizes;

    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();

    // Create the full array with sizes and crop info
    foreach( $get_intermediate_image_sizes as $_size ) {

        if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
            $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
            $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
            $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

        } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
            $sizes[ $_size ] = array( 
                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
            );
        }
    }

    // Get only 1 size if found
    if ( $size ) {
        if( isset( $sizes[ $size ] ) ) {
            return $sizes[ $size ];
        } else {
            return false;
        }
    }

    return $sizes;
}
//==============================================================

//=== CSS для админа (бэкенд).
function my_custom_fonts() {
    $output = '';
    $output .= '<style type="text/css">';

    //Для Theme Options.
    
    $output .= '#optionsframework h3 {';
    $output .= 'background: #ffd77a;';
    $output .= 'margin: 0;';
    $output .= 'padding: 7px 10px;';
    $output .= '}';
    
    $output .= '#optionsframework .admin-background-1, #optionsframework .admin-background-3, #optionsframework .admin-background-5,';
    $output .= '#optionsframework .admin-background-7, #optionsframework .admin-background-9 {';
    $output .= 'background: #faeedd';
    $output .= '}';

    $output .= '#optionsframework .admin-background-2, #optionsframework .admin-background-4, #optionsframework .admin-background-6,';
    $output .= '#optionsframework .admin-background-8, #optionsframework .admin-background-10 {';
    $output .= 'background: #fff8e7';
    $output .= '}';

    $output .= '#optionsframework .admin-first-in-block {';
    $output .= 'border-top: 20px solid #b59ce6;';
    $output .= 'margin-top: 20px;';
    $output .= '}';
    
    $output .= '#optionsframework .admin-block-header {';
    $output .= 'color: #985cff;';
    $output .= 'font-size: 16px;';
    $output .= 'padding-top: 0px;';
    $output .= '}';
    
    //Переводим табы и панель с опциями в вертикальный формат.
    $output .= 'body.wp-admin #optionsframework-wrap.wrap .nav-tab-wrapper {';
    $output .= 'width: 20%;';
    $output .= 'float: left;';
    $output .= 'display: inline-block;';
    $output .= '}';
    $output .= 'body.wp-admin #optionsframework-wrap .nav-tab-wrapper a.nav-tab {';
    $output .= 'display: block;';
    $output .= 'width: 83%;';
    $output .= '}';
    $output .= 'body.wp-admin #optionsframework-wrap #optionsframework-metabox {';
    $output .= 'width: 75%;';
    $output .= 'display: inline-block;';
    $output .= '}';
    $output .= 'body.wp-admin #optionsframework-wrap .nav-tab-wrapper {';
    $output .= 'border-bottom: none;';
    $output .= '}';
    $output .= 'body.wp-admin #optionsframework-wrap .nav-tab-active, body.wp-admin #optionsframework-wrap .nav-tab-active:hover {';
    $output .= 'background: #ffe7ad;';
    $output .= '}';
    $output .= 'body.wp-admin #optionsframework-wrap .settings-error {';
    $output .= 'display: inline-block;';
    $output .= '}';
    
    //Новая высота в окне редактирования записи для метабокса категорий для вкладки Все рубрики.
    $output .= '#poststuff #category-all {';
    $output .= 'max-height: 500px';
    $output .= '}';
    
    $output .= '</style>';
    
    echo $output;
}
add_action('admin_head', 'my_custom_fonts');
//==============================================================

//======================================
//=== Theme Options от Options Framework
//=== http://wptheming.com/options-framework-theme/
//======================================

//=== Контент внизу страницы Theme Options. Взято с http://wptheming.com/2011/05/options-framework-0-6/
function exampletheme_options_after() {
    echo '<p>Сюда можно добавить контент. В functions.php есть хук optionsframework_after</p>';
}
add_action('optionsframework_after','exampletheme_options_after', 100);
//==============================================================

//=== Отключаем вырезание тегов script со всеми атрибутами и embed из элементов textarea.
//=== http://wptheming.com/2011/05/options-framework-0-6/
add_action('admin_init','optionscheck_change_santiziation', 100);
 
function optionscheck_change_santiziation() {
    remove_filter('of_sanitize_textarea', 'of_sanitize_textarea');
    add_filter('of_sanitize_textarea', 'custom_sanitize_textarea');
}
 
function custom_sanitize_textarea($input) {
    return $input;
    
    /* bo сделано так, потому что с фильтрацией атрибутов div какая-то трабла.
    global $allowedposttags;
    
    $custom_allowedtags["embed"] = array(
        "src" => array(),
        "type" => array(),
        "allowfullscreen" => array(),
        "allowscriptaccess" => array(),
        "height" => array(),
        "width" => array()
    );
    $custom_allowedtags["script"] = array(
        "type" => array(),
    );
    
    $custom_allowedtags = array_merge($custom_allowedtags, $allowedposttags);
    $output = wp_kses($input, $custom_allowedtags);
    
    return $output;
    */
}
//==============================================================

//=== Добавление настроек из Theme Options в препроцессор LESS для компиляции CSS файла стилей.
//=== Задаваемые через этот хук переменные не должны присутствовать в style.less в виде пары "параметр:значение" (т.е. в формате присваивания) даже в закомментированном виде, 
//=== иначе параметр не скомпиллируется. Это особенность LESS - нельзя передавать название параметров CSS.
//=== Плюс формируется dynamic.less который при компиляции подключается к style.less. В dynamic.less можно писать любые наборы и множества.
//=== Функционал реализован на основе плагина-обертки BO Lessify Wordpress над lessphp.
//=== http://leafo.net/lessphp/docs/
function set_lessify_vars($lessphp_obj) {
    if ($lessphp_obj) {
        $bo_options = optionsframework_options(); //Весь массив опций из Theme Options.
        //var_dump($bo_options);
        
        $bo_for_less = array(); //Промежуточный массив со значениями параметров. Необходим для удобного формирования style.less и dynamic.less

        $bo_for_less['cat_templates_count'] = array('css_selector' => '', 'prop' => '', 'value' => '6'); //Кол-во шаблонов категорий вместе с базовым шаблоном. Необходимо для цикла в style.less
        $bo_for_less['bobik_color'] = array('css_selector' => '', 'prop' => 'color', 'value' => '#ee4a42'); //#ee4a42; //#1081c5; //#F36C63; //#2997ab;
        
        //GIF процесса загрузки.
        $bo_image_lazyload_loader_gif = sanitize_text_field(of_get_option('bo_image_lazyload_loader_gif', ''));
        $bo_for_less['bo_image_lazyload_loader_gif'] = $bo_image_lazyload_loader_gif ? array('css_selector' => '', 'prop' => 'background', 'value' => 'url("'.$bo_image_lazyload_loader_gif.'") no-repeat center center') : array('css_selector' => '', 'prop' => 'background', 'value' => 'none');

        //=== Разные параметры.
        foreach ($bo_options as $key => $bo_option) {
            //Передаем параметры width, height, margin и прочие.
            if (isset($bo_option['css_prop'])) {
                $bo_for_less[$bo_option['id']] = array('css_selector' => (isset($bo_option['css_selector'])) ? $bo_option['css_selector'] : '', 'prop' => $bo_option['css_prop'], 'value' => sanitize_text_field(of_get_option($bo_option['id'], $bo_option['std'])));
            }
        }
        //============
        //var_dump($bo_for_less);
        
        //=== Полные CSS-правила.
        foreach ($bo_options as $key => $bo_option) {
            //Передаем параметры width, height, margin и прочие.
            if (isset($bo_option['css_selector']) && $bo_option['css_selector'] == 'rules') {
                $bo_for_less[$bo_option['id']] = array('css_selector' => $bo_option['css_selector'], 'prop' => '', 'value' => sanitize_text_field(of_get_option($bo_option['id'], $bo_option['std'])));
            }
        }
        //============

        //=== Фон. Обходимо все опции с типом background и передаем их значения в качестве переменных в style.less
        foreach ($bo_options as $key => $bo_option) {
            if ($bo_option['type'] == 'background') {
                $background = of_get_option($bo_option['id'], array('color' => '', 'repeat' => 'repeat', 'position' => 'top left', 'attachment' => 'scroll'));        
                $bkgrnd_color = apply_filters('of_sanitize_color', $background['color']);
                $res = '';
                
                if (esc_url($background['image'])) {
                    $res = "url('".esc_url($background['image'])."') ".$background['repeat']." ".$background['attachment']." ".$background['position']." ";
                }
                if ($bkgrnd_color) {
                    $res .= $bkgrnd_color;
                }
                if (!$res) {
                    $res = 'none';
                }
                $bo_for_less[$bo_option['id']] = array('css_selector' => (isset($bo_option['css_selector'])) ? $bo_option['css_selector'] : '', 'prop' => 'background', 'value' => $res);
            }
        }
    	//============

        //=== Шрифт. Обходим все опции с типом typography и передаем их значения в качестве переменных в style.less
        foreach ($bo_options as $key => $bo_option) {
            if ($bo_option['type'] == 'typography') {
                $arr = of_get_option($bo_option['id'], array('size' => '16px', 'face' => 'Georgia, serif', 'color' => '#666666'));
                if ($arr) {
                    $bo_for_less[$bo_option['id']] = array('css_selector' => (isset($bo_option['css_selector'])) ? $bo_option['css_selector'] : '', 'prop' => 'font-family', 'value' => $arr['face']);
                    $bo_for_less[$bo_option['id'].'_size'] = array('css_selector' => (isset($bo_option['css_selector'])) ? $bo_option['css_selector'] : '', 'prop' => 'font-size', 'value' => $arr['size']);
                    $bo_for_less[$bo_option['id'].'_weight'] = array('css_selector' => (isset($bo_option['css_selector'])) ? $bo_option['css_selector'] : '', 'prop' => 'font-weight', 'value' => $arr['style']);
                    $bo_for_less[$bo_option['id'].'_color'] = array('css_selector' => (isset($bo_option['css_selector'])) ? $bo_option['css_selector'] : '', 'prop' => 'color', 'value' => $arr['color']);
                }
            }
        }
        //============
        
        //var_dump($bo_for_less);
        
        //Передаем переменные в lessphp и формируем наборы для dynamic.less
        $bo_for_dynamic = array();
        foreach ($bo_for_less as $key => $value) {
            
            //Если значение пустое и это не свойство content, то пропускаем это кривое свойство.
            if (empty($value['value']) && $value['prop'] != 'content') { 
                continue;
            }
            
            //Если задан css_selector, то формируем набор для dynamic.less
            if (!empty($value['css_selector'])) { //не пустое значение как доп. чистка, если по каким-то причинам значение из базы не вытянулось.

                if (!isset($bo_for_dynamic[$value['css_selector']])) {
                    $bo_for_dynamic[$value['css_selector']] = array();
                }
                
                //Если есть название свойства, то задаем в виду prop:value.
                if (isset($value['prop']) && !empty($value['prop'])) {
                    array_push($bo_for_dynamic[$value['css_selector']], $value['prop'].': '.$value['value'].';');
                    
                //Если переданы полные CSS-правила.
                } elseif ($value['css_selector'] == 'rules') {
                    array_push($bo_for_dynamic[$value['css_selector']], $value['value']);
                }

            } else {
                //Передаем переменные в lessphp.
                $lessphp_obj->vars[$key] = $value['value'];                
            }
        }
        //var_dump($bo_for_dynamic);
        
        //Объединяем свойства одинаковых селекторов в один селектор, чтобы dynamic.less был компактнее.
        $sum_dynamic = '';
        foreach ($bo_for_dynamic as $key => $arr) {
            
            //Если переданы полные CSS-правила.
            if ($key == 'rules') {
                $str_temp = '';
                foreach ($arr as $value) {
                    $str_temp .= $value.PHP_EOL;
                }
                
            //Если передана пара свойство:значение.
            } else {
                $str_temp = $key.' {'.PHP_EOL;
                foreach ($arr as $value) {
                    $str_temp .= $value.PHP_EOL;
                }
                $str_temp .= '}'.PHP_EOL;
            }
            
            $sum_dynamic .= $str_temp;
        }
        
        //Записываем результат в dynamic.less
        $file_page_less = trailingslashit(dirname(__FILE__)).'dynamic.less';
        file_put_contents($file_page_less, $sum_dynamic); //Каждая новая запись перезаписывает всё предыдущее содержимое.
    }
    //var_dump($lessphp_obj);
}
//add_action('optionsframework_after_validate', 'set_vars_to_less'); //Вызов после валидации настроек Theme Options.
add_action('bo_lessify_add_vars', 'set_lessify_vars'); //Мой кастомный хук в плагине lessify-wp.
//==============================================================

//=== Устанавливаем рекомпиляцию .less в .css только для админов, т.е. обычные посетители сайта не будут инициировать рекомпиляцию.
//=== bo необходимо сделать рекомпиляцию на основе опции в Theme Options, чтобы не ухудшать производительность.
function lessify_always_recompile() {
    if (current_user_can('manage_options')) {
        update_option('lessify_last_recompilation', gmdate('d.m.Y H:i:s', time() + 3*3600)); //Время по гринвичу + 3 часа. Более надежный способ получить московское время.
        return true;
    } else {
        return false;
    }
}
add_filter('less_force_compile', 'lessify_always_recompile');
//==============================================================

//======================================
//=== Настройка админ панели.
//======================================

//=== Доб в таблицу постов колонки с информацией по элементам поста.
function bo_modify_post_table( $column ) {
	
    /*
    $column['bo_post_oglav_count'] = 'Оглавление';
    $column['bo_post_images_count'] = 'Картинки';
    $column['bo_post_youtube_count'] = 'Youtube';
    $column['bo_post_tables_count'] = 'Таблицы';
    $column['bo_post_links_count'] = 'Ссылки';
    */
	
	if (quark_is_affiliateegg_active()) {
		$column['bo_post_affegg_shortcodes'] = 'AffEgg-шорткоды (в контенте)';
    }
	
    return $column;
}

function bo_modify_post_table_row( $column_name, $post_id ) {
    $doc = new DOMDocument();
    
    $post_content = get_post($post_id)->post_content;
    if ($post_content) {
        @$doc->loadHTML($post_content); //@ - отменяет вывод всех warnings.
            
        switch ($column_name) {
            case 'bo_post_oglav_count' : //Присутствует или нет оглавление к статье. Ищем div с id="oglav".
                //$elements = $doc->getElementById('oglav');
                $elements = $doc->getElementsByTagName('div');
                $inc=0;
                if (!is_null($elements)) {
                    foreach ($elements as $element) {
                        if($element->getAttribute('id')=='oglav') {
                            $inc+=1;
                        }
                    }
                }
                echo $inc;
    
                break;  
                
            case 'bo_post_images_count' : //Кол-во картинок в посте. Ищутся с помощью xpath.
                $xml=simplexml_import_dom($doc);
                $images=$xml->xpath('//img');
                $img_inc=0;
                foreach ($images as $img) {
                    //echo $img['src'] . ' ' . $img['alt'] . ' ' . $img['title'];
                    $img_inc+=1;
                }
                //echo $img_inc.' images';
                echo $img_inc;
                
                break;
                
            case 'bo_post_youtube_count' : //Кол-во видео из Youtube в посте.
                echo substr_count(get_post($post_id)->post_content, "http://www.youtube.com");
    
                break;        
    
            case 'bo_post_tables_count' : //Кол-во таблиц в посте.
                $elements = $doc->getElementsByTagName('table');
                $inc=0;
                if (!is_null($elements)) {
                    foreach ($elements as $element) {
                        $inc+=1;
                    }
                }
                
                //Подсветка.
                if($inc>0) {
                    echo $inc;
                } else {
                    echo '<span style="color:red;">'.$inc.'</span>';
                }
    
                break;
                
            case 'bo_post_links_count' : //Кол-во ссылок в посте исключая якорные ссылки.
                $elements = $doc->getElementsByTagName('a');
                $inc=0;
                if (!is_null($elements)) {
                    foreach ($elements as $element) {
                        if(strpos(trim($element->getAttribute('href')),'#')!==0) {
                            $inc+=1;
                        }
                    }
                }
                echo $inc;
    
                break; 
                
            case 'bo_post_affegg_shortcodes' : //Все AffEgg-шорткоды поста.
                $pos = stripos($post_content, '[affegg'); //[affegg id=166]
                if ($pos !== false) {
                    $shortcodes = '';
                    $arr = explode('[affegg', $post_content);
                    foreach ($arr as $value) {
                        $pos2 = stripos($value, ']'); //[affegg id=166]
                        if ($pos2 !== false) {
                            $shortcodes .= '[affegg'.substr($value, 0, $pos2+1);
                        }
                    }
                    
                    echo $shortcodes;
                }

            default:
        }
    }
}
add_filter('manage_posts_columns', 'bo_modify_post_table');
add_action('manage_posts_custom_column', 'bo_modify_post_table_row', 10, 2);
//==============================================================

//=== Создаем виджет для UniSender.
//=== http://www.wpbeginner.com/wp-tutorials/how-to-create-a-custom-wordpress-widget/
//=== http://code.tutsplus.com/articles/building-custom-wordpress-widgets--wp-25241
class unisender_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'unisender_widget', //Должно совпадать с именем класса.
            'UniSender', //Название виджета будет видно в окне всех виджетов.
            array(
                'description' => 'Email-рассылка от UniSender.com. Виджет настраивается в Theme Options.', //Описание виджета будет видно в окне всех виджетов.
            ) 
        );
    }
    
    //Вывод во фронтенд.
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'].$title.$args['after_title'];
        }
?>
        <span class="unisender-signup-link unisender-signup-sticker" href="#unisender-signup-popup">Подписаться</span>
<?php
        echo $args['after_widget'];
    }
    		
    //Вывод в бэкенд.
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = ''; //'UniSender';
        }
?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo 'Title'; ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
<?php 
    }
    	
    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

//Регистрируем виджет.
function unisender_widget_reg() {
    register_widget('unisender_widget');
}
add_action('widgets_init', 'unisender_widget_reg');
//==============================================================

//Для элемента wp_nav_menu со ссылкой #unisender-signup-popup проставляем класс .unisender-signup-link для того, чтобы эта ссылка открывала форму "Подписаться на Email-рассылку".
function unisender_nav_menu_attributes($atts, $item, $args) {
    /*
    //Поиск через ID элемента меню и добавление к нему произвольных атрибутов.
    $menu_target = 4297;
    if ($item->ID == $menu_target) {
        $atts['data-reveal-id'] = 'myModal1';
        $atts['data-animation'] = 'fade';
    }
    */
    
    if ($atts['href'] == '#unisender-signup-popup') {
        $atts['class'] = 'unisender-signup-link unisender-signup-menu';
    }

    return $atts;
}
add_filter('nav_menu_link_attributes', 'unisender_nav_menu_attributes', 10, 3);
//==============================================================

/*
//=== Сохраняем в базу дефолтные значения для полей Advanced Custom Fields у которых в базе все еще пустые значения.
//=== Это может быть в том случае, если добавились новые поля, но их значения еще не зафиксированы в базе, поскольку пост не обновлялся.
//=== https://wordpress.org/support/topic/plugin-advanced-custom-fields-allow-php-in-fields-default-value
//=== http://www.advancedcustomfields.com/resources/acfload_value/
//=== http://www.advancedcustomfields.com/resources/get_field_object/
function acf_set_default_value($value, $post_id, $field) {
	//if (empty($value)) {
	//if (empty($field['value'])) {
	if (empty($value)) {
		$value = $field['default_value'];
	}
	return $value;
}
add_filter('acf/load_value', 'acf_set_default_value', 10, 3);
//==============================================================
*/

/*
//=== Получение значения произвольного поля поста. Плюс подстановка дефолтного значения на основне постфикса из имени произвольного поля.
//=== Это необходимо, поскольку при добавлении в темабоксы новых полей их значения в постах не сохраняются в базу пока не нажата кнопка Обновить/Сохранить в окне редактирования поста.
function bo_get_field($post_id = null, $field_name = null, $single = true) {
    $value = null;
    
    if ($post_id && $field_name) {
        $value = get_post_meta($post_id, $field_name, $single); //true - значит взять значение из первой найденной строки, а не вернуть значения всех найденных строк (полей).
    	if (empty($value)) {
            $pos = stripos($field_name, '-');
            if ($pos) {
                $value = substr($field_name, $pos+1, 99);
            }
    	}
    }
    
	return $value;
}
//==============================================================
*/

//=== Получение значения произвольного поля поста. Плюс подстановка дефолтного значения, если поле не найдено или значение пустое.
function bo_get_field($post_id = null, $field_name = null, $default = '', $single = true) {
    $value = null;
    
    if ($post_id && $field_name) {
        $value = get_post_meta($post_id, $field_name, $single); //true - значит взять значение из первого элемента и вернуть как строку, а не вернуть в виде набора значений.
    	if (empty($value)) {
            return $default;
    	} else {
    	    return $value;
    	}
    } else {
        return $default;
    }
}
//==============================================================

/*
//=== Шорткод вывода анонсов постов категорий.
function bo_posts_shortcode( $atts ) {
	$original_atts = $atts;

	// Pull in shortcode attributes and set defaults
	$atts = shortcode_atts( array(
		'category'            => '',
		'order'               => 'DESC',
		'orderby'             => 'date',
		'post_type'           => 'post',
		'posts_per_page'      => '10',
		'posts_view_variant'  => '', //'' = списочное представление, 'js-masonry js-masonry-2cols', 'js-masonry js-masonry-3cols', 'js-masonry js-masonry-4cols'
	), $atts, 'display-posts' );

	$category = sanitize_text_field($atts['category']);
	$order = sanitize_key($atts['order']);
	$orderby = sanitize_key($atts['orderby']);
	$post_type = sanitize_text_field($atts['post_type']);
	$posts_per_page = intval($atts['posts_per_page']);
		
	// Set up initial query for post
	$args = array(
		'category_name'       => $category,
		'order'               => $order,
		'orderby'             => $orderby,
		'post_type'           => explode( ',', $post_type ),
		'posts_per_page'      => $posts_per_page,
	);

	$args['post_status'] = array('publish'); //'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any'

	$listing = new WP_Query($args);
	if (!$listing->have_posts()) {
	    return '';
	}
?>
    <div id="posts-list" class="<?php echo sanitize_text_field($atts['posts_view_variant']); ?>">
<?php
	while ($listing->have_posts()) {
	    $listing->the_post();
	    global $post;
		get_template_part('content', get_post_format()); //Именно через get_template_part, чтобы формировать однотипную структуру.
	}
?>
    </div>
<?php

	wp_reset_postdata();
}
add_shortcode('bo_posts', 'bo_posts_shortcode' );
//==============================================================
*/

//=== К странице Theme Options подключаем jQuery для добавления функционала скрытия/отображения дочерних элементов.
//=== https://github.com/devinsays/options-framework-plugin/issues/113#issuecomment-7530716
function bo_optionsframework_js() {

	//Необходимо загрузать в начале документа.
	wp_register_script('optionsframework-custom', trailingslashit(get_template_directory_uri()).'js/admin/optionsframework-custom.js', array(), '1.0.0', false);
	wp_enqueue_script('optionsframework-custom');
}
add_action('optionsframework_custom_scripts', 'bo_optionsframework_js');
//==============================================================

//=== Парсер add_more массивов Piklist.
//=== https://gist.github.com/JasonTheAdams/d40351ecf5bca8a7e7f4
function parse_piklist_array($array) {
    if (empty($array))
    return array();
    
    $keys = array_keys($array);
    if (empty($keys))
    return array();
    
    $results = $values = array();
    $count = count($array[$keys[0]]);
    
    for ($index = 0; $index < $count; $index++) {
        foreach($keys as $key_index => $key) {
            $value = (isset($array[$key][$index])) ? $array[$key][$index] : null;
            if (is_array($value) && !(isset($value[0][0]) || empty($value[0]))) {
                $values[$key] = parse_piklist_array($value, true);
            } else
                $values[$key] = $value;
        }
        
        $results[] = $values;
    }
    
    return $results;
}
//==============================================================

//=== Удаляем вусивуг редактор с типов постов page.
function remove_elements_from_page() { 
    remove_post_type_support('page', 'editor');
}
//add_action('init', 'remove_elements_from_page', 10);
//==============================================================

/*
//=== Набор полей по заданию бэкграунда в Piklist.
function bo_piklist_metaboxe_background($base_name = '') {
    if (!$base_name) {
        return;
    }
    
    piklist('field', array(
        'type' => 'colorpicker',
        'field' => $base_name.'_background_color-',
        'value' => '',
        'label' => 'Фон страницы',
        'description' => '',
        'attributes' => array(
            'class' => 'text',
        ),
        //'columns' => 5,              
    ));
    
    piklist('field', array(
        'type' => 'file',
        'field' => $base_name.'_background_image-',
        'value' => '',
        'label' => 'Фоновая картинка/паттерн',
        'description' => '',
        'options' => array(
            'button' => 'Выбрать',
        ),
    ));
    
    piklist('field', array(
        'type' => 'select',
        'field' => $base_name.'_background_image_repeat-no-repeat',
        'label' => 'Повторение',
        'value' => 'no-repeat',
        'description' => '',
        'attributes' => array(
            'class' => 'text'
        ),
        'choices' => array(
            'no-repeat' => 'No Repeat',
            'repeat-x' => 'Repeat Horizontally',
            'repeat-y' => 'Repeat Vertically',
            'repeat' => 'Repeat All',
        ),
    ));
    
    piklist('field', array(
        'type' => 'select',
        'field' => $base_name.'_background_image_coord-',
        'label' => 'Позиция отсчета',
        'value' => 'top left',
        'description' => '',
        'attributes' => array(
            'class' => 'text'
        ),
        'choices' => array(
            'top left' => 'Top Left',
            'top center' => 'Top Center',
            'top right' => 'Top Right',
            
            'center left' => 'Middle Left',
            'center center' => 'Middle Center',
            'center right' => 'Middle Right',
            
            'bottom left' => 'Bottom Left',
            'bottom center' => 'Bottom Center',
            'bottom right' => 'Bottom Right',
        ),
    ));
    
    piklist('field', array(
        'type' => 'select',
        'field' => $base_name.'_background_image_scroll-scroll',
        'label' => 'Скролл',
        'value' => 'scroll',
        'description' => '',
        'attributes' => array(
            'class' => 'text'
        ),
        'choices' => array(
            'scroll' => 'Scroll Normally',
            'fixed' => 'Fixed in Place',
        ),
    ));
}
*/

//=== Создаем произвольные поля для записей.
//=== http://code.tutsplus.com/tutorials/how-to-create-custom-wordpress-writemeta-boxes--wp-20336
//=== http://www.smashingmagazine.com/2011/10/04/create-custom-post-meta-boxes-wordpress/
//=== http://themefoundation.com/wordpress-meta-boxes-guide/

//Отображаем собственные произвольные поля и заполняем их сохраненными ранее или дефолтными значениями.
function bo_post_metabox_add() {
    add_meta_box('bo-post-metabox', 'Основные настройки записи', 'bo_post_metabox_callback', 'post', 'side', 'high'); 
    //normal/side/advanced
    //high/core/default/
}
add_action('add_meta_boxes', 'bo_post_metabox_add');

function bo_post_metabox_callback($post) {
    //echo 'What you put here, show\'s up in the meta box';
    
    //Сгребаем все произвольные поля текущей записи.
    $values = get_post_custom($post->ID);

    //=== Получаем сохраненные ранее значения полей.
    $bo_cf_post_type_selected = isset($values['bo_cf_post_type']) ? esc_attr($values['bo_cf_post_type'][0]) : of_get_option('bo_post_type_default', 'post');
    $bo_cf_post_attach_selected = isset($values['bo_cf_post_attach']) ? esc_attr($values['bo_cf_post_attach'][0]) : of_get_option('bo_post_attach_default', 'none');
    //==================================
    
    //Доп. инструмент обеспечения безопасности при работе с произвольными полями.
    //http://codex.wordpress.org/Function_Reference/wp_nonce_field
    wp_nonce_field('bo_post_metabox_nonce_action', 'bo_post_metabox_nonce');

    $bo_options = optionsframework_options(); //Весь массив опций из Theme Options.
?>
    <div style="margin-bottom: 10px">
        <div><label for="bo_cf_post_type">Тип записи</label></div>
        <div><select name="bo_cf_post_type" id="bo-cf-post-type">
<?php
            foreach ($bo_options['bo_post_type_default']['options'] as $key => $value) { //Из опции 'bo_post_type_default' берем доступные типы постов.
                if ($key == $bo_cf_post_type_selected) {
                    echo '<option selected value="'.$key.'">'.$value.'</option>';
                } else {
                    echo '<option value="'.$key.'">'.$value.'</option>';
                }
            }
?>
        </select></div>
    </div>

    <div style="margin-bottom: 10px">
        <div><label for="bo_cf_post_attach">Расположение аттачей (изображения и прочее)</label></div>
        <div><select name="bo_cf_post_attach" id="bo-cf-post-attach">
<?php
            foreach ($bo_options['bo_post_attach_default']['options'] as $key => $value) { //Из опции 'bo_post_attach_default' берем доступные способы размещения аттачей.
                if ($key == $bo_cf_post_attach_selected) {
                    echo '<option selected value="'.$key.'">'.$value.'</option>';
                } else {
                    echo '<option value="'.$key.'">'.$value.'</option>';
                }
            }
?>
        </select></div>
    </div>
<?php
}

//Сохранение значений произвольных полей при сохранении поста.
function bo_post_metabox_save($post_id) {
    //Если это автосохранение, то не имеет смысла сохранять через автосохранение.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    //if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['bo_post_metabox_nonce']) || !wp_verify_nonce($_POST['bo_post_metabox_nonce'], 'bo_post_metabox_nonce_action')) return;
    
    //Если текущий юзер не имеет прав на редактирование поста, то не сохраняем.
    if (!current_user_can('edit_post')) return;

    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );

    //Если значения в полях заданы, то сохраняем их.
    if (isset($_POST['bo_cf_post_type'])) {
        update_post_meta($post_id, 'bo_cf_post_type', esc_attr($_POST['bo_cf_post_type']));
    }
    if (isset($_POST['bo_cf_post_attach'])) {
        update_post_meta($post_id, 'bo_cf_post_attach', esc_attr($_POST['bo_cf_post_attach']));
    }
}
add_action('save_post', 'bo_post_metabox_save');
//==============================================================

//=== Добавляем в список постов поле для фильтрации по типу поста.
//=== http://wordpress.stackexchange.com/q/45436/2487

//Формируем поле с фильтром.
function bo_admin_posts_filter_post_type() {
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('post' == $type) { //Цепляемся только к типу постов post.

        $bo_options = optionsframework_options(); //Весь массив опций из Theme Options.
        $bo_posts_types = $bo_options['bo_post_type_default']['options']; //Из опции 'bo_post_type_default' берем доступные типы постов.
?>
        <select name="ADMIN_FILTER_FIELD_VALUE">
            <option value="">Все типы записей</option>
<?php
            $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE'])? $_GET['ADMIN_FILTER_FIELD_VALUE']:'';
            foreach ($bo_posts_types as $key => $label) {
                printf (
                    '<option value="%s"%s>%s</option>',
                    $key,
                    $key == $current_v? ' selected="selected"':'',
                    $label
                );
            }
?>
        </select>
<?php
    }
}
add_action('restrict_manage_posts', 'bo_admin_posts_filter_post_type');

//Выполняем фильтрацию.
function bo_admin_posts_filter_post_type_run($query) {
    global $pagenow;
    
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    
    //Цепляемся только к типу постов post.
    if ('post' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = 'bo_cf_post_type'; //Фильтруем по этому полю.
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
}
add_filter('parse_query', 'bo_admin_posts_filter_post_type_run');
//==============================================================

//=== Вывод логов ошибок в дашборд. Это сборник ошибок которые регистрируем сервер (сайт) при обращении к нему пользователей.
//=== http://wp-kama.ru/id_916/10-manipulyatsiy-nad-adminkoy-wordpress-sayt-dlya-klienta.html
function slt_PHPErrorsWidget() {
	$logfile = $_SERVER['DOCUMENT_ROOT'].'/../../logs/'.$_SERVER['HTTP_HOST'].'.error.log'; // Полный путь до лог файла.
	echo '<div style="word-wrap:break-word; padding:5px; background-color:#eAeAeA;">Лог из файла: '.$logfile.'</div>';
	
	$displayErrorsLimit = 100; // Максимальное количество ошибок, показываемых в виджете.
	$errorLengthLimit = 300; // Максимальное число символов для описания каждой ошибки.
	echo '<div style="word-wrap:break-word; padding:5px; background-color:#eAeAeA;">';
	echo 'Выводится не более: '.$displayErrorsLimit.' сообщений и из каждого сообщения не более '.$errorLengthLimit.' символов.</div>';
	
	$fileCleared = false;
	$userCanClearLog = current_user_can('manage_options');

	// Очистить файл?
	if( $userCanClearLog && isset( $_GET["slt-php-errors"] ) && $_GET["slt-php-errors"]=="clear" ){
		$handle = fopen( $logfile, "w" );
		fclose( $handle );
		$fileCleared = true;
	}
	
	// Читаем файл
	if( file_exists( $logfile ) ){
		$errors = file( $logfile );
		$errors = array_reverse( $errors );
		if ( $fileCleared ) echo '<p><em>Файл очищен.</em></p>';
		if ( $errors ) {
			echo '<p> Ошибок: '. count( $errors ) . '.';
			if ( $userCanClearLog ) 
				echo ' [ <b><a href="'. admin_url() .'?slt-php-errors=clear" onclick="return confirm(\'Вы уверенны?\');">Очистить файл логов</a></b> ]';
			echo '</p>';
			echo '<div id="slt-php-errors" style="max-height:500px; overflow:auto; padding:5px; background-color:#FAFAFA;">';
			echo '<ol style="padding:0; margin:0;">';
			$i = 0;
			foreach( $errors as $error ){
				echo '<li style="padding:2px 4px 6px; border-bottom:1px solid #ececec;">';
				$errorOutput = preg_replace( '/\[([^\]]+)\]/', '<b>[$1]</b>', $error, 1 );
				if( strlen( $errorOutput ) > $errorLengthLimit ){
					echo substr( $errorOutput, 0, $errorLengthLimit ).' [...]';
				}
				else
					echo $errorOutput;
				echo '</li>';
				$i++;
				if( $i > $displayErrorsLimit ) {
					break;
				}
			}
			echo '</ol></div>';
		}
		else
			echo '<p>Ошибок нет!</p>';
	}
	else
		echo '<p><em>Произошла ошибка чтения лог файла.</em></p>';
}

//Добавляем виджет в дашборд.
function slt_dashboardWidgets() {
    if (current_user_can('manage_options')) {
	    wp_add_dashboard_widget('slt-php-errors', 'PHP errors', 'slt_PHPErrorsWidget');
    }
}
add_action('wp_dashboard_setup', 'slt_dashboardWidgets');
//==============================================================

//=== Обработчики после валидации значений опций в Theme Options. После валидации опции прямиком идут на сохранение.
//=== Не забываем, что это action и поэтому он не возвращает обратно никакие значения, даже аргументы по ссылке.
//=== Т.е. получается, что данные хук нужен только для того, чтобы получить значения опций без последующей их записи обратно.
//=== https://github.com/devinsays/options-framework-theme/issues/87
function bo_optionsframework_after_validate($options) {
    
    /*
    //Валидация значения select, чтобы он "случайно" не ухватил значение, которое не представлено в его списке.
    //var_dump($options);
    
    $bo_options = optionsframework_options(); //Весь массив опций из Theme Options.
    //var_dump($bo_options);

    //Собираем все опции типа select.
    $bo_type_select = array();
    foreach ($bo_options as $bo_option) {
        if ($bo_option['type'] == 'select') {
            $bo_type_select[$bo_option['id']] = $bo_option;
        }
    }
    //var_dump($bo_type_select);
    
    //Теперь обходим все select'ы и при необходимости нормализуем их значения.
    $bo_not_exist = array();
    $bo_wrong_value = array();
    $bo_norm_value = array();
    foreach ($bo_type_select as $option_select) {
        if (isset($options[$option_select['id']])) {
            
            //Если в select`е на текущий момент задано значение которое есть среди доступных для него значений, то все гуд. 
            if (isset($option_select['options'][$options[$option_select['id']]])) {
                //
                
            //Нормализуем значение.
            } else {
                
                //Добавляем в массив для вывода на просмотр.
                //Раскомментить для вывода.
                //$bo_wrong_value[$option_select['id']]['cur_value'] = $options[$option_select['id']];
                //$bo_wrong_value[$option_select['id']]['his_options'] = $option_select['options'];
                //$bo_norm_value[$option_select['id']] = $option_select['std'];
                
                //Или же нормализуем (т.е. задаем значение по умолчанию).
                $options[$option_select['id']] = $option_select['std'];
            }
            
        } else {
            $bo_not_exist[] = $option_select['id'];
        }
    }
    
    //Проверка и вывод сообщения об несоответствии.
    if (count($bo_not_exist) > 0) {
        echo count($bo_not_exist).' из всего '.count($bo_type_select).' не передали свое значение на сохранение.<br />';
        var_dump($bo_not_exist);
        exit;
    }
    
    //Вывод select'ов которые содержат некорректные значения.
    if (count($bo_wrong_value) > 0) {
        echo 'Select`ы у которых их текущие значения выходят за рамки обозначенных им опций.<br />';
        var_dump($bo_wrong_value);
        echo 'После нормализации:<br />';
        var_dump($bo_norm_value);
        exit;
    }    
    */
}
//add_action('optionsframework_after_validate', 'bo_optionsframework_after_validate', 10, 2);
//==============================================================

/*
//=== Модификация создаваемых постов.
function on_post_create($data , $postarr) {
    
    //Определяем новый пост или сохранение ранее созданного.
    $post_id = $postarr['ID'];
    
    return $data;
}
//add_filter('wp_insert_post_data', 'on_post_create', '99', 2);
//==============================================================
*/

/*
//=== Модификация постов. Вызывается когда пост или страница только что создана или обновлена (сохранена).
//=== http://codex.wordpress.org/Plugin_API/Action_Reference/save_post
function bo_post_mod($post_id) {
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    //Модифицируем посты, созданные с помощью AffEgg методом автоблоггинга.
    $post_title = get_the_title( $post_id );
    if ($post_title == 'egg-product-card') {
        //$var = do_shortcode( '[gallery]' );
        update_post_meta($post_id, 'bo_cf_post_type', 'product_card');
    }


}
//add_action('save_post', 'bo_post_mod');
//==============================================================
*/

//=== Проверяем параметры спарсенного продукта и принимаем решение - создавать или нет по нему пост.
//=== Данный фильтр отрабатывает до моменнта создания поста.
function after_product_create($product) {
    /*
    if (current_user_can('manage_options')) {
        var_dump($product);
    }
    */
    return $product;
}
add_filter('affegg_product_insert', 'after_product_create', 10, 1);
//==============================================================

//=== Модифицируем посты, созданные с помощью AffEgg методом автоблоггинга.
function after_autoblog($post_id, $products) {
    
    /*
    if (current_user_can('manage_options')) {
        //var_dump($products);

        $result = array();
        array_walk_recursive($products, function($value, $key) use (&$result) {
            $result = array($key, $value); // тут возвращаете как вам хочется
        });
        var_dump($result);
        
        return;
    }
    */
    
    //Основные настройки записи.
    update_post_meta($post_id, 'bo_cf_post_type', 'product-card');
    update_post_meta($post_id, 'bo_cf_post_attach', 'none');

    //Чекаем все родительские категории у заданной для поста (в настройках автоблоггинга) категории.
    //http://codex.wordpress.org/Function_Reference/wp_set_object_terms
    $categories = get_the_category($post_id);
    if ($categories) {
    	foreach ($categories as $category) {
    	    //var_dump($category);
    	    //echo get_category_parents($category->term_id, false, ']');
    	    
    	    $parent_cats = get_ancestors($category->term_id, 'category');
            $temp = wp_set_object_terms($post_id, $parent_cats, 'category', true);
    	}
    }

    //Создаем/обновляем мета-данные поста.
    foreach ($products as $product) {
        bo_post_product_update($post_id, $product);
    }
}
add_action('affegg_autoblog_create_post', 'after_autoblog', 10, 2);
//==============================================================

//=== Дополнительные обработки после обновления AffEgg-продукта.
function after_product_update($product) {
    /*
    if (current_user_can('manage_options')) {
        var_dump($product);
    }
    */
    
    $arr_egg_updates = array();
    $arr_egg_updates = get_transient('egg_updates');
    
    //Обходим все посты типа Карточка товара и актуализируем произвольные поля этих постов-товаров.
    $args = array(
        'post_type'  => 'post',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key'     => 'bo_cf_post_type',
                'value'   => 'product-card',
                'compare' => '=',
            ),
            array(
                'key'     => 'bo_cf_product_id',
                'value'   => $product['id'],
                'compare' => '=',
            ),
        ),
    );
    $query_posts = new WP_Query($args); //http://codex.wordpress.org/Class_Reference/WP_Query
    
    //Если посты найдены, значит данный товар присутствует в виде Карточки товаров, поэтому актуализируем произвольные поля этих постов товара.
    if ($query_posts->have_posts()) {
        /*
        if (current_user_can('manage_options')) {
            var_dump($query_posts);
        }
        */
        
        while ($query_posts->have_posts()) {
            $query_posts->the_post();
            
            bo_post_product_update($query_posts->post->ID, $product, true);
            
            //Ведем лог по обновляемости постов (продуктов).
            $item = array();
            $item['date'] = gmdate('d.m.Y H:i:s', time() + 3*3600); //Время по гринвичу + 3 часа. Более надежный способ получить московское время.
            $item['post_id'] = $query_posts->post->ID;
            $item['post_title'] = $query_posts->post->post_title;
            $item['egg_id'] = $product['egg_id'];
            $item['product_id'] = $product['id'];
            $item['note'] = 'карточка товара';
            if (is_array($arr_egg_updates)) {
                array_unshift($arr_egg_updates, $item); //Добавляем новый элемент в начало массива.
            } else {
                $arr_egg_updates[] = $item;
            }
        }
    
    //Если посты не найдены, значит это товар не для Карточки товара.
    } else {
        $item = array();
        $item['date'] = gmdate('d.m.Y H:i:s', time() + 3*3600); //Время по гринвичу + 3 часа. Более надежный способ получить московское время.
        $item['egg_id'] = $product['egg_id'];
        $item['product_id'] = $product['id'];
        $item['note'] = '';
        if (is_array($arr_egg_updates)) {
            array_unshift($arr_egg_updates, $item); //Добавляем новый элемент в начало массива.
        } else {
            $arr_egg_updates[] = $item;
        }
        
        //Плюс ищем AffEgg-витрины (не продукты) которые не используются ни в одном посте.
        global $wpdb;
        //$numposts = $wpdb->get_var("SELECT count(*) FROM $wpdb->posts WHERE post_type = 'post' AND (post_content LIKE '%[affegg%id=".$item['egg_id']."]%' OR post_content LIKE '%[affegg%id=".$item['egg_id']." %]%')");
        //$numposts = $wpdb->get_var("SELECT count(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_content LIKE '%[affegg%id=".$item['egg_id']."]%'");
        
        //https://codex.wordpress.org/Post_Status#Published
        //$numposts = $wpdb->get_var("SELECT count(*) FROM $wpdb->posts WHERE post_type = 'post' AND (post_status = 'publish' OR post_status = 'draft') AND (post_content LIKE '%[affegg%id=".$item['egg_id']."]%' OR post_content LIKE '%[affegg%id=".$item['egg_id']." %]%')");
        
        //$numposts = $wpdb->get_var("SELECT count(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status LIKE '%' AND (post_content LIKE '%[affegg%id=".$item['egg_id']."]%' OR post_content LIKE '%[affegg%id=".$item['egg_id']." %]%')");
        $numposts = $wpdb->get_var("SELECT count(*) FROM $wpdb->posts WHERE post_content LIKE '%[affegg%id=".$item['egg_id']."]%' OR post_content LIKE '%[affegg%id=".$item['egg_id']." %]%'");

        if ($numposts == 0) {
            $arr_egg_not_used = array();
            $arr_egg_not_used = get_transient('egg_not_used');
            if (is_array($arr_egg_not_used)) {
                array_unshift($arr_egg_not_used, $item); //Добавляем новый элемент в начало массива.
            } else {
                $arr_egg_not_used[] = $item;
            }
            
            if (is_array($arr_egg_not_used)) {
                $arr_egg_not_used = array_slice($arr_egg_not_used, 0, 500); //Оставляем только 500 последних записей об не используемых AffEgg-витринах.
            }
            set_transient('egg_not_used', $arr_egg_not_used);
        }
    }
    //=====================================
    
    if (is_array($arr_egg_updates)) {
        $arr_egg_updates = array_slice($arr_egg_updates, 0, 100); //Оставляем только 100 последних записей об обновлении.
    }
    set_transient('egg_updates', $arr_egg_updates);
    
    return $product;
}
add_filter('affegg_product_update', 'after_product_update', 10, 1);
//==============================================================

//=== Создаем/обновляем мета-данные поста по его продукту. Также нормализуем данные.
function bo_post_product_update($post_id, $product, $update = false) {
    
    //Если ID поста или данные по его продукту не переданы, то ничего не создаем/не обновляем.
    if (empty($post_id) || empty($product)) {
        return;
    }
    
    /*
    if (current_user_can('manage_options')) {
        var_dump($product);
    }
    */
    
    $params_for_cf = array();
    foreach ($product as $key => $value) {
        switch ($key) {
            case 'id':
                if (!$update) {
                    $params_for_cf['bo_cf_product_id'] = $value;
                }
                break;
            case 'egg_id':
                if (!$update) {
                    $params_for_cf['bo_cf_product_egg_id'] = $value;
                }
                break;
            case 'shop_id':
                if (!$update) {
                    $params_for_cf['bo_cf_product_shop_id'] = $value;
                }
                break;
            case 'create_date':
                if (!$update) {
                    $params_for_cf['bo_cf_product_date_create'] = $value;
                }
                break;
            case 'last_update':
                $params_for_cf['bo_cf_product_date_last_update'] = $value;
                break;
            case 'status':
                $params_for_cf['bo_cf_product_status'] = $value;
                break;
            case 'in_stock':
                $params_for_cf['bo_cf_product_in_stock'] = $value;
                break;
            case 'last_in_stock':
                $params_for_cf['bo_cf_product_in_stock_last'] = $value;
                break;
            case 'row_err_count':
                $params_for_cf['bo_cf_product_error_count'] = $value;
                break;
            case 'last_error':
                $params_for_cf['bo_cf_product_error_last'] = $value;
                break;
            case 'title':
                $params_for_cf['bo_cf_product_title'] = $value;
                break;
            case 'description':
                $params_for_cf['bo_cf_product_description'] = $value;
                break;
            case 'manufacturer':
                $params_for_cf['bo_cf_product_manufacturer'] = $value;
                break;
            case 'price':
                $params_for_cf['bo_cf_product_price'] = str_ireplace(' ', '', $value);
                break;
            case 'old_price':
                $params_for_cf['bo_cf_product_price_old'] = str_ireplace(' ', '', $value);
                break;
            case 'currency':
                if (!$update) {
                    $params_for_cf['bo_cf_product_price_currency'] = $value;
                }
                break;
            case 'discount_perc':
                //bo_cf_product_price_disc
                //Расчет скидки ниже.
                break;                
            case 'img_file': //img - на апдейте возвращает урл с сайта источника.
                if ($value) { //Если картинка определена, то заменяем, иначе оставляем имеющуюся.
                    $params_for_cf['bo_cf_product_image'] = $value;
                }
                break;
            case 'orig_img':
                $params_for_cf['bo_cf_product_image_orig'] = $value;
                break;
            case 'orig_img_large':
                $params_for_cf['bo_cf_product_image_orig_large'] = $value;
                break;
            case 'orig_url':
                $params_for_cf['bo_cf_product_orig_url'] = $value;
                break;
            case 'url':
                $params_for_cf['bo_cf_product_cpa_url'] = $value;
                break;
            case 'extra':
                if ($update) { //Хуком affegg_product_update возвращается сериализованный массив.
                    $value = unserialize($value);
                }
                
                foreach ($value as $key2 => $value2) {
                    
                    $cf_value = '';
                    switch ($key2) {
                        case 'features':
                            foreach ($value2 as $value3) {
                                $cf_value .= ($cf_value?'~~~~':'').$value3['name'].'~~'.$value3['value'];
                            }
                            $params_for_cf['bo_cf_features'] = $cf_value;
                            break;
                        case 'images':
                            foreach ($value2 as $value3) {
                                $cf_value .= ($cf_value?'~~~~':'').$value3;
                            }
                            $params_for_cf['bo_cf_product_yetimages_orig'] = $cf_value;
                            break;
                        case 'color':
                            foreach ($value2 as $value3) {
                                $cf_value .= ($cf_value?'~~~~':'').$value3;
                            }
                            $params_for_cf['bo_cf_product_color'] = $cf_value;
                            break;
                        case 'comments':
                            $params_for_cf['bo_cf_product_comments'] = '';
                            break;
                    }
                }
        }
    }
    
    //Расчитываем скидку.
    if ($params_for_cf['bo_cf_product_price_old'] > 0) {
        $params_for_cf['bo_cf_product_price_disc'] = round((($params_for_cf['bo_cf_product_price_old']-$params_for_cf['bo_cf_product_price'])/$params_for_cf['bo_cf_product_price_old'])*100);
    } else {
        $params_for_cf['bo_cf_product_price_disc'] = 0;
    }
    
    //Добавляем в пост произвольные поля.
    foreach ($params_for_cf as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }
    
    //Тест.
    //update_post_meta($post_id, 'bo_test', $product['in_stock'].' - '.$product['discount_perc']);
    
    //=== Дополнительно добавляем/удаляем технические метки.

    //Метка - есть на стоке и скидка 40%+.
    //if ($product['in_stock'] > 0 && $product['discount_perc'] >= 40) {
    if ($params_for_cf['bo_cf_product_in_stock'] > 0 && $params_for_cf['bo_cf_product_price_disc'] >= 40) {

        //Добавляем метку, если ее нет.
        if (!has_term('disc_ot_40', 'tech-tags', $post_id)) { //Указываем slug терма.
            $temp = wp_set_object_terms($post_id, 'disc_ot_40', 'tech-tags', true); //Указываем slug терма. true - значит добавляем к имеющимся, а не перезаписываем.
        }
        
        //Выставляет посту статус Опубликовано, поскольку он подходит по условиям скидки.
        //Опубликовываем и при создании Карточки товара и при обновлении мета-данных Карточки товара.
        //if (!$update) {
        if (get_post_status($post_id) != 'publish') {
            //wp_publish_post($post_id); //эта функция при публикации не задает пермалинк, поэтому черновики переходящие в опубликованные остаются без своего урла.
            wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
        }
        
    } else {
        
        //Убираем метку, если она есть.
        //При этом, если пост ранее был Опубликовано, то в статус Черновик его не переводим, поскольку ранее пост уже мог попасть на глаза поисковым роботам.
        if (has_term('disc_ot_40', 'tech-tags', $post_id)) { //Указываем slug терма.
            wp_remove_object_terms($post_id, 'disc_ot_40', 'tech-tags'); //Указываем slug терма.
        }
    }
    //===========================

}
//==============================================================

//=== Виджет в дашборде с последними обновленными AffEgg-товарами.
function egg_updates_dash_func() {
    
    echo '<div>Информация касается всех обновленных AffEgg-товаров (создание товаров и постов здесь не отображается).</div>';
    echo '<div>Выводятся последние 100 записей об обновлении. Карточки товара помечаются отдельным маркером.</div>';
    echo '<div>За один раз AffEgg обновит не более 35 товаров (из обычных ссылок + из ссылок на каталоги), обойдет не более 3 ссылок на каталоги (для обновления и создания новых товаров) и выполнит не более 3 автоблоггингов (для создания новых товаров и постов к ним).</div><br />';
    
    $arr_egg_updates = get_transient('egg_updates');
    if ($arr_egg_updates) {
        echo '<div style="font-size:11px; max-height:600px; overflow:auto; padding:5px; background-color:#FAFAFA;">';
        
        $odd = true;
        foreach ($arr_egg_updates as $item) {
            if ($odd) {
                $odd = false;
                $line_background = "#dbd7d2";
            } else {
                $odd = true;
                $line_background = "#c1caca";
            }
            //echo '<div style="background:'.$line_background.'; padding:0 5px"><strong>'.$item['date'].'</strong> - '.$item['post_title'].' (Post ID: '.$item['post_id'].', Product ID: '.$item['product_id'].')</div>';
            echo '<div style="background:'.$line_background.'; padding:0 5px"><strong>'.$item['date'].'</strong> - Egg ID: '.$item['egg_id'].', Product ID: '.$item['product_id'].(isset($item['post_id']) ? ', Post ID: '.$item['post_id'] : '').(empty($item['note']) ? '' : ' ('.$item['note'].')').'</div>';
        }
        
        echo '</div>';
    } else {
        echo '<div>Пока обновлений товаров нет.</div>';
    }
}





//Добавляем виджет в дашборд.
if (quark_is_affiliateegg_active()) {
	function egg_updates_dash_widget() {
		if (current_user_can('manage_options')) {
			wp_add_dashboard_widget('egg-updates-dash-widget', 'AffEgg обновление товаров', 'egg_updates_dash_func');
		}
	}
	add_action('wp_dashboard_setup', 'egg_updates_dash_widget');
}





//=== Виджет в дашборде с последними обновленными AffEgg-товарами.
function egg_used_dash_func() {

    echo '<div>AffEgg-витрины нигде не используемые. Поиск производится абсолютно по всем элементам Вордпресса (посты, меню, комменты и т.д.).</div>';
    echo '<div>Информация выводится по мере обновления витрин (товаров) плагином.</div>';
    echo '<div>Выводятся последние 500 записей.</div><br />';

    $arr_egg_not_used = get_transient('egg_not_used');
    if ($arr_egg_not_used) {
        echo '<div style="font-size:11px; max-height:600px; overflow:auto; padding:5px; background-color:#FAFAFA;">';
        
        $odd = true;
        foreach ($arr_egg_not_used as $item) {
            if ($odd) {
                $odd = false;
                $line_background = "#dbd7d2";
            } else {
                $odd = true;
                $line_background = "#c1caca";
            }
            echo '<div style="background:'.$line_background.'; padding:0 5px"><strong>'.$item['date'].'</strong> - Egg ID: '.$item['egg_id'].'</div>';
        }
        
        echo '</div>';
    } else {
        echo '<div>Не используемые AffEgg-витрины не найдены.</div>';
    }
}





//Добавляем виджет в дашборд.
if (quark_is_affiliateegg_active()) {
	function egg_used_dash_widget() {
		if (current_user_can('manage_options')) {
			wp_add_dashboard_widget('egg-used-dash-widget', 'Не используемые AffEgg-витрины', 'egg_used_dash_func');
		}
	}
	add_action('wp_dashboard_setup', 'egg_used_dash_widget');
}





//=== Виджет в дашборде с текущими задачами WP-Cron.
function wp_cron_dash_widget_func() {
    
    echo '<div>Задачи отсортированы сверху вниз, начиная от ближайших на исполнение.</div><br />';
    $item['date'] = gmdate('d.m.Y H:i:s', time() + 3*3600); //Время по гринвичу + 3 часа. Более надежный способ получить московское время.

    $cron_jobs = get_option('cron');
    //var_dump($cron_jobs);
    
    if (count($cron_jobs) > 0) {
        echo '<div style="font-size:11px; max-height:600px; overflow:auto; padding:5px; background-color:#FAFAFA;">';
        
        $odd = true;
        foreach ($cron_jobs as $timestamp => $item) {
            if (is_array($item)) {
                foreach ($item as $key => $item2) {
                    if (is_array($item2)) {
                        foreach ($item2 as $value) {
                    
                            if ($odd) {
                                $odd = false;
                                $line_background = "#dbd7d2";
                            } else {
                                $odd = true;
                                $line_background = "#c1caca";
                            }
            
                            echo '<div style="background:'.$line_background.'; padding:0 5px"><strong>'.$key.'</strong> - след. запуск: '.gmdate('d.m.Y H:i:s', $timestamp + 3*3600).' ('.$value['schedule'].')</div>';
                        }
                    }
                }
            }
        }

        echo '</div>';
    }
}

//Добавляем виджет в дашборд.
function wp_cron_dash_widget() {
    if (current_user_can('manage_options')) {
	    wp_add_dashboard_widget('wp-cron-dash-widget', 'Текущие задачи в WP-Cron', 'wp_cron_dash_widget_func');
    }
}
add_action('wp_dashboard_setup', 'wp_cron_dash_widget');
//==============================================================

//=== Тестовый виджет в дашборде.
function test_dash_widget_func() {
    /*
    echo '<div>Текущие задачи в WP-Cron.</div><br />';
    $item['date'] = gmdate('d.m.Y H:i:s', time() + 3*3600); //Время по гринвичу + 3 часа. Более надежный способ получить московское время.

    $cron_jobs = get_option('cron');
    //var_dump($cron_jobs);
    
    if (count($cron_jobs) > 0) {
        echo '<div style="font-size:11px; max-height:600px; overflow:auto; padding:5px; background-color:#FAFAFA;">';
        
        $odd = true;
        foreach ($cron_jobs as $timestamp => $item) {
            if (is_array($item)) {
                foreach ($item as $key => $item2) {
                    if (is_array($item2)) {
                        foreach ($item2 as $value) {
                    
                            if ($odd) {
                                $odd = false;
                                $line_background = "#dbd7d2";
                            } else {
                                $odd = true;
                                $line_background = "#c1caca";
                            }
            
                            echo '<div style="background:'.$line_background.'; padding:0 5px"><strong>'.$key.'</strong> - след. запуск: '.gmdate('d.m.Y H:i:s', $timestamp + 3*3600).' ('.$value['schedule'].')</div>';
                        }
                    }
                }
            }
        }

        echo '</div>';
    }
    */
    
    //echo '<div>AffEgg-витрины не используемые ни в одном посте. Участвуют в поиске посты со статусом Опубликованно и Черновик (в том числе и находящиеся в Корзине).</div>';
    
    /*
    echo '<div>AffEgg-витрины нигде не используемые. Поиск производится абсолютно по всем элементам Вордпресса (посты, меню, комменты и т.д.).</div>';
    echo '<div>Информация выводится по мере обновления витрин (товаров) плагином.</div>';
    echo '<div>Выводятся последние 500 записей.</div><br />';

    $arr_egg_not_used = get_transient('egg_not_used');
    if ($arr_egg_not_used) {
        echo '<div style="font-size:11px; max-height:600px; overflow:auto; padding:5px; background-color:#FAFAFA;">';
        
        $odd = true;
        foreach ($arr_egg_not_used as $item) {
            if ($odd) {
                $odd = false;
                $line_background = "#dbd7d2";
            } else {
                $odd = true;
                $line_background = "#c1caca";
            }
            echo '<div style="background:'.$line_background.'; padding:0 5px"><strong>'.$item['date'].'</strong> - Egg ID: '.$item['egg_id'].'</div>';
        }
        
        echo '</div>';
    } else {
        echo '<div>Не используемые AffEgg-витрины не найдены.</div>';
    }
    */

    /*
    //Код по перезаданию слагов (пермалинков) для части постов. Это было необходимо, когда использовалась wp_publish_post, которая у черновиков не создавала пермалинки при публикации.
    $posts = get_posts( array (  'numberposts' => -1 ) );
    foreach ( $posts as $post ) {
        $bo_post_type = bo_get_field($post->ID, 'bo_cf_post_type', '');
        if ($bo_post_type == 'product-card') {
            $new_slug = sanitize_title( $post->post_title );
            if ( $post->post_name != $new_slug ) {
                wp_update_post(
                    array (
                        'ID'        => $post->ID,
                        'post_name' => $new_slug
                    )
                );
            }
        }
    }
    */
    
}

//Добавляем виджет в дашборд.
function test_dash_widget() {
    if (current_user_can('manage_options')) {
	    wp_add_dashboard_widget('test-dash-widget', 'Тестовые выводы', 'test_dash_widget_func');
    }
}
add_action('wp_dashboard_setup', 'test_dash_widget');
//==============================================================

//=== Тестовый виджет в дашборде.
function test_dash_widget_2_func() {
    /*
    global $wpdb;
    $numposts = $wpdb->get_var("SELECT count(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status IN ('publish','future','draft','pending','private') AND (post_content LIKE '%[affegg%id=42]%' OR post_content LIKE '%[affegg%id=42 %]%')");
    
    echo '<div>'.$numposts.'</div>';
    */
    
    echo '<div>Последний раз LESS компилировался: '.get_option('lessify_last_recompilation', 'никогда').'</div>';
}

//Добавляем виджет в дашборд.
function test_dash_widget_2() {
    if (current_user_can('manage_options')) {
	    wp_add_dashboard_widget('test-dash-widget-2', 'Тестовые выводы 2', 'test_dash_widget_2_func');
    }
}
add_action('wp_dashboard_setup', 'test_dash_widget_2');
//==============================================================

//=== Добавляем аргументы в основной запрос WP_Query (Main Loop).
//=== $query уже передается по ссылке, поэтому здесь экшен.
function cat_main_query_mod($query) {

    if ($query->is_main_query() && !is_admin() && $query->is_category()) {
        $current_cat_slug = $query->query_vars['category_name'];
        $current_cat_id = get_term_by('slug', $current_cat_slug, 'category')->term_id;
        $current_cat_template_id = of_get_option('bo_cat_'.$current_cat_id.'_template', '1');
        
        $current_cat_type_id = of_get_option('bo_cat_'.$current_cat_id.'_type', 'usual');
        $current_cat_template_postsann_count = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_count', get_option('posts_per_page'));
        //$current_cat_template_postsann_sort = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_sort', '');
        
        //Если тип категории - Категория под скидки, то выводит только определенные посты.
        if ($current_cat_type_id == 'discount') {
            
            /*
            if (current_user_can('manage_options')) {
                //var_dump($query);
                //echo get_option('posts_per_page');
                //echo 'id: '.$current_cat_template_id;
                //echo 'per_page: '.$current_cat_template_postsann_count;
            }
            */

            //Доп фильтры по meta_query (произвольным полям) крайне сильно замедляют выполнение запросов, поэтому ниже используется
            //фильтрация через свою таксономию (технические метки).
            //Какие-то решения есть на
            //http://stackoverflow.com/questions/23331762/wordpress-custom-meta-query-search-is-so-slow-when-in-or-relation
            //http://stackoverflow.com/questions/26991317/very-slow-query-on-wordpress-caused-by-meta-query
            //http://wordpress.stackexchange.com/questions/158898/meta-query-terribly-slow
            /*
            $post_filter_by_cf = array(
                'relation' => 'OR',
                array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'bo_cf_post_type',
                        'value'   => 'product-card',
                        'compare' => '=',
                    ),
                    array(
                        'key'     => 'bo_cf_product_price_disc',
                        'value'   => 39,
                        'compare' => '>',
                    ),
                    array(
                        'key'     => 'bo_cf_product_in_stock',
                        'value'   => 0,
                        'compare' => '>',
                    ),
                    array(
                        'key'     => 'bo_cf_product_egg_id',
                        'value'   => '',
                        'compare' => '!=',
                    ),
                ),
                array(
                    array(
                        'key'     => 'bo_cf_post_type',
                        'value'   => 'discount',
                        'compare' => '=',
                    ),
                ),
            );
            $query->set('meta_query', $post_filter_by_cf);
            */
            
            //Отбираем посты только с целевой меткой (есть на стоке и с нужной велечиной скидки).
            $post_filter_by_tech_tags = array(
                array(
        			'taxonomy' => 'tech-tags',
        			'field'    => 'slug',
        			'terms'    => array('disc_ot_40'),
        			'operator' => 'IN',
        		),
            );
            $query->set('tax_query', $post_filter_by_tech_tags);

            //Сортировка по убыванию скидки.
            $query->set('orderby', 'meta_value_num'); //meta_value_num для числовых значений и meta_value для не числовых.
            $query->set('meta_key', 'bo_cf_product_price_disc');
            $query->set('order', 'DESC');
        }

        //Кол-во постов на страницу.
        $query->set('posts_per_page', $current_cat_template_postsann_count);
    }
    
    return;
}
add_action('pre_get_posts', 'cat_main_query_mod', 1);
//==============================================================

//=== Создадим свою таксономию - технические метки - для обычных типов постов. Эта таксономия не иерархичная, а обычная меточная.
//=== http://codex.wordpress.org/Function_Reference/register_taxonomy
function create_tax_tech_tags() {

	$labels = array(
		'name'                       => 'Технические метки',
		'singular_name'              => 'Техническая метка',
		'search_items'               => 'Поиск тех. меток',
		'popular_items'              => 'Популярные тех. метки',
		'all_items'                  => 'Все тех. метки',
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => 'Редактировать тех. метку',
		'update_item'                => 'Обновить тех. метку',
		'add_new_item'               => 'Добавить новую тех. метку',
		'new_item_name'              => 'Имя новой тех. метки',
		'separate_items_with_commas' => 'Метки разделяются запятыми',
		'add_or_remove_items'        => 'Добавить или удалить тех. метку',
		'choose_from_most_used'      => 'Выбрать из часто используемых меток',
		'not_found'                  => 'Метки не найдены',
		'menu_name'                  => 'Технические метки',
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_in_nav_menus'     => false, //Не показывать для выбора в навигационном меню.
		'show_tagcloud'         => false, //Не создавить виджет Облако меток.
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array('slug' => 'ttag'),
	);

	register_taxonomy('tech-tags', 'post', $args);
}
add_action('init', 'create_tax_tech_tags', 0);
//==============================================================

//=== Создадим свою таксономию - поисковые слова - для обычных типов постов. Эта таксономия не иерархичная, а обычная меточная.
//=== http://codex.wordpress.org/Function_Reference/register_taxonomy
function create_tax_search_words() {

	$labels = array(
		'name'                       => 'Поисковые слова',
		'singular_name'              => 'Поисковое слово',
		'search_items'               => 'Поиск поисковых слов',
		'popular_items'              => 'Популярные поисковые слова',
		'all_items'                  => 'Все поисковые слова',
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => 'Редактировать поисковое слово',
		'update_item'                => 'Обновить поисковое слово',
		'add_new_item'               => 'Добавить новое поисковое слово',
		'new_item_name'              => 'Имя нового поискового слова',
		'separate_items_with_commas' => 'Слова разделяются запятыми',
		'add_or_remove_items'        => 'Добавить или удалить поисковое слово',
		'choose_from_most_used'      => 'Выбрать из часто используемых слов',
		'not_found'                  => 'Слова не найдены',
		'menu_name'                  => 'Поисковые слова',
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_in_nav_menus'     => false, //Не показывать для выбора в навигационном меню.
		'show_tagcloud'         => false, //Не создавить виджет Облако меток.
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array('slug' => 'sword'),
	);

	register_taxonomy('search-words', 'post', $args);
}
add_action('init', 'create_tax_search_words', 0);
//==============================================================

//=== Добавляем справку в верхную вкладку Помощь на странице Внешний вид->Меню.
function bo_page_menu_add_help_tab () {
    $screen = get_current_screen();
    $screen->add_help_tab(array(
        'id'	    => 'bo_help_tab',
        'title'	    => 'Стилизация элементов меню',
        'content'	=> '<p>Класс <strong>caption</strong> позволит стилизовать элемент меню как статичный текст, а не как активную ссылку.</p>
                        <p>Класс <strong>right-margin-10</strong> задаст для элемента правый margin в 10px. Также доступные <strong>right-margin-20</strong> и <strong>right-margin-30</strong>.</p>
                        <p></p>'
    ));
}
add_action('load-nav-menus.php', 'bo_page_menu_add_help_tab');
//==============================================================

//=== Произвольные поля для элементов навигационного меню.
require_once dirname(__FILE__).'/menu-item-cf/menu-item-custom-fields.php';
require_once dirname(__FILE__).'/menu-item-cf/menu-item-custom-fields-example.php'; //Здесь (в функции init()) задаются произвольные поля для элементов меню.

//Walker для всех навигационных меню.
class BO_Walker_Nav_Menu extends Walker_Nav_Menu {

    //Функция вызывается при выводе каждого элемента.
    //Элемент меню ($item) хранится как объект WP_Post и, соответственно, предоставляет доступ к своим произвольным полям как в обычных постах.
    //Основное содержимое для start_el взято с https://codex.wordpress.org/Function_Reference/wp_nav_menu
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        global $wp_query;
        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
        
        // depth dependent classes
        $depth_classes = array(
            ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
            ( $depth >= 2 ? 'sub-sub-menu-item' : '' ),
            ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
            'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
        
        // passed classes
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
        
        // build html
        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';
        
        $cf_display_caption = get_post_meta($item->ID, 'menu-item-display-caption', true);
        $cf_image_left = get_post_meta($item->ID, 'menu-item-image-left', true);
        $cf_image_right = get_post_meta($item->ID, 'menu-item-image-right', true);
        $cf_image_style = get_post_meta($item->ID, 'menu-item-image-style', true);
        $cf_ym_target_id = get_post_meta($item->ID, 'menu-item-ym-target-id', true);
        
        // link attributes
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';
        
        //Задаем или нет идентификатор события для Яндекс.Метрики.
        $ym_counter_id = sanitize_text_field(of_get_option('bo_yandex_metrica_id', ''));
        if (!empty($ym_counter_id)) {
            $attributes .= !empty($cf_ym_target_id) ? ' onclick="yaCounter'.$ym_counter_id.'.reachGoal(\''.$cf_ym_target_id.'\'); return true;"' : '';
        }
        
        $item_output = sprintf('%1$s<a%2$s>%3$s%4$s%5$s%6$s%7$s</a>%8$s',
            $args->before,
            $attributes,
            $args->link_before,
            !empty($cf_image_left) ? '<img src="'.$cf_image_left.'"'.(!empty($cf_image_style) ? ' style="'.$cf_image_style.'"' : '').'>' : '',
            ($cf_display_caption == '-' ? '' : (empty($cf_display_caption) ? $item->title : $cf_display_caption)),
            !empty($cf_image_right) ? '<img src="'.$cf_image_right.'"'.(!empty($cf_image_style) ? ' style="'.$cf_image_style.'"' : '').'>' : '',
            $args->link_after,
            $args->after
        );
        
        // build html
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}
//==============================================================

//=== Добавляем свой walker к каждому (в теме или из виджетов) навигационному меню.
function modify_nav_menu_args($args) {
    return array_merge($args, array('walker' => new BO_Walker_Nav_Menu()));
}
add_filter('wp_nav_menu_args', 'modify_nav_menu_args');
//==============================================================

//=== Вывод OVER-маркеров.
//=== Это дивы которые выводятся поверх родительского блока с классом has-overblocks.
//=== Если has-overblocks отсутствует, то накрывание не произойдет.
function the_overmarkers($post_id, $marker_type = '', $output = '', $pos = 'v-top h-center', $addit_class = '') {

    if (empty($post_id) || empty($marker_type) || empty($output)) {
        return;
    }
    
    if ($marker_type == 'post_attached_images') {

        $post_images = get_children(
            array(
                'post_parent' => $post_id,
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'numberposts' => 999
            )
        );
        if ($post_images) {
            $post_images_count = count($post_images);
            if ($post_images_count > 1) {
                echo '<div class="overblock '.$pos.' '.$addit_class.'">'.sprintf($output, $post_images_count-1).'</div>';
            }
        }
        
    } elseif ($marker_type == 'post_pub_date') {
        echo '<div class="overblock '.$pos.' '.$addit_class.'">'.sprintf($output, get_the_time('d M', $post_id)).'</div>';

    } elseif ($marker_type == 'post_egg_products') {
           
    }

    return;
}
//==============================================================

//=== Урлдекодируем партнерскую ссылку для вывода в AffEgg-элементе.
function affegg_urldecode($url) {

    if (empty($url)) {
        return;
    }

    //Признаки урлов которые необходимо не урлкодировать.
    $affegg_urldecode = explode("\r\n", of_get_option('bo_affegg_urldecode', ''));
    foreach ($affegg_urldecode as $item) {

        $pos = stripos($url, $item);
        if ($pos !== false) {
            return urldecode($url);
        }
    }
    
    return esc_url($url);
}
//==============================================================

//=== Первую букву строки в верхний регистр.
function bo_ucfirst($text) {
	//http://hashcode.ru/questions/105908/php-%D0%BF%D0%BE%D1%87%D0%B5%D0%BC%D1%83-ucfirst-%D0%BD%D0%B5-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%B0%D0%B5%D1%82-%D0%B4%D0%BB%D1%8F-%D1%80%D1%83%D1%81%D1%81%D0%BA%D0%B8%D1%85-%D1%81%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB%D0%BE%D0%B2
    return mb_strtoupper(mb_substr($text, 0, 1)).mb_substr($text, 1);
}
//==============================================================