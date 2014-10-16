<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>AMU - Add Local</title> 

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <script src="index.js"></script>
    <link rel="stylesheet" href="index.css?<?php echo filectime('index.css') ?>" /> 
    <link rel="stylesheet" href="../header.css?<?php echo filectime('../header.css') ?>" />    
    <link rel="stylesheet" href="../body.css?<?php echo filectime('../body.css') ?>" />    
</head>

<body>
   
    <div id="top">
        <?php include("../header.php"); ?>
    </div>


    <div id="middle">
        <div class="inputs">
            <div class="input_name">Continent*
                <img src='../img/meta_info.jpg' class='info' title='input continent'>
                <img src='../img/meta_note.jpg' class='info' title='case sensitive. must be a valid (existing) continent'>
            </div> 
            <input type="text" id="raster_continent" name="raster_continent" class="required" value=""><br> 
            
            <div class="input_name">Country*
                <img src='../img/meta_info.jpg' class='info' title='input country'>
                <img src='../img/meta_note.jpg' class='info' title='case sensitive. must be a valid (existing) country'>
            </div> 
            <input type="text" id="raster_country" name="raster_country" class="required" value=""><br> 
        </div>

        <form id="input_form" >
            <div class="inputs">
                <div class="input_name">Raster File*
                    <img src='../img/meta_info.jpg' class='info' title='upload global raster file'>
                </div> 
                <input type="file" id="raster_file" name="raster_file" class="required" value=""><br> 
                <!-- <input type="text" id="raster_file" name="raster_file" value=""><br>  -->

                <div class="input_name">Type*
                    <img src='../img/meta_info.jpg' class='info' title='input type classification of raster data'>
                    <img src='../img/meta_note.jpg' class='info' title='case sensitive. use underscores instead of spaces. no numbers or symbols'>
                </div> 
                <input type="text" id="raster_type" name="raster_type" class="required" value=""><br> 
                
                <div class="input_name">Sub*
                    <img src='../img/meta_info.jpg' class='info' title='input sub type classification of raster data'>
                    <img src='../img/meta_note.jpg' class='info' title='case sensitive. use underscores instead of spaces. no numbers or symbols'>
                </div> 
                 <input type="text" id="raster_sub" name="raster_sub" class="required" value=""><br> 
                
                <div class="input_name">Year*
                    <img src='../img/meta_info.jpg' class='info' title='input year of raster data'>
                </div> 
                <input type="number" id="raster_year" name="raster_year" class="required" value="" min="0" max="9999"><br>

                <!-- <div class="input_name">Name
                    <img src='../img/meta_info.jpg' class='info' title='input name of raster data (becomes new file name for raster. will use existing file name if left blank)'>
                    <img src='../img/meta_note.jpg' class='info' title='use underscores instead of spaces. no symbols'>
                </div> 
                <input type="text" id="raster_name" name="raster_name" value=""><br> -->
                
                <br>
                
                <div class="input_name">Summary*</div> 
                <textarea id="meta_summary" name="meta_summary" class="required" rows="3" cols="40" maxlength="400"></textarea><br>               

                <div class="input_name">Data Provider*</div> 
                <textarea id="meta_data_provider" name="meta_data_provider" class="required" rows="1" cols="40" maxlength="200"></textarea><br> 

                <div class="input_name">Data Provider Website</div> 
                <textarea id="meta_data_provider_website" name="meta_data_provider_website" rows="1" cols="40"></textarea><br>
            </div>

            <div class="inputs">
                <div class="input_name">Year Data Represents*</div> 
                <textarea id="meta_year_data_represents" name="meta_year_data_represents" class="required" rows="1" cols="40"></textarea><br>

                <div class="input_name">Year Data was Produced</div> 
                <textarea id="meta_year_data_produced" name="meta_year_data_produced" rows="1" cols="40"></textarea><br>    

                <div class="input_name">License Terms </div>
                <select id="meta_license_terms" name="meta_license_terms">
                    <option value="false">Can NOT be distributed</option>
                    <option value="true">Can be distributed</option>               
                </select>

                <div class="input_name">License Terms Website</div> 
                <textarea id="meta_license_terms_website" name="meta_license_terms_website" rows="1" cols="40"></textarea><br>

                <div class="input_name">Version</div> 
                <textarea id="meta_version" name="meta_version" rows="1" cols="40" maxlength="100"></textarea><br>

                <div class="input_name">Variable Interpretation</div> 
                <textarea id="meta_variable_interpretation" name="meta_variable_interpretation" rows="3" cols="40" maxlength="10000"></textarea><br>

                <div class="input_name">Upper Bound*
                    <img src='../img/meta_info.jpg' class='info' title='upper bound of continuous raster variable'>
                </div> 
                <input type="number" id="meta_upper_bound" name="meta_upper_bound" class="required" value="1" step="0.001"><br>
                
                <div class="input_name">Lower Bound*
                    <img src='../img/meta_info.jpg' class='info' title='lower bound of continuous raster variable'>
                </div> 
                <input type="number" id="meta_lower_bound" name="meta_lower_bound" class="required" value="0" step="0.001"><br>

                <div class="input_name">Extraction Type </div>
                <select id="meta_extract_type" name="meta_extract_type">
                    <option value="mean">Mean</option>
                    <option value="sum">Sum</option>               
                </select>

                <div class="input_name">Warnings / Caveats / Licenses Details</div> 
                <textarea id="meta_warnings" name="meta_warnings" rows="3" cols="40" maxlength="10000"></textarea><br>

                <div class="input_name">Other Notes</div> 
                <textarea id="meta_notes" name="meta_notes" rows="3" cols="40" maxlength="10000"></textarea><br>
            </div>
        </form>
    
        <div id="submit_input">            
            <input id="submit" type="button" value="Submit">
        </div>
            
    </div>

</body>

</html>