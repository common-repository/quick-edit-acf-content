jQuery(document).ready(function($) {
    var qacfPanelOpen = false;

    function qacfCheckURLParams() {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.has('updated') && urlParams.get('updated') === 'true';
    }

    function qacfSetPanelCookie() {
        var panelPosition = $('#quick-acf-panel').position();
        var panelSize = {
            width: $('#quick-acf-panel').width(),
            height: $('#quick-acf-panel').height()
        };
        document.cookie = `qacfPanelPositionTop=${panelPosition.top}; path=/`;
        document.cookie = `qacfPanelPositionLeft=${panelPosition.left}; path=/`;
        document.cookie = `qacfPanelWidth=${panelSize.width}; path=/`;
        document.cookie = `qacfPanelHeight=${panelSize.height}; path=/`;
        document.cookie = `qacfPanelScroll=${$("#quick-acf-panel-content").scrollTop()}; path=/`;
        document.cookie = `qacfLiveEdit=${$("#qacf_liveedit_swift").attr('ref')}; path=/`;
    }

    function qacfLoadPanelCookie() {
        var cookies = document.cookie.split(';');
        cookies.forEach(function(cookie) {
            var parts = cookie.split('=');
            var name = parts[0].trim();
            var value = parts[1];
            switch(name) {
                case 'qacfPanelPositionTop':
                    $('#quick-acf-panel').css('top', value + 'px');
                    break;
                case 'qacfPanelPositionLeft':
                    $('#quick-acf-panel').css('left', value + 'px');
                    break;
                case 'qacfPanelWidth':
                    $('#quick-acf-panel').css('width', value + 'px');
                    break;
                case 'qacfPanelHeight':
                    $('#quick-acf-panel').css('height', value + 'px');
                    break;
                case 'qacfPanelScroll':
                    $("#quick-acf-panel-content").attr('scrollto', value);
                    break;      
            }
        });
    }

    function qacfTogglePanel() {
        if (!qacfPanelOpen) {
            $('#quick-acf-panel').addClass("open_panel");
            qacfPanelOpen = true;
            qacfSetPanelCookie();
        } else {
            $('#quick-acf-panel').removeClass("open_panel");
            qacfPanelOpen = false;
        }
    }


 $('#close-quick-acf-panel').on('click', function(e) {
        e.preventDefault();
        qacfTogglePanel();
    });


    $('#wp-admin-bar-qacf-admin-bar-button a').on('click', function(e) {
        e.preventDefault();
        qacfTogglePanel();
    });

    if (qacfCheckURLParams()) {
        qacfLoadPanelCookie();
        qacfTogglePanel();
    }

    $( "#quick-acf-panel").resizable({
        handles: "n, e, s, w, sw, se",
        stop: function(event, ui) {
            qacfSetPanelCookie();
        }
    }).draggable({
        handle: ".quick-acf-header",
        drag: function(event, ui) {
            qacfSetPanelCookie();
        }
    });

    $("#quick-acf-panel-content").scroll(function() {
        if ($(this).scrollTop() > 100) {
            $("a.header-submit-btn").addClass("showme");
        } else {
            $("a.header-submit-btn").removeClass("showme");
        }
    });

    $('#quick-acf-panel a.header-submit-btn').on('click', function(e) {
        e.preventDefault(); 
        qacfSetPanelCookie();
        $("#quick-acf-panel-content form#acf-form").trigger("submit");
    });


    $("#qacf_liveedit_swift").on('click', function(e) {
        e.preventDefault(); 

        if($(this).attr("ref") == "Off")
        {
            $(this).attr("ref", "On");
            $(this).text("Live edit: On");
        }
        else
        {
             $(this).attr("ref", "Off");
             $(this).text("Live edit: Off");
        }
        qacfSetPanelCookie();
        $("#quick-acf-panel-content form#acf-form").trigger("submit");
    });



    var $panelContent = $("#quick-acf-panel-content");
    var scrollToValue = $panelContent.attr("scrollto");
    if (scrollToValue !== undefined) {
        $panelContent.scrollTop(parseInt(scrollToValue));
    }

    jQuery("span.quick-acf-hotspot").each(function() {
        var element = jQuery(this).attr('title');
        jQuery(element).on("keyup keypress", function() {
            jQuery("span.quick-acf-hotspot[title='"+element+"']").text(jQuery(this).val());
        });
    });
});
