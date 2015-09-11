// @author @lmorillas

// loads exhibit api after document is ready


// Exhibit_urlPrefix = "http://beta.programaseducativosaragon.es/mw126/extensions/Cargo/libs/Exhibit/";
Exhibit_urlPrefix = "http://api.simile-widgets.org/exhibit/HEAD/";
//ex_url = "http://beta.programaseducativosaragon.es/mw126/extensions/Cargo/libs/Exhibit/exhibit-api.js"

ex_url = "http://api.simile-widgets.org/exhibit/HEAD/exhibit-api.js?autoCreate=false";

// jQuery.getScript(ex_url);

jQuery.ajax({
    beforeSend: function(){
        jQuery("#loading_exhibit").show();
    },
    url: ex_url,
    dataType: "script",
    cache: true
});


jQuery(document).on("scriptsLoaded.exhibit", function(evt) {
    Exhibit.params.autoCreate = false;
    jQuery("#loading_exhibit").hide();
});

jQuery(document).on("staticComponentsRegistered.exhibit", function(evt) {
    Exhibit.autoCreate();
});


/* Test with cache?
jQuery.ajax({
      url: ex_url,
      dataType: "script",
      cache: true
}).done(function() {
        ...
});
*/