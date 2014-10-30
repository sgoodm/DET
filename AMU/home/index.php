<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>App Management Utility</title> 

    <link rel="stylesheet" href="index.css?<?php echo filectime('index.css') ?>" />  
    <link rel="stylesheet" href="../header.css?<?php echo filectime('header.css') ?>" />  
</head>

<body>
   
    <div id="top">

        <?php include("../header.php"); ?>

    </div>

    
    <div id="middle">

        <br><br>
        <a href="../../www/det.php" style="text-decoration:none;">Link to Data Extraction Tool</a>
        <!-- br><br>
        <a href="../admin" style="text-decoration:none;">Login to Admin Tools</a> -->

        <?php 

        // var_dump($_SERVER['PHP_AUTH_USER']);
        // var_dump($_POST["user"]);        
        ?>
        
      <!--   <div id="login">
            Login to Admin Tools<br>
            <form action="" method="post">
                <label>user: <input type="text" name="user"></label><br>
                <label>pass: <input type="password" name="pass"></label><br>
                <input type="submit" value="Submit">
            </form>
        </div> -->
            
    </div>

</body>

</html>