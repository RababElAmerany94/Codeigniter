<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $title; ?></title>
		<link href="<?php echo base_url('assets/css/login/style.css') ?>" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<div id="wrap">
			<?php if(isset($error) && !empty($error)): ?>
				<p class="alert-danger"><?php echo $error; ?></p>
			<?php endif; ?>
			<div id="form">
				<form action="login" method="POST">
					<input type="text" name="username" placeholder="Utilisateur" required>
				    <input type="password" name="password" placeholder="Mot de Passe" required>
				    	<select name="database">
				    		<?php foreach($dbs as $db): ?>
				    			<option value="<?=$db?>"><?=$db?></option>
				    		<?php endforeach; ?>
				    	</select>
					<button name="loginSubmit" type="submit">Se Connecter</button>
				</form>
			</div>
		</div>
	</body>
</html>
