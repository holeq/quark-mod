<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Quark
 * @since Quark 1.0
 */
?>

<article id="post-0" class="post no-results not-found">
	<header class="entry-header">
		<h1 class="entry-title"><?php echo sanitize_text_field(of_get_option('bo_notfound_header', '')); ?></h1>
	</header><!-- /.entry-header -->

	<div class="entry-content">
<?php
		if (is_search()) {
?>
			<p><?php echo sanitize_text_field(of_get_option('bo_notfound_text', '')); ?></p>
			<?php get_search_form(); ?>
<?php 
        } else {
?>
			<p><?php echo sanitize_text_field(of_get_option('bo_notfound_text', '')); ?></p>
			<?php get_search_form(); ?>
<?php
		}
?>
	</div><!-- /.entry-content -->
</article><!-- /#post-0.post.no-results.not-found -->
