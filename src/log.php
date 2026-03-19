<?php
if(isset($_COOKIE["auth"]) && !isset($_SESSION['connect'])){

    // Variable 
    $secret = htmlspecialchars($_COOKIE["auth"]);

    // Verification
    require"src/connect.php";

    $req = $conn->prepare("SELECT count(*) as numberAcount FROM user WHERE secret = ?");
    $req->execute(array($secret));

    while($user = $req->fetch()){
        if($user["numberAcount"] == 1){
            $reqUser = $db->prepare("SELECT * FROM user WHERE secret = ?");
            $reqUser->execute(array($secret));

            while($userAcount  = $reqUser->fetch(PDO::FETCH_ASSOC)){
               $_SESSION["connect"] = 1 ;
               $_SESSION["email"] = $userAcount["email"];

            }
        }
    }
   
}

if($_SESSION["connect"]){

    require"src/connect.php";

    $reqUser = $db->prepare("SELECT * FROM user WHERE EMAIL = ?");
    $reqUser->execute(array($_SESSION["email"]));

    while($userAcount["blocked"] = 1){
        header("location: ../logout.php");
        exit();
    }
        
    }


?>