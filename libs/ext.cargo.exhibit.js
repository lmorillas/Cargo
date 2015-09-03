// @author @lmorillas

// loads exhibit api after document is ready

var ex_url = "http://api.simile-widgets.org/exhibit/current/exhibit-api.js";

jQuery(document).ready(function() {
    jQuery.getScript(ex_url);
})
