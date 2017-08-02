jQuery(document).ready(function($) {
    if (typeof obj_params != 'undefined') {
        
        var pageNum = parseInt(obj_params.start_page_num) + 1; // The number of the next page to load (/page/x/).
        var max = parseInt(obj_params.pages_count); // The maximum number of pages the current query can return.
        var nextLink = obj_params.next_link; // The link of the next page of posts.
        
        //Если еще есть страницы для подгрузки, то выводим необходимые для этого контейнеры и кнопки
        if (pageNum <= max) {
        	$('#posts-list')
        	    .append('<div id="load-more-posts-empty"></div>') //Промежуточный контейнер для AJAX результата.
        	    .after('<div id="load-more-posts-but"><button class="bo-btn" type="submit">'+obj_params.but_caption_more+'</button></div>');
        
        	$('#nav-below').remove(); // Remove the traditional navigation.
        }
        
        //Подгрузка постов по клику на кнопке.
        $('#load-more-posts-but button').click(function() {
        
        	$(this).text(obj_params.but_caption_loading); // Show that we're working.
        	
        	$('#load-more-posts-empty').load(nextLink + ' .post', //Because we have added .post to the end, it will only copy over the .post divs that it finds (Not the whole page!)
        		function() {
        			// Update page number and nextLink.
        			pageNum++;
        			nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);
                    
                    //Добавляем новые элементы к диву с текущими элементами.
                    var new_items = $();
                    var isotope_container = $('.isotope');
                    $('#load-more-posts-empty').children().each(function() {
                        var this_elem = $(this);
                        //setTimeout(function(){
                            //isotope_container.isotope().append($(this)).isotope('appended', $(this));
                            
                            new_items = new_items.add(this_elem);
                            
                            //isotope_container.append(this_elem);
                            //this_elem.imagesLoaded(function() {
                            //    isotope_container.isotope('appended', this_elem);
                            //});
                        //}, 2000);
                    });
                    
                    new_items.css('display', 'none');
                    new_items.imagesLoaded()
                        .always(function() {
                            //console.log('all images loaded');
                            new_items.css('display', 'block');
                            isotope_container.append(new_items);
                            isotope_container.isotope('appended', new_items);
                        });
                    
        			// Update the button message.
        			if(pageNum <= max) {
        				$('#load-more-posts-but button').text(obj_params.but_caption_more);
        			} else {
        				//$('#load-more-posts-but button').text(obj_params.but_caption_nomore);
        				$('#load-more-posts-but button').remove(); //Убираем кнопку, когда все посты подгружены.
        			}
        			
        			//Подгружаем lazy-изображения попавшие на экран в только что подгруженном элементе. 
        			//jQuery(document).find('img[data-src]').boImagesShowing();
        			//jQuery(document).boIsotopeRepaint();
        			
                    //Подгружаем lazy-изображения блоков article.isotope-item попавших на экран.
                    //jQuery(document).find('article.isotope-item').boImagesShowing();
                    
                    //isotope_container.append(new_items).imagesLoaded(function() {
                    //    isotope_container.isotope('appended', new_items);
                    //});
        		}
        	);

        	return false;
        });
    }
});