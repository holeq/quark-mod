jQuery(document).ready(function($) {
    var $isotope_list_container = $('.isotope.list').isotope({
            layoutMode: 'vertical',
            vertical: {
                horizontalAlignment: 0
            },
            transitionDuration: '0.7s',
            itemSelector: '.isotope-item',
    });
    $isotope_list_container.isotope('layout');
    
    var $isotope_grid_container = $('.isotope.grid').isotope({
            layoutMode: 'fitRows',
            transitionDuration: '0.7s',
            itemSelector: '.isotope-item',
    });    
    $isotope_grid_container.isotope('layout');
    
    var $isotope_masonry_container = $('.isotope.masonry').isotope({
            layoutMode: 'masonry',
            masonry: {
                gutter: 0
            },
            transitionDuration: '0.7s',
            itemSelector: '.isotope-item',
    });
    $isotope_masonry_container.isotope('layout');
});