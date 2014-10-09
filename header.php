<h2> <a href="../home" style="text-decoration:none;">Application Management Utility</a> </h2>


<div id="menu">
 	<div class="menu_item" id="add_global" onclick="window.location='../add_global';"><div> Add Global Raster </div></div><!-- 
 --><div class="menu_item" id="edit_global" onclick="window.location='../edit_global';"><div> Edit Global Meta </div></div><!--
 --><div class="menu_item" id="add_local" onclick="window.location='../add_local';"><div> Add Local Raster </div></div><!-- 
 --><div class="menu_item" id="edit_local" onclick="window.location='../edit_local';"><div> Edit Local Meta </div></div>
</div>

<?php
if ($_SERVER['PHP_AUTH_USER'] == "amuadmin"){
?>
	<div id="admin_menu">
	 	<div class="menu_item" id="add_country" onclick="window.location='../add_country';"><div> Add Country </div></div><!-- 
	 --><div class="menu_item" id="add_gadm" onclick="window.location='../add_gadm';"><div> Add GADM </div></div><!-- 
	 
	 --><div class="menu_item" id="update_global" onclick="window.location='../update_global';"><div> Refresh Global </div></div><!--
	 --><div class="menu_item" id="approve_global" onclick="window.location='../approve_global';"><div> Approve Globals </div></div><!--
	 --><div class="menu_item" id="approve_local" onclick="window.location='../approve_local';"><div> Approve Locals </div></div>
	</div>

<?php
} else if ($_SERVER['PHP_AUTH_USER'] == "amuuser"){
?>



<?php
}
?>


<div id="message">Welcome to the application management utility</div>

