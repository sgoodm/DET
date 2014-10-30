$(document).ready(function(){

	$("#add_global").addClass("active_menu")
	$("#message").html("Add a Global Raster")

	//--validation
	$("#raster_type, #raster_sub").on("change", function(){
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

		var file = $("#raster_file")[0]["files"][0];
		console.log(file);

		var formData = new FormData();
		formData.append("file_input_1", file);
		formData.append("up_dir", "../../uploads/globals/raw")
		
       $.ajax({
            type: 'post',
            url: 'upload.php',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
	        async: false,
	        success: function(result) {
	            $("#message").html("Global was successfully added")
            	$('html, body').animate({ scrollTop: 0 }, 0);
	        }
        }).done(function(data) {
            console.log(data);
            
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
	        data: { type: "crop", up_file: file["name"], stuff: $("#input_form").serialize() },
	        dataType: "text",
	        async: false,
	        success: function(result) {
	            console.log(result)
	        }
	    })		
		
	})


})