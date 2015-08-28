// @author @lmorillas

// Exhibit is loaded with autocreate=false

console.log("Cargado ext.cargo.exhibit 12");

Exhibit.jQuery = jQuery;
if (!Exhibit._jQueryExists) {
    jQuery.noConflict();
}
Exhibit.triggerjQueryLoaded();


jQuery(document).bind("scriptsLoaded.exhibit", function() {
    console.log("creating database");
    window.database = Exhibit.Database.create();
    console.log("created database");
});

function loadDataIntoDB() {
    var links, link, importer;

    links = Exhibit.jQuery("head > link[rel='exhibit/data']").toArray();
    importer = Exhibit.Importer.getImporter("text/csv");

    // cargo visualization only uses a link
    link = links.shift();

    if (typeof importer !== "undefined" && importer !== null) {
        importer.load(link, window.database);
        Exhibit.jQuery(document.body).trigger("dataload.exhibit");
    } else {
        Exhibit.Debug.warn("No csv importer");
    }
}

Exhibit.jQuery(document).one("registerJSONPImporters.exhibit", function(){
    window.setTimeout(loadDataIntoDB, 0);
});

function initExhibit(){
    Exhibit.create = function( database ) {
        return new Exhibit._Impl(database);
    };

    console.log("creating exhibit");
    window.exhibit = Exhibit.create();
    console.log("configuring from dom");
    window.exhibit.configureFromDOM();
}

Exhibit.jQuery(document).one( "dataload.exhibit", function() {
    window.setTimeout( initExhibit, 0 ) ;
});
