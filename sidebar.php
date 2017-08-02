<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Quark
 * @since Quark 1.0
 */

?>
	<div class="col grid_3_of_12">
		<div id="secondary" class="widget-area" role="complementary">
<?php
			do_action('before_sidebar');
			
			$cur_sidebar = 'none';
			if (is_category()) {
    		    global $current_cat_template_id;
                $cur_sidebar = sanitize_text_field(of_get_option('bo_cat_template_'.$current_cat_template_id.'_sidebar', 'none'));
                $cur_sidebar_view_variant = sanitize_text_field(of_get_option('bo_cat_template_'.$current_cat_template_id.'_sidebar_template', 'simple'));
		    } elseif (is_single()) {
		        global $bo_post_type;
                $cur_sidebar = sanitize_text_field(of_get_option('bo_post_'.$bo_post_type.'_sidebar', 'none'));
                $cur_sidebar_view_variant = sanitize_text_field(of_get_option('bo_post_'.$bo_post_type.'_sidebar_template', 'simple'));
			}
			
            if (is_active_sidebar($cur_sidebar)) {
?>
                <div class="sidebar template-<?php echo $cur_sidebar_view_variant; ?>">              
<?php
                    dynamic_sidebar($cur_sidebar);
?>
                </div>
<?php
            }
?>
		</div> <!-- /#secondary.widget-area -->
	</div> <!-- /.col.grid_3_of_12 -->