$(document).ready(function(){

	$("#edit_global").addClass("active_menu")
	$("#message").html("Edit a Global Meta")

	$("#submit").hide()

	$("#meta_list").append('<option id="blank_meta_list_item" class="meta_list_item" value="-----">Select global meta to edit</option>')

	scanDir({ type: "scan", dir: "../../uploads/globals/processed" }, function(options) {
	    for (var op in options){
	        $("#meta_list").append('<option class="meta_list_item" value="' + options[op] + '">' + options[op] + '</option>')
	    }
    })

	var meta_master = readJSON("../meta_fields.json")

	var global 
    $("#meta_list").on("change", function(){
    	$("#submit").show()
    	$("#meta_input").empty()
    	$("#blank_meta_list_item").remove()

    	global = $(this).val()

    	var meta_this = readJSON("../../uploads/globals/processed/" + global + "/meta_info.json")

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


	    $("#submit").click(function(){
	    	
	    	$("#message").html("working...")
        	$('html, body').animate({ scrollTop: 0 }, 0);

			$.ajax ({
		        url: "process.php",
		        data: { type: "edit" , raster: global, data: $("#input_form").serialize() },
		        dataType: "text",
		        type: "post",
		        async: false,
		        success: function(result) {
		        	// console.log(result)
		        	$("#message").html("Meta was successfully edited")
		        	$('html, body').animate({ scrollTop: 0 }, 0);
		        }
		    })
	    })

    })

	function scanDir(data, callback){
		$.ajax ({
	        url: "process.php",
	        data: data,
	        dataType: "json",
	        type: "post",
	        async: false,
	        success: function(result) {
			    callback(result)
			}
	    })
	}

	function readJSON(file) {
	    var request = $.ajax({
	    	type: "GET",
			dataType: "json",
			url: file,
			async: false,
	    })
	    return request.responseJSON
	};

})