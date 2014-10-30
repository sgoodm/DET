<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>AMU - Add Country</title> 

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

        <div id="input" class="inputs">
            <form id="input_form">
                <div class="input_name">New Country <img src='../img/meta_note.jpg' class='info' title='case sensitive. use underscores instead of spaces'></div>
                <input type="text" id="country" name="country" class="required" value="" >
                <div class="input_name">New Continent <img src='../img/meta_note.jpg' class='info' title='case sensitive. use underscores instead of spaces'></div>
                <input type="text" id="continent" name="continent" class="required" value="" >
                <div class="input_name">Country Shapefile</div>
                <input type="file" id="c_file" class="required" value="" multiple>
                <div class="input_name">Country Source Name</div>
                <input type="text" id="c_source" name="c_source" class="required" value="" >
                <div class="input_name">Country Source Link</div>
                <input type="text" id="c_link" name="c_link" class="required" value="" >
                <div class="input_name">Country Source Terms</div>
                <select id="c_terms" name="c_terms">
                    <option value="false">Can NOT be distributed</option>
                    <option value="true">Can be distributed</option>               
                </select>
                <br>
            </form>
            <form id="g_input_form">
                <div class="input_name">New GADM Level</div>
                <select id="level" name="level" class="required" >
                    <option val="1">1</option>
                    <option val="2">2</option>
                    <option val="3">3</option>
                    <option val="4">4</option>
                    <option val="5">5</option>
                    <option val="6">6</option>
                    <option val="7">7</option>
                    <option val="8">8</option>
                    <option val="9">9</option>
                </select>
                <!-- <input type="text" id="level" name="level" class="required" value="" > -->
                <div class="input_name">New GADM Name <img src='../img/meta_note.jpg' class='info' title='case sensitive. must be a single word'></div>
                <input type="text" id="name" name="name" class="required" value="" >
                <div class="input_name">GADM Shapefile</div>
                <input type="file" id="file" class="required" value="" multiple>
                <div class="input_name">Shapefile Year</div>
                <input type="number" id="year" name="year" class="required" value="" min="1000" max="9999">
                <div class="input_name">GADM Source Name</div>
                <input type="text" id="source" name="source" class="required" value="" >
                <div class="input_name">GADM Source Link</div>
                <input type="text" id="link" name="link" class="required" value="" >
                <div class="input_name">GADM Source Terms</div>
                <select id="terms" name="terms">
                    <option value="false">Can NOT be distributed</option>
                    <option value="true">Can be distributed</option>               
                </select>
            </form>
        </div>

        <input id="submit" type="button" value="Add Country">

    </div>
  

</body>

</html>