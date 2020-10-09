<?php
session_start();
if(empty($_SESSION['unikSessionId'])){
  $_SESSION['unikSessionId'] = uniqid();
}
$downloadfile = 'downloaded_'.$_SESSION['unikSessionId'].'.archimate';//downloaded.archimate
$_SESSION['unikFilnavn'] = $downloadfile;
$file = "/tmp/uploads/".$downloadfile;
if (file_exists($file)) {
  header('Content-Description: File Transfer');
  header('Content-Type: application/xml');
  header('Content-Disposition: attachment; filename="'.basename($file).'"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($file));
  readfile($file);
  exit;
}

// definer tomme variabler.
$usr_name = $email = $usr_pwd = $archi_in_file = $archi_out_file = "";
$userErr = $passwordErr = $emailErr = $usr_pwdErr = $archi_in_fileErr = "";
$user = $password = $uploadedfilename = $filnavn = $uploadOk = "";
$startRunScriptTime = $slutRunScriptTime = $rest_tid = $slut = 0;
$start_bredde = 1;
// For handling file uploading.
$target_dir = "/tmp/uploads/";
// Run kitos_tools arimate script
if(isset($_POST['filnavn'])){
  $filnavn = $_POST['filnavn'];
  $_SESSION['filnavn'] = $filnavn;
  // echo "_Filnavn_1_"."\n";
  // echo $filnavn."\n";
}
if(!isset($_SESSION['filnavn']) and isset($_POST['filnavn'])){
  // echo "køre denne if sætning?\n";
  $filnavn = $_POST['filnavn'];
  $_SESSION['filnavn'] = $filnavn;
  // echo "_Filnavn_2_"."\n";
}
if(isset($_SESSION['filnavn'])){
  $filnavn = $_SESSION['filnavn'];
  // echo "_Filnavn_3_"."\n";
}

//
if(isset($_POST["submitUpload"], $_FILES["fileToUpload"]))
 {
    $_SESSION['archi_in_file'] = basename($_FILES["fileToUpload"]["name"]);
    if(isset($_SESSION['isUploadOk'])){
      $uploadOk = $_SESSION['isUploadOk'];
    }
  }
  if(isset($_SESSION['isUploadOk'])){
    $uploadOk = $_SESSION['isUploadOk'];
  }

// include_once '.php';
include 'showwebform.php';
include 'upload.php';
//include 'runarchimatescript.php';

/*
 * A webui, for easy use of the kitos_tools/archimate/import_from_kitos.py
 *
 */

if(isset($_POST["submitUpload"], $_FILES["fileToUpload"])) {// and $uploadOk == 1
  $uploadedfilename = 'uploaded_'.$_SESSION['unikSessionId'].'.archimate';
  $_SESSION['unikUploadFilnavn'] = $uploadedfilename;
  //
  // echo "bliver den her kode kørtFørst?\n";
  if(isset($_SESSION['filnavn'])){
    $tempfilnavn = $_SESSION['filnavn'];
    rename($tempfilnavn,$uploadedfilename);
  }
}
if(isset($_SESSION['unikUploadFilnavn'])){
  $uploadedfilename = $_SESSION['unikUploadFilnavn'];
}

if(isset($_POST["submitUpload"], $_FILES["fileToUpload"])) {
  //kontroller at det er et unik fil navn!
  //if(empty($uploadedfilename)){
  $uploadedfilename = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  //}
  $target_filnavn = basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  // echo "bliver den her kode kørtAnden?\n";
  if($uploadOk == 1){
    $_SESSION['isUploadOk'] = $uploadOk;
  }

  $xmlFileType = strtolower(pathinfo($uploadedfilename, PATHINFO_EXTENSION));

  $XMLReader = new XMLReader();

  $uploadedfil = $_FILES["fileToUpload"]["tmp_name"];
  // echo var_dump($uploadedfil)."\n";
  if(!empty($uploadedfil)){
    // echo var_dump($uploadedfil)."\n";
    $XMLReader->open($uploadedfil);
    // Enable the Parser Property
    $XMLReader->setParserProperty(XMLReader::VALIDATE, true);
  }

  // Check if archimate file is a actual XML/archimate or a valid xml.
  if (isset($_POST["submitUpload"])) {
      // XML validerings forsøg
      $isValid = $XMLReader->isValid();
      if ($isValid) {
        // echo "File is an xml - ";
        // echo mime_content_type($uploadedfil) . "\n";
        $uploadOk = 1;
      }
    }

  // Check if file already exists
  if ($uploadedfilename != "/tmp/uploads/" and file_exists($uploadedfilename)){//file_exists($uploadedfilename)) {
    //TODO lav en extra if, med file_exist, for at være 100%
    if ($uploadOk = True){
      //Filen findes allerede
      echo "Sorry, file already exists.";
    }
  }

  // Check file size
  if ($_FILES["fileToUpload"]["size"] > 2000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // Allow certain file formats
  // Andre godkente filnavne end: archimate?
  if ( isset($xmlFileType) && $xmlFileType != "archimate") {
    echo "Sorry, only Archimate files are allowed.";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
  } else {
    if(isset($_SESSION['unikUploadFilnavn'])){
      // echo $_SESSION['unikUploadFilnavn'] . "\n";
    }
    // echo $uploadedfilename . "\n";
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uploadedfilename)) {
      // Filen blev uploadet
      // echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
      //
      $_SESSION['filnavn'] = $_FILES["fileToUpload"]["name"];
      // kør archi-py script eller skift til en anden side.
    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  }
}
if (isset($uploadOk) and $uploadOk == TRUE) {
  // session
  $startRunScriptTime = time();// date("i:s");
  //runarchimatescript &
  $slutRunScriptTime = $startRunScriptTime + 11;
  $_SESSION['start_tid'] = $startRunScriptTime;
  $_SESSION['slut_tid'] = $slutRunScriptTime;
  $start_bredde = 1;
  run_archimate_script();
}

// echo var_dump($uploadOk)."\n";
function run_archimate_script(){
  // Redundant, siden funktionen bliver kaldet efter en if-test
    $option = "";
    if(isset($_SESSION['filnavn'])){
      $filnavn = $_SESSION['filnavn'];
      $downloadfile = $_SESSION['unikFilnavn'];
      // echo "Der er blevet uploadet".$filnavn."\n";
      // echo "Fil til download er: ".$downloadfile."\n";
    }
    
    if (!empty($filnavn) and !is_dir($filnavn)){
      // echo "Køre upload scriptet.\n";
      // $optionNeworUpdate = "";
      if(isset($_POST["submitUpload"])){
        $radioUpdateOrNot = $_POST["firsttimeOrUpdate"];
        if($radioUpdateOrNot == "True" ){
          $option = " --id ";
        } else{
          $option = "";
        }
      }

      $command = "cd /opt/kitos_tools/ && python3 archimate/import_from_kitos.py " .
      "--infile=/tmp/uploads/" . $filnavn . $option .
      " --outfile=/tmp/uploads/" . $downloadfile ." >/dev/null 2>/dev/null &";
      // echo $command;
      $output = shell_exec($command);
      // echo $output;
      // echo "\n".$_SESSION['unikFilnavn']."\n";
      // $command = "python3 getFileTime.py /tmp/uploads/".$filnavn;
      
      // if(file_exists("/tmp/uploads/".$filnavn)){
      //   $output = shell_exec($command);
      //   // echo $output;
      // }
    }
  //}
}
// TODO: Kontroller om der er et filnavn og eller om der faktisk er blevet kørt script!
// TODO: Debug kode, skal fjernes når det virker!
if ((isset($uploadOk) and $uploadOk == TRUE) and !empty($uploadedfilename)) {
  if (file_exists($uploadedfilename)){
    // echo "File has been uploaded succesfull, and we are now running script"."\n";
    // // $startRunScriptTime = time();// date("i:s");
    // echo "starting at: ". $startRunScriptTime."\n";
    // // echo "".microtime()."\n";
    // echo "".time()."\n";
    // echo "End time: ".$elevenSecFromStart."\n";
  }
}

if ($_SERVER["REQUEST_METHOD"] = "POST") {
  if (empty($_POST["user"])) {
    $userErr = "UserName is required";
  } else {
    if(!checkemail($_POST["user"])){
      $userErr = "Invalid email address.";
    }
    else{
      $userErr = '<b><font color="green">✓</font></b>';
    }
    $user = test_input($_POST["user"]);
  }
  if (empty($_POST["password"])) {
    $passwordErr = "kodeord er krævet";
  } else {
    $password = test_input($_POST["password"]);
  }

  if (empty($_POST["user"])) {
    $userErr = "Email er krævet";
  } else {
    $user = test_input($_POST["user"]);
    // check if e-mail address is well-formed
    if (!filter_var($user, FILTER_VALIDATE_EMAIL)) {
      $userErr = "Invalid user/email format";
    }
  }
  // verify user&password isValid
  if (!empty($_POST["user"]) and !empty($_POST["password"])) {
    $command = "cd /opt/kitos_tools/ && python3 /var/www/html/authorize_kitos_user.py";
    $output = trim(shell_exec($command));
    // echo trim(var_dump($output));
    if($output == "Invalid-User-Login"){
      $passwordErr = "<b>🚫Ugyldig bruger login information</b>";
    } else{
      $passwordErr = '<b><font color="green">✓</font></b>';
    }
  }
}

function checkemail($str) {
  return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

// borrowed from https://www.w3schools.com/php/php_form_validation.asp
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// ShowWebForm();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KITOS archimate Web Brugerflade</title>
    <link rel="stylesheet" type="text/css" href="styles.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <link rel="shortcut icon" href="https://os2.eu/sites/all/themes/osto_web/favicon.ico" type="image/vnd.microsoft.icon" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
    h1 {
        color: rgb(255, 255, 255);
    }
    .error {
        color: #FF0000;
    }
    .custom-file-upload {
      color: red;
      display: inline-block;
      padding: 6px 12px;
      cursor: pointer;
    }
    #progressBar {
      width: 90%;
      margin: 10px auto;
      height: 22px;
      border: 1px solid #111;
      background-color: #292929;
    }
    #progressBar div {
      height: 100%;
      text-align: right;
      padding: 0 10px;
      line-height: 22px; /* same as #progressBar height if we want text middle aligned */
      width: 0;
      color: #fff;
      background-color: #0099ff;
      box-sizing: border-box;
      box-shadow: 0 2px 2px #333;
    }
    </style>
</head>

<body>
    <div class="header" style="background-color: rgb(5, 102, 141);">
        <h1>
            Nem hent af:
        </h1>
    </div>
    <h3>
        Skriv dit brugernavn og kodeord til:
    </h3>
    <!--  -->
    <!-- web-form upload2.php -->
    <span class="error">* påkrævet felt</span>
    <!-- Start Forms -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data" name="uploadForm">

            <div class="row">
                <div class="col-25">
                    <label>Brugernavn:</label>
                </div>
                <div class="col-75">
                    <input type="text" name="user" value="<?php echo $user; ?>">
                    <span class="error">* <?php echo $userErr;?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-25">
                    <label>Kodeord:</label>
                </div>
                <div class="col-75">
                    <input type="password" name="password" value="<?php echo $password; ?>">
                    <span class="error">* <?php echo $passwordErr;?></span>
                </div>
            </div>
            <div class="row">
              <div class="col-25">
                <input type="Radio" id="firstTimeSynced" name="firsttimeOrUpdate" value="False" checked>
              </div>
              <div class="col-75">
                <label for="firstTimeSynced">Er det første gang, at modellen bliver synkroniseret?</label>
              </div>
              <div class="col-25">
                <input type="Radio" id="updateModel" name="firsttimeOrUpdate" value="True">
              </div>
              <div class="col-75">
                <label for="updateModel">Opdater eksisterende model.</label>
              </div>
            </div>

            <!-- <input type="submit" name="submitCheckBruger" class="btn btn-primary" value="CheckBruger"> -->
            <!-- <br> -->

            <h3>Upload din archimate model fil:</h3>
            <label for="fileSelect">Filnavn:</label>
            <input type="file" name="fileToUpload" id="fileToUpload" multiple>
            <!-- <label for="fileToUpload" class="custom-file-upload" id="noFileMsg"></label> -->
            <!-- <input type="submit" name="submitUpload" value="Upload" id="submitUploadButton"> -->
            <?php
              //if (isset($_POST['submitUpload'])) {
                // echo var_dump($filnavn)."\n".var_dump($uploadedfilename)."\n";
                if (!empty($uploadedfilename) and !is_dir($uploadedfilename) and file_exists($uploadedfilename)){
                  // echo var_dump($uploadedfilename)."\n";
                  echo '<input id="submitUploadButton" type="button" name="submitUpload" value="Filen er blevet uploaded" class="w3-button w3-green">';
                } else {
                  echo '<input type="submit" name="submitUpload" value="Upload" id="submitUploadButton" onclick="checkIfFileSelected();">';
                }
              //}
            ?>
            <!-- On Upload pressed checkIfFileSelected(); -->
            <p id = "FilValgt" style = "color:red; font-size: 20px; font-weight: bold;"></p> 
        </form>
        <!-- <br> -->
        <!-- // Vis fil køre knap, hvis filen blev uploadet. -->
        <!-- TODO BUG, når CheckBruger, trykkes, vises knappen!? -->
        <?php if (!empty($uploadedfilename) and !is_dir($uploadedfilename) and file_exists($uploadedfilename) and !file_exists("/tmp/uploads/" . $downloadfile )) : ?>
          <!-- <form action="runarchimatescript.php?" method="post"> -->
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
              <!-- <input type="submit" name="submitRunScript" value="Kør archimate Script"> -->
              <input type="hidden" name="filnavn" id="hiddenField" value="<?php echo $target_filnavn ?>" />
          </form>
          <button class="w3-button w3-green" onclick="run_script_pressed_TWO();">Kør archimate Script</button>
        <?php endif; ?>
        <?php if (!empty($uploadedfilename) and !is_dir($uploadedfilename) and file_exists($uploadedfilename)) : ?>
          <!-- Viskørelsetid -->
          <!-- $startRunScriptTime -->
          <!-- <progress id="progressScriptRunTime" value="0" max="1100" style="width: 100%; height: 45px;"></progress> -->
          <!-- <div id="runScriptProgress" class="w3-light-blue" style="height:24px;width:0"></div> -->
          <div id="progressBar">
              <div></div>
          </div>
        <?php endif; ?>
        <?php if (file_exists("/tmp/uploads/" . $downloadfile )) : ?>
        <a href="/tmp/uploads/<?php echo $downloadfile ?>" Download>
            <!-- <button class="btn"><i class="fa fa-download"></i>[Download 🗎]</button> -->
            <input type="submit" name="submitDownload" value="[Download 🗎]">
        </a>
        <?php endif; ?>
    </div>
    <!-- End forms -->
    <!-- <div class="w3-container">
        <div class="w3-light-grey">
            <div id="runScriptProgressOLD" class="w3-light-blue" style="height:24px;width:0">
              <div></div>
            </div>
        </div>
        <button class="w3-button w3-green" onclick="move_old();">Kør progress-bar</button> -->
        <!-- <input type="submit" name="submitProgressBar" value="Kør progress-bar" onclick="move()"> -->
    <!-- </div> -->
    <p>
        <?php
 $temp_file=tempnam(sys_get_temp_dir(), 'Kitos_' );
//  echo $temp_file."\n<br>";
    //$tmpfname=tempnam(sys_get_temp_dir(), "Pre_" );
    rename($temp_file, $temp_file .='.archimate' );
    // echo $temp_file."\n";
    /*
     * web-brugerfladen
     */

     // isset() ? : null; $kitos_usr_name=$POST['kitos_user'];//form->input->name="user"
    //$kitos_pwd_name = $POST['kitos_password'];//form->input->name="password"

    if (isset($_GET['kitos_user'])) {
        switch ($variable) {
            case 'value':
                visDownloadKnap();
                break;

            default:
                visWebForm();
                break;
        }
    }
?>
    </p>

    <?php
    //Get Userforms info, and update settings.
    $filename = '/opt/kitos_tools/settings/settings.json';
    $json_object = file_get_contents($filename);
    $data = json_decode($json_object, true);
    if(empty($_POST['user'])){
      //Do nothing. maybe !empty()
    } else{
      $username = $_POST['user'];
      $data["KITOS_USER"] = $username;
      $data["KITOS_PASSWORD"] = $password;
    }
    $json_object = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    file_put_contents($filename, $json_object);
    ?>
    <?php //echo "target_file:".var_dump($uploadedfilename)."\n";
          //echo "filnavn:".var_dump($filnavn)."\n";
          //print_r($_SESSION);
    ?>
    <script>
    function move(bredde) {
        debugger;
        if(bredde>100){
          bredde=100;
        }
        var elem = document.getElementById("runScriptProgress");
        var width = bredde;//php echo $start_bredde ; //1;
        var id = setInterval(frame, 110);

        function frame() {
            if (width >= 100) {
                clearInterval(id);
            } else {
                width++;
                elem.style.width = width + '%';
            }
        }
        //location.reload(true);
        //Find en bedre måde at få den ind på?!
    }

    // function move_old() {
    //   var elem = document.getElementById("runScriptProgress");   
    //   var width = 1;
    //   var id = setInterval(frame, 110);
    //   function frame() {
    //     if (width >= 100) {
    //       clearInterval(id);
    //     } else {
    //       width++; 
    //       elem.style.width = width + '%'; 
    //     }
    //   }
    // }

    function run_script_pressed() {
        <?php $nu_tid = time();
        echo "var tidNu = new Date()/1000;";
        if(empty($_SESSION['start_tid'])){
          echo "//tom start tid\n";
        }else{
          $startRunScriptTime = $_SESSION['start_tid'];
          $jsStartTid = (int) $_SESSION['start_tid'];//*1000;
          echo "var starttid = ".$jsStartTid.";";
          $slutRunScriptTime = $_SESSION['slut_tid'];
          $jsSlutTid = (int) $_SESSION['slut_tid'];//*1000;
          echo "var sluttid = ".$jsSlutTid.";";
          $rest_tid = (int) $slutRunScriptTime - $nu_tid;
          echo "var resttid = sluttid - tidNu;";
          
          echo "//start: ".$startRunScriptTime."\n";
          echo "//rest: ".$rest_tid."\n";
          echo "//nu: ".$nu_tid."\n";
        }
          ?>
          alert(resttid);
          var absRestTid = Math.abs(resttid);
          var resttidspunkt = new Date((absRestTid*1000)).toGMTString();
          alert(resttidspunkt);

          vis_progress_bar(<?php echo $slutRunScriptTime ?>, <?php echo $rest_tid ?>);
    }

    function run_script_pressed_TWO() {
      var tidNu = new Date()/1000;
      //if(empty(< ? php $_SESSION['start_tid'] ? >)){
      var jsStartTid = <?php if(!empty($_SESSION['start_tid'])):
          echo $_SESSION['start_tid'];
        else:
          echo 0;
        endif; ?>;
      if(!jsStartTid){
        alert("tom start tid");
          }else{
        //$startRunScriptTime = $_SESSION['start_tid'];
        //var jsStartTid = (int) $_SESSION['start_tid'];
        var tidsforskel = tidNu - jsStartTid;
        
        if(tidsforskel>11){
          // updateBar(1100);
          //alert(tidsforskel);
          //location.reload(true);
          location.assign(location.toString());
        } else {
          console.log(tidsforskel);
          console.log(tidsforskel*100);
          //updateBar(tidsforskel*100);
          progress(Math.floor(16 - tidsforskel), 11, $('#progressBar'));
          //alert(tidsforskel);
          var resttidspunkt = new Date((tidsforskel*1000)).toGMTString();
          //alert(resttidspunkt);
        }
      }
    }
    
    // function updateBar(startPoint) {
    //   var bar = document.getElementById('progressScriptRunTime');
    //   bar.value = startPoint;
    //   startPoint += 10;
    //   //var sim = setTimeout("updateBar(" + startPoint + ")", 10);
    //   var sim = setTimeout(function() {
	
    //     updateBar(startPoint);
    //   }, 500);

    //   if (startPoint >= 1100) {
    //     bar.value = 1100;
    //     clearTimeout(sim);
    //     // force Reload of serverside page, to make the download like available
    //     location.assign(location.toString());
    //   }
		// }

    function progress(timeleft, timetotal, $element) {
			var progressBarWidth = timeleft * $element.width() / timetotal;
			$element.find('div').animate({ width: progressBarWidth }, 500).html(timeleft + " seconds left");
			if(timeleft > 0) {
				setTimeout(function() {
					progress(timeleft - 1, timetotal, $element);
				}, 1000);
			} else if(timeleft <= 0){
        location.assign(location.toString());
      }
		};

    function vis_progress_bar(slut,rest_tid) {
        <?php 
        // $startRunScriptTime = $_SESSION['start_tid'];
        // $slutRunScriptTime = $_SESSION['slut_tid'];
        echo "//rest: ".$rest_tid."\n";
        //$start_bredde = (int) $slutRunScriptTime - $rest_tid;
        $start_bredde = (int) intval(($rest_tid/11)*100);//11=sekunder_run_time
        //echo var_dump($slutRunScriptTime);
        echo "//start_brede: ".$start_bredde."\n";
        echo "//slut: ".$slut."\n"; ?>
        // echo "slut: ".$slutRunScriptTime."\n"; ? >
        move(<?php echo $start_bredde ?>);
    }

    var noFileMsgOLD = document.getElementById('FilValgt');
    //var noFileMsg = document.getElementById('noFileMsg');
    var file = document.getElementById("fileToUpload"); 

    // https://stackoverflow.com/questions/8664486/javascript-code-to-stop-form-submission
    // https://stackoverflow.com/questions/3350247/how-to-prevent-form-from-being-submitted/34347610
    function checkIfFileSelected() {
        if(file.files.length == 0 ){
            //noFileMsg.innerHTML = "Ingen file er valgt!";
            noFileMsgOLD.innerHTML = "Ingen fil er valgt!";
            event.preventDefault();
        }
    }
    </script>

</body>

</html>
