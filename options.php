<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * http://wptheming.com/options-framework-theme/
 */

function optionsframework_option_name() {
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace( "/\W/", "_", strtolower( $themename ) );
	return $themename;
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'quark'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 * 
 * http://wptheming.com/options-framework-theme/
 * 
 * Валидатор WP_KSES
 * http://wp-kama.ru/function/wp_kses
 * http://codex.wordpress.org/Function_Reference/wp_kses
 * 
 * 
 */

function optionsframework_options() {

	// If using image radio buttons, define a directory path
	$imagepath = trailingslashit(get_template_directory_uri()).'images/';

	// Background Defaults
	$background_defaults = array(
		'color' => '#dedede',
		//'image' => $imagepath . 'dark-noise.jpg',
		'repeat' => 'repeat',
		'position' => 'top left',
		'attachment'=>'scroll' );

	// Editor settings
	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);
	
	//=== Шрифты для использования.
    //Системные шрифты.
    
    /*
    //Есть проблемы с парсингом шрифтов в lessphp, поэтому передаем только по одному шрифту и строго в кавычках.
	$os_fonts = array(
		'"Arial"' => 'Arial',
		'"Cambria"' => 'Cambria',
		'"Times New Roman"' => 'Times New Roman',
		'"Georgia"' => 'Georgia',
		'"Helvetica"' => 'Helvetica',
		'"Tahoma"' => 'Tahoma',
		'"Verdana"' => 'Verdana'
	);
	*/
	
	$os_fonts = array(
		'Arial, sans-serif' => 'Arial',
		'Cambria, Georgia, serif' => 'Cambria',
		'Garamond, "Hoefler Text", Times New Roman, Times, serif' => 'Garamond (Times New Roman)',
		'Georgia, serif' => 'Georgia',
		'Helvetica, sans-serif' => 'Helvetica',
		'Tahoma, sans-serif' => 'Tahoma',
		'Verdana' => 'Verdana',
	);
    
    //Подключенные Google шрифты в дополнение к системным шрифтам.
    $typography_fonts = array_merge($os_fonts, bo_google_fonts_normalize(true));

    //Normal и Bold для шрифтов.
	$typography_fonts_weight = array(
		'normal' => 'Normal',
		'bold' => 'Bold',
	);
    //==============================================================

	//Форматы и размеры изображений из настроек Вордпресс.
	$settings_image_sizes = get_image_sizes(); //http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
    $image_sizes = array();
    foreach($settings_image_sizes as $key=>$item) {
        $image_sizes[$key] = $key.' '.$item['width'].'x'.$item['height'].($item['crop']? esc_html__(' (кроп по центру)'):esc_html__(''));
    };
    
    //Тип категорий. Данный массив загружается в качестве доступных типов категорий.
    //Добавление/изменение категорий в этом массиве должно сопровождаться корректировками php файлов шаблона.
    //Корректировать код опций Theme Options не требуется.
    $cat_types = array(
        'usual' => 'Обычная категория',
		'discount' => 'Категория под скидки',
    );
    
    //Шаблоны категорий. Данный массив загружается в качестве доступных для выбора шаблонов категорий.
    //Добавление/изменение шаблонов в этом массиве должно сопровождаться корректировками php файлов шаблона, 
    //функции set_lessify_vars в functions.php и style.less
    //Корректировать код опций Theme Options не требуется.
    $cat_templates = array(
        '1' => 'Шаблон 1',
		'2' => 'Шаблон 2',
		'3' => 'Шаблон 3',
		'4' => 'Шаблон 4',
		'5' => 'Шаблон 5',
		'6' => 'Шаблон 6',
    );
    
    //Доступные сайдбары (т.е. только боковые панели), блоки под постами.
    global $wp_registered_sidebars;
    $active_sidebars = array();
    $active_underpostblocks = array();
    
    $active_sidebars['none'] = 'Отсутствует';
    $active_underpostblocks['none'] = 'Отсутствует';
    
    foreach ($wp_registered_sidebars as $item) {
        if ($item) {
            if (stripos($item['id'], 'sidebar-') !== false) {
                $active_sidebars[$item['id']] = $item['name'];
            } elseif (stripos($item['id'], 'underpostblock-') !== false) {
                $active_underpostblocks[$item['id']] = $item['name'];
            }
        }   
    };
    
    //Типы постов. Также используются в Piklist.
    global $post_types;
    $post_types = array(
        'post' => 'Обычная запись',
        'news' => 'Новость',
		'look' => 'Образ',
		'discount' => 'Скидка',
		'product-card' => 'Карточка товара',
		'food-recipe' => 'Рецепт блюда',
    );
    
    //Виды представления постов на странице. Реализованы на Isotope.
    $elements_view = array(
        'list' => 'List',
        'grid grid-2cols' => 'Grid (2 колонки)',
        'grid grid-3cols' => 'Grid (3 колонки)',
        'grid grid-4cols' => 'Grid (4 колонки)',
        'grid grid-5cols' => 'Grid (5 колонки)',
		'masonry masonry-2cols' => 'Masonry (2 колонки)',
		'masonry masonry-3cols' => 'Masonry (3 колонки)',
		'masonry masonry-4cols' => 'Masonry (4 колонки)',
		'masonry masonry-5cols' => 'Masonry (5 колонки)',
    );
    
    //Доступные билдеры блоков для статических страниц и категорий.
    $blocks_builder = array(
        'bb1' => 'Билдер блоков 1',
        'bb2' => 'Билдер блоков 2',
        //'bb3' => 'Билдер блоков 3',
        //'bb4' => 'Билдер блоков 4',
        //'bb5' => 'Билдер блоков 5',
    );    

	
	

	
	$options = array();

	//=== Базовые
	$options[] = array(
		'name' => 'Базовые',
		'type' => 'heading'
	);
	
	$options[] = array(
		'name' => 'Favicon',
		'desc' => 'В случае не выбора favicon`ки в ее качестве будет использован установленый логотип.',
		'id' => 'bo_favicon',
		'std' => '',
		'type' => 'upload',
	);

	$options[] = array(
		'name' => 'Основной фон',
		'desc' => 'Цвет и/или изображение/паттерн.',
		'id' => 'bo_body_background',
		'std' => $background_defaults,
		'type' => 'background', //Для типа background задавать css_prop не нужно.
	);
	
	//404
	$options[] = array(
    	'name' => '============ Страница - 404 ============',
    	'desc' => 
            wp_kses('', 
    		    array(
        			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
        				'href' => array(),
        				'target' => array(),
        			),
    			)
    		),
    	'type' => 'info'
	);
	$options[] = array(
		'name' => 'Заголовок',
		'desc' => '',
		'id' => 'bo_404_header',
		'std' => '404',
		'type' => 'text',
	);
	$options[] = array(
		'name' => 'Сообщение',
		'desc' => '',
		'id' => 'bo_404_text',
		'std' => 'Страницы не существует',
		'type' => 'text',
	);
	
	//Ничего не найдено при поиске
	$options[] = array(
    	'name' => '============ Страница - Поиск не дал результатов  ============',
    	'desc' => 
            wp_kses('', 
    		    array(
        			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
        				'href' => array(),
        				'target' => array(),
        			),
    			)
    		),
    	'type' => 'info'
	);
	$options[] = array(
		'name' => 'Заголовок',
		'desc' => '',
		'id' => 'bo_notfound_header',
		'std' => 'Ничего не найдено',
		'type' => 'text',
	);
	$options[] = array(
		'name' => 'Сообщение',
		'desc' => '',
		'id' => 'bo_notfound_text',
		'std' => 'Попробуйте поискать что-нибудь ещё.',
		'type' => 'text',
	);

	
	
	
	
	//=== Шрифты
	$options[] = array(
		'name' => esc_html__( 'Шрифты', 'quark' ),
		'type' => 'heading'
	);
	
	$options[] = array(
	'name' => esc_html__( '============ Подключить Google шрифты ============', 'quark' ),
	'desc' => 
        wp_kses('Помимо стандартных системных шрифтов, будут доступны для выбора подключенные <a href="https://www.google.com/fonts" target="_blank">Google шрифты</a>.', 
		    array(
    			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
    				'href' => array(),
    				'target' => array(),
    			),
			)
		),
	'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__( 'Google шрифты', 'quark' ),
		'desc' => esc_html__( '', 'quark' ),
		'id' => 'bo_fonts_google',
		/*
		'std' => array(
            'Open+Sans' => '1',
            'Open+Sans+Condensed' => '0'
        ),
        */
		'std' => '',
		'type' => 'multicheck',
		'options' => array(
            'open_sans' => esc_html__( 'Open Sans', 'quark' ),
            'open_sans_condensed' => esc_html__( 'Open Sans Condensed', 'quark' ),
            'roboto' => esc_html__( 'Roboto', 'quark' ),
            'roboto_condensed' => esc_html__( 'Roboto Condensed', 'quark' ),
            'roboto_slab' => esc_html__( 'Roboto Slab', 'quark' ),
            'pt_sans' => esc_html__( 'PT Sans', 'quark' ),
            'pt_sans_narrow' => esc_html__( 'PT Sans Narrow', 'quark' ),
            'lora' => esc_html__( 'Lora', 'quark' ),
            'lobster' => esc_html__( 'Lobster', 'quark' ),
            'ubuntu_condensed' => esc_html__( 'Ubuntu Condensed', 'quark' ),
            'ubuntu_mono' => esc_html__( 'Ubuntu Mono', 'quark' ),
            'playfair_display' => esc_html__( 'Playfair Display', 'quark' ),
            'play' => esc_html__( 'Play', 'quark' ),
            'poiret_one' => esc_html__( 'Poiret One', 'quark' ),
            'cuprum' => esc_html__( 'Cuprum', 'quark' ),
            'exo_2' => esc_html__( 'Exo 2', 'quark' ),
            'fira_sans' => esc_html__( 'Fira Sans', 'quark' ),
            'tinos' => esc_html__( 'Tinos', 'quark' ),
            'eb_garamond' => esc_html__( 'EB Garamond', 'quark' ),
            'comfortaa' => esc_html__( 'Comfortaa', 'quark' ),
            'philosopher' => esc_html__( 'Philosopher', 'quark' ),
            'jura' => esc_html__( 'Jura', 'quark' ),
            'marck_script' => esc_html__( 'Marck Script', 'quark' ),
            'oranienbaum' => esc_html__( 'Oranienbaum', 'quark' ),
            'marmelad' => esc_html__( 'Marmelad', 'quark' ),
            'kelly_slab' => esc_html__( 'Kelly Slab', 'quark' ),
            'prosto_one' => esc_html__( 'Prosto One', 'quark' )
        ),
    );
    
	
	

	
	//=== Шапка
	$options[] = array(
		'name' => 'Шапка',
		'type' => 'heading'
	);

	$options[] = array(
		'name' => '============ Показывать верхний бар ============',
		'desc' => 'Это самый верхний ряд, т.е. прямо под адресной строкой браузера.',
		'id' => 'bo_topbar',
		'std' => '0',
		'class' => 'has-hidden admin-first-in-block',
		'type' => 'checkbox'
	);
	
    	$options[] = array(
    		'name' => 'Фон',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => 'bo_topbar_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    	);





	$options[] = array(
		'name' => '============ Показывать верхний бар 2 ============',
		'desc' => 'Это ряд сразу же под верхним (первым) баром.',
		'id' => 'bo_topbar2',
		'std' => '0',
		'class' => 'has-hidden admin-first-in-block',
		'type' => 'checkbox'
	);
	
    	$options[] = array(
    		'name' => 'Фон',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => 'bo_topbar2_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    	);




    	
	$options[] = array(
		'name' => '============ Показывать верхнюю панель ============',
		'desc' => 'Это ряд над верхним меню (под вторым верхним баром).',
		'id' => 'bo_toppanel',
		'std' => '0',
		'class' => 'has-hidden admin-first-in-block',
		'type' => 'checkbox'
	);
		$options[] = array(
			'name' => 'Логотип',
			'desc' => '',
			'id' => 'bo_toppanel_logo_url',
			'std' => '',
			'class' => 'start-hidden',
			'type' => 'upload'
		);
    	$options[] = array(
    		'name' => 'Фон',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => 'bo_toppanel_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    	    'css_selector' => '#toppanel', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	/*
    	$options[] = array(
    		'name' => 'Скрывать для мобильных устройств',
    		'desc' => 'Панель будет полностью скрыта при ширине экрана менее 900px.',
    		'id' => 'bo_toppanel_mobile_hide',
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny start-hidden', //mini, tiny, small
    		'options' => array(
        		'none' => 'Да',
        		'block' => 'Нет',
    	    ),
    	    'css_selector' => '#toppanel', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	    'css_prop' => 'display', //Наличие css_prop является признаком объявления переменной для LESS.
        );
        */
        foreach (array(1,2,3) as $block_inc) {
			
        	$options[] = array(
        		'name' => 'Блок '.$block_inc,
        		'desc' => '',
        		'id' => 'bo_toppanel_block'.$block_inc,
        		'std' => 'none',
        		'type' => 'select',
        		'class' => 'tiny start-hidden has-hidden-select', //mini, tiny, small
        		'options' => array(
            		'none' => 'Отсутствует',
            		'logo' => 'Логотип',
            		/*'social_groups' => 'Иконки соц. групп',*/
            		/*'search' => 'Поиск',*/
        	    ),
            );
            
            	$options[] = array(
            		'name' => 'Блок '.$block_inc.' - грид',
            		'desc' => '',
            		'id' => 'bo_toppanel_block'.$block_inc.'_grid',
            		'std' => 'grid_4_of_12',
            		'type' => 'select',
            		'class' => 'tiny start-hidden-select bo_toppanel_block'.$block_inc.' notshow-none', //mini, tiny, small
            		'options' => array(
            		    'grid_4_of_12' => '4 из 12',
            		    'grid_8_of_12' => '8 из 12',
                		'grid_12_of_12' => '12 из 12',
            	    ),
                );
                
            	$options[] = array(
            		'name' => 'Блок '.$block_inc.' - выравнивание содержимого',
            		'desc' => '',
            		'id' => 'bo_toppanel_block'.$block_inc.'_align',
            		'std' => 'left',
            		'type' => 'select',
            		'class' => 'tiny start-hidden-select bo_toppanel_block'.$block_inc.' notshow-none', //mini, tiny, small
            		'options' => array(
            		    'left' => 'По левому краю',
            		    'center' => 'По центру',
                		'right' => 'По правому краю',
            	    ),
            	    'css_prop' => 'text-align', //Наличие css_prop является признаком объявления переменной для LESS.
            	    'css_selector' => '#toppanel .block'.$block_inc, //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
                );
        }
        //CSS-правила
    	$options[] = array(
    		'name' => 'CSS-правила',
    		'desc' => '<b>Верхняя панель</b>: #toppanel<br>'.
    		            '<br>'.
    		            'Селектор на <b>блок со всем контентом</b> в верхней панели: + .site-header<br>'.
                        'Селектор на <b>блок с логотипом</b>: + .site-header .logo'.'<br>'.
                        '<br>'.
                        'Селектор на <b>блок 1</b> и т.п.: + .site-header .block1'.'<br>'.
                        '<br>'
                        ,
    		'id' => 'bo_toppanel_css',
    		'std' => '',
    		'class' => 'maxi start-hidden',
    		'type' => 'textarea',
    		'css_selector' => 'rules', //rules - значит в dynamic.less будет записано содержимое поля в качестве полных CSS-правил.
    	);





	$options[] = array(
		'name' => '============ Верхнее меню ============',
		'desc' => '',
		'type' => 'info',
		'class' => 'admin-first-in-block',
	);
    	$options[] = array(
    		'name' => 'Фон',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => 'bo_topmenu_background',
    		'std' => $background_defaults,
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    	);
    	$options[] = array(
    		'name' => esc_html__('Закрепить', 'quark'),
    		'desc' => esc_html__('Верхнее меню будет закреплено сверху и будет всегда видно при прокрутке страницы.', 'quark'),
    		'id' => 'bo_topmenu_fixed',
    		'std' => '0',
    		'type' => 'checkbox'
    	);
    	$options[] = array(
    		'name' => 'Выравнивание элементов',
    		'desc' => '',
    		'id' => 'bo_topmenu_align',
    		'std' => 'left',
    		'type' => 'select',
    		'options' => array(
    		    'left' => 'По левому краю',
    		    'center' => 'По центру',
        		'right' => 'По правому краю',
    	    ),
        );
    	$options[] = array(
    		'name' => 'Показывать минилоготип',
    		'desc' => 'Мини логотип в блоке верхнего меню только в мобильных вариантах страниц.',
    		'id' => 'bo_topmenu_minilogo',
    		'std' => '1',
    		'class' => 'has-hidden',
    		'type' => 'checkbox'
    	);
        	/*
        	$options[] = array(
        		'name' => 'Показывать в мобильном варианте',
        		'desc' => 'Минилоготип будет показываться в мобильном варианте страниц.',
        		'id' => 'bo_topmenu_minilogo_mobile',
        		'std' => '1',
        		'type' => 'checkbox',
        		'class' => 'start-hidden',
        	);
        	$options[] = array(
        		'name' => 'Показывать при скроллинге',
        		'desc' => 'Минилоготип будет показываться при скролинге страницы. Доп условие - верхнее меню должно быть закреплено сверху.',
        		'id' => 'bo_topmenu_minilogo_scroll',
        		'std' => '1',
        		'type' => 'checkbox',
        		'class' => 'start-hidden',
        	);
	        */

        $menus = get_registered_nav_menus();
        
        foreach ($menus as $key => $name) {
        	
            if (stripos($key, 'top-') !== false) {
            	
            	$options[] = array(
            		'name' => '============ Меню - '.$name.' ============',
            		'desc' => '',
            		'type' => 'info'
            	);
            	$options[] = array(
            		'name' => 'Margin',
            		'desc' => '',
            		'id' => 'bo_topmenu_'.$key.'_margin',
            		'std' => '0px',
            		'type' => 'text',
            		'css_prop' => 'margin', //Наличие css_prop является признаком объявления переменной для LESS.
            		'css_selector' => '#topmenu .menu-'.$key.'-container', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            	);
            	$options[] = array(
            		'name' => 'Шрифт',
            		'desc' => '',
            		'id' => 'bo_topmenu_'.$key.'_font',
            		'std' => array( 'size' => '16px', 'face' => 'Georgia, serif', 'color' => '#666666'),
            		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
                	'options' => array(
                		'faces' => $typography_fonts,
                		'styles' => $typography_fonts_weight,
                	),
                	'css_selector' => '#topmenu .menu-'.$key.'-container ul li, #topmenu .menu-'.$key.'-container ul li a', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            	);
            	
            	/*
            	$options[] = array(
            		'name' => 'Цвет нижнего бордера элемента',
            		'desc' => 'Если у элементов меню на странице Внешний вид->Меню задан класс bottom-border, то будет выведен нижний бордер с указанным здесь цветом.',
            		'id' => 'bo_topmenu_'.$key.'_border_bottom_color',
            		'std' => 'inherit',
            		'type' => 'color',
            		'css_prop' => 'border-color', //Наличие css_prop является признаком объявления переменной для LESS.
            		'css_selector' => '#topmenu .menu-'.$key.'-container li.bottom-border.menu-item', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            	);
            	*/
            	
            	/*
            	$menu_items = wp_get_nav_menu_items($key);
            	foreach ((array)$menu_items as $key2 => $menu_item) {
            	    //var_dump($menu_item);
                	$options[] = array(
                		'name' => 'Элемент меню - '.$menu_item->title,
                		'desc' => '',
                		'type' => 'info'
                	);
                	
                	$options[] = array(
                		'name' => 'Картинка слева',
                		'desc' => '',
                		'id' => 'bo_topmenu_'.$key.'_'.$menu_item->ID.'_image_left',
                		'std' => '',
                		'type' => 'upload'
                	);
            	}
            	*/
            }
        }
	    //CSS-правила
		$options[] = array(
			'name' => 'CSS-правила',
			'desc' => '<b>Верхнее меню</b>: #topmenu<br>'.
			            '<br>'.
			            'Селектор на <b>минилоготип в меню</b>: + .logo img<br>'.
	                    '<br>'
	                    ,
			'id' => 'bo_topmenu_css',
			'std' => '',
			'class' => 'maxi start-hidden',
			'type' => 'textarea',
			'css_selector' => 'rules', //rules - значит в dynamic.less будет записано содержимое поля в качестве полных CSS-правил.
		);





	//=== Категории постов
	$options[] = array(
		'name' => esc_html__( 'Категории', 'quark' ),
		'type' => 'heading'
	);

    $cat_active_templates = array();
    foreach ($cat_templates as $key => $value) {
        if ($key == 1) { //Шаблон-1 всегда доступен для категорий.
            $cat_active_templates[$key] = $value;
        } elseif (of_get_option('bo_cat_template_'.$key, '')) {
            $cat_active_templates[$key] = $value;
        }
    }
    $options = category_templates($cat_active_templates, $options, 0, $cat_types); //0 for all categories; or cat ID  

    /*
	$options[] = array(
		'name' => esc_html__( '============ AJAX-подгрузка постов ============', 'quark' ),
		'desc' => 
	        wp_kses(__( '', 'quark'), 
    		    array(
        			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
        				'href' => array(),
        				'target' => array(),
        			),
    			)
    		),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Включить AJAX-подгрузку', 'quark'),
		'desc' => esc_html__('Стандартная пагинация заменяется одной кнопкой с AJAX-подгрузкой постов.', 'quark'),
		'id' => 'bo_cat_posts_ajax_load_more',
		'std' => '1',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__( 'Надпись на кнопке (состояние 1)', 'quark' ),
		'desc' => esc_html__( 'Состояние 1 - есть еще посты для подгрузки.', 'quark' ),
		'id' => 'bo_cat_posts_ajax_load_more_but_caption_more',
		'std' => 'Показать еще',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__( 'Надпись на кнопке (состояние 2)', 'quark' ),
		'desc' => esc_html__( 'Состояние 2 - посты подгружаются.', 'quark' ),
		'id' => 'bo_cat_posts_ajax_load_more_but_caption_loading',
		'std' => 'Загрузка постов...',
		'type' => 'text'
	);	
	
	$options[] = array(
		'name' => esc_html__( 'Надпись на кнопке (состояние 3)', 'quark' ),
		'desc' => esc_html__( 'Состояние 3 - больше нет постов для подгрузки.', 'quark' ),
		'id' => 'bo_cat_posts_ajax_load_more_but_caption_nomore',
		'std' => 'Больше нет',
		'type' => 'text'
	);
	*/
    //==============================================================
    
	//=== Шаблоны категорий
	$options[] = array(
		'name' => esc_html__( 'Шаблоны категорий', 'quark' ),
		'type' => 'heading'
	);

	foreach ($cat_templates as $key => $value) {
	    $cat_template_name = 'bo_cat_template_'.$key;
	    
    	$options[] = array(
    		'name' => 'Шаблон '.$key,
    		'desc' => '',
    		'type' => 'info',
    		'class' => 'admin-block-header admin-first-in-block',
    	);
    	
    	if ($key != 1) {
        	$options[] = array(
        		'name' => 'Включить шаблон',
        		'desc' => 'После включения данный шаблон будет доступен для выбора в качестве шаблона категории.',
        		'id' => $cat_template_name,
        		'std' => '0', //
        		'class' => 'has-hidden',
        		'type' => 'checkbox'
        	);
    	}
	    
    	$options[] = array(
    		'name' => 'Фон категории',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $cat_template_name.'_backg',
    		'std' => $background_defaults,
    		'class' => 'start-hidden',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    		'css_selector' => 'body.category.template-'.$key, //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	
    	//Сайдбары для категорий.
    	$options[] = array(
    		'name' => 'Сайдбар',
    		'desc' => '',
    		'id' => $cat_template_name.'_sidebar',
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny start-hidden has-hidden-select', //mini, tiny, small
    		'options' => $active_sidebars,
        );
        	$options[] = array(
        		'name' => 'Позиция сайдбара',
        		'desc' => '',
        		'id' => $cat_template_name.'_sidebar_pos',
        		'std' => 'left',
        		'type' => 'select',
        		'class' => 'mini start-hidden start-hidden-select '.$cat_template_name.'_sidebar notshow-none', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
        		'options' => array(
            		'left' => 'Слева',
            		'right' => 'Справа',
        	    ),
            );
        	$options[] = array(
        		'name' => 'Шаблон сайдбара',
        		'desc' => '',
        		'id' => $cat_template_name.'_sidebar_template',
        		'std' => 'simple',
        		'type' => 'select',
        		'class' => 'mini start-hidden start-hidden-select '.$cat_template_name.'_sidebar notshow-none', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
        		'options' => array(
            		'simple' => 'Простой',
        	    ),
            );
            
        /*
        //Блоки в начале.
    	$options[] = array(
    		'name' => 'Блок 1 в начале',
    		'desc' => '',
    		'id' => $cat_template_name.'_block1',
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny start-hidden has-hidden-select', //mini, tiny, small
    		'options' => array(
                'none' => 'Отсутствует',
                'textarea' => 'Поле ввода',
                'postsann' => 'Анонсы постов',
            ),
        );
        	$options[] = array(
        		'name' => 'Поле ввода',
        		'desc' => '',
        		'id' => $cat_template_name.'_block1_textarea_text',
        		'std' => '',
        		'class' => 'start-hidden-select '.$cat_template_name.'_block1 show-textarea',
        		'type' => 'textarea'
        	);
        */
        
    	$options[] = array(
    		'name' => 'Выводить блок над анонсами',
    		'desc' => '',
    		'id' => $cat_template_name.'_input_above',
    		'std' => '0', //
    		'class' => 'start-hidden has-hidden',
    		'type' => 'checkbox'
    	);
        	$options[] = array(
        		'name' => 'HTML',
        		'desc' => 'CSS для блока можно задать в поле "Дополнительный CSS шаблона"',
        		'id' => $cat_template_name.'_input_above_html',
        		'std' => '',
        		'class' => 'start-hidden',
        		'type' => 'textarea'
        	);

        //Анонсы постов.
    	$options[] = array(
    		'name' => 'Выводить анонсы "своих" постов',
    		'desc' => 'Анонсы постов из своей и из дочерних категорий.',
    		'id' => $cat_template_name.'_postsann',
    		'std' => '1', //
    		'class' => 'start-hidden has-hidden',
    		'type' => 'checkbox'
    	);
        	$options[] = array(
        		'name' => 'Заголовок над анонсами',
        		'desc' => 'Если заголовок пустой, то он и его разметка не будут выведены на страницу категории.',
        		'id' => $cat_template_name.'_postsann_h',
        		'std' => '',
        		'type' => 'text',
        		'class' => 'start-hidden',
        	);
            	$options[] = array(
            		'name' => 'Шрифт заголовка над анонсами',
            		'desc' => '',
            		'id' => $cat_template_name.'_postsann_font_h',
            		'std' => array( 'size' => '24px', 'face' => 'Georgia, serif', 'color' => '#333333'),
            		'class' => 'start-hidden',
            		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
                	'options' => array(
                		'faces' => $typography_fonts,
                		'styles' => $typography_fonts_weight,
                	),
                	'css_selector' => 'body.category.template-'.$key.' #posts-list-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            	);
        	$options[] = array(
        		'name' => 'Вид анонсов',
        		'desc' => '',
        		'id' => $cat_template_name.'_postsann_view',
        		'std' => 'list',
        		'type' => 'select',
        		'class' => 'start-hidden tiny', //mini, tiny, small
        		'options' => $elements_view,
            );

			$temp = array(
				'thumb-above' => 'Thumb + снизу: заголовок',
				'thumb-above-2' => 'Thumb + снизу: заголовок, мета',
				'thumb-above-3' => 'Thumb + снизу: заголовок, мета, отрывок',
				'thumb-under' => 'Заголовок + снизу: thumb',
				'thumb-under-2' => 'Заголовок, мета + снизу: thumb',
				'thumb-under-3' => 'Заголовок, мета + снизу: thumb, отрывок',        		
			);			
			if (quark_is_affiliateegg_active()) {
				$temp['egg-thumb'] = 'Egg+Миниатюра';
            	$temp['egg-discount'] = 'Egg-скидки';
			}
        	$options[] = array(
        		'name' => 'Макет анонса',
        		'desc' => '',
        		'id' => $cat_template_name.'_postsann_mockup',
        		'std' => 'thumb-above',
        		'type' => 'select',
        		'class' => 'start-hidden tiny', //mini, tiny, small
        		'options' => $temp,
            );
        	$options[] = array(
        		'name' => 'Маркер поверх миниатюры: кол-во приаттаченых изображений',
        		'desc' => '',
        		'id' => $cat_template_name.'_postsann_attimgs',
        		'std' => '0', //
        		'class' => 'start-hidden has-hidden',
        		'type' => 'checkbox'
        	);
            	$options[] = array(
            		'name' => 'Выводить',
            		'desc' => 'Вместо %s будет выведено кол-во аттачей -1. Возможный пример: В посте %s фото. Разрешен HTML.',
            		'id' => $cat_template_name.'_postsann_attimgs_input',
            		'std' => '<span>+%s фото</span>',
            		'class' => 'miny start-hidden',
            		'type' => 'textarea'
            	);
            	$options[] = array(
            		'name' => 'Положение маркера',
            		'desc' => 'Доп. смещение от углов можно задать CSS-свойствами, например: position: relative; top: 7px; right: 5px;',
            		'id' => $cat_template_name.'_postsann_attimgs_pos',
            		'std' => 'v-top h-left',
            		'type' => 'select',
            		'class' => 'start-hidden tiny', //mini, tiny, small
            		'options' => array(
                		'v-top h-left' => 'От верхнего левого угла',
                		'v-top h-right' => 'От верхнего правого угла',
                		'v-bottom h-left' => 'От нижнего левого угла',
                		'v-bottom h-right' => 'От нижнего правого угла',
            	    ),
            		/*'options' => array(
                		'top-left' => 'Сверху слева',
                		'top-center' => 'Сверху по центру',
                		'top-right' => 'Сверху справа',
                		'bot-left' => 'Снизу слева',
                		'bot-center' => 'Снизу по центру',
                		'bot-right' => 'Снизу справа',        		
            	    ),*/
                );
        	$options[] = array(
        		'name' => 'Маркер поверх миниатюры: дата публикации поста',
        		'desc' => 'Формат даты: "d M"',
        		'id' => $cat_template_name.'_postsann_pubdate',
        		'std' => '0', //
        		'class' => 'start-hidden has-hidden',
        		'type' => 'checkbox'
        	);
            	$options[] = array(
            		'name' => 'Выводить',
            		'desc' => 'Вместо %s будет выведена дата публикации. Возможный пример: Дата: %s. Разрешен HTML.',
            		'id' => $cat_template_name.'_postsann_pubdate_input',
            		'std' => '<span>%s</span>',
            		'class' => 'miny start-hidden',
            		'type' => 'textarea'
            	);
            	$options[] = array(
            		'name' => 'Положение маркера',
            		'desc' => 'Доп. смещение от углов можно задать CSS-свойствами, например: position: relative; top: 7px; right: 5px;',
            		'id' => $cat_template_name.'_postsann_pubdate_pos',
            		'std' => 'v-top h-left',
            		'type' => 'select',
            		'class' => 'start-hidden tiny', //mini, tiny, small
            		'options' => array(
                		'v-top h-left' => 'От верхнего левого угла',
                		'v-top h-right' => 'От верхнего правого угла',
                		'v-bottom h-left' => 'От нижнего левого угла',
                		'v-bottom h-right' => 'От нижнего правого угла',
            	    ),
            		/*'options' => array(
                		'top-left' => 'Сверху слева',
                		'top-center' => 'Сверху по центру',
                		'top-right' => 'Сверху справа',
                		'bot-left' => 'Снизу слева',
                		'bot-center' => 'Снизу по центру',
                		'bot-right' => 'Снизу справа',        		
            	    ),*/
                );
			if (quark_is_affiliateegg_active()) {
				$options[] = array(
					'name' => '(not work) Маркер поверх миниатюры: кол-во egg-товаров',
					'desc' => '',
					'id' => $cat_template_name.'_postsann_eggs',
					'std' => '0', //
					'class' => 'start-hidden has-hidden',
					'type' => 'checkbox'
				);
					$options[] = array(
						'name' => 'Выводить',
						'desc' => 'Вместо %s будет выведено кол-во egg-товаров. Возможный пример: Товаров: %s.  Разрешен HTML.',
						'id' => $cat_template_name.'_postsann_eggs_input',
						'std' => '',
						'class' => 'miny start-hidden',
						'type' => 'textarea'
					);        	
					$options[] = array(
						'name' => 'Положение маркера',
						'desc' => 'Доп. смещение от углов можно задать CSS-свойствами, например: position: relative; top: 7px; right: 5px;',
						'id' => $cat_template_name.'_postsann_eggs_pos',
						'std' => 'v-top h-left',
						'type' => 'select',
						'class' => 'start-hidden tiny', //mini, tiny, small
						'options' => array(
							'v-top h-left' => 'От верхнего левого угла',
							'v-top h-right' => 'От верхнего правого угла',
							'v-bottom h-left' => 'От нижнего левого угла',
							'v-bottom h-right' => 'От нижнего правого угла',      		
						),
					);
			};
        	$options[] = array(
        		'name' => 'Фон анонса',
        		'desc' => 'Цвет и/или изображение/паттерн.',
        		'id' => $cat_template_name.'_postsann_backg',
        		'std' => $background_defaults,
        		'class' => 'start-hidden',
        		'type' => 'background', //Для типа background задавать css_prop не нужно.
        		'css_selector' => 'body.category.template-'.$key.' #posts-list .post', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	$options[] = array(
        		'name' => 'Формат миниатюр',
        		'desc' => 'Здесь автоматически собраны данные по размерам из Настройки->Медиафайлы, а также из кода темы и плагинов.',
        		'id' => $cat_template_name.'_postsann_thumb_format',
        		'std' => 'thumbnail',
        		'type' => 'select',
        		'class' => 'start-hidden tiny', //mini, tiny, small
        		'options' => $image_sizes, //Составление массива в начале функции.
            );
            /*$options[] = array(
            	'name' => 'Заглушка миниатюры (внутри поста)',
            	'desc' => 'Если миниатюра поста отсутствует, то вместо нее будет выведено данное изображение. Данная заглушка применяется только к категориям.',
            	'id' => $cat_template_name.'_postsann_thumb_gag',
            	'std' => '',
            	'class' => 'start-hidden',
            	'type' => 'upload'
            );*/
        	$options[] = array(
        		'name' => 'Шрифт заголовка анонса',
        		'desc' => '',
        		'id' => $cat_template_name.'_postsann_ann_font_h',
        		'std' => array( 'size' => '24px', 'face' => 'Georgia, serif', 'color' => '#333333'),
        		'class' => 'start-hidden',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $typography_fonts,
            		'styles' => $typography_fonts_weight,
            	),
            	'css_selector' => 'body.category.template-'.$key.' #posts-list .post .entry-header .entry-title a', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	$options[] = array(
        		'name' => 'Шрифт текста анонса',
        		'desc' => '',
        		'id' => $cat_template_name.'_postsann_ann_font_excerpt',
        		'std' => array( 'size' => '16px', 'face' => 'Georgia, serif', 'color' => '#666666'),
        		'class' => 'start-hidden',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $typography_fonts,
            		'styles' => $typography_fonts_weight,
            	),
            	'css_selector' => 'body.category.template-'.$key.' #posts-list .post .entry-summary p', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	/*
        	$options[] = array(
        		'name' => 'Анонсы из указанных категорий',
        		'desc' => 'Иначе будут выводиться анонсы из внутренних категорий.',
        		'id' => $cat_template_name.'_postsann_cats',
        		'std' => '1',
        		'class' => 'start-hidden has-hidden',
        		'type' => 'checkbox'
        	);
            	$options[] = array(
            		'name' => 'Slug`и категорий',
            		'desc' => 'Через запятую и без пробелов, например, "obrazy,skidki"',
            		'id' => $cat_template_name.'_postsann_cats_slug',
            		'std' => '',
            		'class' => 'start-hidden',
            		'type' => 'text'
            	);
            */
        	$options[] = array(
        		'name' => 'Кол-во анонсов для вывода',
        		'desc' => 'В случае использования AJAX-подгрузки данное кол-во анонсов будет запрашиваться при Load More. ',
        		'id' => $cat_template_name.'_postsann_count',
        		'std' => '12',
        		'class' => 'mini start-hidden',
        		'type' => 'text'
        	);
        	$options[] = array(
        		'name' => 'Включить пагинацию',
        		'desc' => '',
        		'id' => $cat_template_name.'_postsann_pagin',
        		'std' => '1',
        		'class' => 'start-hidden has-hidden',
        		'type' => 'checkbox'
        	);
            	//AJAX-подгрузка постов.
            	$options[] = array(
            		'name' => 'Включить AJAX-подгрузку',
            		'desc' => 'Стандартная пагинация заменяется одной кнопкой с AJAX-подгрузкой постов.',
            		'id' => $cat_template_name.'_postsann_pagin_loadmore',
            		'std' => '1',
            		'class' => 'start-hidden has-hidden',
            		'type' => 'checkbox'
            	);
                	$options[] = array(
                		'name' => 'Ширина кнопки',
                		'desc' => '',
                		'id' => $cat_template_name.'_postsann_pagin_loadmore_but_width',
                		'std' => '30%',
                		'type' => 'select',
                		'class' => 'start-hidden tiny', //mini, tiny, small
                		'options' => array(
                    		'10%' => '10%',
                    		'20%' => '20%',
                    		'30%' => '30%',
                    		'40%' => '40%',
                    		'50%' => '50%',
                    		'60%' => '60%',
                    		'70%' => '70%',
                    		'80%' => '80%',
                    		'90%' => '90%',
                    		'100%' => '100%',
                	    ),
                	    'css_prop' => 'width', //Наличие css_prop является признаком объявления переменной для LESS.
                	    'css_selector' => 'body.category.template-'.$key.' #load-more-posts-but button', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
                    );
                    /* Здесь есть особенность - стили нужно накладывать конкретно на целевую кнопку и в вариациях для градиента, hover и др. 
                	$options[] = array(
                		'name' => 'Фон кнопки',
                		'desc' => 'Цвет и/или изображение/паттерн.',
                		'id' => $cat_template_name.'_posts_ajax_load_more_but_background',
                		'std' => $background_defaults,
                		'class' => 'start-hidden',
                		'type' => 'background', //Для типа background задавать css_prop не нужно.
                	    'css_selector' => 'body.category.template-'.$key.' #load-more-posts-but button', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
                	);
                	*/
                	$options[] = array(
                		'name' => 'Надпись на кнопке (состояние 1)',
                		'desc' => 'Состояние 1 - есть еще посты для подгрузки.',
                		'id' => $cat_template_name.'_postsann_pagin_loadmore_but_caption_more',
                		'std' => 'Показать еще',
                		'class' => 'start-hidden',
                		'type' => 'text'
                	);
                	$options[] = array(
                		'name' => 'Надпись на кнопке (состояние 2)',
                		'desc' => 'Состояние 2 - посты подгружаются.',
                		'id' => $cat_template_name.'_postsann_pagin_loadmore_but_caption_loading',
                		'std' => 'Загрузка постов...',
                		'class' => 'start-hidden',
                		'type' => 'text'
                	);	
                	$options[] = array(
                		'name' => 'Надпись на кнопке (состояние 3)',
                		'desc' => 'Состояние 3 - больше нет постов для подгрузки.',
                		'id' => $cat_template_name.'_postsann_pagin_loadmore_but_caption_nomore',
                		'std' => 'Больше нет',
                		'class' => 'start-hidden',
                		'type' => 'text'
                	);
        	
        //Дополнительный CSS шаблона
    	$options[] = array(
    		'name' => 'Дополнительный CSS шаблона',
    		'desc' => 'Селектор <b>всего шаблона</b>: body.category.template-'.$key.'<br>'.
    		            '<br>'.
    		            'Селектор на <b>блок со всем контентом</b> в шапке: + .site-header<br>'.
    		            'Селектор на <b>блок со всем контентом</b> в середине: + .site-content<br>'.
    		            'Селектор на <b>блок со всем контентом</b> в подвале: + .site-footer<br>'.
    		            '<br>'.
    		            'Селектор на каждый <b>виджет</b> сайдбара: + .sidebar .widget<br>'.
    		            '<br>'.
    		            'Селектор на <b>блок заголовка</b> категории: + header.archive-header<br>'.
    		            'Селектор на <b>сам заголовок</b> категории: + header.archive-header h1.archive-title<br>'.
    		            'Селектор на <b>блок анонсов постов</b>: + #posts-list<br>'.
    		            'Селектор на каждый <b>анонс поста</b>: + #posts-list .post<br>'.
                        '<br>'
                        ,
    		'id' => $cat_template_name.'_css',
    		'std' => '',
    		'class' => 'start-hidden',
    		'type' => 'textarea',
    		'css_selector' => 'rules', //rules - значит в dynamic.less будет записано содержимое поля в качестве полных CSS-правил.
    	);

	}
	
	
	
	
	
	//=== Пост
	$options[] = array(
		'name' => 'Пост',
		'type' => 'heading'
	);
	
	//Тип поста по умолчанию.
	$options['bo_post_type_default'] = array(
		'name' => 'Тип поста по умолчанию',
		'desc' => '',
		'id' => 'bo_post_type_default',
		'std' => 'post',
		'type' => 'select',
		'class' => 'tiny', //mini, tiny, small
		'options' => $post_types,
    );
    
	//Расположение аттачей по умолчанию.
	$options['bo_post_attach_default'] = array(
		'name' => 'Расположение аттачей по умолчанию',
		'desc' => '',
		'id' => 'bo_post_attach_default',
		'std' => 'none',
		'type' => 'select',
		'class' => 'tiny', //mini, tiny, small
		'options' => array(
    		'none' => 'Не выводить',
    		'first' => 'В начале поста',
	    ),
    );    

	foreach ($post_types as $key => $value) {

    	$options[] = array(
    		'name' => 'Тип поста - '.$value,
    		'desc' => '',
    		'type' => 'info',
    		'class' => 'admin-block-header admin-first-in-block',
    	);
    	
        //Вид поста
		$temp = array(
			'usual' => 'Обычное представление',
			'slider-content' => 'Слайдер слева + Контент справа',
		);
		if (quark_is_affiliateegg_active()) {
			$temp['egg-product-card'] = 'Карточка товара AffEgg';
		}
    	$options[] = array(
    		'name' => 'Вид поста',
    		'desc' => '',
    		'id' => 'bo_post_'.$key.'_template',
    		'std' => 'usual',
    		'type' => 'select',
    		'class' => 'tiny has-hidden-select', //mini, tiny, small
    		'options' => $temp,        
        );
		
		//Настройка миниатюр внутри постов.
        if (!in_array($key, array('product-card'))) {
            $options[] = array(
        		'name' => 'Показывать миниатюру в посте',
        		'desc' => 'При отключении миниатюры ее вывод будет полностью исключен.',
        		'id' => 'bo_post_'.$key.'_thumb',
        		'std' => '0',
        		'type' => 'checkbox',
        		'class' => 'tiny has-hidden',
        	);
        	$options[] = array(
        		'name' => 'Формат миниатюр',
        		'desc' => 'Автоматический сборщик данных по размерам из Настройки->Медиафайлы, а также из кода.',
        		'id' => 'bo_post_'.$key.'_thumb_format',
        		'std' => 'thumbnail',
        		'type' => 'select',
        		'class' => 'tiny start-hidden', //mini, tiny, small
        		'options' => $image_sizes, //Составление массива в начале функции.
            );
        }
        
        //Миниатюра-заглушка для постов (доделать для категорий).
        $options[] = array(
        	'name' => 'Миниатюра-заглушка для постов',
        	'desc' => 'Если включен показ миниатюр и она у поста не задана, то вместо миниатюры будет выведено данное изображение-заглушка.',
        	'id' => 'bo_post_'.$key.'_thumb_gag',
        	'std' => '',
        	'class' => '',
        	'type' => 'upload'
        );

    	//Сайдбары.
    	$options[] = array(
    		'name' => 'Сайдбар',
    		'desc' => '',
    		'id' => 'bo_post_'.$key.'_sidebar',
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny has-hidden-select', //mini, tiny, small
    		'options' => $active_sidebars,
        );
        	$options[] = array(
        		'name' => 'Позиция сайдбара',
        		'desc' => '',
        		'id' => 'bo_post_'.$key.'_sidebar_pos',
        		'std' => 'left',
        		'type' => 'select',
        		'class' => 'mini start-hidden-select bo_post_'.$key.'_sidebar notshow-none', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
        		'options' => array(
            		'left' => 'Слева',
            		'right' => 'Справа',
        	    ),
            );
        	$options[] = array(
        		'name' => 'Шаблон сайдбара',
        		'desc' => '',
        		'id' => 'bo_post_'.$key.'_sidebar_template',
        		'std' => 'simple',
        		'type' => 'select',
        		'class' => 'mini start-hidden-select bo_post_'.$key.'_sidebar notshow-none', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
        		'options' => array(
            		'simple' => 'Простой',
        	    ),
            );
            
        //Блок под постом.
    	$options[] = array(
    		'name' => 'Блок под постом',
    		'desc' => '',
    		'id' => 'bo_post_'.$key.'_underpostblock',
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny has-hidden-select', //mini, tiny, small
    		'options' => $active_underpostblocks,
        );

        //Ссылки на пред. и след. посты.
        $options[] = array(
    		'name' => 'Показывать ссылки Prev-Next',
    		'desc' => 'Ссылки на предыдущий и следующий посты.',
    		'id' => 'bo_post_'.$key.'_nav_prevnext',
    		'std' => '0',
    		'type' => 'checkbox',
    		'class' => 'tiny',
    	);
    	
    	//Размещение кнопок UpToLike.
    	$options[] = array(
    		'name' => 'Показывать кнопки "Поделиться" (UpToLike)',
    		'desc' => '',
    		'id' => 'bo_post_'.$key.'_uptolike_share_pos',
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny has-hidden-select', //mini, tiny, small
    		'options' => array(
        		'none' => 'Нет',
        		'under_post' => 'В начале поста',
        		'after_post' => 'В конце поста',
        		'under_after_post' => 'В начале и в конце поста',
    	    ),
        );
    	$options[] = array(
    		'name' => 'Кнопки "Поделиться" (UpToLike) под слайдером',
    		'desc' => 'Помимо чуть ранее заданого положения, еще разместить и под слайдером.',
    		'id' => 'bo_post_'.$key.'_uptolike_share_pos_slider',
    		'std' => '0',
    		'type' => 'checkbox',
    		//'class' => 'tiny start-hidden-select bo_post_'.$key.'_template show-slider-content', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
            'class' => 'tiny', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
            //'cond-show' => 'show:bo_post_'.$key.'_template=slider-content;notshow',
            'cond-show' => 'bo_post_'.$key.'_template=slider-content',
            //'cond-show' => array(
            //    array(
            //        'bo_post_'.$key.'_template' => 'slider-content',
            //    ),
            //),
        );
    	$options[] = array(
    		'name' => 'Заголовок кнопок',
    		'desc' => 'Будет расположен над кнопками.',
    		'id' => 'bo_post_'.$key.'_uptolike_share_h',
    		'std' => '',
    		'type' => 'text',
    		//'cond-show' => 'notshow:bo_post_'.$key.'_uptolike_share_pos=none,bo_post_'.$key.'_uptolike_share_pos_slider=0,bo_post_'.$key.'_template=slider-content;notshow:bo_post_'.$key.'_uptolike_share_pos=none;show',
    		'cond-show' => 'bo_post_'.$key.'_uptolike_share_pos!=none;bo_post_'.$key.'_template=slider-content,bo_post_'.$key.'_uptolike_share_pos_slider=1',
    	);
        	
    	//Шрифт заголовка поста.
    	$options[] = array(
    		'name' => 'Шрифт заголовка',
    		'desc' => '',
    		'id' => 'bo_post_'.$key.'_font_header',
    		'std' => array( 'size' => '32px', 'face' => 'Georgia, serif', 'color' => '#333333'),
    		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
        	'options' => array(
        		'faces' => $typography_fonts,
        		'styles' => $typography_fonts_weight,
        	),
        	'css_selector' => 'body.post-'.$key.' header.entry-header h1.entry-title', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	
        //CSS-правила
    	$options[] = array(
    		'name' => 'CSS-правила',
    		'desc' => '<b>Основной</b> блок: body.post-'.$key.'<br>'.
    		            '<br>'.
    		            'Селектор на <b>весь блок в середине</b>: + .site-content<br>'.
                        'Селектор на <b>блок всего поста</b>: + .site-content article.post'.'<br>'.
                        '<br>'.
                        'Селектор на <b>блок всего заголовка поста</b>: + .site-content article.post header.entry-header'.'<br>'.
                        'Селектор на <b>сам заголовок</b>: + .site-content article.post header.entry-header .entry-title'.'<br>'.
                        'Селектор на блок с <b>мета-информацией</b> поста: + .site-content article.post header.entry-header .header-meta'.'<br>'.
                        'Селектор на <b>категории</b> поста: + .site-content article.post header.entry-header .header-meta .post-categories a'.'<br>'.
                        '<br>'.
                        'Селектор непосредственно на <b>содержимое поста</b>: + .site-content article.post .entry-content'.'<br>'.
                        'Селектор на <b>блок всего подвала поста</b>: + .site-content article.post footer.entry-meta'.'<br>'.
                        '<br>'.
                        'Селектор на <b>сайдбар</b>: + .site-content .sidebar'.'<br>'.
                        'Селектор на <b>область комментирования</b>: + .site-content .comments-area'.'<br>'.
                        '<br>'
                        
                        
                        
                        ,
    		'id' => 'bo_post_'.$key.'_css',
    		'std' => '',
    		'class' => '', //maxi
    		'type' => 'textarea',
    		'css_selector' => 'rules', //rules - значит в dynamic.less будет записано содержимое поля в качестве полных CSS-правил.
    	);
	}
	
	
	
	
	
	//=== Меню
	$menus = get_terms('nav_menu', array('hide_empty' => true));
	
    foreach ($menus as $menu) {

        $bo_term_id = $menu->term_id;
        $bo_menu_slug = $menu->slug;
        $bo_menu_css = '#menu-'.$bo_menu_slug;
        
    	$options[] = array(
    		'name' => 'Меню "'.$menu->name.'"',
    		'type' => 'heading'
    	);
		
        //CSS-правила
    	$options[] = array(
    		'name' => 'CSS-правила',
    		'desc' => '<b>Основной</b> блок: '.$bo_menu_css.'<br>'.
    		            '<br>'.
                        'Элементы только <b>1-ого уровня</b> и т.д.: '.$bo_menu_css.' li.menu-item-depth-0'.'<br>'.
                        'Элементы имеющие <b>дочерние элементы</b>: '.$bo_menu_css.' li.menu-item-has-children'.'<br>'.
                        'Элементы <b>2-ого и далее уровней</b>: '.$bo_menu_css.' ul.sub-menu li'.'<br>'.
                        '<br>'.
                        '<b>Активный</b> элемент: '.$bo_menu_css.' .current-menu-item'.'<br>'.
                        '<br>'
                        ,
    		'id' => 'bo_menu'.$bo_term_id.'_css',
    		'std' => '',
    		'class' => 'maxi',
    		'type' => 'textarea',
    		'css_selector' => 'rules', //rules - значит в dynamic.less будет записано содержимое поля в качестве полных CSS-правил.
    	);
    }
	
	
	
	
	
	//=== Affiliate Egg
	$options[] = array(
		'name' => 'Affiliate Egg',
		'type' => 'heading'
	);
	
	if (quark_is_affiliateegg_active()) {
		
		//Изображение-заглушка для товаров.
		$options[] = array(
			'name' => 'Изображение-заглушка для товаров',
			'desc' => 'Если у товара отсутствует изображение, то вместо него будет выведено данное изображение-заглушка. Пока заглушка применяется только к Карточкам товаров.',
			'id' => 'bo_affegg_image_gag',
			'std' => '',
			'class' => '',
			'type' => 'upload'
		);
		
		//Какие ПП-ссылки декодировать, т.е. не передавать урлкодированными. Например, AD1.ru в диплинках не переваривает урлкодированные дипурлы.
		$options[] = array(
			'name' => 'Партнерские ссылки без урлкодирования',
			'desc' => 'Например, указав строку "c.cpl1.ru" все партнерские ссылки с содержанием этой строки будут размещены на сайте без урлкодирования. Каждое значение с новой строки.',
			'id' => 'bo_affegg_urldecode',
			'std' => '',
			'type' => 'textarea'
		);
		
	} else {
		
		$options[] = array(
			'name' => 'Плагин Affiliate Egg не установлен или не активирован.',
			'desc' => '',
			'type' => 'info'
		);		
	}
	

	
	
	
	//=== UpToLike
	$options[] = array(
		'name' => esc_html__( 'UpToLike', 'quark' ),
		'type' => 'heading'
	);
	
	$options[] = array(
		'name' => esc_html__( 'Сервис UpToLike', 'quark' ),
		'desc' => 
	        wp_kses(__( 'Размещение социальных кнопок и мониторинг социальной активности на сайте с помощью <a href="http://uptolike.ru/" target="blank">UpToLike.ru</a>', 'quark'), 
    		    array(
        			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
        				'href' => array(),
        				'target' => array(),
        			),
    			)
    		),
		'type' => 'info'
	);
	$options[] = array(
		'name' => esc_html__( 'Javascript-код кнопок "Поделиться"', 'quark' ),
		'desc' => esc_html__( 'Код необходимо сгенерировать на сайте UpToLike.ru', 'quark' ),
		'id' => 'bo_uptolike_share_js',
		'std' => '',
		'type' => 'textarea'
	);
	$options[] = array(
		'name' => esc_html__( 'Html-метка кнопок "Поделиться"', 'quark' ),
		'desc' => esc_html__( 'Код необходимо сгенерировать на сайте UpToLike.ru', 'quark' ),
		'id' => 'bo_uptolike_share_html',
		'std' => '',
		'type' => 'textarea'
	);
	//==============================================================
	
	//=== UniSender
	$options[] = array(
		'name' => esc_html__( 'UniSender', 'quark' ),
		'type' => 'heading'
	);
	
	$options[] = array(
		'name' => esc_html__( 'Сервис UniSender', 'quark' ),
		'desc' => 
	        wp_kses(__( 'Email-маркетинг (форма подписки, уведомления, рассылки) с помощью <a href="http://unisender.com/" target="blank">UniSender.com</a>', 'quark'), 
    		    array(
        			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
        				'href' => array(),
        				'target' => array(),
        			),
    			)
    		),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__( 'Форма "Подписаться', 'quark' ),
		'desc' => 
	        wp_kses(__( 'Для отображения формы можно воспользоваться виджетом UniSender и/или добавить пункт "Ссылка" в меню и указать "#unisender-signup-popup" 
	                    в качестве параметра URL.', 'quark'), 
    		    array(
        			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
        				'href' => array(),
        				'target' => array(),
        			),
        			'br' => array(),
    			)
    		),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__( 'Код формы "Подписаться"', 'quark' ),
		'desc' => esc_html__( 'Код необходимо сгенерировать на сайте UniSender.com', 'quark' ),
		'id' => 'bo_unisender_signup_html',
		'std' => '',
		'type' => 'textarea'
	);
	
	$options[] = array(
		'name' => esc_html__('Плавающий стикер виджета', 'quark'),
		'desc' => esc_html__('Стикер почти всегда будет виден на экране.', 'quark'),
		'id' => 'bo_unisender_signup_widget_sticker',
		'std' => '0',
		'type' => 'checkbox'
	);
	//==============================================================

    //=== Счётчики
	$options[] = array(
		'name' => 'Счётчики',
		'type' => 'heading'
	);

	$options[] = array(
		'name' => 'Яндекс.Метрика',
		'desc' => '',
		'id' => 'bo_yandex_metrica_code',
		'std' => '',
		'type' => 'textarea',
		'class' => 'admin-first-in-block'
	);
    	$options[] = array(
    		'name' => 'Номер счетчика Яндекс.Метрики',
    		'desc' => 'Необходим для проставления целей по событию.',
    		'id' => 'bo_yandex_metrica_id',
    		'std' => '',
    		'class' => '',
    		'type' => 'text'
    	);
    	$options[] = array(
    		'name' => 'Код верификации сайта для Яндекса',
    		'desc' => 'Например: 555accab79694b2e',
    		'id' => 'bo_yandex_verif_id',
    		'std' => '',
    		'class' => '',
    		'type' => 'text'
    	);
	
	$options[] = array(
		'name' => 'Google Analytics',
		'desc' => '',
		'id' => 'bo_google_analytics_code',
		'std' => '',
		'type' => 'textarea',
		'class' => 'admin-first-in-block'
	);
	$options[] = array(
		'name' => 'Рейтинг@Mail.ru',
		'desc' => 'Он же Top.Mail.ru',
		'id' => 'bo_mailru_top_code',
		'std' => '',
		'type' => 'textarea',
		'class' => 'admin-first-in-block'
	);
	$options[] = array(
		'name' => 'LiveInternet',
		'desc' => '',
		'id' => 'bo_li_code',
		'std' => '',
		'type' => 'textarea',
		'class' => 'admin-first-in-block'
	);
	$options[] = array(
		'name' => 'Вконтакте',
		'desc' => '',
		'id' => 'bo_vk_code',
		'std' => '',
		'type' => 'textarea',
		'class' => 'admin-first-in-block'
	);
    //==============================================================
    
    //=== Остальное
	$options[] = array(
		'name' => 'Остальное',
		'type' => 'heading'
	);
	
	//Инициализация Lazy Load изображений.
    $options[] = array(
		'name' => 'Включить Lazy Load изображений',
		'desc' => 'Применяется для всех изображений (с аттрибутом data-src) расположенных в блоках article.isotope-item',
		'id' => 'bo_image_lazyload',
		'std' => '0',
		'type' => 'checkbox',
		'class' => 'tiny has-hidden',
	);
		$options['bo_image_lazyload_loader_gif'] = array(
			'name' => 'GIF процесса загрузки',
			'desc' => 'В случае не выбора GIF`ки изображения будут появляться как бы из пустого места.',
			'id' => 'bo_image_lazyload_loader_gif',
			'std' => '',
			'type' => 'upload',
			'class' => 'start-hidden',
		);
    $options[] = array(
		'name' => 'Включить админ-бар',
		'desc' => 'Включится для всех авторизованных пользователей.',
		'id' => 'bo_admin_bar',
		'std' => '0',
		'type' => 'checkbox',
		'class' => 'tiny',
	);		
	
	
	
	

    //=== WooCommerce
	if (quark_is_woocommerce_active()) {
		$options[] = array(
		'name' => esc_html__( 'WooCommerce settings', 'quark' ),
		'type' => 'heading' );

		$options[] = array(
			'name' => esc_html__('Shop sidebar', 'quark'),
			'desc' => esc_html__('Display the sidebar on the WooCommerce Shop page', 'quark'),
			'id' => 'woocommerce_shopsidebar',
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'name' => esc_html__('Products sidebar', 'quark'),
			'desc' => esc_html__('Display the sidebar on the WooCommerce Single Product page', 'quark'),
			'id' => 'woocommerce_productsidebar',
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'name' => esc_html__( 'Cart, Checkout & My Account sidebars', 'quark' ),
			'desc' => esc_html__( 'The &lsquo;Cart&rsquo;, &lsquo;Checkout&rsquo; and &lsquo;My Account&rsquo; pages are displayed using shortcodes. To remove the sidebar from these Pages, simply edit each Page and change the Template (in the Page Attributes Panel) to the &lsquo;Full-width Page Template&rsquo;.', 'quark' ),
			'type' => 'info' );

		$options[] = array(
			'name' => esc_html__('Shop Breadcrumbs', 'quark'),
			'desc' => esc_html__('Display the breadcrumbs on the WooCommerce pages', 'quark'),
			'id' => 'woocommerce_breadcrumbs',
			'std' => '1',
			'type' => 'checkbox');
	}
	
	
	
	

    //=== Страницы Page в виде табов.
	$options = page_tabs($options, $background_defaults, $image_sizes, $typography_fonts, $typography_fonts_weight, $elements_view, $active_sidebars);
	
	
	
	

    /*
    //=== Билдеры блоков.
    $params_all = array();
    $params_all['background_defaults'] = $background_defaults;
    $params_all['image_sizes'] = $image_sizes;
    $params_all['typography_fonts'] = $typography_fonts;
    $params_all['typography_fonts_weight'] = $typography_fonts_weight;
    $params_all['elements_view'] = $elements_view;
    //$params_all['active_sidebars'] = $active_sidebars;
    
    foreach ($blocks_builder as $key => $value) {
        $options = bo_blocks_builder($options, $key, $value, $params_all);
    }
    //==============================================================
    */
    
	
	
	
	
    //=== Справка
	$options[] = array(
		'name' => 'Справка',
		'type' => 'heading'
	);
	
    $tech_info = 'Загруженный php.ini: '.php_ini_loaded_file().'<br /><br />';
    
    $tech_info .= 'Макс. кол-во входных переменных передаваемых через $_POST и $_GET (отдельно на каждый): '.ini_get('max_input_vars').'<br />';
    $tech_info .= 'Макс. кол-во секунд на разбор всех данных передаваемых через $_POST и $_GET (отдельно на каждый): '.ini_get('max_input_time').'<br />';
    $tech_info .= 'Макс. объем памяти доступной для работы скрипту: '.ini_get('memory_limit').'<br />';
    $tech_info .= 'Макс. кол-во секунд на выполнение скрипта: '.ini_get('max_execution_time').'<br />';
    $tech_info .= 'Макс. размер данных, отправляемых через $_POST: '.ini_get('post_max_size').'<br />';
    $tech_info .= 'Макс. размер закачиваемого файла: '.ini_get('upload_max_filesize').'<br />';
    $tech_info .= 'Макс. кол-во одновременно закачиваемых файлов: '.ini_get('max_file_uploads').'<br />';
    
    $tech_info .= '<br />K (КБайт), M (МБайт), G (ГБайт)<br />';
    
    $tech_info .= '<br />Описание директив:<br />';
    $tech_info .= '<a href="http://php.net/manual/ru/ini.core.php#ini.open-basedir">Описание встроенных директив</a><br />';
    $tech_info .= '<a href="http://php.net/manual/ru/info.configuration.php#ini.max-execution-time">Настройка во время выполнения</a><br />';
    
	$options[] = array(
		'name' => 'Текущие директивы php.ini',
		'desc' => $tech_info,
		'type' => 'info'
	);
	
	
	
	

	return $options;
}

//Выводим все категории, раздаем им их шаблоны и дополнительные поля.
function category_templates($cat_active_templates, $options, $cat, $cat_types) {
	
    $next = get_categories('hide_empty=0&orderby=name&order=ASC&parent='.$cat);
	
    if ($next) {
        
        foreach ($next as $cat) {
            
            //Для категорий без родителей выставляем класс.
            $cat_highlight_class = '';
            if ($cat->parent == 0) {
                $cat_highlight_class = 'cat-parent-level-0';
            }
            
        	$options[] = array(
            	'name' => $cat->name,
            	'desc' => 
                    wp_kses(__( 'Slug: '.$cat->slug.'; ID: '.$cat->term_id.' (Parent ID: '.$cat->category_parent.'); Кол-во постов: '.$cat->count, 'quark'), 
            		    array(
                			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
                				'href' => array(),
                				'target' => array(),
                			),
            			)
            		),
            	'class' => $cat_highlight_class.' admin-first-in-block',
            	'type' => 'info',
        	);

            
        	$options[] = array(
        		'name' => '',
        		'desc' => '',
        		'id' => 'bo_cat_'.$cat->term_id.'_type',
        		'std' => 'usual',
        		'type' => 'select',
        		'class' => 'mini', //mini, tiny, small
        		'options' => $cat_types,
            );        	
            
            
        	$options[] = array(
        		'name' => '',
        		'desc' => '',
        		'id' => 'bo_cat_'.$cat->term_id.'_template',
        		'std' => '1',
        		'type' => 'select',
        		'class' => 'mini', //mini, tiny, small
        		'options' => $cat_active_templates,
            );
            
        	$options[] = array(
        		'name' => 'Заголовок категории',
        		'desc' => 'Если заголовок пустой, то он и его разметка не будут выведены на страницу категории.',
        		'id' => 'bo_cat_'.$cat->term_id.'_header',
        		'std' => $cat->name,
        		'type' => 'text',
        	);

            //Теперь рекурсивно получаем дочерние категории только что выведенной категории.
            $options = category_templates($cat_active_templates, $options, $cat->term_id, $cat_types);
        }
    }
    
    return $options;
}

//Выводим в виде вкладок Theme Settings все опубликованные страницы (не посты).
function page_tabs($options, $background_defaults, $image_sizes, $typography_fonts, $typography_fonts_weight, $elements_view, $active_sidebars) {
    
	$args = array( //http://wp-kama.ru/function/get_pages
    	 'sort_order' => 'ASC',
    	 'sort_column' => 'ID',
    	 'hierarchical' => 0,
    	 'parent' => -1,
    	 'post_type' => 'page',
    	 'post_status' => 'publish,draft',
    );
    $pages = get_pages($args);
    
    foreach ($pages as $page) {
		
        setup_postdata($page);
        
    	$options[] = array(
    		'name' => 'Страница - '.(mb_strlen($page->post_title) <= 10 ? $page->post_title : mb_substr($page->post_title, 0, 10).'...').' ('.$page->ID.')', //strlen и substr почему-то не отрабатывает корректно.
    		'type' => 'heading'
    	);
    	
    	$options[] = array(
    	'name' => 'Заголовок: '.$page->post_title,
    	'desc' => 
            wp_kses('ID: '.$page->ID.';', 
    		    array(
        			'a' => array( //Разрешенные html-теги и атрибуты этих тегов. Все остальное будет вырезаться.
        				'href' => array(),
        				'target' => array(),
        			),
    			)
    		),
    	'type' => 'info'
    	);
        	$options[] = array(
        		'name' => 'Показывать заголовок страницы',
        		'desc' => '',
        		'id' => 'bo_page_'.$page->ID.'_header',
        		'std' => '1',
        		'class' => '',
        		'type' => 'checkbox'
        	);

    	//=============== Билдер страниц ===================
    	$options = bo_page_builder($options, $background_defaults, $image_sizes, $typography_fonts, $typography_fonts_weight, $elements_view, $active_sidebars, 'bo_page', $page->ID, false);
    	//==================================
    }
    wp_reset_postdata();
    
    return $options;
}

//Билдер статических страниц и категорий.
function bo_page_builder($options, $background_defaults, $image_sizes, $typography_fonts, $typography_fonts_weight, $elements_view, $active_sidebars, $bo_id_prefix, $bo_term_id, $is_category = false) {
    $blocks = array(1,2,3,4,5,6);

    //Если категория.
    if ($is_category) {
        $base_css_selector = 'body.category.template-'.$bo_term_id;

    //Если статическая страниц.
    } else {
        $base_css_selector = 'body.page.page-id-'.$bo_term_id;
    }

    /*
	$options[] = array(
        'name' => '============ Билдер страниц ============',
        'desc' => '',
        'type' => 'info'
	);
	*/
	
	$options[] = array(
		'name' => 'Фон страницы',
		'desc' => 'Цвет и/или изображение/паттерн.',
		'id' => $bo_id_prefix.'_'.$bo_term_id.'_background',
		'std' => $background_defaults,
		'type' => 'background', //Для типа background задавать css_prop не нужно.
		//'css_selector' => 'page-'.$page->ID, //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
	    'css_selector' => $base_css_selector, //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
	);
    	
   	if ($is_category && 1>2) {

    	//Сайдбар.
    	$options[] = array(
    		'name' => 'Сайдбар',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_sidebar',
    		'std' => 'none',
    		'type' => 'select',
    		//'class' => 'tiny start-hidden has-hidden-select', //mini, tiny, small
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none has-hidden-select', //mini, tiny, small
    		'options' => $active_sidebars,
        );
    	$options[] = array(
    		'name' => 'Позиция сайдбара',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_sidebar_pos',
    		'std' => 'left',
    		'type' => 'select',
    		'class' => 'mini start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_sidebar notshow-none '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
    		'options' => array(
        		'left' => 'Слева',
        		'right' => 'Справа',
    	    ),
        );
    	$options[] = array(
    		'name' => 'Шаблон сайдбара',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_sidebar_template',
    		'std' => 'simple',
    		'type' => 'select',
    		'class' => 'mini start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_sidebar notshow-none '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none', //mini, tiny, small //notshow-option значит не показывать для такой-то option.
    		'options' => array(
        		'simple' => 'Простой',
    	    ),
        );
    }
	
	//Собираем блоки в соответствии с их порядковыми номерами.
	$blocks_order = array();
	foreach($blocks as $block_inc) {
	    //$cur_block = $tax_prefix.'_block'.$block_inc;
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
	
	//foreach($blocks as $block_inc) {
	foreach ($blocks_order as $block_inc) {
	    $block_css_selector = $base_css_selector.' .block-'.$block_inc;
	    
    	$options[] = array(
    		'name' => 'Тип блока',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc,
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny has-hidden-select admin-first-in-block', //mini, tiny, small
    		'options' => array(
        		'none' => 'Отсутствует',
        		'slider-slick' => 'Слайдер slick',
        		'posts-list' => 'Анонсы постов',
        		'textarea' => 'Поле ввода',
        		'banner' => 'Баннер',
    	    ),
        );
        
        //Позиция блока.
    	$options[] = array(
    		'name' => 'Позиция блока',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_order',
    		'std' => '180',
    		'type' => 'select',
    		'class' => 'mini start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none', //mini, tiny, small
    		'options' => array(
        		1 => '1ый',
        		20 => '2ой',
        		40 => '3ий',
        		60 => '4ый',
        		80 => '5ый',
        		100 => '6ой',
        		120 => '7ой',
        		140 => '8ой',
        		160 => '9ый',
        		180 => '10ый',
    	    ),
        );
        
        //Контент блока на всю ширину экрана
    	$options[] = array(
    		'name' => 'Контент блока на всю ширину экрана',
    		'desc' => 'Если не выбрана, то контент разместится по середине экрана.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_content_wide',
    		'std' => '0',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none',
    		'type' => 'checkbox'
    	);
    	
    	//Фон ряда (вся ширина экрана) блока
    	$options[] = array(
    		'name' => 'Фон ряда (вся ширина экрана) блока',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_row_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
		    'css_selector' => $block_css_selector, //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);

        //Заголовок блока
    	$options[] = array(
    		'name' => 'Заголовок блока',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_text',
    		'std' => '',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none',
    		'type' => 'text',
    	);
        	$options[] = array(
        		'name' => 'Шрифт заголовка блока',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_font',
        		'std' => array( 'size' => '28px', 'face' => 'Georgia, serif', 'color' => '#333333'),
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $typography_fonts,
            		'styles' => $typography_fonts_weight,
            	),
            	'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	/*
        	$options[] = array(
        		'name' => 'Выравнивание заголовка блока',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_align',
        		'std' => 'center',
        		'type' => 'select',
        		'class' => 'tiny start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none', //mini, tiny, small
        		'options' => array(
        		    'left' => 'По левому краю',
        		    'center' => 'По центру',
            		'right' => 'По правому краю',
        	    ),
        	    'css_prop' => 'text-align', //Наличие css_prop является признаком объявления переменной для LESS.
        	    'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            );
        	$options[] = array(
        		'name' => 'Margin заголовка блока',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_margin',
        		'std' => '0',
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none',
        		'type' => 'text',
        		'css_prop' => 'margin', //Наличие css_prop является признаком объявления переменной для LESS.
    		    'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	$options[] = array(
        		'name' => 'Нижний бордер заголовка блока',
        		'desc' => 'Например: 1px solid #eeeeee',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_bborder',
        		'std' => '0',
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none',
        		'type' => 'text',
        		'css_prop' => 'border-bottom', //Наличие css_prop является признаком объявления переменной для LESS.
    		    'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	*/

        //Слайдер slick.
    	$options[] = array(
    		'name' => 'Фон слоя с контентом',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-slider-slick',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
		    'css_selector' => $block_css_selector.' .slider-slick', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Высота слайдера',
    		'desc' => '200px, 100%',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_height',
    		'std' => '300px',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-slider-slick',
    		'type' => 'text',
    		'css_prop' => 'height', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .slider-slick', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Высота изображений (max-height)',
    		'desc' => '200px, 100%',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_image_height',
    		'std' => '100%',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-slider-slick',
    		'type' => 'text',
    		//Именно max-height, а не height.
    		'css_prop' => 'max-height', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .slider-slick .slick-slide img', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	/*
    	$options[] = array(
    		'name' => 'Прозрачность слайдера (opacity)',
    		'desc' => 'от 0.00 до 1',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_opacity',
    		'std' => '1',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-slider-slick',
    		'type' => 'text',
    		'css_prop' => 'opacity', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .slider-slick .slick-slide img', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	*/
    	$options[] = array(
    		'name' => 'На полную ширину экрана',
    		'desc' => 'Если не выбрана, то слайдер растянется на ширину контентного блока.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_wide',
    		'std' => '1',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-slider-slick',
    		'type' => 'checkbox'
    	);
    	$options[] = array(
    		'name' => 'Код слайдера (slick)',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_code',
    		'std' => '',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-slider-slick',
    		'type' => 'textarea'
    	);
    	
    	//Анонсы постов.
    	$options[] = array(
    		'name' => 'Фон слоя с контентом',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    		'css_selector' => $block_css_selector.' .posts-ann', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Вид анонсов постов',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_view',
    		'std' => 'list',
    		'type' => 'select',
    		'class' => 'tiny start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list', //mini, tiny, small
    		'options' => $elements_view,
        );
		
		$temp = array(
			'thumb-above' => 'Thumb + снизу: заголовок',     		
		);
		if (quark_is_affiliateegg_active()) {
			$temp['thumb-egg'] = 'Thumb + Egg';
		}
    	$options[] = array(
    		'name' => 'Макет анонса',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_mockup',
    		'std' => 'thumb-above',
    		'type' => 'select',
    		'class' => 'start-hidden tiny', //mini, tiny, small
    		'options' => $temp,
        );

    	if (!$is_category) {
    	    
        	$options[] = array(
        		'name' => 'Slug`и категорий выводимых анонсов постов',
        		'desc' => 'Через запятую и без пробелов, например, "obrazy,skidki"',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_cats',
        		'std' => '',
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'text'
        	);
    	
        	$options[] = array(
        		'name' => 'Кол-во постов для вывода',
        		'desc' => '15',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_count',
        		'std' => '9',
        		'class' => 'mini start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'text'
        	);
    	}

        //Кусок для категорий.
        if ($is_category && 1>2) {
            
        	$options[] = array(
        		'name' => 'Анонсов на страницу',
        		'desc' => 'Кол-во анонсов на 1 страницу категории. Это значение также влияет и на пагинацию/load more. Базовое кол-во задается в Настройки->Настройки чтения.',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_per_page',
        		'std' => '12',
        		'class' => 'mini start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'text'
        	);            

        	$options[] = array(
        		'name' => 'Макет анонса',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_mockup',
        		'std' => 'thumb',
        		'type' => 'select',
        		'class' => 'tiny start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list', //mini, tiny, small
        		'options' => array(
            		'simple-thumb' => 'Thumb',
            		'egg-thumb' => 'Egg+Миниатюра',
            		'egg-discount' => 'Egg-скидки',
        	    ),
            );
            
        	$options[] = array(
        		'name' => 'Заголовок и мета под миниатюрой',
        		'desc' => 'В постах заголовок и мета останутся сверху.',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_titlemeta_under_thumb',
        		'std' => '0',
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'checkbox'
        	);
        	
        	$options[] = array(
        		'name' => 'Показывать мета',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_meta',
        		'std' => '1',
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'checkbox'
        	);
        	
        	$options[] = array(
        		'name' => 'Показывать отрывок из поста',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_excerpt',
        		'std' => '1',
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'checkbox'
        	);
        	
        	$options[] = array(
        		'name' => 'Фон поста',
        		'desc' => 'Цвет и/или изображение/паттерн.',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_background',
        		'std' => $background_defaults,
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'background', //Для типа background задавать css_prop не нужно.
        		'css_selector' => 'body.category.template-'.$key.' #posts-list .post', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	
        	$options[] = array(
        		'name' => 'Тень для элементов',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_shadow_element',
        		'std' => 'none',
        		'type' => 'select',
        		'class' => 'tiny start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list', //mini, tiny, small
        		'options' => array(
            		'none' => 'Отсутствует',
            		'0 1px 2px rgba(0, 0, 0, 0.3)' => 'Средняя тень', //Для передачи через переменную LESS тень нужно передавать в доп. кавычках.
        	    ),
        	    'css_prop' => 'box-shadow', //Наличие css_prop является признаком объявления переменной для LESS.
        	    'css_selector' => 'body.category.template-'.$key.' #posts-list .post', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            );
        	
        	$options[] = array(
        		'name' => 'Шрифт заголовка',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_font_header',
        		'std' => array( 'size' => '32px', 'face' => 'Georgia, serif', 'color' => '#333333'),
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $typography_fonts,
            		'styles' => $typography_fonts_weight,
            	),
            	'css_selector' => 'body.category.template-'.$key.' #posts-list .post .entry-header .entry-title a', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	
        	$options[] = array(
        		'name' => 'Шрифт текста',
        		'desc' => '',
        		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_font_excerpt',
        		'std' => array( 'size' => '16px', 'face' => 'Georgia, serif', 'color' => '#666666'),
        		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $typography_fonts,
            		'styles' => $typography_fonts_weight,
            	),
            	'css_selector' => 'body.category.template-'.$key.' #posts-list .post .entry-summary p', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);            
            
        	//AJAX-подгрузка постов.
        	$options[] = array(
        		'name' => 'Включить AJAX-подгрузку',
        		'desc' => 'Стандартная пагинация заменяется одной кнопкой с AJAX-подгрузкой постов.',
        		'id' => $cat_template_name.'_posts_ajax_load_more',
        		'std' => '1',
        		'class' => 'start-hidden has-hidden',
        		'type' => 'checkbox'
        	);
            	$options[] = array(
            		'name' => 'Ширина кнопки',
            		'desc' => '',
            		'id' => $cat_template_name.'_posts_ajax_load_more_but_width',
            		'std' => '30%',
            		'type' => 'select',
            		'class' => 'start-hidden tiny', //mini, tiny, small
            		'options' => array(
                		'10%' => '10%',
                		'20%' => '20%',
                		'30%' => '30%',
                		'40%' => '40%',
                		'50%' => '50%',
                		'60%' => '60%',
                		'70%' => '70%',
                		'80%' => '80%',
                		'90%' => '90%',
                		'100%' => '100%',
            	    ),
            	    'css_prop' => 'width', //Наличие css_prop является признаком объявления переменной для LESS.
            	    'css_selector' => 'body.category.template-'.$key.' #load-more-posts-but button', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
                );
                /* Здесь есть особенность - стили нужно накладывать конкретно на целевую кнопку и в вариациях для градиента, hover и др. 
            	$options[] = array(
            		'name' => 'Фон кнопки',
            		'desc' => 'Цвет и/или изображение/паттерн.',
            		'id' => $cat_template_name.'_posts_ajax_load_more_but_background',
            		'std' => $background_defaults,
            		'class' => 'start-hidden',
            		'type' => 'background', //Для типа background задавать css_prop не нужно.
            	    'css_selector' => 'body.category.template-'.$key.' #load-more-posts-but button', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            	);
            	*/
            	$options[] = array(
            		'name' => 'Надпись на кнопке (состояние 1)',
            		'desc' => 'Состояние 1 - есть еще посты для подгрузки.',
            		'id' => $cat_template_name.'_posts_ajax_load_more_but_caption_more',
            		'std' => 'Показать еще',
            		'class' => 'start-hidden',
            		'type' => 'text'
            	);
            	$options[] = array(
            		'name' => 'Надпись на кнопке (состояние 2)',
            		'desc' => 'Состояние 2 - посты подгружаются.',
            		'id' => $cat_template_name.'_posts_ajax_load_more_but_caption_loading',
            		'std' => 'Загрузка постов...',
            		'class' => 'start-hidden',
            		'type' => 'text'
            	);	
            	$options[] = array(
            		'name' => 'Надпись на кнопке (состояние 3)',
            		'desc' => 'Состояние 3 - больше нет постов для подгрузки.',
            		'id' => $cat_template_name.'_posts_ajax_load_more_but_caption_nomore',
            		'std' => 'Больше нет',
            		'class' => 'start-hidden',
            		'type' => 'text'
            	);
        }

    	$options[] = array(
    		'name' => 'Формат миниатюр',
    		'desc' => 'Здесь автоматически собраны данные по размерам из Настройки->Медиафайлы, а также из кода темы и плагинов.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_thumb_format',
    		'std' => 'thumbnail',
    		'type' => 'select',
    		'class' => 'tiny start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list', //mini, tiny, small
    		'options' => $image_sizes, //Составление массива в начале функции.
        );
    	$options[] = array(
    		'name' => 'Шрифт заголовков элементов',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_font_header',
    		'std' => array( 'size' => '28px', 'face' => 'Georgia, serif', 'color' => '#333333'),
    		'class' => 'tiny start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list', //mini, tiny, small
    		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
        	'options' => array(
        		'faces' => $typography_fonts,
        		'styles' => $typography_fonts_weight,
        	),
        	'css_selector' => $block_css_selector.' .posts .entry-header .entry-title a', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'На полную ширину экрана',
    		'desc' => 'Если не выбрана, то блок растянется на ширину контентного блока.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_wide',
    		'std' => '0',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-posts-list',
    		'type' => 'checkbox'
    	);

    	//Поле ввода.
    	$options[] = array(
    		'name' => 'Фон слоя с контентом',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_textarea_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-textarea',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    		'css_selector' => $block_css_selector.' .textarea', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Поле ввода',
    		'desc' => '',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_textarea_text',
    		'std' => '',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-textarea',
    		'type' => 'textarea'
    	);
    	
    	//Баннер.
    	$options[] = array(
    		'name' => 'Фон слоя с контентом',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_banner_background',
    		'std' => $background_defaults,
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-banner',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    		'css_selector' => $block_css_selector.' .banner', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Высота баннера',
    		'desc' => '200px, 100%',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_banner_height',
    		'std' => '300px',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' show-banner',
    		'type' => 'text',
    		'css_prop' => 'height', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .banner', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	
        //Дополнительный CSS блока
    	$options[] = array(
    		'name' => 'Дополнительный CSS блока',
    		'desc' => 'Для нацеливания CSS на этот блок добавляем преселектор: '.$block_css_selector.' Для комментирования используем только /* */',
    		'id' => $bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_css',
    		'std' => '',
    		'class' => 'start-hidden-select '.$bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.' notshow-none',
    		'type' => 'textarea',
    		'css_selector' => 'rules', //rules - значит в dynamic.less будет записано содержимое поля в качестве полных CSS-правил.
    	);
	}

    return $options;
}


//Билдер блоков для статических страниц и категорий.
function bo_blocks_builder($options, $bb_id, $bb_name, $params_all) {
    //В $params_all переданы: $background_defaults, $image_sizes, $typography_fonts, $typography_fonts_weight, $elements_view, $active_sidebars
    
    $blocks = array(1,2,3,4,5,6);
    $base_css_selector = '.'.$bb_id;

	$options[] = array(
		'name' => mb_substr($bb_name,0,20), //substr почему-то не отрабатывает корректно
		'type' => 'heading'
	);

    /*
	$options[] = array(
        'name' => '============ Билдер страниц ============',
        'desc' => '',
        'type' => 'info'
	);
	*/
	
	//Собираем блоки в соответствии с их порядковыми номерами.
	$blocks_order = array();
	foreach($blocks as $block_inc) {
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
	
	foreach($blocks as $block_inc) {
	//foreach ($blocks_order as $block_inc) {
	    $block_css_selector = $base_css_selector.'.block-'.$block_inc;
	    
    	$options[] = array(
    		'name' => 'Тип блока',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc,
    		'std' => 'none',
    		'type' => 'select',
    		'class' => 'tiny has-hidden-select admin-first-in-block', //mini, tiny, small
    		'options' => array(
        		'none' => 'Отсутствует',
        		'slider-slick' => 'Слайдер slick',
        		'posts-list' => 'Анонсы постов',
        		'textarea' => 'Поле ввода',
        		'banner' => 'Баннер',
    	    ),
        );
        
        //Позиция блока.
    	$options[] = array(
    		'name' => 'Позиция блока',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc.'_order',
    		'std' => '180',
    		'type' => 'select',
    		'class' => 'mini start-hidden-select '.$bb_id.'_b'.$block_inc.' notshow-none', //mini, tiny, small
    		'options' => array(
        		1 => '1ый',
        		20 => '2ой',
        		40 => '3ий',
        		60 => '4ый',
        		80 => '5ый',
        		100 => '6ой',
        		120 => '7ой',
        		140 => '8ой',
        		160 => '9ый',
        		180 => '10ый',
    	    ),
        );

        //Margin блока
    	$options[] = array(
    		'name' => 'Margin блока',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc.'_margin',
    		'std' => '0',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' notshow-none',
    		'type' => 'text',
    		'css_prop' => 'margin', //Наличие css_prop является признаком объявления переменной для LESS.
		    'css_selector' => $block_css_selector, //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);

        //Заголовок блока
    	$options[] = array(
    		'name' => 'Заголовок блока',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc.'_h_text',
    		'std' => '',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' notshow-none',
    		'type' => 'text',
    	);
        	$options[] = array(
        		'name' => 'Шрифт заголовка блока',
        		'desc' => '',
        		'id' => $bb_id.'_b'.$block_inc.'_h_font',
        		'std' => array( 'size' => '28px', 'face' => 'Georgia, serif', 'color' => '#333333'),
        		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' notshow-none',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $params_all['typography_fonts'],
            		'styles' => $params_all['typography_fonts_weight'],
            	),
            	'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	$options[] = array(
        		'name' => 'Выравнивание заголовка блока',
        		'desc' => '',
        		'id' => $bb_id.'_b'.$block_inc.'_h_align',
        		'std' => 'center',
        		'type' => 'select',
        		'class' => 'tiny start-hidden-select '.$bb_id.'_b'.$block_inc.' notshow-none', //mini, tiny, small
        		'options' => array(
        		    'left' => 'По левому краю',
        		    'center' => 'По центру',
            		'right' => 'По правому краю',
        	    ),
        	    'css_prop' => 'text-align', //Наличие css_prop является признаком объявления переменной для LESS.
        	    'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            );
        	$options[] = array(
        		'name' => 'Margin заголовка блока',
        		'desc' => '',
        		'id' => $bb_id.'_b'.$block_inc.'_h_margin',
        		'std' => '0',
        		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' notshow-none',
        		'type' => 'text',
        		'css_prop' => 'margin', //Наличие css_prop является признаком объявления переменной для LESS.
    		    'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	$options[] = array(
        		'name' => 'Нижний бордер заголовка блока',
        		'desc' => 'Например: 1px solid #eeeeee',
        		'id' => $bb_id.'_b'.$block_inc.'_h_bborder',
        		'std' => '0',
        		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' notshow-none',
        		'type' => 'text',
        		'css_prop' => 'border-bottom', //Наличие css_prop является признаком объявления переменной для LESS.
    		    'css_selector' => $block_css_selector.' .block-header', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);





        //=== Слайдер slick.
    	$options[] = array(
    		'name' => 'Фон блока',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bb_id.'_b'.$block_inc.'_slider_backg',
    		'std' => $params_all['background_defaults'],
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-slider-slick',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
		    'css_selector' => $block_css_selector.' .slider-slick', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Высота слайдера',
    		'desc' => '200px, 100%',
    		'id' => $bb_id.'_b'.$block_inc.'_slider_height',
    		'std' => '300px',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-slider-slick',
    		'type' => 'text',
    		'css_prop' => 'height', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .slider-slick', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Высота изображений (max-height)',
    		'desc' => '200px, 100%',
    		'id' => $bb_id.'_b'.$block_inc.'_slider_image_height',
    		'std' => '100%',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-slider-slick',
    		'type' => 'text',
    		//Именно max-height, а не height.
    		'css_prop' => 'max-height', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .slider-slick .slick-slide img', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	
    	$options[] = array(
    		'name' => 'Прозрачность слайдера (opacity)',
    		'desc' => 'от 0.00 до 1',
    		'id' => $bb_id.'_b'.$block_inc.'_slider_opacity',
    		'std' => '1',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-slider-slick',
    		'type' => 'text',
    		'css_prop' => 'opacity', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .slider-slick .slick-slide img', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'На полную ширину экрана',
    		'desc' => 'Если не выбрана, то слайдер растянется на ширину контентного блока.',
    		'id' => $bb_id.'_b'.$block_inc.'_slider_wide',
    		'std' => '1',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-slider-slick',
    		'type' => 'checkbox'
    	);
    	$options[] = array(
    		'name' => 'Код слайдера (slick)',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc.'_slider_code',
    		'std' => '',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-slider-slick',
    		'type' => 'textarea'
    	);
    	
    	
    	
    	
    	
    	//=== Анонсы постов.
    	$options[] = array(
    		'name' => 'Фон блока',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bb_id.'_b'.$block_inc.'_postsann_backg',
    		'std' => $params_all['background_defaults'],
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    		'css_selector' => $block_css_selector.' .posts', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Вид анонсов',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc.'_postsann_view',
    		'std' => 'list',
    		'type' => 'select',
    		'class' => 'tiny start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list', //mini, tiny, small
    		'options' => $params_all['elements_view'],
        );
		
		$temp = array(
			'thumb-above' => 'Thumb + снизу: заголовок',
			'thumb-above-2' => 'Thumb + снизу: заголовок, мета',
			'thumb-above-3' => 'Thumb + снизу: заголовок, мета, отрывок',
			'thumb-under' => 'Заголовок + снизу: thumb',
			'thumb-under-2' => 'Заголовок, мета + снизу: thumb',
			'thumb-under-3' => 'Заголовок, мета, отрывок + снизу: thumb',
		);
		if (quark_is_affiliateegg_active()) {
			$temp['egg-thumb'] = 'Egg+Миниатюра';
			$temp['egg-discount'] = 'Egg-скидки';
		};
    	$options[] = array(
    		'name' => 'Макет анонса',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc.'_postsann_mockup',
    		'std' => 'thumb-above',
    		'type' => 'select',
    		'class' => 'tiny start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list', //mini, tiny, small
    		'options' => $temp,
        );
        	$options[] = array(
        		'name' => 'Фон анонса',
        		'desc' => 'Цвет и/или изображение/паттерн.',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_backg',
        		'std' => $params_all['background_defaults'],
        		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
        		'type' => 'background', //Для типа background задавать css_prop не нужно.
        		'css_selector' => $block_css_selector.' #posts-list .post', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	$options[] = array(
        		'name' => 'Формат миниатюр',
        		'desc' => 'Здесь автоматически собраны данные по размерам из Настройки->Медиафайлы, а также из кода темы и плагинов.',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_thumb_format',
        		'std' => 'thumbnail',
        		'type' => 'select',
        		'class' => 'tiny start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list', //mini, tiny, small
        		'options' => $params_all['image_sizes'], //Составление массива в начале функции.
            );
        	$options[] = array(
        		'name' => 'Тень для элементов',
        		'desc' => '',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_item_shadow',
        		'std' => 'none',
        		'type' => 'select',
        		'class' => 'tiny start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list', //mini, tiny, small
        		'options' => array(
            		'none' => 'Отсутствует',
            		'0 1px 2px rgba(0, 0, 0, 0.3)' => 'Средняя тень', //Для передачи через переменную LESS тень нужно передавать в доп. кавычках.
        	    ),
        	    'css_prop' => 'box-shadow', //Наличие css_prop является признаком объявления переменной для LESS.
        	    'css_selector' => $block_css_selector.' #posts-list .post', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            );
        	$options[] = array(
        		'name' => 'Шрифт заголовка',
        		'desc' => '',
        		'id' => $bb_id.'_b'.$block_inc.'_posts_font_h',
        		'std' => array( 'size' => '24px', 'face' => 'Georgia, serif', 'color' => '#333333'),
        		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $params_all['typography_fonts'],
            		'styles' => $params_all['typography_fonts_weight'],
            	),
            	'css_selector' => $block_css_selector.' #posts-list .post .entry-header .entry-title a', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	$options[] = array(
        		'name' => 'Шрифт текста',
        		'desc' => '',
        		'id' => $bb_id.'_b'.$block_inc.'_posts_font_excerpt',
        		'std' => array( 'size' => '16px', 'face' => 'Georgia, serif', 'color' => '#666666'),
        		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
        		'type' => 'typography', //Для типа typography задавать css_prop не нужно.
            	'options' => array(
            		'faces' => $params_all['typography_fonts'],
            		'styles' => $params_all['typography_fonts_weight'],
            	),
            	'css_selector' => $block_css_selector.' #posts-list .post .entry-summary p', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);

    	$options[] = array(
    		'name' => 'Анонсы из указанных категорий',
    		'desc' => 'Иначе будут выводиться анонсы из внутренних категорий.',
    		'id' => $bb_id.'_b'.$block_inc.'_postsann_cats',
    		'std' => '1',
    		'class' => 'has-hidden start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
    		'type' => 'checkbox'
    	);
    	$options[] = array(
    		'name' => 'Slug`и категорий',
    		'desc' => 'Через запятую и без пробелов, например, "obrazy,skidki"',
    		'id' => $bb_id.'_b'.$block_inc.'_postsann_cats_slug',
    		'std' => '',
    		'class' => 'start-hidden start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
    		'type' => 'text'
    	);
    	$options[] = array(
    		'name' => 'Кол-во анонсов для вывода',
    		'desc' => 'В случае использования AJAX-подгрузки данное кол-во анонсов будет запрашиваться при Load More. ',
    		'id' => $bb_id.'_b'.$block_inc.'_postsann_count',
    		'std' => '12',
    		'class' => 'mini start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
    		'type' => 'text'
    	);

    	//AJAX-подгрузка постов.
    	$options[] = array(
    		'name' => 'Включить AJAX-подгрузку',
    		'desc' => 'Стандартная пагинация заменяется одной кнопкой с AJAX-подгрузкой постов.',
    		'id' => $bb_id.'_b'.$block_inc.'_postsann_loadmore',
    		'std' => '1',
    		'class' => 'start-hidden has-hidden start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
    		'type' => 'checkbox'
    	);
        	$options[] = array(
        		'name' => 'Ширина кнопки',
        		'desc' => '',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_loadmore_but_width',
        		'std' => '30%',
        		'type' => 'select',
        		'class' => 'start-hidden tiny start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list', //mini, tiny, small
        		'options' => array(
            		'10%' => '10%',
            		'20%' => '20%',
            		'30%' => '30%',
            		'40%' => '40%',
            		'50%' => '50%',
            		'60%' => '60%',
            		'70%' => '70%',
            		'80%' => '80%',
            		'90%' => '90%',
            		'100%' => '100%',
        	    ),
        	    'css_prop' => 'width', //Наличие css_prop является признаком объявления переменной для LESS.
        	    'css_selector' => $block_css_selector.' #load-more-posts-but button', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
            );
            /* Здесь есть особенность - стили нужно накладывать конкретно на целевую кнопку и в вариациях для градиента, hover и др. 
        	$options[] = array(
        		'name' => 'Фон кнопки',
        		'desc' => 'Цвет и/или изображение/паттерн.',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_loadmore_but_backg',
        		'std' => $params_all['background_defaults'],
        		'class' => 'start-hidden',
        		'type' => 'background', //Для типа background задавать css_prop не нужно.
        	    'css_selector' => $block_css_selector.' #load-more-posts-but button', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
        	);
        	*/
        	$options[] = array(
        		'name' => 'Надпись на кнопке (состояние 1)',
        		'desc' => 'Состояние 1 - есть еще посты для подгрузки.',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_loadmore_but_caption_more',
        		'std' => 'Показать еще',
        		'class' => 'start-hidden start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
        		'type' => 'text'
        	);
        	$options[] = array(
        		'name' => 'Надпись на кнопке (состояние 2)',
        		'desc' => 'Состояние 2 - посты подгружаются.',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_loadmore_but_caption_loading',
        		'std' => 'Загрузка постов...',
        		'class' => 'start-hidden start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
        		'type' => 'text'
        	);	
        	$options[] = array(
        		'name' => 'Надпись на кнопке (состояние 3)',
        		'desc' => 'Состояние 3 - больше нет постов для подгрузки.',
        		'id' => $bb_id.'_b'.$block_inc.'_postsann_loadmore_but_caption_nomore',
        		'std' => 'Больше нет',
        		'class' => 'start-hidden start-hidden-select '.$bb_id.'_b'.$block_inc.' show-posts-list',
        		'type' => 'text'
        	);




    	//=== Поле ввода.
    	$options[] = array(
    		'name' => 'Фон блока',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bb_id.'_b'.$block_inc.'_textarea_backg',
    		'std' => $params_all['background_defaults'],
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-textarea',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    		'css_selector' => $block_css_selector.' .textarea', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Поле ввода',
    		'desc' => '',
    		'id' => $bb_id.'_b'.$block_inc.'_textarea_text',
    		'std' => '',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-textarea',
    		'type' => 'textarea'
    	);
    	
    	
    	
    	
    	//=== Баннер.
    	$options[] = array(
    		'name' => 'Фон блока',
    		'desc' => 'Цвет и/или изображение/паттерн.',
    		'id' => $bb_id.'_b'.$block_inc.'_banner_backg',
    		'std' => $params_all['background_defaults'],
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-banner',
    		'type' => 'background', //Для типа background задавать css_prop не нужно.
    		'css_selector' => $block_css_selector.' .banner', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
    	$options[] = array(
    		'name' => 'Высота баннера',
    		'desc' => '200px, 100%',
    		'id' => $bb_id.'_b'.$block_inc.'_banner_height',
    		'std' => '300px',
    		'class' => 'start-hidden-select '.$bb_id.'_b'.$block_inc.' show-banner',
    		'type' => 'text',
    		'css_prop' => 'height', //Наличие css_prop является признаком объявления переменной для LESS.
    		'css_selector' => $block_css_selector.' .banner', //Значение данной опции будет записано в dynamic.less в селектор из css_selector для свойства css_prop (если задан).
    	);
	}

    return $options;
}