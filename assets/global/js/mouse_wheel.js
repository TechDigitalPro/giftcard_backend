/**
 * Created by Administrator on 11/6/2019.
 */
var maxSections = $(".page-section").length;
var sectionNamePrefix = 'page-section';

var currSection = 1;
if($.cookie('page_scroll_position') != "" && $.cookie('page_scroll_position') != null && $.cookie('page_scroll_position') != undefined){
    currSection = $.cookie('page_scroll_position');
}

function scrollToHash(e,sectionNumber) {
    console.log('sectionNumber', sectionNumber);
    var hash = '#' + sectionNamePrefix + sectionNumber;
    var target = $(hash);
    target = target.length ? target : $('[name=' + hash.slice(1) +']');
    // e = event;
    if (target.length) {
        e.preventDefault();
        $('html,body').stop().animate({
            scrollTop: target.offset().top
        }, 600, function() {
            //location.hash = hash;
        });
        $.cookie('page_scroll_position',sectionNumber)
    }
}

/////////////////////////////////////////////////////////////////

// Based on http://www.sitepoint.com/html5-javascript-mouse-wheel/
var handleWheel = function (event)
{
    // cross-browser wheel delta
    // Chrome / IE: both are set to the same thing - WheelEvent for Chrome, MouseWheelEvent for IE
    // Firefox: first one is undefined, second one is MouseScrollEvent
    var e = window.event || event;
    // Chrome / IE: first one is +/-120 (positive on mouse up), second one is zero
    // Firefox: first one is undefined, second one is -/+3 (negative on mouse up)
    var delta = Math.max(-1, Math.min(1, e.wheelDelta || -e.detail));

    // Do something with `delta`

    if (delta > 0)	{
        if (currSection > 1) { currSection--; }
        scrollToHash(event,currSection);
    }
    else {
        if (currSection < maxSections) { currSection++; }
        scrollToHash(event,currSection);

    }
    e.preventDefault();
};

var add_mouse_wheel_event_listener = function (scrollHandler)
{
    if (window.addEventListener)
    {
        // IE9+, Chrome, Safari, Opera
        window.addEventListener("mousewheel", scrollHandler, {passive:false});
        // Firefox
        window.addEventListener("DOMMouseScroll", scrollHandler, {passive:false});
    }
    else
    {
        // // IE 6/7/8
        window.attachEvent("onmousewheel", scrollHandler);
    }
};

window.onkeyup = function(event) {
    var key = event.keyCode ? event.keyCode : event.which;

    if (key == 38) { //up directive
        if (currSection > 1) { currSection--; }
        scrollToHash(event,currSection);
    }else if (key == 40) { //down directive
        if (currSection < maxSections) { currSection++; }
        scrollToHash(event,currSection);
    }
};

var addMouseWheelEventListener = function(){
    add_mouse_wheel_event_listener(handleWheel);
};