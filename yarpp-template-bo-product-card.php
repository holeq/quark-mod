<?php
/**
 * YARPP Template: Похожие товары
 * Description: Для товарных CPA.
 * Author: BO
*/ 

?>

<?php
    if (have_posts()) {
        
        $upload_dir = wp_upload_dir();
?>
        <div class="bo-yarp-template-product-card">
            <div class="header">Похожие предложения</div>
            <div class="items">
<?php
                $inc = 0;
            	while (have_posts()) {
            	    the_post();
            	    
                    //Вывод фото товара или ее заглушки.
                    $product_image = bo_get_field($post->ID, 'bo_cf_product_image', '');
                    if ($product_image && file_exists($upload_dir['basedir'].'/'.$product_image)) {
                        $cur_thumb = '<img class="show-loader" src="'.$upload_dir['baseurl'].'/'.$product_image.'">';
                    } elseif ($thumb_gag = of_get_option('bo_post_'.$bo_post_type.'_thumb_gag', '')) {
    		            $cur_thumb = '<img class="thumb-gag" src="'.$thumb_gag.'">';
    		        }

        		    $inc++;
?>
            		<div class="item clearfix<?php echo ($inc == 10 ? ' last' : ''); ?>">
            		    <div class="item-img">
            		        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo $cur_thumb; ?></a>
            		    </div>
            		    <div class="item-content">
            		        <div class="item-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(); ?></a></div>
            		    </div>
            		</div> <!-- /.item -->
<?php
            		if ($inc == 3 && 1>2) {
            		    $inc = 0;
?>
            		    <div class="clearfix"></div>
<?php
            		}
            		
            		//Выводим не более 8 похожих товаров.
            		if ($inc == 8) {
            		    break;
            		}
            	}
?>
            </div> <!-- /.items -->
        </div> <!-- /.bo-yarp-template-thumbnail -->
<?php
    } else {
?>
        <p>Похожих образов пока нет.</p>
<?php
    }
?>