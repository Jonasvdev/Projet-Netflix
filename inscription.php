<?php
session_start();

if (isset($_SESSION["connect"]) && $_SESSION[""]) { 
    header("location : index.php");
    exit();

}

$errors = [];

require('src/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password_two = $_POST['password_two'] ?? '';

    if (!$email) {
        $errors[] = 'L\'adresse e-mail est invalide.';
    } else {
        $domain = substr(strrchr($email, '@'), 1);
        if (!$domain || !(checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'))) {
            $errors[] = 'Le domaine de l\'adresse e-mail ne semble pas valide.';
        }
    }

    if ($password !== $password_two) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }

    if (empty($errors)) {
        $usersFile = __DIR__ . '/users.json';
        $users = [];

        if (file_exists($usersFile)) {
            $content = file_get_contents($usersFile);
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $users = $decoded;
            }
        }

        if (isset($users[$email])) {
            $errors[] = 'Cette adresse e-mail est déjà utilisée.';
        } else {
            $users[$email] = [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('c'),
            ];

            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            header('Location: index.php?success=1&message=' . urlencode('Inscription réussie, veuillez vous connecter.'));
            exit;
        }
    }
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$errors[] = 'L\'adresse e-mail est invalide.';
	header('Location: inscription.php?error=1&message=' . urlencode('L\'adresse e-mail est invalide.'));
	exit;
}

 
// Email dejà utilisé

$req = $db->prepare('SELECT COUNT(*) as numberEmail FROM users WHERE email = ?');
$req->execute(array([$email]));

while($email_verification = $req->fetch());{

	if($email_verification['number'] !=1 ){
		header('location:inscription.php?error=1&message=Votre email est déjà utilisé par un autre utilisateur');
		exit;

	}

if ($req->fetchColumn() > 0) {
    $errors[] = 'Cette adresse e-mail est déjà utilisée.';
    header('Location: inscription.php?error=1&message=' . urlencode('Cette adresse e-mail est déjà utilisée.'));
    exit;
}
}

// hash
$secret = sha1($email->secret).time();
$secret = sha1($secret).time();

// envoi
$req = $db->prepare('INSERT INTO user(email, password, secret) VALUES(?,?,?');
$req->execute(array($email, $password, $secret));
    header('location: inscription.php?success=1'. urlencode(''));
    exit;

// chiffrage du mot de passe 
    $password = "aq1".sha1($password."1234")."25";
    
// connexion

$req = $db->prepare("SELECT * FROM  user WHERE email = ?");
$req->execute(array($email,));

while($user = $req->fetch()){
    if($user["email"] != $email_verification["email"]){
    if($email_verification["number"] != 1){}
    if($password == $user["password"]){
        $session['connect'] = 1 ;
        $session['$email'] = $user['$email'] ;

        header('location : index.php?success');
    }
}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">

        <?php  
        if(isset($_SESSION['connect'])){ ?>

        <?php  }  ?>
        
			<h1>S'inscrire</h1>

			<?php 
            if (!empty($errors)){
                echo ''. implode('', $errors) .'';
             ?>
				<div class="alert error">
					<ul>
						<?php 
                        foreach ($errors as $error): ?>
							<li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
                
			<?php ;
           } else if(!isset($_GET['success'])){

                $_GET['success'] = '';

                echo'<div class = "alert success"> Vous etes desormais inscrit. <a href="index.php"> 
                Connectez-vous </a> </div>.';

            } ?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>