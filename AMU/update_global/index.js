$(document).ready(function(){

	$("#update_global").addClass("active_menu")
	$("#message").html("Refresh a Global Raster")

	$("#submit").hide()
	// $("#global_list").empty()
	// $("#meta_input").empty()
	$("#global_list").append('<option id="blank_global_list_item" class="global_list_item" value="-----">Select global raster</option>')

	scanDir({ type: "scan", dir: "../../uploads/globals/processed" }, function(options) {
	    for (var op in options){
	        $("#global_list").append('<option class="global_list_item" value="' + options[op] + '">' + options[op] + '</option>')
	    }
    })

    
    var global 
    var updateList = []
    $("#global_list").on("change", function(){
    	updateList = []
		$("#submit").hide()
    	$("#blank_global_list_item").remove()
		$("#country_list").empty()
    	
    	global = $(this).val()
    	// console.log(global)

    	processCrop({type: "check", global: global}, function(result){
    		// console.log(result)
    		if (result.length > 0){
    			$("#country_message").html("Countries without Global Raster:")
	    		for (var i=0;i<result.length;i++){
	    			$("#country_list").append("<li>" + result[i] + "</li>")
    			}    
    			updateList = result		
    			$("#submit").show()
    		} else {

    			$("#country_message").html("This global exists in all countries.")
    		}
    	})
    	// console.log(updateList)
    })


	$("#submit").click(function(){

    	$("#message").html("working...")
        $('html, body').animate({ scrollTop: 0 }, 0);

		console.log("click")
		console.log(updateList.length)
		if (updateList.length > 0 ){
			console.log(updateList)
			for (var i=0; i<updateList.length ;i++){
				console.log("inside")
				console.log(global)
				console.log(updateList[i])
				console.log({type: "recrop", global: global, cc: updateList[i]})
				processCrop({type: "recrop", global: global, cc: updateList[i]}, function(result){
					console.log(result)
		            $("#message").html("Global was successfully updated")
		            $('html, body').animate({ scrollTop: 0 }, 0);
				})	
			}
		} else {
			console.log("nope")
		}
		
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


	function processCrop(data, callback){
	    $.ajax ({
	        type: "post",
	        url: "process.php",
	        data: data,
	        dataType: "json",
	        async: false,
	        success: function(result) {
	           callback(result)
	        }
	    })	
	}


})