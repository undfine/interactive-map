
jQuery(document).ready( function($){


    // Define constant jQuery objects
    var $lotScreen = $('<div class="lot-screen"></div>').appendTo( $('body') );
    var $lotInner = $('<div class="lot-inner"></div>').appendTo($lotScreen);  // Append to body for full-screen popup
    var $mapContent = $(".map-content");
    var $mapViewport = $(".map-viewport");

    // Add "active" class to paths to enable them
    $(".lot-holder").each(function(){
      var target = $(this).attr("id");
      var lottype =  ($(this).hasClass('homestead')) ? 'lot' : 'home';

      if ( !isNaN(target) ) {
        target = parseInt(target,10);
        target = target.toString();
      }

        $(".lot-path[data-name='"+target+"']").addClass("active "+lottype);
        $(".lot-number:contains("+target+")").each(function(){
          if( $(this).text() === target) { $(this).addClass("active"); }
        });

    });

    //
    $(".map-toggle").click(function() {
      var target = $(this).attr("data-layer");
      var icon = $(this).children().get(0).outerHTML;
      if( $(".map-layer[data-name="+target+"]").hasClass("active") ) {
        $(".map-layer[data-name="+target+"]").removeClass("active");
        $(this).removeClass("active").html(icon+"SHOW "+target);
        $("#"+target).addClass("disabled");
      } else {
        $(".map-layer[data-name="+target+"]").addClass("active");
        $(this).addClass("active").html(icon+"HIDE "+target);
        $("#"+target).removeClass("disabled");
      }
    });

    // Cursor Info
    $(".lot-path, .lot-number, .map-quadrants, .golf-hole, .focal-point, .trail-line").on("mouseenter",function(){
        $(".map-info img").hide();
    }).on("mouseleave", function(){
        $(".map-info img").show();
    });

    $mapContent.draggable({ containment: "parent", scroll: false });
    $mapContent.on("drag", function(){
        $(this).css("background-position", "-" + $(this).position().left + "px -" + $(this).position().top + "px");
    });

    //Map Info Follows cursor
    $mapContent.mousemove(function(e) {
        $(".map-info").offset({left: e.pageX + 20,top: e.pageY + 20});
    });


    $mapViewport.on('click', function(e){

        if ( $(e.target).is(".lot-number, .lot-path, .golf-hole, .focal-point, .trail-line") ) { return; }
        else {

            if( $mapViewport.hasClass("zoom1") ) {

                $mapViewport.removeClass("zoom1").addClass("zoom2");
                //$mapContent.css("left","12.5%").css("top","12.5%");
                $(".map-zoom-toggle span").removeClass("active");
                $(".map-zoom-toggle [data-zoom='zoom2']").addClass("active");

            } else if($mapViewport.hasClass("zoom2")) {

                $mapViewport.removeClass("zoom2").addClass("zoom3");
                //$mapContent.css("left","16.667%").css("top","16.667%");
                $(".map-zoom-toggle span").removeClass("active");
                $(".map-zoom-toggle [data-zoom='zoom3']").addClass("active");

            } else if($mapViewport.hasClass("zoom3")) {

                $mapViewport.removeClass("zoom3").addClass("zoom1");
                //$mapContent.css("left","0%").css("top","0%");
                $(".map-zoom-toggle span").removeClass("active");
                $(".map-zoom-toggle [data-zoom='zoom1']").addClass("active");

            } else {
              $mapViewport.addClass("zoom1");
              $(".map-zoom-toggle span").removeClass("active");
              $(".map-zoom-toggle [data-zoom='zoom1']").addClass("active");
              $mapContent.css("left","0%").css("top","0%");
            }
        }
    });


    function setZoom(level){
      var maxZ = 4;
      var newZ = (level > maxZ) ? 1 : level;
      var currentZ = parseInt($mapViewport.attr("data-zoom"),10);

      $zoomControls.find('span').removeClass('active');
      $zoomControls.find("[data-zoom="+newZ+"]").addClass("active");

      var zPerc = toString(newZ*100)+%;
      var zFrac = toString(100 - (100 / newZ))+%;

      $zoomWrapper.height(zPerc).width(zPerc);
      $mapContent.height(zFrac).width(zFrac);

      $mapViewport.data('zoom',newZ);
    }




    // Original zoom function
    //$(".map-viewport, .map-zoom-toggle").click( function(e){
    //     if( $(e.target).is(".lot-number, .lot-path, .golf-hole, .focal-point, .trail-line") || $(e.target).is(".map-q1") || $(e.target).is(".map-q2") || $(e.target).is(".map-q3") || $(e.target).is(".map-q4")) {return;} else {
    //
    //         if($(".map-viewport").hasClass("zoomq1") || $(".map-viewport").hasClass("zoomq2") || $(".map-viewport").hasClass("zoomq3") || $(".map-viewport").hasClass("zoomq4")) {
    //             $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq1 zoomq2 zoomq3 zoomq4").addClass("zoom1");
    //
    //         } else if($(".map-viewport").hasClass("zoom1")) {
    //
    //             $(".map-viewport").removeClass("zoom1").addClass("zoom2");
    //             $(".map-content").css("left","12.5%").css("top","12.5%");
    //             $(".map-zoom-toggle span").removeClass("active");
    //             $(".map-zoom-toggle [data-zoom='zoom2']").addClass("active");
    //
    //         } else if($(".map-viewport").hasClass("zoom2")) {
    //
    //             $(".map-viewport").removeClass("zoom2").addClass("zoom3");
    //             $(".map-content").css("left","16.667%").css("top","16.667%");
    //             $(".map-zoom-toggle span").removeClass("active");
    //             $(".map-zoom-toggle [data-zoom='zoom3']").addClass("active");
    //
    //         } else if($(".map-viewport").hasClass("zoom3")) {
    //
    //             $(".map-viewport").removeClass("zoom3").addClass("zoom1");
    //             $(".map-content").css("left","0%").css("top","0%");
    //             $(".map-zoom-toggle span").removeClass("active");
    //             $(".map-zoom-toggle [data-zoom='zoom1']").addClass("active");
    //
    //         } else {
    //           $(".map-viewport").addClass("zoom1");
    //           $(".map-zoom-toggle span").removeClass("active");
    //           $(".map-zoom-toggle [data-zoom='zoom1']").addClass("active");
    //           $(".map-content").css("left","0%").css("top","0%");
    //         }
    //     }
    // });

    // zoom quadrants not being used
    // $(".map-q1").click(function(){
    //   if($(".map-viewport").hasClass("zoomq1")) {
    //       $(".map-viewport").removeClass("zoomq1");
    //   } else {
    //       $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq2 zoomq3 zoomq4").addClass("zoomq1");
    //   }
    // });
    // $(".map-q2").click(function(){
    //   if($(".map-viewport").hasClass("zoomq2")) {
    //       $(".map-viewport").removeClass("zoomq2");
    //   } else {
    //       $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq1 zoomq3 zoomq4").addClass("zoomq2");
    //   }
    // });
    // $(".map-q3").click(function(){
    //   if($(".map-viewport").hasClass("zoomq3")) {
    //       $(".map-viewport").removeClass("zoomq3");
    //   } else {
    //       $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq2 zoomq1 zoomq4").addClass("zoomq3");
    //   }
    // });
    // $(".map-q4").click(function(){
    //   if($(".map-viewport").hasClass("zoomq4")) {
    //       $(".map-viewport").removeClass("zoomq4");
    //   } else {
    //     $mapViewport.removeClass("zoom1 zoom2 zoom3 zoomq2 zoomq3 zoomq1").addClass("zoomq4");
    // }
    // });




    $(".lot-holder").on("click", function() {
          var thisHolder = $(this);
          $(".lot-holder").not(thisHolder).removeClass("active");
          thisHolder.addClass("active");
          thisHolder.find(".lot-images-inner").children("img").each(function(){
            var newSrc = $(this).attr("data-src");
            if(newSrc) $(this).attr("src", newSrc);
          });
    });


    // Add event handlers to get target content and launch overlay popup
    $(".lot-path.active, .golf-hole, .focal-point, .trail-line").on( "click", function(e){
      e.preventDefault();
      var lot = $(this);
      var lotID = lot.attr("data-name");

      //if the lot is disabled abort!
      if ( lot.parents().hasClass("disabled") ) return;

      if (lotID && lotID != "undefined") {
        lotID = lotID.replace("_","");
        if (lotID.length < 2) { lotID = "0"+lotID; }
        if (lotID.length < 3) { lotID = "0"+lotID; }
      } else {
        lotID = lot.attr("data-id");
      }


      var $closeBtn = $('<i class="close fa fa-times" aria-hidden="true"></i>');

      var msg = "Info coming soon.";
      var lotCaption = "";
      var planInfo = "";
      var lotContent = $(".homesite-lot-data").find("#"+lotID).html();


      if(lotContent) {

        $lotInner.empty();
        $lotInner.stop(true).show().animate( {opacity: 0}, 200, function() {

          $lotInner.append(lotContent).append($closeBtn);

          // Display Overlay "$lotScreen"
          $lotScreen.stop(true).css("z-index","99999").show().animate( {opacity: 1},200,function(){

            var $lotImages = $lotInner.find(".lot-images");
            var $slider = $lotImages.children(".lot-images-inner");
            var slideCount = $slider.children("div").length;
            // Add the background image for each div
            $slider.children("div").each( function(){
              var newSrc = $(this).attr("data-src");
              if(newSrc) $(this).css("background-image", "url("+newSrc+")");
            });

            // Add Navigation if more than one slide
            if (slideCount > 1){

              $sliderNav = $('<div class="lot-slider-nav"><a href="#" class="lot-slider-prev" data-nav="prev">&lsaquo;</a><a href="#" class="lot-slider-next" data-nav="next">&rsaquo;</a></div>').appendTo($lotImages);
              $navNext = $('.lot-slider-next').on('click',slideNextPrev);
              $navPrev = $('.lot-slider-prev').on('click',slideNextPrev);


              function slideNextPrev(e) {
                e.preventDefault();

                // Get nav direction from data-nav
                var navDirection = $(this).attr('data-nav');
                var currentSlide = parseInt($slider.attr("data-slide"));

                // Set default direction = next
                // Loop back to 1 if next is higher than slide count
                var newSlide = (currentSlide+1 > slideCount) ? 1 : currentSlide+1;


                if (navDirection == "prev"){
                    newSlide = (currentSlide-1 <= 0) ? slideCount : currentSlide-1;
                }
                var pos = (newSlide-1) * -100;
                //console.log('currentSlide: '+currentSlide+' newSlide: '+newSlide);
                $slider.css("margin-left", pos+"%").attr("data-slide", newSlide);
              }

            }


            $lotInner.animate({opacity: 1}, 300, function() {

              $lotScreen.on('click', closeOverlay);
              $closeBtn.on('click', closeOverlay);
            });

          });
        });
      }

    });

    // New close handler
    function closeOverlay(e) {
      e.preventDefault();
      var $target = $(e.target)
      if( $target.is(".lot-screen") || $target.is(".close") ) {
        $lotInner.stop(true).animate({opacity: 0}, 200, function(){
          $lotScreen.animate({opacity: 0},300).css("z-index","-1").hide();
          $lotInner.find(".lot-content").remove();
          $lotInner.hide();
        });
      }
    }

    $(".lot-file").click(function() {
      var location = $(this).attr("href");
      window.open( location, "_blank" );
    });

    //statusListeners();

});
