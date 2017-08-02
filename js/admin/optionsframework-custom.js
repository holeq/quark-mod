//=== Хайды на базе значения checkbox.
jQuery(document).ready(function($) {
    var checker = jQuery('div.has-hidden');
    checker.each(function() {
        if (!jQuery(this).find('input[type="checkbox"]').is(':checked')) {
            jQuery(this).nextAll("div[id*='"+jQuery(this).attr('id')+"_'].start-hidden").hide('fast');
        }
    });
});

var checker2 = jQuery('div.has-hidden');
checker2.find('input[type="checkbox"]').click(function() {
    var checker2_parents = jQuery(this).parents('div.has-hidden'); //По факту будет один родитель.
    
    if (jQuery(this).is(':checked')) {
        //checker2.nextAll("div[id*='"+jQuery(this).attr('id')+"_'].start-hidden").show('fast');
        checker2_parents.nextAll("div[id*='"+jQuery(this).attr('id')+"_'].start-hidden").show('fast');
    } else {
        //checker2.nextAll("div[id*='"+jQuery(this).attr('id')+"_'].start-hidden").hide('fast');
        checker2_parents.nextAll("div[id*='"+jQuery(this).attr('id')+"_'].start-hidden").hide('fast');
    }
});
//=================================

//=== Хайды на базе значения select.

/*
jQuery(document).ready(function($) {
    var selecter = jQuery('div.has-hidden-select');
    selecter.each(function() {
        jQuery(this).nextAll("div.start-hidden-select").hide('fast');
    });
});
*/

/*
var selecter2 = jQuery('div.has-hidden-select');
selecter2.find('select').change(function() {
    jQuery(this).children('option').each(function() {
        if (jQuery(this).is(':selected')) {
            //alert('Выбран: '+"div.start-hidden-select.show-"+this.value);
            selecter2.nextAll("div.start-hidden-select.show-"+this.value).show('fast');
        } else {
            //alert('Не выбран: '+"div.start-hidden-select.show-"+this.value);
            selecter2.nextAll("div.start-hidden-select.show-"+this.value).hide('fast');
        }
    });
});
*/

/*
(function($){
jQuery.fn.boHide = function() {
    var selecter2 = jQuery('div.has-hidden-select');
    selecter2.find('select').each(function() {
        var selecter2_item = jQuery(this);
        
        jQuery(this).children('option').each(function() {
            if (jQuery(this).is(':selected')) {
                selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.
            } else {
                selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
            }
        });
    });
};
})(jQuery);
*/

//nextAll очень медленно работает. Вместо него .nextUntil(".group")
(function($){
jQuery.fn.boHideBySelect = function() {
    var make = function() {
        jQuery(this).find('select').each(function() {
            var selecter2_parents = jQuery(this).parents('div.has-hidden-select'); //По факту будет один родитель.
            var selecter2_item = jQuery(this);
            
            jQuery(this).children('option').each(function() {
                if (jQuery(this).is(':selected')) {
                    //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.
                    selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.

                } else {
                    //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
                    selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
                }
            });
        });
    };

    jQuery(this).each(make);
};
})(jQuery);

//nextAll очень медленно работает. Вместо него .nextUntil(".group")
jQuery(document).ready(function($) {
    var selecter2 = jQuery('div.has-hidden-select');
    //var selecter3 = jQuery('div.start-hidden-select');
    
    selecter2.find('select').each(function() {
        var selecter2_parents = jQuery(this).parents('div.has-hidden-select'); //По факту будет один родитель.
        var selecter2_item = jQuery(this);
        
        selecter2_item.children('option').each(function() {
            if (jQuery(this).is(':selected')) {
                //alert('Выбран: '+"div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value);
                
                //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).show('fast'); //Если опция выбрана, то показывать элементы с show-option.
                //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.

                //selecter3.hasClass(selecter2_item.attr('id')+".show-"+this.value).show('fast'); //Если опция выбрана, то показывать элементы с show-option.
                //selecter3.hasClass(selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.
                
                //if (selecter3.hasClass("."+selecter2_item.attr('id')+".show-"+this.value)) {
                //    jQuery(this).show('fast'); //Если опция выбрана, то показывать элементы с show-option.
                //}
                //if (selecter3.hasClass("."+selecter2_item.attr('id')+".notshow-"+this.value)) {
                //    jQuery(this).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.
                //}

                selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).show('fast'); //Если опция выбрана, то показывать элементы с show-option.
                selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.

            } else {
                //alert('Не выбран: '+"div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value);
                
                //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
                //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).show('fast'); //Если опция не выбрана, то показывать элементы с notshow-option.
            
                //selecter3.hasClass(selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
                //selecter3.hasClass(selecter2_item.attr('id')+".notshow-"+this.value).show('fast'); //Если опция не выбрана, то показывать элементы с notshow-option.
                
                //if (selecter3.hasClass("."+selecter2_item.attr('id')+".show-"+this.value)) {
                //    jQuery(this).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
                //}
                //if (selecter3.hasClass("."+selecter2_item.attr('id')+".notshow-"+this.value)) {
                //    jQuery(this).show('fast'); //Если опция не выбрана, то показывать элементы с notshow-option.
                //}       
                
                selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
                selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).show('fast'); //Если опция не выбрана, то показывать элементы с notshow-option.

                //.nextUntil(".group")
            }
        });
    });
});

//nextAll очень медленно работает. Вместо него .nextUntil(".group")
var selecter2 = jQuery('div.has-hidden-select');
//selecter2.find('select').change(function() {
selecter2.on('change', 'select', function() {
    var selecter2_parents = jQuery(this).parents('div.has-hidden-select'); //По факту будет один родитель.
    var selecter2_item = jQuery(this);
    
    selecter2_item.children('option').each(function() {
        if (jQuery(this).is(':selected')) {
            //alert('Выбран: '+"div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value);
            
            //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).show('fast'); //Если опция выбрана, то показывать элементы с show-option.
            //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.
        
            selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).show('fast'); //Если опция выбрана, то показывать элементы с show-option.
            selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).hide('fast'); //Если опция выбрана, то скрывать элементы с notshow-option.
        
        } else {
            //alert('Не выбран: '+"div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value);
            
            //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
            //selecter2.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).show('fast'); //Если опция не выбрана, то показывать элементы с notshow-option.
        
            selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".show-"+this.value).hide('fast'); //Если опция не выбрана, то скрывать элементы с show-option.
            selecter2_parents.nextAll("div.start-hidden-select."+selecter2_item.attr('id')+".notshow-"+this.value).show('fast'); //Если опция не выбрана, то показывать элементы с notshow-option.
        }
    });
    
    //selecter2_item.find('div.has-hidden-select').boHideBySelect();
    selecter2_parents.nextAll('div.has-hidden-select').boHideBySelect();
});

//=================================


//=== Универсальный hide/show.
//=== Необходимо в options.php у опции прописать параметр cond-show с правилами указывающие в каких случаях секция будет отображаться.
//=== Например:
//=== bo_post_look_uptolike_share_pos!=none;bo_post_look_template=slider-content,bo_post_look_uptolike_share_pos_slider=1
//=== ; разделяет несколько наборов условий - полное выполнение любого из которых инициирует отображение элемента.
//=== , каждый набор может состоять из нескольких условий выполнение которых будет означать полное выполнение всего набора, что инициирует отображение элемента.
//=== != и = допускается использовать равенство и неравенство при сравнении.
//=== если ни один из наборов не выполняется, то производится скрытие элемента.
var sections = jQuery('div.section');
sections.on('change', 'select, input', function() {
    //console.log('Iam');

    //Пробегаемся по всем секциям, имеющим атрибут cond-show, и проверяем - не привязаны ли эти секции к изменяемому сейчас контролу.
    jQuery('div[cond-show]').each(function() {
        
        var elem = jQuery(this);
        var elem_cond = elem.attr('cond-show');
        var show = true;
        
        //Пробегаемся по каждому набору условий в отдельности.
        var conditions = elem_cond.split(';');
        jQuery.each(conditions, function(index, item) { //each от jQuery работает и с array и с object и имеет вменяемую остановку перебора.
            //console.log(item);
            show = true;

            var params = item.split(','); //Наборы условий при одновременном соблюдении которых производить скрытие/показ.
            jQuery.each(params, function(index2, param) { //each от jQuery работает и с array и с object.
                //console.log(param);
                
                //Определяем последующий вид сравнения: равно или не равно.
                var equal = false;
                var temp = param.split('!=');
                //console.log(temp.length);
                if (temp.length === 1) {
                    equal = true; //Значит нужно равенство значения проверяемого элемента и значения условия.
                    temp = param.split('=');
                }
                
                //Вытягиваем значение измененного контрола (вызвавшего событие).
                var check = jQuery('#'+temp[0]);
                var check_value = '-1';
                
                //Получаем содержимое контрола. Значение или же выбрано/не выбрано.
                if (check.is('select')) {
                    check_value = check.val();
                } else if (check.is('input')) {
                    if (check.prop("type") == 'checkbox') {
                        if (check.prop('checked')) {
                            check_value = 1;
                        } else {
                            check_value = 0;
                        }
                    } else if (check.prop("type") == 'text') {
                        check_value = check.val();
                    }
                }
                //console.log(temp[1]+' ? '+check_value);
                
                if (equal) {
                    if (temp[1] != check_value) { //Для равенства условие невыполнено - если одно значение не равно другому .
                        show = false;
                        console.log('Условие не совпало');
                        return false; //Выходим из цикла, поскольку одно из необходимых условий в этой части не сработало. Переходим к другой части.
                    }               
                    
                } else {
                    if (temp[1] == check_value) { //Для неравенства условие невыполнено - если одно значение равно другому .
                        show = false;
                        console.log('Условие не совпало');
                        return false; //Выходим из цикла, поскольку одно из необходимых условий в этой части не сработало. Переходим к другой части.
                    }
                }
            });

            if (show) {
                console.log('Отображаем '+elem.prop('id'));
                elem.show('fast');
                return false; //Выходим из цикла, поскольку нашли часть со всеми выполненными условиями.
            }
		});
		
		console.log('show='+show);
		
		if (!show) {
            console.log('Скрываем');
            elem.hide('fast');
		}
    });
});
//=================================