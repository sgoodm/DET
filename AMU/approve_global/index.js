$(document).ready(function(){

	$("#approve_global").addClass("active_menu")
	$("#message").html("Approve / Deny a Global Raster Submission")
	resetPage()

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

	function resetPage(){
		$("#submit, #reject").hide()
		$("#meta_list").empty()
		$("#meta_input").empty()
		$("#meta_list").append('<option id="blank_meta_list_item" class="meta_list_item" value="-----">Select submitted global</option>')

		scanDir({ type: "scan", dir: "../../uploads/globals/pending" }, function(options) {
		    for (var op in options){
		        $("#meta_list").append('<option class="meta_list_item" value="' + options[op] + '">' + options[op] + '</option>')
		    }
	    })
	}

    $("#meta_list").on("change", function(){

    	$("#submit, #reject").show()
    	$("#meta_input").empty()
    	$("#blank_meta_list_item").remove()

      	var global = $(this).val()
    	// console.log(global)

    	var file
    	scanDir({ type: "scan", dir: "../../uploads/globals/pending/"+global }, function(files) {
    		$.each(files, function(key, val){
    			if (val != "meta_info.json"){
    				file = val
    				console.log(global)
    				console.log(file)
    			}
    		})
    	})

    	$("#meta_input").append('<div class="input_name" >Raster File</div>')
    	$("#meta_input").append('<a href="../../uploads/globals/pending/' + global +'/'+ file + '">' + file + '</a>')

		$.ajax ({
	        url: "process.php",
	        data: { type: "meta" , global: global },
	        dataType: "json",
	        type: "post",
	        async: false,
	        success: function(meta) {
	        	// console.log(meta)
	        	var disabled = ["created", "modified" ]
	        	var required = ["raster_type", "raster_sub", "raster_year", "meta_summary", "meta_data_provider", "meta_year_data_represents", "meta_license_terms", , "meta_license_terms_website" ]
			    $.each(meta, function(key, val){
			    	var edit = ""
			    	if (disabled.indexOf(key)>-1){ edit = 'style="font-style:italic"  readonly' }
		    		if (required.indexOf(key)>-1){ edit += ' class="required"' }
			    	$("#meta_input").append('<div class="input_name" '+edit+'>'+ key.substr(key.indexOf("_")+1) +'</div>')
			    	if (key.indexOf("raster_")>-1){
			    		$("#meta_input").append('<input type="text" id="'+ key +'" name="'+ key +'" value="'+ val +'" '+edit+'>')
			    	} else if (key.indexOf("meta_")>-1){
			    		$("#meta_input").append('<textarea id="'+ key +'" name="'+ key +'" rows="5" cols="40" maxlength="400" '+edit+'>'+ val +'</textarea><br>')	
			    	} else {
			    		$("#meta_input").append('<input type="text" id="'+ key +'" name="'+ key +'" value="'+ val +'" '+edit+'>')
			    	}
                    										    	
			    })
	        },
		    error: function(XMLHttpRequest, textStatus, errorThrown) { 
		        alert("Status: " + textStatus); 
		        alert("Error: " + errorThrown); 
		    }   
	    })
		
		if (!$("#reason").length){
			$("#middle").append('<br><br><div class="input_name" >Reason for rejecting:</div>')
			$("#middle").append('<textarea id="reason" rows="5" cols="40" maxlength="400" ></textarea><br>')	
		}

 		//--validation
		$("#raster_type, #raster_sub").on("change", function(){
			var val = $(this).val()
			val = val.replace(/ /g, "_")
			val = val.toLowerCase()
			val = val.replace(/[^a-zA-z]/g, '')
			val = val.replace("__","_")
			$(this).val(val)
			console.log($(this).val())
		})

		$("#raster_year").on("change", function(){
			var val = $(this).val()
			if (val > 9999){
				$(this).val(9999)
			}
		})
		//--

	    $("#submit").click(function(){
	    	
	    	$("#message").html("working...")
        	$('html, body').animate({ scrollTop: 0 }, 0);

			var required = true
			$(".required").each(function(){
				if ($(this).val() == ""){
					required = false
				}
			})

			if (required == false){
				console.log("please complete the required fields")
				$("#message").html("please complete the required fields")
	            $('html, body').animate({ scrollTop: 0 }, 0);
				return 
			}

	    	//console.log( $("#input_form").serialize())
	    	console.log(global)
	    	console.log(file)
			$.ajax ({
		        url: "process.php",
		        data: { type: "crop" , global: global, file: file, data: $("#input_form").serialize() },
		        dataType: "text",
		        type: "post",
		        async: false,
		        success: function(result) {
		        	console.log(result)
		        	// window.location = self.location
		        	$("#message").html("Global has been approved and processed")
		        	$('html, body').animate({ scrollTop: 0 }, 0);
		        	resetPage()
		        	window.location = self.location		
		        }
		    })

	    })

		$("#reject").click(function(){

			$("#message").html("working...")
        	$('html, body').animate({ scrollTop: 0 }, 0);
		
			$.ajax ({
		        url: "process.php",
		        data: { type: "reject" , global: global, file: file, reason: $("#reason").val() },
		        dataType: "text",
		        type: "post",
		        async: false,
		        success: function(result) {
		        	console.log(result)
		        	$("#message").html("Global has been rejected")
		        	$('html, body').animate({ scrollTop: 0 }, 0);
		        	resetPage()
		        	window.location = self.location
		        }
		    })

		})

    })



})