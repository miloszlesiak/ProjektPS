<?php
	include 'includes/session.php';

	if(isset($_POST['signup'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$repassword = $_POST['repassword'];

		$_SESSION['firstname'] = $firstname;
		$_SESSION['lastname'] = $lastname;
		$_SESSION['email'] = $email;

		if($password != $repassword){
			$_SESSION['error'] = 'Hasła są różne';
			header('location: signup.php');
		}
		else{
			$conn = $pdo->open();

			$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0){
				$_SESSION['error'] = 'Konto z tym adresem email już istnieje';
				header('location: signup.php');
			}
			else{
				$now = date('Y-m-d');
				$password = password_hash($password, PASSWORD_DEFAULT);

				try{
					$stmt = $conn->prepare("INSERT INTO users (email, password, firstname, lastname, created_on) VALUES (:email, :password, :firstname, :lastname, :now)");
					$stmt->execute(['email'=>$email, 'password'=>$password, 'firstname'=>$firstname, 'lastname'=>$lastname, 'now'=>$now]);
					$userid = $conn->lastInsertId();

					$message = "
						<h2>Dziękujemy za rejestrację.</h2>
						<p>Twoje konto:</p>
						<p>Adres email: ".$email."</p>
						<p>Hasło: ".$_POST['password']."</p>
					";

					unset($_SESSION['firstname']);
					unset($_SESSION['lastname']);
					unset($_SESSION['email']);

					$_SESSION['success'] = 'Konto zostało stworzone.';
					header('location: signup.php');
				}
				catch(PDOException $e){
					$_SESSION['error'] = $e->getMessage();
					header('location: register.php');
				}
				$pdo->close();
			}
		}
	}
	else{
		$_SESSION['error'] = 'Wypełnij formularz rejestracji';
		header('location: signup.php');
	}
?>