<?php
/**
 * The template for displaying an archive page for Categories.
 *
 * @package Quark
 * @since Quark 1.3
 */

get_header();
?>

	<div id="primary" class="site-content row" role="main">
<?php
	    if ($current_cat_sidebar!='none') {
	        
	        if ($current_cat_sidebar_pos=='left') {
	            get_sidebar(); //Внутри .col.grid_3_of_12
	        }
?>
		    <div class="col grid_9_of_12">
<?php
		} else {
?>
		    <div class="col grid_12_of_12">
<?php
		}
		
		$header_outputed = false;
		//Тест.
		
		$current_cat_postsann = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann', '1');
		$current_cat_postsann_h = of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_h', '');
		$current_cat_input_above = of_get_option('bo_cat_template_'.$current_cat_template_id.'_input_above', '0');
		$current_cat_input_above_html = of_get_option('bo_cat_template_'.$current_cat_template_id.'_input_above_html', '');
		
		//Выводим Поле ввода 1.
		if ($current_cat_input_above && !empty($current_cat_input_above_html)) {
		    $header_outputed = true;
		    
		    if ($cat_header || category_description()) {
?>
        	    <header class="archive-header">
<?php
                    if ($cat_header) {
?>
			            <h1 class="archive-title"><?php echo $cat_header; ?></h1>
<?php
                    }
    	        	if (category_description()) {
?>
			            <div class="archive-meta"><?php echo category_description(); ?></div>
<?php
		            }
?>
	            </header> <!-- /.archive-header -->
<?php
		    }
		    
		    echo $current_cat_input_above_html;
		}

        if ($current_cat_postsann) {
            if (have_posts()) {
                
                //Если ранее заголовок не выводился, то выводим сейчас.
                if (!$header_outputed) {
                    if ($cat_header || category_description()) {
?>
	        	        <header class="archive-header">
<?php
                            if ($cat_header) {
?>
				                <h1 class="archive-title"><?php echo $cat_header; ?></h1>
<?php
                            }
    		        	    if (category_description()) {
?>
				                <div class="archive-meta"><?php echo category_description(); ?></div>
<?php
			                }
?>
		                </header> <!-- /.archive-header -->
<?php
                    }
                }
                
                //Выводим заголовок над анонсами, если таковой задан.
                if ($current_cat_postsann_h) {
?>
                    <div id="posts-list-header"><span><?php echo $current_cat_postsann_h; ?></span></div>
<?php
                }
?>
			    <div id="posts-list" class="isotope <?php echo sanitize_text_field(of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_view', 'list')); ?>">
<?php
    				while (have_posts()) {
    				    //$inc_items_in_row++;
    				    the_post();
    				    //get_template_part('content', get_post_format());
    				    get_template_part('content');
                    }
?>
                </div> <!-- /#posts-list -->
<?php
                if (of_get_option('bo_cat_template_'.$current_cat_template_id.'_postsann_pagin', '1')) {
                    quark_content_nav('nav-below');
                }

            } else {
                if ($current_cat_type_id == 'discount') {
?>
                <article id="post-0" class="post no-results">
                	<div class="entry-content">
                        <div class="attention">Закончились скидки в категории <span class="bold"><?php echo $cat_header; ?></span>.</div>
                        <div class="attention-description">Мы уже ищем новые и интересные предложения.</div>
                	</div><!-- /.entry-content -->
                </article><!-- /#post-0.post.no-results.not-found -->
<?php
                } else {
                    get_template_part('no-results');
                }
            }
?>
		</div> <!-- /.col.grid_9_of_12 or .col.grid_12_of_12 -->
<?php
        }
        
	    if ($current_cat_sidebar!='none' && $current_cat_sidebar_pos=='right') {
		    get_sidebar(); //Внутри .col.grid_3_of_12
		}
?>
	</div> <!-- /#primary.site-content.row -->
<?php
get_footer();
?>
