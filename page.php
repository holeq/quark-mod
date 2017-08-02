<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Quark
 * @since Quark 1.0
 */

get_header();

//Вывод с вусивуг редактора.
if (1>2) {
?>
	<div id="primary" class="site-content row" role="main">
        <div class="row clearfix">
		    <div class="col grid_12_of_12">
<?php
    			if (have_posts()) {
                    while (have_posts()) {
                        the_post();
?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        	<?php if (is_front_page()) { ?>
                        		<header class="entry-header">
                        			<h1 class="entry-title"><?php the_title(); ?></h1>
                        			<?php if ( has_post_thumbnail() && !is_search() && !post_password_required() ) { ?>
                        				<?php the_post_thumbnail( 'post_feature_full_width' ); ?>
                        			<?php } ?>
                        		</header>
                        	<?php } ?>
                        	<div class="entry-content">
                        		<?php the_content(); ?>
                        		<?php wp_link_pages( array(
                        			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'quark' ),
                        			'after' => '</div>',
                        			'link_before' => '<span class="page-numbers">',
                        			'link_after' => '</span>'
                        		) ); ?>
                        	</div><!-- /.entry-content -->
                        	<footer class="entry-meta">
                        	</footer><!-- /.entry-meta -->
                        </article><!-- /#post -->
<?php
                    }
    			}
?>
            </div> <!-- /.col.grid_12_of_12 -->
        </div> <!-- /.row clearfix -->
    </div> <!-- /#primary.site-content.row -->
    
    
<?php
}
//Выводим заголовок страницы.
if (of_get_option('bo_page_'.$page_id.'_header', '1')) {
?>
    <div class="site-content row">
		<div class="row clearfix">
		    <div class="col grid_12_of_12">
		        <h1 class="page-title"><?php the_title(); ?></h1>
		    </div> <!-- /.col.grid_12_of_12 -->
		</div> <!-- /.row clearfix -->
	</div><!-- /.site-content.row -->
<?php
}

//Билдер страниц.

/*
if ($page_id == 8111) {
    quark_blocks_builder('bb1', false);
} else {
    quark_bo_page_builder('bo_page', $page_id, false);
}
*/


$bo_id_prefix = 'bo_page';
$bo_term_id = $page_id;

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
        
        //Контент на всю ширину экрана или по середине.
        $block_content_pre = $block_content_post = '';
        if (!of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_content_wide', '0')) {
            $block_content_pre = '<div class="middle-block row">'; $block_content_post = '</div> <!-- /.middle-block.row -->';
        }

//if (current_user_can('manage_options')) {
?>
        <div class="<?php echo $block_class; ?>">
<?php
            echo $block_content_pre;




            //Слайдер Slick.
            if ($block_type=='slider-slick' && of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_code', '')) {
?>
                <div class="slider-slick">
<?php
                    if ($block_header = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_text', '')) {
?>
                        <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                    }
                    echo of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_code', '');
?>
                </div> <!-- /.slider-slick -->
<?php
                       






            //Анонсы постов.
            } elseif ($block_type=='posts-list') {
                
                //Макет анонса поста.
                $posts_ann_mockup = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_mockup', 'thumb-above');
                /*
                if (current_user_can('manage_options')) {
                    $posts_ann_mockup = 'thumb-egg';
                } else {
                    $posts_ann_mockup = 'thumb-above';
                }
                */
    
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
            	if ($listing->have_posts()) {
?>
                    <div class="posts-ann">
<?php
                        if ($block_header = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_text', '')) {
?>
                            <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                        }
?>
                        <div id="posts-list" class="posts isotope <?php echo sanitize_text_field(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_view', 'list')); ?>">
<?php
                        	while ($listing->have_posts()) {
                        	    $listing->the_post();
                        	    global $post;
?>
                                <div id="post-<?php the_ID(); ?>" <?php post_class($posts_ann_mockup.' isotope-item'); ?>>
                                    <div class="entry-header clearfix">
<?php
                                        //============== ЕСЛИ МАКЕТ АНОНСА: Thumb + снизу: заголовок ==============
                                        if ($posts_ann_mockup == 'thumb-above') {
                                            if (has_post_thumbnail() && !is_search()) {
?>
                                                <div class="entry-thumb has-overblocks">
                                    				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>">
<?php
                                    					the_post_thumbnail(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_thumb_format', 'thumbnail'));
    
                                                        //OVER-таркеры.
                                                        the_overmarkers($post->ID, 'post_attached_images', '<span class="images-count">+%s фото</span>', 'v-bottom h-right', '');
                                                        the_overmarkers($post->ID, 'post_pub_date', '<span class="pub-date">%s</span>', 'v-bottom h-right');
?>
                                    				</a>
                                				</div>
<?php
                                            }
?>
                            				<div class="entry-title">
                            					<a href="<?php the_permalink(); ?>" class="max-zindex" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                            				</div>
<?php




                                        //============== ЕСЛИ МАКЕТ АНОНСА: Thumb + статический Egg ==============
                                        } elseif ($posts_ann_mockup == 'thumb-egg') {
?>
                            				<div class="entry-title">
                            					<a href="<?php the_permalink(); ?>" class="max-zindex" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                            				</div>
<?php
                                            if (has_post_thumbnail() && !is_search()) {
?>
                                                <div class="entry-thumb-egg clearfix">
                                                    <div class="col grid_5_of_12">
                                                        <div class="thumb has-overblocks">
                                        				    <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'quark' ), the_title_attribute( 'echo=0' ) ) ); ?>">
<?php
                                                                $cur_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_thumb_format', 'thumbnail'));
                                                                if ($lazyload_image) {
                                                                    echo '<img class="test-img show-loader" src="" data-src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"><noscript><img src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'"></noscript>';
                                                                } else {
                                                                    echo '<img class="test-img show-loader" src="'.$cur_thumb[0].'" width="'.$cur_thumb[1].'" height="'.$cur_thumb[2].'">';
                                                                }
                                
                                            					//the_post_thumbnail(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_thumb_format', 'thumbnail'));
            
                                                                //OVER-таркеры.
                                                                the_overmarkers($post->ID, 'post_attached_images', '<span class="images-count">+%s фото</span>', 'v-bottom h-right', '');
                                                                the_overmarkers($post->ID, 'post_pub_date', '<span class="pub-date">%s</span>', 'v-bottom h-right');
?>
                                        				    </a>
                                    				    </div> <!-- /.thumb.has-overblocks -->
                                                    </div>
                                                    
                                                    <div class="col grid_7_of_12">
                                                        <div class="egg">
<?php
                                                            //Собираем Egg шорткоды.
                                                            $content = $post->post_content;
                                                        
                                                            $arr_shortcodes = array();
                                                            $pos_begin = stripos($content, '[affegg'); //[affegg id=60]
                                                            while ($pos_begin !== false) {
                                                                $pos_end = stripos($content, ']', $pos_begin);
                                                                $arr_shortcodes[] = substr($content, $pos_begin, $pos_end-$pos_begin+1);
                                                        
                                                                $pos_begin = stripos($content, '[affegg', $pos_begin+1);
                                                            }
                                                            //var_dump($arr_shortcodes);
                                                            
                                                            echo do_shortcode(implode('', $arr_shortcodes));
                                                            
                                                            /*
                                                            //$post_content = get_post($post->ID)->post_content;
                                                            $post_content = $post->post_content;
                
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
                                                                
                                                                echo do_shortcode($shortcodes);
                                                            }
                                                            */
?>
                                                        </div>
                                                    </div>
                                                </div> <!-- /.entry-thumb-staticegg -->
<?php
                                            }
                                        }
?>
                                    </div> <!-- /.entry-header -->
                                </div> <!-- /#post -->
<?php
                	        }
?>
                        </div> <!-- /#posts-list -->
<?php
                        wp_reset_postdata();
?>
                    </div> <!-- /.posts-ann -->
<?php
                }
        	
        	
        	
        	
        	
            //Поле ввода.
            } elseif ($block_type=='textarea') {
?>
        	    <div class="textarea">
<?php
                    echo do_shortcode(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_textarea_text', ''));
?>
                </div> <!-- /.textarea -->
<?php




            //Баннер.
            } elseif ($block_type=='banner') {
                
            }
        
            echo $block_content_post; 
?>
        </div> <!-- /.block -->






<?php
//=== Ниже ничего не меняем.
//} else {


/*
        //Слайдер Slick.
        if ($block_type=='slider-slick' && of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_code', '')) {
?>
            <div class="<?php echo $block_class; ?>">
<?php
                $slider_pre = $slider_post = '';
                if (!of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_wide', '1')) {
                    $slider_pre = '<div class="middle-block row">'; $slider_post = '</div> <!-- /.middle-block.row -->';
                }
                
                echo $slider_pre;
?>
                <div class="slider-slick">
<?php
                    if ($block_header = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_text', '')) {
?>
                        <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                    }
                    echo of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_slider_code', '');
?>
                </div> <!-- /.slider-slick -->
<?php
                echo $slider_post;        
?>
            </div>
<?php





        //Анонсы постов.
        } elseif ($block_type=='posts-list') {

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
        	if ($listing->have_posts()) {
?>
                <div class="site-content row">
            		<div class="row clearfix">
            		    <div class="col grid_12_of_12">
            		        <div class="<?php echo $block_class; ?>">
<?php
                                if ($block_header = of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_header_text', '')) {
?>
                                    <div class="block-header"><span><?php echo $block_header; ?></span></div>
<?php
                                }
?>
                                <div id="posts-list" class="posts isotope <?php echo sanitize_text_field(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_posts_ann_view', 'list')); ?>">
<?php
                                	while ($listing->have_posts()) {
                                	    $listing->the_post();
                                	    global $post;
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
?>
                                </div> <!-- /#posts-list -->
                            </div>
            		    </div> <!-- /.col.grid_12_of_12 -->
            		</div> <!-- /.row clearfix -->
            	</div><!-- /.site-content.row -->
<?php
    	    }
            wp_reset_postdata();
        	
        	
        	
        	
        	
        //Поле ввода.
        } elseif ($block_type=='textarea') {
?>
            <div class="site-content row">
        		<div class="row clearfix">
        	        <div class="col grid_12_of_12">
        		        <div class="<?php echo $block_class; ?>">
        		            <div class="textarea">
<?php
                                echo do_shortcode(of_get_option($bo_id_prefix.'_'.$bo_term_id.'_block'.$block_inc.'_textarea_text', ''));
?>
                            </div> <!-- /.textarea -->
                        </div>
        	        </div> <!-- /.col.grid_12_of_12 -->
        		</div> <!-- /.row clearfix -->
        	</div><!-- /.site-content.row -->
<?php




        //Баннер.
        } elseif ($block_type=='banner') {
            
        }
        
        
        }
*/
    }
}


get_footer();
?>
