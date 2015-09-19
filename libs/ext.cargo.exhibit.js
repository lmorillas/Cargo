// @author @lmorillas

// loads exhibit api after document is ready


// Exhibit_urlPrefix = "http://beta.programaseducativosaragon.es/mw126/extensions/Cargo/libs/Exhibit/";
Exhibit_urlPrefix = "http://api.simile-widgets.org/exhibit/HEAD/";
Exhibit_TimeExtension_urlPrefix = Exhibit_urlPrefix + "extensions/time/";
Exhibit_MapExtension_urlPrefix = Exhibit_urlPrefix + "extensions/map/";
Timeline_ajax_url="http://YOUR_SERVER/javascripts/timeline/timeline_ajax/simile-ajax-api.js";
Timeline_urlPrefix='http://YOUR_SERVER/javascripts/timeline/timeline_js/';
Timeline_parameters='bundle=true';


window.Exhibit_parameters="?autoCreate=false";

// TODO posibility of local files
//ex_url = "http://beta.programaseducativosaragon.es/mw126/extensions/Cargo/libs/Exhibit/exhibit-api.js"

ex_url = "http://api.simile-widgets.org/exhibit/HEAD/exhibit-api.js";
jQuery("#loading_exhibit").show();
//jQuery.getScript(ex_url);

jQuery.ajax({
    url: ex_url,
    dataType: "script",
    cache: true
});

jQuery(document).on("scriptsLoaded.exhibit", function(evt) {
    jQuery("#loading_exhibit").hide();
});

jQuery(document).on("staticComponentsRegistered.exhibit", function(evt) {
    Exhibit.autoCreate();
});
