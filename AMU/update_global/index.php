<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>AMU - Update Global</title> 

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

        <div>Global Raster List
            <img src='../img/meta_info.jpg' class='info' title='list of all global rasters'>
        </div>

        <select id="global_list"></select>

        <div id="country_info"><h3 id="country_message"></h3>
            <div><ul id="country_list"></ul></div>
        </div>

        <input id="submit" type="button" value="Refresh Global Raster">
            
    </div>

</body>

</html>