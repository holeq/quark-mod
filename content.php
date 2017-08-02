<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package Quark
 * @since Quark 1.0
 */

global $wp_session;
global $bo_post_type;
global $current_cat_id;
global $current_cat_template_id;
global $lazyload_image;

//Цели для Я.Метрики.
$ym_counter_id = sanitize_text_field(of_get_option('bo_yandex_metrica_id', ''));
$ym_target_echo = ($ym_counter_id ? ' onclick="yaCounter'.$ym_counter_id.'.reachGoal(\'click-affegg\'); return true;"' : '');

$upload_dir = wp_upload_dir();




//================================= ЕСЛИ ТИП СТРАНИЦЫ: ПОСТ =================================
if (is_single()) {
    
    //================================= ЕСЛИ ВИД ПОСТА: Слайдер слева + Контент справа =================================
    if (of_get_option('bo_post_'.$bo_post_type.'_template', 'usual') == 'slider-content') {
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('isotope-item post-'.$bo_post_type); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
<?php
                quark_posted_on();

                //Вывод миниатюры или ее заглушки.
    		    if (of_get_option('bo_post_'.$bo_post_type.'_thumb', '0')) {
    		        if (has_post_thumbnail()) {
                        $cur_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), of_get_option('bo_post_'.$bo_post_type.'_thumb_format', 'thumbnail'));
                        if ($lazyload_image) {
                            echo '<img class="show-loader" src="" data-src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"><noscript><img src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"></noscript>';
                        } else {
                            echo '<img class="show-loader" src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'">';
                        }
    		        } elseif ($thumb_gag = of_get_option('bo_post_'.$bo_post_type.'_thumb_gag', '')) {
    		            echo '<img class="thumb-gag" src="'.$thumb_gag.'">';
    		        }
		        }
?>
            </header> <!-- /.entry-header -->
            
    		<div class="entry-content">
    		    <div class="col grid_5_of_12">
<?php
                    if (bo_get_field($post->ID, 'bo_cf_post_attach', of_get_option('bo_post_attach_default', 'none')) == 'first') {
                        
                        //Получаем и выводим изображения прикрепленные к этому посту в виде слайдера.
                        $args = array(
                         'post_type' => 'attachment',
                         'numberposts' => -1,
                         'orderby'=> 'menu_order',
                         'order' => 'ASC',
                         'post_mime_type' => 'image',
                         'post_status' => null,
                         'post_parent' => $post->ID
                        );
                        
                        $attachments = get_posts($args);
                        if ($attachments) {
                            $inc_images = 0;
                            $images_large_output = '';
                            $images_thumb_output = '';
                            foreach ($attachments as $attachment) {
                                $inc_images++;
                                //echo '<li>'.wp_get_attachment_image($attachment->ID , 'large').'</li>';
                                $image_large_attributes = wp_get_attachment_image_src($attachment->ID, 'large');
                                $images_large_output .= '<div><img class="show-loader" src="'.$image_large_attributes[0].'" width="'.$image_large_attributes[1].'" height="'.$image_large_attributes[2].'"></div>';
    
                                $image_thumb_attributes = wp_get_attachment_image_src($attachment->ID, 'thumbnail');
                                $images_thumb_output .= '<div><img class="show-loader" src="'.$image_thumb_attributes[0].'" width="'.$image_thumb_attributes[1].'" height="'.$image_thumb_attributes[2].'"></div>';                            
                            }
?>
                            <div id="post-slider" class="attach-first">
                                <div class="attach-slider">
<?php
                                    echo $images_large_output;
?>
                                </div>
                            </div> <!-- /#post-slider -->

                            <script type='text/javascript'>
                            jQuery('.attach-slider').slick({
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: true,
                                fade: false,
                                mobileFirst: true,
                                dots: true,
                                prevArrow: '<i class="slider-prev fa fa-chevron-left fa-2x">',
                                nextArrow: '<i class="slider-next fa fa-chevron-right fa-2x">'
                            });
                            </script>
<?php
                		    //Вывод соцкнопок "Поделиться" под слайдером.
                	        if (of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos2', '0')) {
                	            echo '<div class="social-share under-slider">'.of_get_option('bo_uptolike_share_html', '').'</div>';
                	        }






//Это вариант со основным слайдером и его навигационным слайдером из тумбов. Здесь нужно продумать мобильный вариант и вертикальные тумбы навигации.
if (1>2) {
?>
                        <div id="post-slider" class="attach-first" style="width:500px">
                            <div class="slider-for">
<?php
                                echo $images_large_output;
?>
                            </div> <!-- /.slider-for -->

                            
                            <div class="slider-nav">
<?php
                                echo $images_thumb_output;
?>
                            </div> <!-- /.slider-nav -->
                        </div> <!-- /#post-slider -->

<script type='text/javascript'>
jQuery('.slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: false,
    asNavFor: '.slider-nav',
    mobileFirst: true
});
jQuery('.slider-nav').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    asNavFor: '.slider-for',
    dots: true,
    centerMode: true,
    focusOnSelect: true,
    mobileFirst: true,
    vertical: true
});
</script>

<?php
}





                        }
                    }
?>
                </div> <!-- /.col.grid_5_of_12 -->
                
                <div class="col grid_7_of_12">
<?php
        		    //Вывод соцкнопок "Поделиться".
        	        if (of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_post' || of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_after_post') {
        	            echo '<div class="social-share">'.of_get_option('bo_uptolike_share_html', '').'</div>';
        	        }
        	        
    			    the_content();
    			    
            	    //Вывод соцкнопок "Поделиться".
                    if (of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'after_post' || of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_after_post') {
                        echo '<div class="social-share">'.of_get_option('bo_uptolike_share_html', '').'</div>';
                    }
?>
    			</div> <!-- /.col.grid_7_of_12 -->
    	    </div> <!-- /.entry-content -->
    	    
    	    <footer class="entry-meta">
<?php
                quark_entry_meta();

        		if (get_the_author_meta( 'description' ) && is_multi_author()) {
        			// If a user has filled out their description and this is a multi-author blog, show their bio
        			get_template_part('author-bio');
        		}
?>
            </footer> <!-- /.entry-meta -->
        </article> <!-- /#post -->
<?php

    






    //================================= ЕСЛИ ВИД ПОСТА: Обычное представление =================================
    } elseif (of_get_option('bo_post_'.$bo_post_type.'_template', 'usual') == 'usual') {

?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('isotope-item post-'.$bo_post_type); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
<?php
                quark_posted_on();
                
    		    //Вывод соцкнопок "Поделиться".
    	        if (of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_post' || of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_after_post') {
    	            echo '<div class="social-share">'.of_get_option('bo_uptolike_share_html', '').'</div>';
    	        }
    	        
    	        //Вывод миниатюры или ее заглушки.
    		    if (of_get_option('bo_post_'.$bo_post_type.'_thumb', '0')) {
    		        if (has_post_thumbnail()) {
                        $cur_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), of_get_option('bo_post_'.$bo_post_type.'_thumb_format', 'thumbnail'));
                        if ($lazyload_image) {
                            echo '<img class="show-loader" src="" data-src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"><noscript><img src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"></noscript>';
                        } else {
                            echo '<img class="show-loader" src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'">';
                        }
    		        } elseif ($thumb_gag = of_get_option('bo_post_'.$bo_post_type.'_thumb_gag', '')) {
    		            echo '<img class="thumb-gag" src="'.$thumb_gag.'">';
    		        }
		        }
?>
            </header> <!-- /.entry-header -->
            
    		<div class="entry-content">
<?php
                if (bo_get_field($post->ID, 'bo_cf_post_attach', of_get_option('bo_post_attach_default', 'none')) == 'first') {
                    //Получаем и выводим изображения прикрепленные к этому посту.
                    $args = array(
                     'post_type' => 'attachment',
                     'numberposts' => -1,
                     'orderby'=> 'menu_order',
                     'order' => 'ASC',
                     'post_mime_type' => 'image',
                     'post_status' => null,
                     'post_parent' => $post->ID
                    );
                    
                    $attachments = get_posts($args);
                    if ($attachments) {
                        $inc_images = 0;
?>
                        <div id="post-slider" class="attach-first">
<?php
                            foreach ($attachments as $attachment) {
                                $inc_images++;
                                //echo '<li>'.wp_get_attachment_image($attachment->ID , 'large').'</li>';
                                $image_attributes = wp_get_attachment_image_src($attachment->ID, 'large');
    
                                //if (current_user_can('edit_themes')) {
                                    if ($lazyload_image) {
                                        //if ($inc_images <= 2) { //Первые 2 изображения загружаем сразу.
                                        //    echo '<img class="show-loader" src="'.$image_attributes[0].'" width="'.$image_attributes[1].'" height="'.$image_attributes[2].'">';
                                        //} else {
                                            echo '<img class="show-loader" src="" data-src="'.$image_attributes[0].'" width="'.$image_attributes[1].'" height="'.$image_attributes[2].'"><noscript><img src="'.$image_attributes[0].'" width="'.$image_attributes[1].'" height="'.$image_attributes[2].'"></noscript>';
                                        //}
                                    } else {
                                        echo '<img class="show-loader" src="'.$image_attributes[0].'" width="'.$image_attributes[1].'" height="'.$image_attributes[2].'">';
                                    }
                            }
?>
                        </div> <!-- /#post-slider -->
<?php
                    }
                }
    
    			the_content(wp_kses(__('Читать далее <span class="meta-nav">&rarr;</span>', 'quark'), array('span' => array(
    				'class' => array())))
    			);
        				
    			wp_link_pages(array(
    				'before' => '<div class="page-links">Страниц:',
    				'after' => '</div>',
    				'link_before' => '<span class="page-numbers">',
    				'link_after' => '</span>'
    			));
?>
    	    </div> <!-- /.entry-content -->
    	    
    	    <footer class="entry-meta">
<?php
        	    //Вывод соцкнопок "Поделиться".
                if (of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'after_post' || of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_after_post') {
                    echo '<div class="social-share">'.of_get_option('bo_uptolike_share_html', '').'</div>';
                }
        
                quark_entry_meta();
        
        		if (get_the_author_meta( 'description' ) && is_multi_author()) {
        			// If a user has filled out their description and this is a multi-author blog, show their bio
        			get_template_part('author-bio');
        		}
?>
            </footer> <!-- /.entry-meta -->
        </article> <!-- /#post -->
<?php







    //================================= ЕСЛИ ВИД ПОСТА: Карточка товара AffEgg =================================
    } elseif (of_get_option('bo_post_'.$bo_post_type.'_template', 'usual') == 'egg-product-card') {
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('isotope-item post-'.$bo_post_type); ?>>

    		<div class="entry-content">
    		    <div class="col grid_5_of_12">
<?php
                    //Вывод фото товара или ее заглушки.
                    $product_image = bo_get_field($post->ID, 'bo_cf_product_image', '');
                    if ($product_image && file_exists($upload_dir['basedir'].'/'.$product_image)) {
                        echo '<img class="show-loader" src="'.$upload_dir['baseurl'].'/'.$product_image.'">';
                    } elseif ($thumb_gag = of_get_option('bo_post_'.$bo_post_type.'_thumb_gag', '')) {
    		            echo '<img class="thumb-gag" src="'.$thumb_gag.'">';
    		        }
?>
                </div> <!-- /.col.grid_5_of_12 -->
                
                <div class="col grid_7_of_12">

                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>

		                <div class="header-meta"><span class="post-categories"><?php echo get_the_category_list(' '); ?></span></div>
                    </header> <!-- /.entry-header -->
<?php
        		    //Вывод соцкнопок "Поделиться".
        	        if (of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_post' || of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_after_post') {
        	            echo '<div class="social-share">'.of_get_option('bo_uptolike_share_html', '').'</div>';
        	        }
        	        
        	        //Контент намеренно не выводим, поскольку Карточки товара жестко структурирована данными из произвольных полей.
    			    //the_content();
?>
                    <div class="product-description"><?php echo bo_ucfirst(bo_get_field($post->ID, 'bo_cf_product_description', '')); ?></div>
                    
                    <div class="out">
        			    <a class="bo-btn" href="<?php echo str_ireplace('bo_tmp_subid', $wp_session['user_hash'], bo_get_field($post->ID, 'bo_cf_product_cpa_url', '')); ?>" target="blank"<?php echo $ym_target_echo; ?>>
        			    <i class="fa fa-info-circle"></i> Подробнее</a>
    			    </div>
<?php
            	    //Вывод соцкнопок "Поделиться".
                    if (of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'after_post' || of_get_option('bo_post_'.$bo_post_type.'_uptolike_share_pos', '') == 'under_after_post') {
                        echo '<div class="social-share">'.of_get_option('bo_uptolike_share_html', '').'</div>';
                    }
?>
    			</div> <!-- /.col.grid_7_of_12 -->
    	    </div> <!-- /.entry-content -->
    	    
    	    <footer class="entry-meta">
<?php
                quark_entry_meta();

        		if (get_the_author_meta( 'description' ) && is_multi_author()) {
        			// If a user has filled out their description and this is a multi-author blog, show their bio
        			get_template_part('author-bio');
        		}
?>
            </footer> <!-- /.entry-meta -->
        </article> <!-- /#post -->
<?php

    }







//================================= ЕСЛИ ТИП СТРАНИЦЫ: ВСЕ КРОМЕ ПОСТА =================================
} else {
    
    //Макет анонса поста.
    $postsann_mockup = sanitize_text_field(of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_mockup', 'thumb-above'));
    //Тип поста.
    $bo_post_type = bo_get_field($post->ID, 'bo_cf_post_type', of_get_option('bo_post_type_default', 'post'));
    
    //Маркер поверх миниатюры: кол-во приаттаченых изображений.
    if (of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_attimgs', '0') &&
        $postsann_attimgs_input = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_attimgs_input', '')) {
        $postsann_attimgs_pos = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_attimgs_pos', 'v-top h-left');
    }
    //Маркер поверх миниатюры: дата публикации поста.
    if (of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pubdate', '0') &&
        $postsann_pubdate_input = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pubdate_input', '')) {
        $postsann_pubdate_pos = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pubdate_pos', 'v-top h-left');
    }
    //Маркер поверх миниатюры: кол-во egg-товаров.
    if (of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_eggs', '0') &&
        $postsann_eggs_input = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_eggs_input', '')) {
        $postsann_eggs_pos = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_eggs_pos', 'top-left');
    }
    
    /*
    //Получаем кол-во элементов в 1 ряду. Это необходимо для разметки последних элементов ряда.
    $items_in_row = 0;
    if (stripos($postsann_mockup, 'masonry-2cols') !== false) {
        $items_in_row = 2;
    } elseif (stripos($postsann_mockup, 'masonry-3cols') !== false) {
        $items_in_row = 3;
    } elseif (stripos($postsann_mockup, 'masonry-4cols') !== false) {
        $items_in_row = 4;
    }
    global $inc_items_in_row;
    */
    
    //================================= ЕСЛИ МАКЕТ АНОНСА: EGG-THUMB =================================
    if ($postsann_mockup == 'egg-thumb') {

        //Собираем Egg шорткоды.
        $content = get_the_content();
    
        $arr_shortcodes = array();
        $pos_begin = stripos($content, '[affegg'); //[affegg id=60]
        while ($pos_begin !== false) {
            $pos_end = stripos($content, ']', $pos_begin);
            $arr_shortcodes[] = substr($content, $pos_begin, $pos_end-$pos_begin+1);
    
            $pos_begin = stripos($content, '[affegg', $pos_begin+1);
        }
        //var_dump($arr_shortcodes);
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class($postsann_mockup.' isotope-item post-'.$bo_post_type); ?>>
    		<div class="entry-title">
    			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
    		</div>
    
            <div class="entry-egg-thumb clearfix">
                <div class="col grid_8_of_12">
                    <div class="egg">
                        <?php echo do_shortcode(implode('', $arr_shortcodes)); ?>
                    </div>
                </div>
                
                <div class="col grid_4_of_12">
                    <div class="thumb has-overblocks">
            			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>">
<?php
                            $cur_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_thumb_format', 'thumbnail'));
                            if ($lazyload_image) {
                                echo '<img class="test-img show-loader" src="" data-src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"><noscript><img src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"></noscript>';
                            } else {
                                echo '<img class="test-img show-loader" src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'">';
                            }
                            
                            //OVER-таркеры.
                            the_overmarkers($post->ID, 'post_attached_images', $postsann_attimgs_input, $postsann_attimgs_pos);
                            the_overmarkers($post->ID, 'post_pub_date', $postsann_pubdate_input, $postsann_pubdate_pos);
?>
            			</a>
        			</div> <!-- /.thumb.has-overblocks -->
    			</div>
    		</div>
        
        
        </article> <!-- /#post -->
<?php







    //================================= ЕСЛИ МАКЕТ АНОНСА: EGG-DISCOUNT =================================
    } elseif ($postsann_mockup == 'egg-discount') {

        //================================= ЕСЛИ ТИП ПОСТА АНОНСА: КАРТОЧКА ТОВАРА =================================
        if ($bo_post_type == 'product-card') {
            
            //Выводим карточку товара, если товар есть на стоке и скидка по нему больше 39%.
            //Данная проверка заложена ранее в основном запросе WP_Query (функция create_tax_tech_tags в functions.php).
            //if (bo_get_field($post->ID, 'bo_cf_product_in_stock', '0') > 0 && bo_get_field($post->ID, 'bo_cf_product_price_disc', '0') > 39) {

            $egg_id = bo_get_field($post->ID, 'bo_cf_product_egg_id', '');
            if ($egg_id) {
?>
                <article id="post-<?php the_ID(); ?>" <?php post_class($postsann_mockup.' isotope-item post-'.$bo_post_type); ?>>
                    <div class="entry-egg clearfix">
                        <div class="col grid_12_of_12">
                            <div class="egg">
                                <?php echo do_shortcode('[affegg id='.$egg_id.']'); ?>
                            </div>
                        </div>
            		</div>
                </article> <!-- /#post -->
<?php
            }

            //Пока нельзя формировать анонсы Карточек товара из мета-данных поста, поскольку, например, при смене в опциях AffEgg партнерской ссылки, придеться
            //как-то корректировать все посты.
            if (1>2) {
            $product_manufacturer = bo_get_field($post->ID, 'bo_cf_product_manufacturer', '');
            $product_title = bo_get_field($post->ID, 'bo_cf_product_title', '');
            $product_price_disc = bo_get_field($post->ID, 'bo_cf_product_price_disc', '');
            
            $product_image = bo_get_field($post->ID, 'bo_cf_product_image', '');
            if (!$product_image || !file_exists($upload_dir['basedir'].'/'.$product_image)) {
                $product_image = '2015/04/no-thumb.png';
            }
            list($image_width, $image_height) = getimagesize($upload_dir['baseurl'].'/'.$product_image);
            
            //Добавляем сгенерированный user_hash для урла в качесиве subid.
            $bo_url = str_ireplace('bo_tmp_subid', $wp_session['user_hash'], esc_url($item['url']));
?>
            <article id="post-<?php the_ID(); ?>" <?php post_class($postsann_mockup.' isotope-item post-'.$bo_post_type); ?>>
                <div class="entry-egg clearfix">
                    <div class="col grid_12_of_12">
                        <div class="egg">
                            <div class="container-fluid">
                                <div class="row">                            
                                    <a rel="nofollow" target="_blank" href="<?php echo $bo_url; ?>"<?php echo $ym_target_echo; ?>>
                                        <div class="col-xs-12 egg-item productbox">
<?php 
                                            if ($product_price_disc) {
?>
                                                <div class="affegg-promotion">
                                                    <span class="affegg-discount">- <?php echo $product_price_disc; ?>%</span>
                                                </div>				
<?php
                                            }

                                            if ($lazyload_image) {
?>
                                                <img class="img-responsive show-loader" src="" data-src="<?php echo $upload_dir['baseurl']; ?>/<?php echo esc_attr($product_image) ?>" alt="<?php echo esc_attr($product_title); ?>" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" />
                                                <noscript><img class="img-responsive" src="<?php echo $upload_dir['baseurl']; ?>/<?php echo esc_attr($product_image) ?>" alt="<?php echo esc_attr($product_title); ?>" /></noscript>
<?php
                                            } else {
?>
                                                <img class="img-responsive" src="<?php echo $upload_dir['baseurl']; ?>/<?php echo esc_attr($product_image) ?>" alt="<?php echo esc_attr($product_title); ?>" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" />
<?php
                                            }
?>
                                            <div class="producttitle">
<?php
                                                if ($product_manufacturer) {
                                                    echo esc_html($product_manufacturer);
                                                }
?>
                                                <span><?php echo esc_html($product_title); ?></span>                  
                                            </div>

                                        </div>
                                    </a>
                                </div>
                            </div> <!-- /.container-fluid -->
                        </div>
                    </div>
        		</div>
            </article> <!-- /#post -->
<?php
            }
        
        //================================= ЕСЛИ ТИП ПОСТА АНОНСА: ВСЕ КРОМЕ КАРТОЧКИ ТОВАРА =================================
        } else {
        
            //Собираем Egg шорткоды.
            $content = get_the_content();
    
            $arr_shortcodes = array();
            $pos_begin = stripos($content, '[affegg'); //[affegg id=60]
            while ($pos_begin !== false) {
                $pos_end = stripos($content, ']', $pos_begin);
                $arr_shortcodes[] = substr($content, $pos_begin, $pos_end-$pos_begin+1);
        
                $pos_begin = stripos($content, '[affegg', $pos_begin+1);
            }
            //var_dump($arr_shortcodes);

?>
            <article id="post-<?php the_ID(); ?>" <?php post_class($postsann_mockup.' isotope-item post-'.$bo_post_type); ?>>
        		<div class="entry-title">
        			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
        		</div>

                <div class="entry-egg clearfix">
                    <div class="col grid_12_of_12">
                        <div class="egg">
                            <?php echo do_shortcode(implode('', $arr_shortcodes)); ?>
                        </div>
                    </div>
        		</div>
            </article> <!-- /#post -->
<?php
        }





    //========================== ЕСЛИ МАКЕТ АНОНСА: ОСТАЛЬНЫЕ КРОМЕ EGG-THUMB и EGG-DISCOUNT МАКЕТА ==========================
    
    //=== thumb-above
    } elseif ($postsann_mockup == 'thumb-above' || $postsann_mockup == 'thumb-above-2' || $postsann_mockup == 'thumb-above-3') {
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class($postsann_mockup.' isotope-item post-'.$bo_post_type); ?>>
            <header class="entry-header clearfix">
<?php
                if (has_post_thumbnail()) {
?>
                    <div class="entry-thumb has-overblocks">
        				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>">
<?php
                            $cur_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_thumb_format', 'thumbnail'));
                            if ($lazyload_image) {
                                echo '<img class="show-loader" src="" data-src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"><noscript><img src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"></noscript>';
                            } else {
                                echo '<img class="show-loader" src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'">';
                            }
                            
                            //OVER-таркеры.
                            the_overmarkers($post->ID, 'post_attached_images', $postsann_attimgs_input, $postsann_attimgs_pos);
                            the_overmarkers($post->ID, 'post_pub_date', $postsann_pubdate_input, $postsann_pubdate_pos);
?>
        				</a>
    				</div>
<?php
                }
?>
				<div class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</div>
<?php
				if ($postsann_mockup == 'thumb-above-2' || $postsann_mockup == 'thumb-above-3') {
				    quark_posted_on();
				}
?>
            </header> <!-- /.entry-header -->
            
<?php
			if ($postsann_mockup == 'thumb-above-3') {
?>
        		<div class="entry-summary">
        		    <noindex><?php the_excerpt(); ?></noindex>
        		</div> <!-- /.entry-summary -->
<?php
			}
?>
        </article> <!-- /#post -->
<?php



    //=== thumb-under
    } elseif ($postsann_mockup == 'thumb-under' || $postsann_mockup == 'thumb-under-2' || $postsann_mockup == 'thumb-under-3') {
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class($postsann_mockup.' isotope-item post-'.$bo_post_type); ?>>
            <header class="entry-header clearfix">
				<div class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</div>
<?php
				if ($postsann_mockup == 'thumb-under-2' || $postsann_mockup == 'thumb-under-3') {
				    quark_posted_on();
				}

                if (has_post_thumbnail()) {
?>
                    <div class="entry-thumb has-overblocks">
        				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>">
<?php
                            $cur_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_thumb_format', 'thumbnail'));
                            if ($lazyload_image) {
                                echo '<img class="show-loader" src="" data-src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"><noscript><img src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"></noscript>';
                            } else {
                                echo '<img class="show-loader" src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'">';
                            }
                            
                            //OVER-таркеры.
                            the_overmarkers($post->ID, 'post_attached_images', $postsann_attimgs_input, $postsann_attimgs_pos);
                            the_overmarkers($post->ID, 'post_pub_date', $postsann_pubdate_input, $postsann_pubdate_pos);
?>
        				</a>
    				</div>
<?php
                }
?>
            </header> <!-- /.entry-header -->
            
<?php
			if ($postsann_mockup == 'thumb-under-3') {
?>
        		<div class="entry-summary">
        		    <noindex><?php the_excerpt(); ?></noindex>
        		</div> <!-- /.entry-summary -->
<?php
			}
?>
        </article> <!-- /#post -->
<?php
    }
}
