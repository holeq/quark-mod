jQuery(document).ready(function($) {
    var $container = jQuery(".js-masonry");
    $container.imagesLoaded(function() {
        $container.masonry({
            itemSelector: ".masonry-item",
            gutter: 30,
            transitionDuration: "0.1s",
        });
    });
});

function bo_reload_masonry() {
    var $container = jQuery(".js-masonry");
    $container.imagesLoaded(function() {
        $container.masonry({
            itemSelector: ".masonry-item",
            gutter: 30,
            transitionDuration: "0.1s",
        });
        $container.masonry('reloadItems');
        $container.masonry('layout');
    });
}