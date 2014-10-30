$(document).ready(function(){

	$("#edit_local").addClass("active_menu")
	$("#message").html("Edit a Local Meta")

	$("#submit").hide()

	$("#meta_list").append('<option id="blank_meta_list_item" class="meta_list_item" value="-----">Select local meta to edit</option>')

	process({ type: "scan" }, function(options) {
		console.log(options)
	    for (var op in options){
	        $("#meta_list").append('<option class="meta_list_item" value="' + options[op] + '">' + options[op] + '</option>')
	    }
    })
    
	var meta_master = readJSON("../meta_fields.json")

	var local 
    $("#meta_list").on("change", function(){
    	$("#submit").show()
    	$("#meta_input").empty()
    	$("#blank_meta_list_item").remove()

    	local = $(this).val()
    	// console.log(local)

    	var meta_this = readJSON("../../resources/" + local + "/meta_info.json")

    	$.each(meta_master, function(field, props){

    		var value = ""
    		if (meta_this[field]){
    			value = meta_this[field]
    		} else if (props.default != undefined){
    			value = props.default
    		}

	    	var edit = ''

	    	if (props.edit == false){ edit='style="font-style:italic"  readonly' }

	    	$("#meta_input").append('<div class="input_name" '+edit+'>'+ field.substr(field.indexOf("_")+1) +'</div>')

	    	switch(props.type){
	    		case "text":
		    		$("#meta_input").append('<textarea id="'+ field +'" name="'+ field +'" rows='+ props.options.rows +' cols='+ props.options.cols +' maxlength='+ props.options.maxlength +' '+edit+'>'+ value +'</textarea><br>')	
		    		break

		    	case "num": 
		    		$("#meta_input").append('<input type="number" id="'+ field +'" name="'+ field +'" value="'+ value +'" min='+ props.options.min +' max='+ props.options.max +' step='+ props.options.step +'  '+edit+'>')
		    		break

		    	case "select":
		    		$("#meta_input").append('<select id="'+ field +'" name="'+ field +'">')
		    		for (var i=0;i<props.options.values.length;i++){
			    		$("#"+field).append('<option value="'+props.options.values[i]+'">'+props.options.text[i]+'</option>')
		    		}
		    		$("#meta_input").append('</select>')
		    		break

		    	case "time":
		    		// var date = new Date();
					// date.setUTCSeconds(Math.floor(parseInt(value)/1000)); 
		    		$("#meta_input").append('<select id="'+ field +'" name="'+ field +'" '+edit+'><option value="'+value+'">'+ value +'</option></select')
		    		break		    	
		    	
		    	default: // props.type == "str"
		    		$("#meta_input").append('<input type="text" id="'+ field +'" name="'+ field +'" value="'+ value +'" '+edit+'>')
		    		break
	    	}
            										    	
	    })

		// process({ type: "meta" , raster: local }, function(meta) {
  //       	// console.log(meta)
  //       	var disabled = ["raster_type", "raster_sub", "raster_year", "raster_name", "created", "modified" ]
		//     $.each(meta, function(key, val){
		//     	var edit = ""
		//     	if (disabled.indexOf(key)>-1){ edit='style="font-style:italic"  readonly' }
		//     	$("#meta_input").append('<div class="input_name" '+edit+'>'+ key.substr(key.indexOf("_")+1) +'</div>')
		//     	if (key.indexOf("raster_")>-1){
		//     		$("#meta_input").append('<input type="text" id="'+ key +'" name="'+ key +'" value="'+ val +'" '+edit+'>')
		//     	} else if (key.indexOf("meta_")>-1){
		//     		$("#meta_input").append('<textarea id="'+ key +'" name="'+ key +'" rows="5" cols="40" maxlength="400" '+edit+'>'+ val +'</textarea><br>')	
		//     	} else {
		//     		$("#meta_input").append('<input type="text" id="'+ key +'" name="'+ key +'" value="'+ val +'" '+edit+'>')
		//     	}
                										    	
		//     })
	        
	 //    })

	    $("#submit").click(function(){

	    	$("#message").html("working...")
        	$('html, body').animate({ scrollTop: 0 }, 0);
        	
	    	//console.log( $("#input_form").serialize())
			process({ type: "edit" , raster: local, data: $("#input_form").serialize() },function(result) {
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

	function readJSON(file) {
	    var request = $.ajax({
	    	type: "GET",
			dataType: "json",
			url: file + "?nocache=" + (new Date()).getTime(),
			async: false,
	    })
	    return request.responseJSON
	};



})