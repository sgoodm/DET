$(document).ready(function(){

	$("#add_country").addClass("active_menu")
	$("#message").html("Add a Country")

	//--validation
	$("#continent, #country").on("change", function(){
		var val = $(this).val()
		val = val.replace(/ /g, "_")
		val = val.toLowerCase()
		val = val.replace(/[^a-zA-z]/g, '')
		val = val.replace("__","_")
		$(this).val(val)
		console.log(val)
	})

	$("#name").on("change", function(){
		var val = $(this).val()
		val = val.toLowerCase()
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

    $("#submit").click(function(){

    	$("#message").html("working...")
        $('html, body').animate({ scrollTop: 0 }, 0);

    	var path = {}
    	path.continent = $("#continent").val()
    	path.country = $("#country").val()

    	//check for required fields
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

		//check if country exists
		var exists = false
		scanDir({ type: "scan", dir: "../../resources/"+ path.continent  +"/"+ path.country }, function(options){
			if (options != null && options.length>0){
				exists = true
			}
		})

		if (exists == true){
			console.log("this country exists")
			return 
		}

		//build directory structure
		var cc = path.continent + "/" + path.country
		path.cache = "../../resources/" + cc + "/cache"
		path.rasters = "../../resources/" + cc + "/data/rasters"
		path.shapefiles = "../../resources/" + cc + "/shapefiles"
		scanDir({type: "build", cache: path.cache, rasters: path.rasters, shapefiles: path.shapefiles, continent: path.continent, country: path.country}, function(x){})

		//load country shapefile
  		var files = $("#c_file")[0]["files"];
		console.log(files);
		var fileData = new FormData();
		for (var f=0; f<files.length; f++){
			var file = files[f]
			fileData.append(file.name, file);
    	}
		fileData.append( "dir", path.shapefiles )
		fileData.append( "p_shp", "/" + cc + "/shapefiles" )
		fileData.append( "p_leaf", "/" + cc + "/shapefiles" )
		fileData.append( "meta", $("#input_form").serialize())
		uploadFiles(fileData, function(x){})
		
		//load gadm shapefile
		var shps = $("#file")[0]["files"];
		console.log(shps);
		var shpData = new FormData();
		for (var f=0; f<shps.length; f++){
			var shp = shps[f]
			shpData.append(shp.name, shp);
    	}
		shpData.append( "dir", path.shapefiles +"/"+ $("#level").val() +"_"+ $("#name").val() +"/"+ $("#year").val() )
		shpData.append( "p_shp", "/" + cc + "/shapefiles/"+ $("#level").val() +"_"+ $("#name").val() +"/"+ $("#year").val() )
		shpData.append( "p_leaf", "/" + cc + "/shapefiles" +"/"+ $("#level").val() +"_"+ $("#name").val() )
		shpData.append( "meta", $("#g_input_form").serialize())
		uploadFiles(shpData, function(x){})
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

	function uploadFiles(data, callback){
	  	$.ajax({
            type: 'post',
            url: 'upload.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
	        async: false,
	        success: function(result) {
	            callback(result)
	        }
        }).done(function(data) {
            console.log(data);
            $("#message").html("Country was successfully added")
            $('html, body').animate({ scrollTop: 0 }, 0);
        }).fail(function(jqXHR,status, errorThrown) {
            console.log(errorThrown);
            console.log(jqXHR.responseText);
            console.log(jqXHR.status);
            $("#message").html("Error")
            $('html, body').animate({ scrollTop: 0 }, 0);
        });

	}

})