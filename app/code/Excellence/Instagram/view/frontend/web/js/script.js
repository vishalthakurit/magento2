
  require([
    'jquery',
    'prototype'
    ], function(jQuery){
     jQuery(document).ready(function(){
      jQuery(".owl-carousel").owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        responsive:{
          0:{
            items:1
          },
          600:{
            items:3
          },
          1000:{
            items:5
          }
        }
      });
    });
     var owl = jQuery('.owl-carousel');
     owl.on('mousewheel', '.owl-stage', function (e) {
      if (e.deltaY>0) {
        owl.trigger('next.owl');
      } else {
        owl.trigger('prev.owl');
      }
      e.preventDefault();
    });
      // var owl = $('.owl-carousel');
      owl.owlCarousel({
        items:4,
        loop:true,
        margin:10,
        autoplay:true,
        autoplayTimeout:1000,
        autoplayHoverPause:true
      });
       
       var that = this;

      setTimeout(function() {   //calls click event after a certain time
         // that.element.click();
         owl.trigger('play.owl.autoplay',[1000]);
      }, 10000);
    });
