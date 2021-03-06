// @author @lmorillas

// loads exhibit api after document is ready


// Exhibit_urlPrefix = "http://beta.programaseducativosaragon.es/mw126/extensions/Cargo/libs/Exhibit/";
Exhibit_urlPrefix = "http://api.simile-widgets.org/exhibit/HEAD/";
Exhibit_TimeExtension_urlPrefix = Exhibit_urlPrefix + "extensions/time/";
Exhibit_MapExtension_urlPrefix = Exhibit_urlPrefix + "extensions/map/";

window.Exhibit_parameters="?autoCreate=false";

window.tableStyler = function(table, database){
    $(table).addClass("cargoTable");
};

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
