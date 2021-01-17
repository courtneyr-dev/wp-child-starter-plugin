jQuery(document).ready(function(){
	jQuery(".hider").hide();
	jQuery("#queryheader").click(function(e){
		jQuery("#query").toggle();
		e.preventDefault();
		return false;
	});
	jQuery("#editquery").click(function(){
		jQuery("#filters").toggle();
	});
	jQuery("#newquery").click(function(){
		jQuery("#querynav").toggle();
	});
	// set up main objects
	var exportlinks = jQuery("#databasebrowser .export");
	// when an export link is clicked, export the data using AJAX
	exportlinks.on("click", function(event){
		// get the format
		var format = this.id;
		if (bike.length) {
			jQuery.post(
				ajaxurl,
				{
					action : 'databasebrowser-export',
					format : format
				},
				function(response) {
					jQuery("#bike").html(response);
				}
			);
		}
		event.preventDefault();
	});
});