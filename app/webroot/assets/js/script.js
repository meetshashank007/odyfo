$(function(){
	windowheight();
	contactheight();
	
	// $('.app-feature-section-div .app-feature-section .slidedownrw').click(function(){
		// var nextul = $(this).parents('.app-feature-section-div .app-feature-section').find('.slidedownContextbx');
		// if(nextul.css('display')=='none'){
			// $('.slidedownContextbx').slideUp();
			// nextul.slideDown();
			// $('app-feature-section-div .app-feature-section').removeClass('active');
			// $(this).parents('.tagfilterrow').addClass('active');
		// }
		// else{
			// nextul.slideUp();
			// $('.app-feature-section-div .app-feature-section').removeClass('active');
		// }
	// });
	
});
function windowheight(){
 $('.header').css({'height':$(window).height()});
 $(window).resize(function(){
  $('.header').css({'height':$(window).height()});
 });
}
function contactheight(){
 $('.contact-section,.contact-image').css({'height':$(window).height()});
 $(window).resize(function(){
  $('.contact-section,.contact-image').css({'height':$(window).height()});
 });
}


// Nav Start Here
var didScroll;
var lastScrollTop = 0;
var delta = 5;
var navbarHeight = $('nav').outerHeight();

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 250);

function hasScrolled() {
    var st = $(this).scrollTop();
    
    // Make sure they scroll more than delta
    if(Math.abs(lastScrollTop - st) <= delta)
        return;
    
    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop && st > navbarHeight){
        // Scroll Down
        $('nav').addClass('contact-nav');
    } else {
        // Scroll Up
        // if(st + $(window).height() < $(document).height()) {
            // $('nav').removeClass('contact-nav');
        // }
    }
    
    lastScrollTop = st;
}

