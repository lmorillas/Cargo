// @author @lmorillas

// loads exhibit api after document is ready


// Exhibit_urlPrefix = "http://beta.programaseducativosaragon.es/mw126/extensions/Cargo/libs/Exhibit/";
Exhibit_urlPrefix = "http://api.simile-widgets.org/exhibit/HEAD/";
Exhibit_TimeExtension_urlPrefix = Exhibit_urlPrefix + "extensions/time/";
Exhibit_MapExtension_urlPrefix = Exhibit_urlPrefix + "extensions/map/";

// TODO posibility of local files
//ex_url = "http://beta.programaseducativosaragon.es/mw126/extensions/Cargo/libs/Exhibit/exhibit-api.js"

ex_url = "http://api.simile-widgets.org/exhibit/HEAD/exhibit-api.js";

// get exhibit-api js
jQuery.getScript(ex_url);

// important: autoCreate=false
jQuery(document).on("scriptsLoaded.exhibit", function(evt) {
    Exhibit.params.autoCreate = false;
});

jQuery(document).on("staticComponentsRegistered.exhibit", function(evt) {
    Exhibit.autoCreate();
});
