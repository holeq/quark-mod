<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id #maincontentcontainer div and all content after.
 * There are also four footer widgets displayed. These will be displayed from
 * one to four columns, depending on how many widgets are active.
 *
 * @package Quark
 * @since Quark 1.0
 */
?>

<?php	
		do_action('quark_after_woocommerce');
?>
	</div> <!-- /#maincontentcontainer -->

	<div id="footercontainer">
<?php
		//Подсчитываем кол-во не пустых областей в подвале. Это необходимо для просчета грида.
		$footerareas_count = 0;
		for ($x = 1; $x <= 4; $x++) {
			if (is_active_sidebar('footerarea-'.$x)) {
				$footerareas_count++;
			}
		}

		//Выводим не пустые области.
		if ($footerareas_count > 0) {
?>
			<footer class="site-footer row" role="contentinfo">
<?php
			$containerClass = "grid_" . 12 / $footerareas_count . "_of_12";

			for ($x=1; $x<=4; $x++) {
				
				if (is_active_sidebar('footerarea-'.$x)) {
?>
					<div class="col <?php echo $containerClass?>">
						<div class="widget-area" role="complementary">
<?php
							dynamic_sidebar('footerarea-'.$x);
?>
						</div>
					</div> <!-- /.col.grid_*_of_12 -->
<?php 
				}
			}
?>
			</footer> <!-- /.site-footer.row -->
<?php
		}
?>

	</div> <!-- /.footercontainer -->
</div> <!-- /.#wrapper.hfeed.site -->

<?php
//Форма "Подписаться на Email-рассылку".
if (of_get_option('bo_unisender_signup_html', '')) {
?>
    <div id="unisender-signup-popup" class="white-popup mfp-with-anim mfp-hide">
<?php
	    echo of_get_option('bo_unisender_signup_html', '');
?>
    </div>
<?php
}
?>

<?php
wp_footer();

//Счетчики статистики, ретаргетинга и прочего.
foreach (array('bo_yandex_metrica_code', 'bo_google_analytics_code', 'bo_mailru_top_code', 'bo_li_code', 'bo_vk_code') as $value) {
    if (of_get_option($value, '')) {
        echo of_get_option($value, '');
    }
}

//$bo_options = optionsframework_options(); //Весь массив опций из Theme Options.
//var_dump($bo_options);

//Некоторые тех.данные.
echo '<!-- MySQL: '.get_num_queries().' запросов / '; timer_stop(1);			
if (function_exists('memory_get_usage')) {
    echo ' сек. Память: ' .round(memory_get_usage()/1024/1024, 2).' мегабайт -->';	
}
?>
</body>
</html>
