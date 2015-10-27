jQuery(document).ready(function(){
            jQuery('#data').after('<div id="nav"></div>');
            var rowsShown = 10;
            var rowsTotal = jQuery('#data tbody tr').length;
            var numPages = rowsTotal/rowsShown;
            for(i = 0;i < numPages;i++) {
                var pageNum = i + 1;
                jQuery('#nav').append('<a href="#" rel="'+i+'">'+pageNum+'</a> ');
            }
            jQuery('#data tbody tr').hide();
            jQuery('#data tbody tr').slice(0, rowsShown).show();
            jQuery('#nav a:first').addClass('active');
            jQuery('#nav a').bind('click', function(){
 
                jQuery('#nav a').removeClass('active');
                jQuery(this).addClass('active');
                var currPage = jQuery(this).attr('rel');
                var startItem = currPage * rowsShown;
                var endItem = startItem + rowsShown;
                jQuery('#data tbody tr').css('opacity','0.0').hide().slice(startItem, endItem).
                        css('display','table-row').animate({opacity:1}, 300);
            });
        });
