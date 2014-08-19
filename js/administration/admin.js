$(function() {

    $('#side-menu').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
$(function() {
    $(window).bind("load resize", function() {
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
	   height = $(window).height();
	   
        if (width < 768) {
            $('div.sidebar-collapse').addClass('collapse')
		  $('#right-navigation').css('height','');
		  
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
		  $('#right-navigation').css('height',height);
        }
    })
})
