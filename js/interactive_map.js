jQuery(document).ready( function($){


    // Define constant jQuery objects
    var $lotScreen    = $('<div class="lot-screen"></div>').appendTo( $('body') ),
        $lotInner     = $('<div class="lot-inner"></div>').appendTo( $lotScreen ),  // Append to body for full-screen popup
        $mapContent   = $('.map-content'),
        $mapViewport  = $('.map-viewport'),
        $zoomControls = $('.map-zoom-toggle'),
        $dataHolders  = $('.map-item-data'),
        $mapSVG       = $('.map-lots-layer svg'),

        clickableItems  = '.lot-path.active, .lot-icon.active .golf-hole, .focal-point, .trail-line';

        iconsLayer = $('<g id="lot-points"></g>');
        iconsList = '';

    // Add "active" class to paths to enable them
    $dataHolders.children().each(function(){
      var target = $(this).attr("id");

      //var lottype =  $(this).hasClass('homestead') ? 'lot' : 'home';
      var lottype = ( $(this).attr('data-type') != '') ? $(this).attr('data-type') : lot;

      if ( !isNaN(target) ) {
        target = parseInt(target,10);
        target = target.toString();
      }

        //$mapSVG.find(".lot-path[data-name="+target+"]").addClass("active pointer "+lottype);
        $mapSVG.find("[data-name="+target+"]").each(function(){
          $(this).addClass("active pointer "+lottype);
          //if( $(this).text() === target) { $(this).addClass("active pointer"); }
        });
    });

    // prevent scroll event from bubbling
    $dataHolders[0].addEventListener('wheel', function(event){
      event.stopPropagation();
    });

    $mapSVG.find('')

    // Map Layer Toggles
    $(".map-toggle").click(function() {
      var layer   = $(this).attr("data-layer");
      var $target = $(".map-layer[data-name="+layer+"]");
      var icon    = $(this).children().get(0).outerHTML;

      if( $target.hasClass("active") ) {
        $target.removeClass("active");
        $(this).removeClass("active").html(icon+"SHOW "+layer);
        $("#"+layer).addClass("disabled");

      } else {
        $target.addClass("active");
        $(this).addClass("active").html(icon+"HIDE "+layer);
        $("#"+layer).removeClass("disabled");
      }
    });

    // Cursor Info
    // $(".lot-path, .lot-number, .map-quadrants, .golf-hole, .focal-point, .trail-line").on("mouseenter",function(){
    //     $(".map-info img").hide();
    // }).on("mouseleave", function(){
    //     $(".map-info img").show();
    // });


    // grab the DOM element that you want to be draggable/zoomable:
    // and forward it it to panzoom.


    const zmin = 1,
          zmax = 3,
          zstep = 0.5,
          mapzoom = panzoom( $mapContent[0], {
            zoomSpeed: 0.065, // 6.5% per mouse wheel event
            pinchSpeed: 2, // 2 = zoom two times faster than the distance between fingers
            maxZoom: zmax,
            minZoom: zmin,
            bounds: false,
            boundsPadding: 1,
            beforeWheel: function(e) {
              if (!e.ctrlKey){
                $mapViewport.addClass('show-scroll-key');
                setTimeout(function() {
                    $mapViewport.removeClass('show-scroll-key');
                  }, 1000);
              } else{
                $mapViewport.removeClass('show-scroll-key');
              }
              // allow wheel-zoom only if ctrlKey is down. Otherwise - ignore
              var shouldIgnore = !e.ctrlKey;
              return shouldIgnore;
            }
          });
    var znow = 1;


    $mapContent.addClass('zoomin');
    mapzoom.on('panstart', function(e) { $mapContent.removeClass('zoomin zoomout').addClass('panning'); });
    mapzoom.on('panend', function(e) { $mapContent.removeClass('panning').addClass('zoomin'); });
    mapzoom.on('zoom', function(e) {
      var trans = mapzoom.getTransform();
      updateZoom(trans.scale);
    });

    // Zoom Controls
    $zoomControls.find('.zoom-in').on('click', ()=> {zoomControl("in")} );
    $zoomControls.find('.zoom-out').on('click', ()=> {zoomControl("out")} );

    /*$zoomControls.find('.zoom-range').on('input', (event) => {
        zoomControl("range" , event.target.valueAsNumber);
    }); */


    // ADD RANGE SLIDER
    var rs_sliding = false;
    var $rs = $(".zoom-range-slider");
    $rs.ionRangeSlider({
            skin:'round',
            from: zmin,
            min: zmin,
            max: zmax,
            step: 0.1,
            //grid: false,
            grid_snap: false,
            hide_min_max: true,
            hide_from_to: true,
            onChange: function(data){
                rs_sliding  = true;
                zoomControl("range" , data.from );
            },
            onFinish: function(data){
                rs_sliding  = false;
            }
        });
    var rs_instance = $rs.data('ionRangeSlider');


    function zoomControl(dir, val){

      // if zoom "out" set negative, else step in, or use range value
      var zoom = (dir == "out") ? znow - zstep : znow + zstep;
          zoom = zoomInRange(zoom);
          zoom = isNaN(val) ? zoom : val;


      var center = viewportCenter();
      //console.log(center);

      // if not current zoom (znow), do zoom
      if (zoom != znow){
        if (dir == "range"){
          //don't use smooth positioning with range slider
          mapzoom.zoomAbs(center.x,center.y,zoom);
        } else{
          mapzoom.smoothZoomAbs(center.x,center.y,zoom);
        }
      }
    }

    // check if zoom is in range and return new value or min/max value
    function zoomInRange(zoom){
      zoom = (zoom < zmin) ? zmin : zoom;
      zoom = (zoom > zmax) ? zmax : zoom;
      return zoom;
    }

    // Update zoom controls when zooming starts
    function updateZoom(zoom){
       if (zoom != znow){
         zoom = zoomInRange(zoom);
         zoom = parseFloat(zoom.toFixed(2));
         //$zoomControls.find('.zoom-range-slider').val(znow);
         if (!rs_sliding){ rs_instance.update({from:zoom}); }
         znow = zoom;
       }
    }

    function viewportCenter(){
      var parent = $mapContent[0].getBoundingClientRect(),
          centerY = (parent.width / 2) + parent.x,
          centerY = (parent.height / 2) + parent.y;
      return {x:centerY, y:centerY};
    }


    // translate page to SVG co-ordinate
    function offsetCenterPoint(element) {
      var pt = $mapSVG[0].createSVGPoint();
      // var pos = element.getScreenCTM();
      // pt.x = pos.e;
      // pt.y = pos.f;

      let clientRect = element.getBBox();
      pt.x = clientRect.x + (clientRect.width / 2);
      pt.y = clientRect.y + (clientRect.height / 2);

      return pt.matrixTransform(element.getScreenCTM());
    }

    /*
    function centerOn(ui) {

      var parent = ui.ownerSVGElement;
      if (!parent)
        throw new Error('ui element is required to be within the scene');
      else
        parent = $mapContent[0];//ui.parentNode;

        var newx, newy,
            transform = mapzoom.getTransform(),
            z = transform.scale,
            //client = ui.getScreenCTM()
            point = offsetCenterPoint(ui),
            container = parent.getBoundingClientRect()
            center = viewportCenter();

        let clientRect = ui.getBoundingClientRect(),
            cx = clientRect.left + clientRect.width / 2,
            cy = clientRect.top + clientRect.height / 2,
            dx = container.width / 2 - cx;
            dy = container.height / 2 - cy;

            // dx = center.x + point.x;
            // dy = center.y + point.y;

        console.log(container);
        console.log(point);
        console.log(center);


        // newx = point.x - container.x ;
        // newy = point.y - container.y ;
        // newx = (container.width / 2) + point.x * z;
        // newy = (container.height / 2) + point.y * z;

        console.log("newx: " + dx + " newy: " + dy);

        //mapzoom.smoothMoveTo(dx, dy);
        mapzoom.moveBy(point.x, point.y, true);
        //mapzoom.smoothZoomAbs(dx, dy, zoomInRange(znow+zstep) );
      }
      */

      function getOffsetXY(e) {
        var offsetX, offsetY;
        var ownerRect = $mapContent[0].getBoundingClientRect();
        offsetX = e.clientX + ownerRect.left;
        offsetY = e.clientY + ownerRect.top;
        return { x: offsetX, y: offsetY };
      }


    $(".lot-holder").hover(function(){
        var thisID = $(this).attr("id");
        var mapLot = getLotbyID(thisID).toggleClass('hover');
    });

    $(".lot-holder").on("click", function() {
          var thisHolder = $(this);
          var thisID = thisHolder.attr("id");
          $(".lot-holder").not(thisHolder).removeClass("active");
          thisHolder.addClass("active");

          /*thisHolder.find(".lot-images-inner").children("img").each(function(){
            var newSrc = $(this).attr("data-src");
            if(newSrc) $(this).attr("src", newSrc);
          }); */

          var mapLot = getLotbyID(thisID);

          if (mapLot){
            mapLot.trigger('click');
          }

    });

    function getLotbyID(id){
      return $mapContent.find('.lot-path[data-name="'+id+'"]');
    }


    // Add event handlers to get target content and launch overlay popup
    $(clickableItems).on( "click", function(e){
      e.preventDefault();
      var lot = $(this);
      var lotID = lot.attr("data-name");

      var offset = getOffsetXY(e);
      var center = viewportCenter(),
          newx = center.x + offset.x,
          newy = center.y + offset.y;

      //console.log("off.x: "+offset.x+"\t off.y: "+offset.y);
      //console.log("new.x: "+newx+"\t new.y: "+newy);
      //mapzoom.smoothMoveTo(newx, newy, znow);

      mapzoom.centerOn(this);


      //if the group is disabled abort!
      if ( lot.parents().hasClass("disabled") ) return;

      if (lotID && lotID != "undefined") {
        lotID = lotID.replace("_","");
        //lotID = String(lotID).padStart(3, '0'); //pad num to 3 decimals
      } else {
        lotID = lot.attr("data-id");
      }


      var $closeBtn = $('<i class="close fa fa-times" aria-hidden="true"></i>');

      var msg = "Info coming soon.";
      var lotCaption = "";
      var planInfo = "";
      var lotContent = $dataHolders.find("#"+lotID+' .popup-content').html();


      if (lotContent) {

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

    // var cursorRules ='<style>';
    //     cursorRules += newCursorRule(".zoomin", "f00e", 24, '#fff',"#5b7cad",1.5, true);
    //     cursorRules += newCursorRule(".zoomout", "f010", 24, "#5b7cad");
    //     cursorRules += newCursorRule(".panning", "f31e", 24, "#5b7cad","#fff", 0.5, true, 45);
    //     cursorRules += newCursorRule(".pointer", "f0a6", 24, "#5b7cad", "#fff",1, true -45,);
    //     cursorRules += '</style>';
    //
    // $('head').append( $(cursorRules) );

    function newCursorRule( selector = '.mycursor', content, size = 24, fill='', stroke='',strokewidth='1', shadow=false, rotate=0) {
      //utf code for Font Awesome icon in the base form. Ex: "f00e"
      content = (!content) ? '' : String.fromCharCode(parseInt(content, 16));

      if (!fill && !stroke){
        fill = "#000000";
      }

      var canvas = document.createElement("canvas");
      canvas.width = size*1.5;
      canvas.height = size*1.5;
      var center = canvas.width / 2;
      var offset = canvas.width - size;
      //document.body.appendChild(canvas);
      var ctx = canvas.getContext("2d");
      ctx.fillStyle = fill;
      ctx.strokeStyle = stroke;
      ctx.lineWidth = strokewidth;
      ctx.font = "900 "+size+"px FontAwesome";
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";

      if (!rotate || rotate != 0){
        ctx.translate(center, center);
        ctx.rotate(rotate * Math.PI / 180);
        center = 0;
      }
      if (shadow != false){
        ctx.shadowOffsetX = 1;
        ctx.shadowOffsetY = 3;
        ctx.shadowColor="rgba(0,0,0,0.4)";
        ctx.shadowBlur=5;
      }
      if (fill != ''){
        ctx.fillText(content, center, center);
      }
      if (stroke != ''){
        ctx.shadowColor="transparent";
        ctx.shadowBlur=0;
        ctx.strokeText(content, center, center);
      }

      var dataURL = canvas.toDataURL('image/png');
      var cssRule = selector +'{cursor:url('+dataURL+') '+offset+' '+offset+', auto !important;}\n';
      return cssRule;

    }

});
