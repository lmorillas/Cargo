// @author @lmorillas

// loads exhibit api after document is ready



console.log("running ext.cargo.exhibit 72");

var ex_url = "http://api.simile-widgets.org/exhibit/current/exhibit-api.js?autoCreate=false&bundle=false";
var ex_map_url = "http://api.simile-widgets.org/exhibit/current/extensions/map/map-extension.js";

/*
    jQuery(document).bind("scriptsLoaded.exhibit", function() {
        window.database = Exhibit.Database.create();

    });


jQuery(document).bind("staticComponentsRegistered.exhibit", function() {
    console.log("staticComponentsRegistered.exhibit");
    window.database = Exhibit.Database.create();
    window.database.loadLinks();
});
*/

jQuery(document).ready(function() {
    console.log("ready!");

    jQuery.getScript(ex_url, function() {
        console.log(Exhibit.parms);
    })
});
//        jQuery.getScript( ex_map_url );
/*
        function fDone() {
            console.log(" dataload ");
            console.log("The database has ", window.database.getAllItemsCount(), " items.");

            Exhibit.create = function(database) {
                return new Exhibit._Impl(database);
            };
            window.exhibit = Exhibit.create();

            // window.exhibit = new Exhibit._Impl(database);
            console.log(' Exhibit created ');
            window.setTimeout(window.exhibit.configureFromDOM, 0);
            console.log(' Exhibit configured From DOM ');
        };

        jQuery(document.body).one("dataload.exhibit", fDone);
*/
jQuery(document).bind("delayCreation.exhibit", function(evt, delayID) {
    console.log("delayCreation.exhibit");
});

jQuery(document).bind("delayFinished.exhibit", function(evt, delayID) {
    console.log("delayCreation.exhibit");
});

jQuery(document).bind("localeSet.exhibit", function(evt, localeURLs) {
    console.log("localeSet.exhibit");
});

jQuery(document).bind("error.exhibit", function(evt, e, msg) {
    console.log("error.exhibit");
});

jQuery(document).bind("localeLoaded.exhibit", function(evt) {
    console.log("localeloaded.exhibit");
});

jQuery(document).bind("scriptsLoaded.exhibit", function(evt) {
    console.log("scriptsLoaded.exhibit");
});

jQuery(document).bind("staticComponentsRegistered.exhibit", function(evt) {
    console.log("staticComponentsRegistered.exhibit");

});

jQuery(document).bind("exhibitConfigured.exhibit", function(evt, ex) {
    console.log("exhibitConfigured.exhibit");
});


jQuery(document).bind("loadExtensions.exhibit", function(evt) {
    console.log("loadExtensions.exhibit");
});
