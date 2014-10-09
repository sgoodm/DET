<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>DET</title> 

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />

    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">

    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/fixedheader/2.1.2/js/dataTables.fixedHeader.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/fixedheader/2.1.2/css/dataTables.fixedHeader.css">

    <script type="text/javascript" charset="utf8" src="../libs/jquery.dataTables.rowGrouping.js"></script>

    <script type="text/javascript" charset="utf8" src="../libs/underscore.js"></script>

    <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />

    <!-- <script src="https://raw.github.com/calvinmetcalf/leaflet-ajax/master/dist/leaflet.ajax.min.js"></script> -->

    <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/spin.js/2.0.1/spin.js"></script> -->

    <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/qtip2/2.2.0/jquery.qtip.js"></script> -->
    <!-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/qtip2/2.2.0/jquery.qtip.min.css" /> -->

    <script src="det.js"></script>
    <link rel="stylesheet" href="det.css?<?php echo filectime('det.css') ?>" />    
</head>

<body>
    <div id="top">
        <!-- <div id="top_container"> -->
            <img src="img/top.jpg">
        <!-- </div> -->
    </div>

    <div id="middle">
        <div id="banner">Data Extraction Tool</div>
        <div id="message">Choose Shapefile Details</div>
        <div id="content">
            <div id="left">
                <div id="input_tools">
                    <div id="list_input">
                        <div>
                            <div class="list_name">Continent
                                <img src='img/meta_info.jpg' class='info' title='select continent'>
                            </div>
                            <select id="list_continent" class="list_item" ></select>
                        </div>
                        <div>
                            <div class="list_name">Country
                                <img src='img/meta_info.jpg' class='info' title='select country'>
                            </div>
                            <select id="list_country"   class="list_item" ></select>
                        </div>
                        <div>
                            <div class="list_name">Level
                            <img src='img/meta_info.jpg' class='info' title='select global administrative level'>
                            </div>
                            <select id="list_level"     class="list_item" ></select>
                        </div>
                        <div>
                            <div class="list_name">Boundary Year
                            <img src='img/meta_info.jpg' class='info' title='select boundary year of the global administrative level'>
                            </div>
                            <select id="list_year"      class="list_item" ></select>
                        </div>
                    </div>

                    <div>
                        <button id="submit"  type="button" style="visibility:hidden;">Next >></button> 
                    </div>

                    <div>
                        <button id="restart" type="button" style="visibility:hidden">Restart</button>         
                    </div>
                </div>         

                <div id="output_tools" style="display:none">
                    <label><input type="checkbox" id="raw_data" value="raw_data" >Include Raw Data Files</label>
                    <img src='img/meta_info.jpg' class='info' title="shapefile & rasters. Some raw data may not be available for distribution due to the data provider's terms of use. See the documentation provided with your request results for more details on the licenses associated with selected data">
                    <br>
                    <br>
                    Email: <input type="text" id="user_email" value="aiddatatest@gmail.com">
                    <br>
                    <br>
                    <button id="request"  type="button" disabled="true">Submit Request</button> 
                </div>
            </div>

            <div id="right">
               <div id="leaflet">
               </div>
               <div id="data" style="display:none">
                   <div id="date_input">
                        Start: <select id="date_start"></select> - End: <select id="date_end"></select>
                        <button id="update" type="button" >Update</button> 
                    </div>

                    <div id="data_selection">
                        
                    </div>

                </div>
                <div id="loading" style="display:none">
                    <img src='img/loading.gif'>
                </div>
            </div>
        </div>    
        <div id="confirm_loading" style="display:none">
            <img src='img/loading.gif'>
        </div>
        <div id="confirmation" style="display:none;">
        
            <div id="confirm_text">
            </div>
            <br>
            <button id="return" type="button">Return</button>
        </div>
    </div>

    <div id="bottom">
        <!-- <div id="bottom_container"> -->
            <img src="img/bottom.jpg">
        <!-- </div> -->
    </div>


</body>

</html>