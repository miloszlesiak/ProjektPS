<?php
	include 'includes/session.php';

	if(isset($_GET['complaint'])){
		$payid = $_GET['pay'];

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM cart WHERE product_id=:id");
		$stmt->execute(['id'=>$product]);
		$row = $stmt->fetch();

		if($row['numrows'] > 0){
			$_SESSION['error'] = 'Produkt już jest w koszyku';
		}
		else{
			try{
				$stmt = $conn->prepare("INSERT INTO sales (user_id, pay_id, title, description) VALUES (:user_id, :pay_id, :title, :description)");
				$stmt->execute(['user_id'=>$user['id'], 'pay_id'=>$payid, 'title'=>$title, 'description'=>$description]);

				$_SESSION['success'] = 'Dodano produkt do koszyka';
			}
			catch(PDOException $e){
				$_SESSION['error'] = $e->getMessage();
			}
		}

		$pdo->close();

		header('location: profile.php');
	}

?>