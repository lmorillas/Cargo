// @author @lmorillas

// Exhibit is loaded with autocreate=false

$(document).one("scriptsLoaded.exhibit", function() {
    window.database = Exhibit.Database.create();
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

$(document).one("registerJSONPImporters.exhibit", function(){
    window.setTimeout(loadDataIntoDB, 0);
});

$(document).one( "dataload.exhibit", function() {

    Exhibit.create = function( database ) {
        return new Exhibit._Impl(database);
    };

    window.exhibit = Exhibit.create();
    window.exhibit.configureFromDOM();
});
