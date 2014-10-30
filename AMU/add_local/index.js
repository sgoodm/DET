$(document).ready(function(){

	$("#add_local").addClass("active_menu")
	$("#message").html("Add a Local Raster")

	//--validation
	$("#raster_continent, #raster_country, #raster_type, #raster_sub").on("change", function(){
		var val = $(this).val()
		val = val.replace(/ /g, "_")
		val = val.toLowerCase()
		val = val.replace(/[^a-zA-z]/g, '')
		val = val.replace("__","_")
		$(this).val(val)
		console.log(val)
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

		// var base = "../../resources/"+  $("#raster_continent").val() +"/"+ $("#raster_country").val() +"/data/rasters"
		// var path = base +"/"+ $("#raster_type").val() +"/"+ $("#raster_sub").val() +"/"+ $("#raster_year").val()  
		var base = "../../resources/"+  $("#raster_continent").val() +"/"+ $("#raster_country").val()
		var path = "../../uploads/locals/pending/" + $("#raster_continent").val() +"__"+ $("#raster_country").val() +"__"+ $("#raster_type").val() +"__"+ $("#raster_sub").val() +"__"+ $("#raster_year").val()  
		

		var exists = false
		countryExists(base, function(result){
			exists = result
		})

		if (exists == false){
			$("#message").html("The continent/country you entered does not exist.")
            $('html, body').animate({ scrollTop: 0 }, 0);
			return
		}

		
		var file = $("#raster_file")[0]["files"][0];
		console.log(file);
		var formData = new FormData();
		formData.append("file_input_1", file);
		formData.append("up_dir", path)

       $.ajax({
            type: 'post',
            url: 'upload.php',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
	        async: false,
	        success: function(result) {
	            // console.log(result + "x")
	        }
        }).done(function(data) {
            console.log(data);
            $("#message").html("Raster was successfully added")
            $('html, body').animate({ scrollTop: 0 }, 0);
        }).fail(function(jqXHR,status, errorThrown) {
            console.log(errorThrown);
            console.log(jqXHR.responseText);
            console.log(jqXHR.status);
            $("#message").html("Error")
            $('html, body').animate({ scrollTop: 0 }, 0);
        });

	    $.ajax ({
	        type: "post",
	        url: "process.php",
	        data: { type: "add", path: path, stuff: $("#input_form").serialize(), continent: $("#raster_continent").val(), country: $("#raster_country").val()},
	        dataType: "text",
	        async: false,
	        success: function(result) {
	            console.log(result)
	        }
	    })
		

		function countryExists(base, callback){
		    $.ajax ({
		        type: "post",
		        url: "process.php",
		        data: { type: "exists", path: base},
		        dataType: "text",
		        async: false,
		        success: function(result) {
		            callback(result)
		        }
		    })
		}		
		
	})


})