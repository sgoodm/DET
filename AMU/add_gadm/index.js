$(document).ready(function(){

	$("#add_gadm").addClass("active_menu")
	$("#message").html("Add a GADM")

	//--validation

	$("#name").on("change", function(){
		var val = $(this).val()
		val = val.toLowerCase()
		val = val.replace(/ /g, "")
		val = val.replace(/_/g, "")
		val = val.replace(/[^a-zA-z]/g, '')
		$(this).val(val)
		console.log(val)
	})

	$("#year").on("change", function(){
		var val = $(this).val()
		if (val > 9999){
			$(this).val(9999)
		}
	})
	//--

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

	$("#submit").hide()
	$("#input").hide()
	$("#gadm_country_list").append('<option id="blank_gadm_country_list_item" class="gadm_country_list_item" value="-----">Select Country to Add GADM to</option>')

	var continents = []
	scanDir({ type: "scan", dir: "../../resources" }, function(options){
		    for (var op in options){
		        continents.push(options[op]) 
		    }
	})
	
	//var countries = []
	for (var c in continents){
		scanDir({ type: "scan", dir: "../../resources/"+continents[c] }, function(options){
			    for (var op in options){
	        		$("#gadm_country_list").append('<option class="gadm_country_list_item" value="'+continents[c]+'/'+options[op]+'">'+continents[c]+'/'+options[op]+'</option>')
			    }
		})
	}

	// for (var c in countries){

	// }


	var cc
    $("#gadm_country_list").on("change", function(){
    	$("#submit").show()
    	$("#input").show()
    	$("#info").empty()
    	$("#blank_gadm_country_list_item").remove()

    	cc = $(this).val()

    	scanDir({type: "scan", dir: "../../resources/"+cc+"/shapefiles"}, function(options){
			    for (var op in options){
			        if (options[op].indexOf(".")==-1 && options[op].indexOf("_") == 1){
			        	//console.log(options[op]) 
			        	$("#info").prepend('<input type="text" value="'+ options[op] +'" readonly>')
			    	}
			    }
			    $("#info").prepend('<div class="input_name">Existing GADM</div>')

    	})

    })

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

		var path = "../../resources/" + cc + "/shapefiles/" + $("#level").val() +"_"+ $("#name").val() +"/"+ $("#year").val()

  		var files = $("#file")[0]["files"];
		console.log(files);
		
		var formData = new FormData();
		for (var f=0; f<files.length; f++){
			var file = files[f]
			formData.append(file.name, file);
    	}
		formData.append( "dir", path )
		formData.append( "p_shp", "/" + cc + "/shapefiles/" + $("#level").val() +"_"+ $("#name").val() +"/"+ $("#year").val() )
		formData.append( "p_leaf", "/" + cc + "/shapefiles/" + $("#level").val() +"_"+ $("#name").val() )
		formData.append( "meta", $("#input_form").serialize())

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
            $("#message").html("GADM was successfully added")
            $('html, body').animate({ scrollTop: 0 }, 0);
        }).fail(function(jqXHR,status, errorThrown) {
            console.log(errorThrown);
            console.log(jqXHR.responseText);
            console.log(jqXHR.status);
            $("#message").html("Error")
            $('html, body').animate({ scrollTop: 0 }, 0);
        });

    })

})