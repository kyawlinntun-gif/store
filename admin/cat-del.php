<?php
    session_start();
    include("confs/config.php");

    $id = strip_tags($_GET['id']);

    // Select delete name

    $result = $conn->prepare('SELECT name FROM categories WHERE id = :id');
	$result->execute(array( 'id' => $id ));
	while($row = $result->fetch()) {
		$_SESSION['delete_name'] = strip_tags($row['name']);
	}

    // Delete Query
    $conn->exec("DELETE FROM categories WHERE id=$id;");
    header('location: cat-list.php');
    exit();
?>