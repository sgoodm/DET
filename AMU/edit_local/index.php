<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>AMU - Edit Local Meta</title> 

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

        <div>Local Meta List
            <img src='../img/meta_info.jpg' class='info' title='list of all locals rasters with meta info'>
        </div>

        <select id="meta_list"></select>

        <form id="input_form">
            <div id="meta_input" class="inputs"></div>
        </form>

       <input id="submit" type="button" value="Update Meta">

    </div>
  

</body>

</html>