$(document).ready(function() {
    // Toggle scroll menu
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        // Adjust menu background
        if (scroll >= 100) {
          $('.sticky-navigation').addClass('shadow-bottom');
        } else {
          $('.sticky-navigation').removeClass('shadow-bottom');
        }
    
        // Adjust scroll to top
        if (scroll >= 600) {
          $('.scroll-top').addClass('active');
        } else {
          $('.scroll-top').removeClass('active');
        }
      });
    
      // Scroll to top
      $('.scroll-top').click(function() {
        $('html, body').stop().animate({
          scrollTop: 0
        }, 1000);
      });
});

