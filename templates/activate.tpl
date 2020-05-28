<!DOCTYPE HTML>
<html>

<head>

    <title>LiveZilla Activator</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./templates/style_activate.css">
    <link rel="stylesheet" type="text/css" href="./mobile/css/livezilla6.css?acid=ahgzixd7">
    <link rel="stylesheet" type="text/css" href="./fonts/font-awesome.min.css">
    <script type="text/javascript" src="./mobile/js/md5.js"></script>
    <script type="text/javascript" src="./mobile/js/sha256.js"></script>
    <script type="text/javascript" src="./mobile/js/basesf.js"></script>
    <script type="text/javascript" src="./mobile/js/lzm/classes/CommonToolsClass.js"></script>
    <script type="text/javascript" src="./mobile/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="./mobile/js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="./mobile/js/jquery-migrate-3.1.0.min.js"></script>
    <script>
        function paste(){
            var key = $("#tb-serial-1").val().trim();
            if (key.length == 23 && (key.match(/-/g) || []).length == 3) {
                var ocl = key.split('-');
                if (ocl.length == 4) {
                    $("#tb-serial-1").val(ocl[0]);
                    $("#tb-serial-2").val(ocl[1]);
                    $("#tb-serial-3").val(ocl[2]);
                    $("#tb-serial-4").val(ocl[3]);
                }
            }
        }

        function activate(){
            var s = $("#tb-serial-1").val().trim() + '-' + 
                    $("#tb-serial-2").val().trim() + '-' + 
                    $("#tb-serial-3").val().trim() + '-' + 
                    $("#tb-serial-4").val().trim();
            $('#aform_serial').val(s);
            $('#aform').submit();
        }
    </script>
</head>
<body>

    <div id="index_main_container">
        <img src="./images/livezilla.png" class="index_logo" alt="" style="width:500px;height:auto;">
        <br><br><br><br>
            <div id="server_url" class="index_main_box top-space-double" style="margin:0 auto;width:500px;">
                <br><br>
                <b>Activate LiveZilla License Key</b><br><br>
                Please enter your LiveZilla license key:
                <div class="hspaced">
                    <!--lang_index_server_url_apps--><br><br>
                    <input id="tb-serial-1" onkeyup="paste()" type="text" style="width:80px;text-align: center;">&nbsp;&nbsp;-&nbsp;
                    <input id="tb-serial-2" type="text" style="width:80px;text-align: center;">&nbsp;&nbsp;-&nbsp;
                    <input id="tb-serial-3" type="text" style="width:80px;text-align: center;">&nbsp;&nbsp;-&nbsp;
                    <input id="tb-serial-4" type="text" style="width:80px;text-align: center;">
                </div>
                <div style="display:<!--res-->">
                    <br><br>
                    <div style="display:<!--res_error-->" style="white-space:nowrap">
                        <i class="fa fa-warning icon-red icon-large"></i>&nbsp;&nbsp;<b style="color:var(--red) !important;"><!--response--></b>
                    </div>
                    <div style="display:<!--res_success-->" style="white-space:nowrap">
                        <i class="fa fa-warning icon-red icon-large"></i>&nbsp;&nbsp;<b style="color:var(--green) !important;"><!--response--></b>
                    </div>
                </div>
                <br><br>
                <div><a class="index-button index-button-l index-button-gray" href="http://www.livezilla.net/downloads/en/" target="_blank" onclick="activate()">Activate Key</a></div>
            </div>
         <br>
        <br>
        <br>
        <br>
        <br>
        <form id="aform" action="./activate.php" method="post">
            <input type="hidden" value="" id="aform_serial" name="serial"></input>
        </form>
    </div>
    </body>

</html>