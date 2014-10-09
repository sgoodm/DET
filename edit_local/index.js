$(document).ready(function(){

	$("#edit_local").addClass("active_menu")
	$("#message").html("Edit a Local Meta")

	$("#submit").hide()
	// $("#meta_list").empty()
	// $("#meta_input").empty()
	$("#meta_list").append('<option id="blank_meta_list_item" class="meta_list_item" value="-----">Select local meta to edit</option>')

	process({ type: "scan" }, function(options) {
		console.log(options)
	    for (var op in options){
	        $("#meta_list").append('<option class="meta_list_item" value="' + options[op] + '">' + options[op] + '</option>')
	    }
    })

	var global 
    $("#meta_list").on("change", function(){
    	$("#submit").show()
    	$("#meta_input").empty()
    	$("#blank_meta_list_item").remove()

    	global = $(this).val()
    	// console.log(global)

		process({ type: "meta" , raster: global }, function(meta) {
        	// console.log(meta)
        	var disabled = ["raster_type", "raster_sub", "raster_year", "raster_name", "created", "modified" ]
		    $.each(meta, function(key, val){
		    	var edit = ""
		    	if (disabled.indexOf(key)>-1){ edit='style="font-style:italic"  readonly' }
		    	$("#meta_input").append('<div class="input_name" '+edit+'>'+ key.substr(key.indexOf("_")+1) +'</div>')
		    	if (key.indexOf("raster_")>-1){
		    		$("#meta_input").append('<input type="text" id="'+ key +'" name="'+ key +'" value="'+ val +'" '+edit+'>')
		    	} else if (key.indexOf("meta_")>-1){
		    		$("#meta_input").append('<textarea id="'+ key +'" name="'+ key +'" rows="5" cols="40" maxlength="400" '+edit+'>'+ val +'</textarea><br>')	
		    	} else {
		    		$("#meta_input").append('<input type="text" id="'+ key +'" name="'+ key +'" value="'+ val +'" '+edit+'>')
		    	}
                										    	
		    })
	        
	    })

	    $("#submit").click(function(){

	    	$("#message").html("working...")
        	$('html, body').animate({ scrollTop: 0 }, 0);
        	
	    	//console.log( $("#input_form").serialize())
			process({ type: "edit" , raster: global, data: $("#input_form").serialize() },function(result) {
	        	console.log(result)
	        	// window.location = self.location
	        	$("#message").html("Meta was successfully edited")
	        	$('html, body').animate({ scrollTop: 0 }, 0);
		    })
	    })

    })


	function process(data, callback){
		$.ajax ({
	        url: "process.php",
	        data: data,
	        dataType: "json",
	        type: "post",
	        async: false,
	        success: function(result) {
			    callback(result)
			},
		    error: function(XMLHttpRequest, textStatus, errorThrown) { 
		        alert("Status: " + textStatus); 
		        alert("Error: " + errorThrown); 
		    }  
	    })
	}


})