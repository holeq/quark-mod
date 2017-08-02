<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Quark
 * @since Quark 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content row" role="main">
		<div class="col grid_12_of_12">

			<article id="post-0" class="post error404 no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><i class="fa fa-frown-o fa-lg"></i><?php echo sanitize_text_field(of_get_option('bo_404_header', '')); ?></h1>
				</header>
				<div class="entry-content">
					<p><?php echo sanitize_text_field(of_get_option('bo_404_text', '')); ?></p>
				</div><!-- /.entry-content -->
			</article><!-- /#post-0 -->

		</div> <!-- /.col.grid_12_of_12 -->
	</div> <!-- /#primary.site-content.row -->

<?php get_footer(); ?>