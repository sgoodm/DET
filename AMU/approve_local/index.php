<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>AMU - Approve Local</title> 

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

       <div>List of Submitted Locals
            <img src='/aiddata/imgs/meta_info.jpg' class='info' title='list of all global rasters with meta info'>
        </div>

        <select id="meta_list"></select>

        <form id="input_form">
            <div id="meta_input" class="inputs"></div>
        </form>

       <input id="submit" type="button" value="Submit">
       <input id="reject" type="button" value="Reject">
    </div>

</body>

</html>