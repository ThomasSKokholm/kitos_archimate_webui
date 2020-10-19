<?php

session_start(); // if it's not already started.
session_unset();
session_destroy();

// Typically, after doing this you will redirect the user
// to the home page with something like:
// 
// header('Location: index.php');
?>

<script>
    //location.assign(location.toString());
    alert(location.toString());
    //var str = "";
    str = location.toString();
    var newstr = str.substring(0, str.lastIndexOf("/"));
    var webuiUrl = newstr + '/webui/';
    alert(webuiUrl);
    location.assign(webuiUrl);
</script>

<html>
    <head>
</head>
<body>
if submitResetSession == _POST
</body>

</html>

<?php
    //echo "target_file:".var_dump($uploadedfilename)."\n";
    //echo "filnavn:".var_dump($filnavn)."\n";
    //print_r($_SESSION);
?>

<script>
    document.cookie = 'PHPSESSID=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
</script>
