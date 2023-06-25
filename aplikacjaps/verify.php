<?php
	include 'includes/session.php';
	$conn = $pdo->open();

	if(isset($_POST['login'])){
		
		$email = $_POST['email'];
		$password = $_POST['password'];

		try{
			$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0){
				if(password_verify($password, $row['password'])){
					if($row['type']){
						$_SESSION['admin'] = $row['id'];
					}
					else{
						$_SESSION['user'] = $row['id'];
					}
				}
				else{
					$_SESSION['error'] = 'Nieprawidłowe hasło';
				}
			}
			else{
				$_SESSION['error'] = 'Nie znaleziono adresu email';
			}
		}
		catch(PDOException $e){
			echo "Problem z połączeniem: " . $e->getMessage();
		}

	}
	else{
		$_SESSION['error'] = 'Wpisz dane logowania';
	}

	$pdo->close();

	header('location: login.php');

?>