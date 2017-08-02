<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Quark
 * @since Quark 1.0
 */

get_header();
?>
	<div id="primary" class="site-content row" role="main">
<?php
	    if (sanitize_text_field(of_get_option('bo_post_'.$bo_post_type.'_sidebar', 'none'))!='none') {
	        if (sanitize_text_field(of_get_option('bo_post_'.$bo_post_type.'_sidebar_pos', 'right'))=='left') {
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
                while (have_posts()) {
                    the_post();
                    get_template_part('content', get_post_format());
                    
                    $cur_underpostblock = sanitize_text_field(of_get_option('bo_post_'.$bo_post_type.'_underpostblock', 'none'));
                    if (is_active_sidebar($cur_underpostblock)) {
?>
                        <div id="underpostblock">              
<?php
                            dynamic_sidebar($cur_underpostblock);
?>
                        </div>
<?php
                    }

        			if (of_get_option('bo_post_'.$bo_post_type.'_nav_prevnext', '0')) {
        			    quark_content_nav('nav-below');
        			}
        		}
?>
	        </div> <!-- /.col.grid_9_of_12 or .col.grid_12_of_12 -->
<?php
        if (sanitize_text_field(of_get_option('bo_post_'.$bo_post_type.'_sidebar', 'none'))!='none' &&
            sanitize_text_field(of_get_option('bo_post_'.$bo_post_type.'_sidebar_pos', 'right'))=='right') {
    	    get_sidebar(); //Внутри .col.grid_3_of_12
    	}
?>
        <div class="col grid_10_of_12">
<?php        
    		//If comments are open or we have at least one comment, load up the comment template
			if (comments_open() || '0' != get_comments_number()) {
				comments_template('', true);
			}
?>
        </div> <!-- /.col.grid_10_of_12 -->
        
	</div> <!-- /#primary.site-content.row -->
<?php
get_footer();