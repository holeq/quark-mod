<?php
/**
 * YARPP Template: Похожие образы
 * Description: Для товарных CPA.
 * Author: BO
*/ 

?>

<?php
    if (have_posts()) {
?>
        <div class="bo-yarp-template-look">
            <div class="header">Похожие образы</div>
            <div class="items">
<?php
                $inc = 0;
            	while (have_posts()) {
            	    the_post();

            		if (has_post_thumbnail()) {
            		    $inc++;
            		    $cur_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
?>
                		<div class="item clearfix<?php echo ($inc == 5 ? ' last' : ''); ?>">
                		    <div class="item-img">
                		        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="<?php echo $cur_thumb[0]; ?>"></a>
                		    </div>
                		    <div class="item-content">
                		        <div class="item-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(); ?></a></div>
                		    </div>
                		</div> <!-- /.item -->
<?php
            		}
            		
            		if ($inc == 3 && 1>2) {
            		    $inc = 0;
?>
            		    <div class="clearfix"></div>
<?php
            		}
            		
            		//Выводим не более 5 похожих образов.
            		if ($inc == 5) {
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