// @author @lmorillas

// loads exhibit api after document is ready



console.log("running ext.cargo.exhibit 90");
jQuery(document).ready(function() {
    console.log(" doc ready ");
    jQuery(document).bind("staticComponentsRegistered.exhibit", function(evt) {
        console.log("Static components registered ... ");
        Exhibit.autoCreate();
});

});

/*
jQuery(document).on("staticComponentsRegistered.exhibit", function() {
   // window.database.loadLinks();
    console.log("static components registered ...");
});

jQuery(document.body).on("dataload.exhibit", function(){
     console.log("The database has ", window.database.getAllItemsCount(), " items.");
});

/*
jQuery(document).ready(function() {
    console.log("ready!");

    jQuery.getScript(ex_url, function() {
        console.log("Exhibit.parms");
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

jQuery(document).on("delayCreation.exhibit", function(evt, delayID) {
    console.log("delayCreation.exhibit");
});

jQuery(document).on("delayFinished.exhibit", function(evt, delayID) {
    console.log("delayCreation.exhibit");
});

jQuery(document).on("localeSet.exhibit", function(evt, localeURLs) {
    console.log("localeSet.exhibit");
});

jQuery(document).on("error.exhibit", function(evt, e, msg) {
    console.log("error.exhibit");
});

jQuery(document).on("localeLoaded.exhibit", function(evt) {
    console.log("localeloaded.exhibit");
});

jQuery(document).on("scriptsLoaded.exhibit", function(evt) {
    console.log("scriptsLoaded.exhibit");
});

jQuery(document).on("staticComponentsRegistered.exhibit", function(evt) {
    console.log("staticComponentsRegistered.exhibit");

});

jQuery(document).on("exhibitConfigured.exhibit", function(evt, ex) {
    console.log("exhibitConfigured.exhibit");
});


jQuery(document).on("loadExtensions.exhibit", function(evt) {
    console.log("loadExtensions.exhibit");
});
