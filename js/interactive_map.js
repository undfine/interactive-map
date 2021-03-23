
jQuery(document).ready(function($){

    // Append lot-screen to body for full-screen popup
    $('body').append( $('.lot-screen') );

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

    $(".map-viewport, .map-zoom-toggle").click(function(e){
        if( $(e.target).is(".lot-number, .lot-path, .golf-hole, .focal-point, .trail-line") || $(e.target).is(".map-q1") || $(e.target).is(".map-q2") || $(e.target).is(".map-q3") || $(e.target).is(".map-q4")) {return;} else {

            if($(".map-viewport").hasClass("zoomq1") || $(".map-viewport").hasClass("zoomq2") || $(".map-viewport").hasClass("zoomq3") || $(".map-viewport").hasClass("zoomq4")) {
                $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq1 zoomq2 zoomq3 zoomq4").addClass("zoom1");

            } else if($(".map-viewport").hasClass("zoom1")) {

                $(".map-viewport").removeClass("zoom1").addClass("zoom2");
                $(".map-content").css("left","12.5%").css("top","12.5%");
                $(".map-zoom-toggle span").removeClass("active");
                $(".map-zoom-toggle [data-zoom='zoom2']").addClass("active");

            } else if($(".map-viewport").hasClass("zoom2")) {

                $(".map-viewport").removeClass("zoom2").addClass("zoom3");
                $(".map-content").css("left","16.667%").css("top","16.667%");
                $(".map-zoom-toggle span").removeClass("active");
                $(".map-zoom-toggle [data-zoom='zoom3']").addClass("active");

            } else if($(".map-viewport").hasClass("zoom3")) {

                $(".map-viewport").removeClass("zoom3").addClass("zoom1");
                $(".map-content").css("left","0%").css("top","0%");
                $(".map-zoom-toggle span").removeClass("active");
                $(".map-zoom-toggle [data-zoom='zoom1']").addClass("active");

            } else {
              $(".map-viewport").addClass("zoom1");
              $(".map-zoom-toggle span").removeClass("active");
              $(".map-zoom-toggle [data-zoom='zoom1']").addClass("active");
              $(".map-content").css("left","0%").css("top","0%");
            }
        }
    });

    $(".map-q1").click(function(){
      if($(".map-viewport").hasClass("zoomq1")) {
          $(".map-viewport").removeClass("zoomq1");
      } else {
          $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq2 zoomq3 zoomq4").addClass("zoomq1");
      }
    });
    $(".map-q2").click(function(){
      if($(".map-viewport").hasClass("zoomq2")) {
          $(".map-viewport").removeClass("zoomq2");
      } else {
          $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq1 zoomq3 zoomq4").addClass("zoomq2");
      }
    });
    $(".map-q3").click(function(){
      if($(".map-viewport").hasClass("zoomq3")) {
          $(".map-viewport").removeClass("zoomq3");
      } else {
          $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq2 zoomq1 zoomq4").addClass("zoomq3");
      }
    });
    $(".map-q4").click(function(){
      if($(".map-viewport").hasClass("zoomq4")) {
          $(".map-viewport").removeClass("zoomq4");
      } else {
        $(".map-viewport").removeClass("zoom1 zoom2 zoom3 zoomq2 zoomq3 zoomq1").addClass("zoomq4");
    }
    });
    $(".map-content").draggable({ containment: "parent", scroll: false });
    $(".map-content").on("drag", function(){
        $(this).css("background-position", "-" + $(this).position().left + "px -" + $(this).position().top + "px");
    });

    // Map Info Follows cursor
    // $(".map-content").mousemove(function(e) {
    //     $(".map-info").offset({left: e.pageX + 20,top: e.pageY + 20});
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

    $(".lot-path.active, .golf-hole, .focal-point, .trail-line").on("click", function(e){
    e.preventDefault();
    var lot = $(this);
    var lotID = lot.attr("data-name");

    if (lotID && lotID != "undefined") {
      lotID = lotID.replace("_","");
      if (lotID.length < 2) { lotID = "0"+lotID; }
      if (lotID.length < 3) { lotID = "0"+lotID; }
    } else {
      lotID = lot.attr("data-id");
    }



    var lotInner = $(".lot-inner");
    var lotScreen = $(".lot-screen");
    var msg = "Info coming soon.";
    var lotCaption = "";
    var planInfo = "";
    var lotContent = $(".homesite-lot-data").find("#"+lotID).html();

    if(lotContent && !lot.parents().hasClass("disabled")) {
      lotInner.empty();
      lotInner.stop(true).show().animate({opacity: 0}, 200, function() {
        lotInner.append(lotContent);
        lotScreen.stop(true).css("z-index","99999").show().animate({opacity: 1},200,function(){
          resetListeners();
          lotInner.find(".lot-images-inner").children("div").each(function(){
            var newSrc = $(this).attr("data-src");
            if(newSrc) $(this).css("background-image", "url("+newSrc+")");
          });

          /*
          var contentY = Math.round((lotInner.outerHeight()-lotInner.find(".lot-content").outerHeight())/2);
          lotInner.find(".lot-content").css("margin-top",contentY+"px");
          var innerY = Math.round(lotInner.outerHeight()/2);
          var innerX = Math.round(lotInner.outerWidth()/2);
          if (lotInner.outerHeight() > $(window).outerHeight() ) { innerY = 0; }
          lotInner.css("margin-top","-"+innerY+"px").css("margin-left","-"+innerX+"px");
          */

          lotInner.animate({opacity: 1}, 300, function() {
            statusListeners();
          });

        });
      });
    }

    });

    function resetListeners() {
    $(".close, .lot-screen, .lot-slider-next, .lot-slider-prev").unbind("click");
    $(".lot-desc a").bind("click", function(){
      window.open($(this).attr("href"), "_blank");
    });
    }

    function statusListeners() {
    $(".close, .lot-screen").click(function(e) {
      e.preventDefault();
      if( $(e.target).is(".lot-screen") || $(e.target).is(".close") ) {
        var lotInner = $(".lot-inner");
        var lotScreen = $(".lot-screen");
        lotInner.stop(true).animate({opacity: 0}, 200, function(){
          lotScreen.animate({opacity: 0},300).css("z-index","-1").hide();
          lotInner.find(".lot-content").remove();
          lotInner.hide();
        });
      }
    });
    $(".lot-slider-next").click(function(e) {
      //console.log("clicked lot-slider-next");
      e.preventDefault();
      var slider = $(this).parents(".lot-slider-nav").siblings(".lot-images-inner");
      var maxSlides = slider.children("div").length;
      var curSlide = slider.attr("data-slide");
      if(curSlide == maxSlides) { slider.attr("data-slide", 1); slider.css("margin-left","0%"); }
      else { slider.css("margin-left", "-"+curSlide+"00%"); curSlide++; slider.attr("data-slide", curSlide); }
    });
    $(".lot-slider-prev").click(function(e) {
      //console.log("clicked lot-slider-prev");
      e.preventDefault();
      var slider = $(this).parents(".lot-slider-nav").siblings(".lot-images-inner");
      var maxSlides = slider.children("div").length;
      var curSlide = slider.attr("data-slide");
      if(curSlide == 1) { slider.attr("data-slide", maxSlides); slider.css("margin-left", "-"+(maxSlides-1)+"00%"); }
      else { slider.css("margin-left", "-"+(curSlide-2)+"00%"); curSlide--; slider.attr("data-slide", curSlide); }
    });
    $(".lot-file").click(function() {
      var location = $(this).attr("href");
      window.open( location, "_blank" );
    });
    }

    statusListeners();

});
