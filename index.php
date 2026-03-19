<?php
session_start();

require("src/log.php");

$_SESSION['email'] = $email;

// Connexion

$req = $db->prepare('SELECT * FROM uder WHERE email = ?');
$req->execute(array($email));
	while($user = $req->fetch(PDO::FETCH_ASSOC)) {
		if($password == $user['password']) {
			$_SESSION['email'] = $user['email'];

			$_SESSION['connect'] = 1 ;
			$_SESSION['$email'] = $user['email'];

			if(isset($_POST['auto'])) {
				setcookie('auth', $user['email'], time() + 365*24*3600,'/');	
			}
		}
	}

$user->email = $_SESSION['email'];

if(!empty($_POST["email"]) && !empty($_POST['password'])){
	$user = new User($_POST['email'], $_POST['password']);
	$user->password = $_POST['password'];
$users = $_POST['users'];


	$email = htmlspecialchars($_POST['email']);
	$password = $_POST['password'];
	
}

// Adresse email syntax

if(filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)){
	$user->email != $_SESSION['email'];
	header('location: index.php?erroor=1&message= "votre adress email est invalide"');
if(!empty($_POST['']) && !empty($_POST)){	
	$email = htmlspecialchars($_POST['email']);
;}
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email) {
        $errors[] = 'L\'adresse e-mail est invalide.';
    }

    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
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

        if (!isset($users[$email]) || !password_verify($password, $users[$email]['password'] ?? '')) {
            $errors[] = 'E-mail ou mot de passe incorrect.';
        } else {
            $_SESSION['user'] = $email;

            header('Location: index.php?success=1&message=' . urlencode('Connexion réussie.'));
            exit;
        }
    }
}

if (isset($_GET['success'], $_GET['message'])) {
    $successMessage = htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
				<h1>Bonjour</h1>
				
				<?php
				if(isset($_GET['success']) && $_GET['success'] == 1) {
					echo'<div class="alert success" Vous êtes maintenat conneté.</div>';
					}
				?>

				<p>Qu'allez-vous regarsez aujourd'hui ?</p>

				<small><a href="logout.php">Déconnexion</a></small>

				<?php if (!empty($successMessage)): ?>
					<div class="alert success"><?php echo $successMessage; ?></div>
				<?php endif; ?>

				<?php if (!empty($errors)): ?>
					<div class="alert error">
						<ul>
							<?php foreach ($errors as $error): ?>
								<li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php 
				if(isset($_GET['error'])) {

					if(isset($_GET['error']['message'])) {
						echo' <div class="alert error">' .htmlspecialchars($_GET['error']['message']).' </div>'
				
					;}
				;}


				 ?>

				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>
			

				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>