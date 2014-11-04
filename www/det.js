// ++
// ++ manages user interface for det.php
// ++

$(document).ready(function(){

	// -- initialize request data
	var output = {	
					"queue":9999, "priority":1, "request":0, "completion":0, "expiration":0, "email":"", 
					"continent":"", "country":"", "level":"", "year":"", "parent":"", "file":"", "shapefile":"", 
					"rtype":[], "rsub":[], "ryear":[], "rparent":[], "rfile":[], "raster":[],
					"raw":false
				}

//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------

	// -- shapefile data selection ui

	//initialize ui
	var select_tiers = {"continent":"continent", "country":"country", "level":"level", "year":"year"}
	var path_data = {"base":"../resources", "continent":"", "country":"", "level":"", "year":"", "file":""}
	var path = "../resources"
	getOptions(path, "continent", -1)

	
	//build basic leaflet
	var map = L.map('leaflet')

	var tiles = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap contributors</a>'
	}).addTo(map)

	map.setView([0,0], 1);

	map.eachLayer(function (layer) {
		console.log(layer)
	});

	function readJSON(file) {
	    var request = new XMLHttpRequest();
	    request.open('GET', file, false);
	    request.send(null);
	    if (request.status == 200)
	        return request.responseText;
	};


	function addGeo(file){
		map.eachLayer(function (layer) {
			console.log(layer)
		    if ( !layer["_container"] ){
		    	map.removeLayer(layer);
			}
		});

		var geojsonFeature = JSON.parse(readJSON(file))
		
		var myStyle = {
		    "color": "#000000",
		    "weight": 1,
		    "opacity": 0.85
		}

		var myLayer = L.geoJson(geojsonFeature,{style: myStyle})//.addTo(map);
		// myLayer.addData(geojsonFeature);

		map.fitBounds( myLayer.getBounds() )

		map.addLayer(myLayer)
	}

	//update the current path
	function updatePath(){
		for (folder in path_data){
			if (folder == "base"){
				path = path_data[folder]
			} else if (path_data[folder] != "" && path_data[folder] != "-----"){
				path += path_data[folder]
			}
		}
	}

	//populate "tier" select list with valid "options"  (manages ajax results for shapefile selection)
	function buildSelect(tier, options){
		var x
		if (tier == "level"){
			x = 2
		} else{
			x = 0
		}
		$("#list_"+tier).append('<option value="-----" selected="selected" >-----</option>')
	    for (op in options){
	    	if (x == 2 && options[op].indexOf("_") == 1){
	        	$("#list_"+tier).append('<option value="' + options[op] + '">' + options[op].substr(x) + '</option>')
	        } else if (x == 0 && options[op].indexOf(".") == -1 && options[op].indexOf("Leaflet") == -1) {
	        	$("#list_"+tier).append('<option value="' + options[op] + '">' + options[op].substr(x) + '</option>')
	        }
	        // if (options[op].indexOf("info") == -1 && options[op].indexOf(".shp") > -1 && options[op].indexOf("ADM") == -1 && options[op].indexOf("_")==1){
	        // 	$("#list_"+tier).append('<option value="' + options[op] + '">' + options[op].substr(x) + '</option>')
	        // 	tier =  "end"
	        // } else if (options[op].indexOf(".") == -1){
	        //     $("#list_"+tier).append('<option value="' + options[op] + '">' + options[op].substr(x) + '</option>')
	        // }
	    }
	}  

	//get shapefile based on year selection (ajax result management)
	function yearSelect(files){
		for (file in files){
			if (files[file].indexOf(".shp") > -1){
				path_data["file"] = "/" + files[file]
				updatePath()
			}
		}
	}

	//scan directory for selector options
	function getOptions(input, key, build){
	    $.ajax ({
	        url: "getDir.php",
	        data: { type : "dir", action : input },
	        dataType: "json",
	        type: "post",
	        async: false,
	        success: function(result) {
	            switch (build){
	            	case -1:
	            		buildSelect(select_tiers[key], result)
		    			$("#list_"+select_tiers[key]).removeAttr("disabled")
		    			break
		    		case 0:
		    			yearSelect(result)
		    			break
		    	}
	        }
	    })
	}

	//update UI based on select changes
	$("#list_input").on("change", ".list_item", function(){
		
		var item_tier = $(this).attr("id").substr(5)
		var select_keys = Object.keys(select_tiers)
		var this_index = select_keys.indexOf(item_tier)
		var next_key = select_keys[this_index+1]

		switch(item_tier){
			case "continent":
				$("#list_country, #list_level, #list_year").empty()
				path_data["continent"] = path_data["country"] = path_data["level"] = path_data["year"] = path_data["file"] = ""
				updatePath()
				if ($(this).val()!="-----"){	
					path_data["continent"] = "/" + $(this).val()
					updatePath()
					getOptions(path, next_key, -1)
				}
				output["continent"] = $(this).val()
				break;

			case "country":
				$("#list_level, #list_year").empty()
				path_data["country"] = path_data["level"] = path_data["year"] = path_data["file"] = ""
				updatePath()
				if ($(this).val()!="-----"){	
					path_data["country"] = "/" + $(this).val() + "/shapefiles"
					updatePath()
					getOptions(path, next_key, -1)	
				}
				output["country"] = $(this).val()
				addGeo("../resources/"+output.continent+"/"+output.country+"/shapefiles/Leaflet.geojson")
				break;

			case "level":
				$("#list_year").empty()
				path_data["level"] = path_data["year"] = path_data["file"] = ""
				updatePath()
				if ($(this).val()!="-----"){			
					path_data["level"] = "/" + $(this).val()
					updatePath()
					getOptions(path, next_key, -1)
				}
				output["level"] = $(this).val()
				addGeo("../resources/"+output.continent+"/"+output.country+"/shapefiles/"+output.level+"/Leaflet.geojson") 
				break;

			case "year":
				path_data["year"] = path_data["file"] = ""
				updatePath()
				if ($(this).val()!="-----"){
					$(".list_item, #submit").prop("disabled", false)
					$("#submit").css("visibility","visible")
					// $("#submit").prop("disabled",false)
					path_data["year"] = "/" + $(this).val()
					updatePath()
					getOptions(path, next_key, 0)
					output["file"] = path_data["file"].substring(1)
					output["file"] = output["file"].substring(0, output["file"].length-4)
				}
				output["year"] = $(this).val()
				break;		
		}

		if ($("#list_year").val()=="-----" || $("#list_year").val()==null){
			$("#submit").css("visibility","hidden")
			path_data["year"] = ""
			path_data["file"] = ""
			updatePath()
		}	
	})

	//submit button
	$("#submit").click( function(){
		$(".list_item, #submit").prop("disabled", true)
		$("#restart").css("visibility","visible")
		output["parent"] = output["continent"] + "/" + output["country"] + "/shapefiles/" + output["level"] + "/" + output["year"] 
		output["shapefile"] = output["parent"] + "/" + output["file"]
		
		//initialize raster data table and associated content
		$("#message").html("Building Table...")
		buildDataTable(1)
		$("#leaflet").css("display","none")
		$("#data, #output_tools").css("display","block")
	})

	//restart button
	$("#restart").click( function(){
		$("#message").html("Choose Shapefile Details")
		$(".list_item, #submit").prop("disabled", false)
		$("#restart").css("visibility","hidden")
		$("#data, #output_tools").css("display","none")
		$("#leaflet").css("display","block")
		$("#data_table").DataTable().destroy()
		$("#data_selection").empty()
	})

//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------

	// -- raster data table ui

	//initialize raster data table variables
	var type_data
	var sub_data
	var year_data
	var file_data

	//update data range for raster data table
	$("#update").click( function(){
		$("#message").html("Updating Table...")
		// hideShow("#data_selection", "#loading")
		$("#data_selection").hide(0, function(){

			$("#loading").show(500, function(){

				$("#data_table").DataTable().destroy()
				$("#data_selection").empty()

				buildDataTable(0)

				$("#loading").hide(500, function(){
					$("#data_selection").show(500)
				})
			})	
		})
	})

	//build date selectors
	function buildDateSelector(date_element, start, end){
		if (end >= start){
			for (var i=start; i<=end;i++){
				$("#"+date_element).append('<option value='+i+' id="'+date_element+'_'+i+'">'+i+'</option>')
			}	
		} else {
			for (var i=start; i>=end;i--){
				$("#"+date_element).append('<option value='+i+' id="'+date_element+'_'+i+'">'+i+'</option>')
			}
		}	
	}
	
	//handles building / updating raster data table
	function buildDataTable(date_init){
		//initialize date selectors
		var year_min
		var year_max
		if (date_init == 1){
			buildDateSelector("date_start", 1950, 2100)
			buildDateSelector("date_end", 2100, 1950)
			year_min = 2000
			year_max = 2050
		} else {
			year_min = parseInt($("#date_start").val())
			year_max = parseInt($("#date_end").val())
		}

		var newType = (output["continent"] + "/" + output["country"] + "/data/rasters")
		
		$("#data_selection").append('<table id="data_table" class="cell-border">'    
						           		+'<thead><tr><td></td><td></td></tr></thead>'
						            	+'<tbody></tbody>'
					            	+'</table>'
					        )

		//build header year array (for all years where data exists)
		var year_values = []
		
		//search types
		getData(newType,1)
		for (type in type_data){

			//search sub types
			var newSub = newType + "/" + type_data[type] 
			getData(newSub,2)
			for (sub in sub_data){
				
	    		//search years
	    		var newYear = newSub + "/" + sub_data[sub]
	    		getData(newYear,3)
	    		for (year in year_data){
	    			if(year_data[year] >= year_min && year_data[year] <= year_max){
	    				year_values.push(parseInt(year_data[year]))
	    			}
	    		}
			}
		}
	
		var year_range = [];
		$.each(year_values, function(i, v){
		    if($.inArray(v, year_range) === -1) year_range.push(v);
		})
		year_range.sort()

		for (j in year_range){	
			$("#data_table>thead>tr").append('<td class="col_header">'+year_range[j]+'</td>') 		
		}	

		//build row groups (types)
		for (type in type_data){

			//build rows (sub types)
			var newSub = newType + "/" + type_data[type] 
			getData(newSub,2)
			for (sub in sub_data){
				var newYear = newSub + "/" + sub_data[sub]
				$("#data_table>tbody").append('<tr id="'+type_data[type]+"_"+sub_data[sub]+'" class="c_sub"><td class="row_group">'+type_data[type].replace(/_/g," ")+'</td><td id="'+newYear+'" class="row_header">'+sub_data[sub].replace(/_/g," ")+'</td></tr>')
				
	    		//build cells (years)
	    		getData(newYear,3)

				for (j in year_range){	
					var cb_name = type_data[type]+"/"+sub_data[sub]+"/"+year_range[j]
	    			//for each year in range check for sub year
	    			if ( _.values(year_data).indexOf(year_range[j].toString()) > -1 ){
	    				//for each available sub year build cell
	    				$("#"+type_data[type]+"_"+sub_data[sub]).append('<td id="label_'+cb_name+'"class="s_cb"><input type="checkbox" id="'+cb_name+'" name="'+cb_name+'" value="'+cb_name+'" class="cb_option"></td>')
	    			} else {
	    				//fill in cell with no year available
	    				$("#"+type_data[type]+"_"+sub_data[sub]).append('<td id="label_'+cb_name+'"class="s_fill"></td>')
	    			}    			
	    		}
			}
		}

		//handle raster data selections / deselections
		$(".s_cb").click(function(){
			var cb_click = $(this).attr("id").substr(6)
			var cb_click_explode = cb_click.split("/")
			var newFile = newType+"/"+cb_click
			getData(newFile,4)
			var fileOut = newFile+"/"+file_data[2]
			var dataExists = output["raster"].indexOf(fileOut)
			var cb_id = "#"+ (cb_click.split("/")).join("\\/")
			if ( dataExists > -1 ){
				$(cb_id).prop("checked","")
				output["rtype"].splice(dataExists,1)
				output["rsub"].splice(dataExists,1)
				output["ryear"].splice(dataExists,1)
				output["rfile"].splice(dataExists,1)
				output["rparent"].splice(dataExists,1)
				output["raster"].splice(dataExists,1)
			} else {
				$(cb_id).prop("checked","checked")
				output["rtype"].push(cb_click_explode[0])
				output["rsub"].push(cb_click_explode[1])
				output["ryear"].push(cb_click_explode[2])
				output["rparent"].push(newFile)
				output["rfile"].push(file_data[2])
				output["raster"].push(fileOut)
			}
			validRequest()
		})

		//set up dataTables
		var newTable =  $("#data_table").DataTable({
			"bAutoWidth": false,
			"bSort": false,
		})
	    $("#data_table").dataTable().rowGrouping()

	    //add tooltip icons
	    $(".row_header").each(function(){
	    	var rowID = $(this).attr("id")
	    	var $header = $(this)
	    	getText("../resources/"+rowID+"/meta_info.txt", function(meta){
				if (meta != "null") {
	    			$header.append("<img src='img/meta_info.jpg' class='info' title='"+meta+"'>")
	    		}
	    		$
	    	})
	   		//getText("../resources/"+rowID+"/meta_note.txt", function(meta){
	   		//	if (meta != "null") {
			// 		$header.append("<img src='img/meta_note.jpg' class='info' title='"+meta+"'>")	    		
			// 	}
	   		//})
	    })

	    $("#message").html("Select Data")

	}

	//scan resource directory for data related fields
	function getData(input, build){
	    $.ajax ({
	        url: "getDir.php",
	        data: { type : "dir", action : input },
	        dataType: "json",
	        type: "post",
	        async: false,
	        success: function(result) {
	            switch (build){
		    		case 1:
		    			type_data = {}
						type_data = $.extend(true, {}, result);
		    			break
		    		case 2:
						sub_data = {}
						sub_data = $.extend(true, {}, result);
		    			break
		    		case 3:
						year_data = {}
						year_data = $.extend(true, {}, result);
		    			break
		    		case 4:
		    			file_data = {}
						file_data = $.extend(true, {}, result);
		    			break;
		    	}
	        }
	    })
	}

	//get text for table tooltips
	function getText(dir, callback){
	    $.ajax ({
	        url: "getDir.php",
	        data: { type : "text", action : dir },
	        dataType: "text",
	        type: "post",
	        async: false,
	        success: function(result) {
	            callback(result)
	        }
	    })

	}

//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------

	//select / deselect raw data option
	$("#raw_data").on("change", function(){
		if( $(this).prop("checked") ){
			output["raw"] = true
		} else {
			output["raw"] = false
		}
	})

	//manage email input
	$("#user_email").on("input", function(){
		validRequest()
	})

    //submit request
	$("#request").click(function(){
		$("#request").prop("disabled", true)
		
		//get queue number
		var newQueue
		getQueue("queue", function(log){ 
			var queueLog = log.map(function(item) {
			    return parseInt(item);
			})
			newQueue = Math.max.apply(Math, queueLog) + 1
		})

		if (newQueue > 0){
			output.queue = newQueue	
		} else {
			output.queue = 0001	
		}	

		//determine priority
		var cachePath = "../resources/" + output["continent"] + "/" + output["country"] + "/cache"
		output["priority"] = 1
		for (cacheItem in output["raster"]){
			if (output["priority"]==1){
				var cacheId = output["continent"] + "__" + output["country"] + "__" + output["level"] + "__" + output["year"] + "__" + output["rtype"][cacheItem] + "__" + output["rsub"][cacheItem] + "__" + output["ryear"][cacheItem] + ".csv"
				$.ajax({
				    url: cachePath + "/" + cacheId,
				    type:'HEAD',
				    async: false,
				    error: function(){	
				        output["priority"] = 0
				    }
				})
			}
		}

		//set date time
		var date = new Date()
		output.request = Math.floor( date.getTime() / 1000 )		

		//set email
		output.email = $("#user_email").val() 

		confirmRequest()
	    
	})

	$("#return").click(function(){
		location.href='det.php'
	})

	//determine if minimum requirements for a valid request have been met
	function validRequest(){
		if (output["raster"].length == 0 || $("#user_email").val() == ""){
			$("#request").prop("disabled", true)
		} else {
			$("#request").prop("disabled", false)
		}
	}

	//confirm and process request
	function confirmRequest(){
		if(confirm("Send request results to " + output.email + "?")){
			$("#message").html("Submitting Request...")
			hideShow("#content", "#confirm_loading")

			buildQueue()

			var queuePos
			getQueue("priority", function(log){ 
				var queueLog = log.map(function(item) {
			    	return parseInt(item);
				})
				if (output.priority == 1){
					queuePos = _.without(queueLog, 0).length
				} else {
					queuePos =  queueLog.length
				}
			})
			
			var confirmHTML = "" +
		     		"Once your request has been processed an additional email will be sent containing details on how to access the data you requested. <br><br>" +
					"Current position in queue: <b>" + queuePos + "</b><br><br>" +
					"<b>Request Summary</b>" +
					"<br>Country: " + output.country +
					"<br>GADM: " + output.level.substr(2) +
					"<br>Boundary Year: " + output.year +
					"<br>Data: " + output.rsub +
					"<br>Data Years: " + output.ryear +
					"<br>Include Raw Data: " + output.raw
			
			//send confirmation email
		    $.ajax ({
		        url: "getDir.php",
		        data: { type : "email", email: output.email, queue: output.queue, message: confirmHTML },
		        type: "post",
		        dataType: "text",
		        async: false,
		        success: function(result) {
		            console.log(result)
		            $("#message").html("Request has been submitted")
		    		hideShow("#confirm_loading", "#confirmation")
		    		$("#confirm_text").html( "" + 
		    			"An email has been sent to <b>" + output.email + "</b> confirming your submission <br>" +
						"(please check your spam folder if you do not receive a confirmation email within a few minutes) <br><br>" +
						confirmHTML 
					)
					
		        }
		    })				

		}
	}
	
	//ajax function to read queue contents
	function getQueue(call, callback){
	    $.ajax ({
	        url: "getDir.php",
	        data: { type : "read", call: call },
	        type: "post",
	        dataType: "json",
	        async: false,
	        success: function(result) {
	            callback(result)
	        }
	    })		
	}

	//ajax function to add request to queue
	function buildQueue(){
		var json_output = JSON.stringify(output)
	    $.ajax ({
	        url: "getDir.php",
	        data: { type : "write", action : json_output },
	        dataType: "text",
	        type: "post",
	        async: false,
	        success: function(result) {
	            console.log(result)
	        }
	    })
	}

	function hideShow(hide, show){
		$(hide).hide()
		$(show).show()
	}

})