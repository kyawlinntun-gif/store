<?php
    session_start();
	$token = md5(rand(1, 1000) . time());
    setcookie("csrf", $token);

    // Category Exits
    if(isset($_GET['id']))
    {
        $category_id = strip_tags($_GET['id']);
        if(isset($category_id))
        {
            include('confs/config.php');
            $id = $category_id;
            $result = $conn->prepare('SELECT * FROM categories WHERE id = :id');
            $result->execute(array( 'id' => $id ));
            while($row = $result->fetch()) {
                $category_id = strip_tags($row['id']);
                $category_name = strip_tags($row['name']);
                $category_remark = strip_tags($row['remark']);
            }
            if(!($category_name))
            {
                $_SESSION['category_exist'] = 'No found the data in the Category.';
                header('location: cat-list.php');
                exit();
            }
        }
    }

    // Check Form Validation
    if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST['submit']))
    {
        // Check CSRF
        $cookie_token = $_COOKIE['csrf'];
		$form_token = $_POST['token'];
		if($cookie_token != $form_token) exit("Unauthorized Request!");

        // Error
        $error_name = '';
        $error_remark ='';

        // Post Data
        $id = strip_tags($_POST['id']);
        $name = strip_tags($_POST['name']);
        $remark = strip_tags($_POST['remark']);
        $old_name = $name;
        $old_remark = $remark;

        // Check name
        if(empty($name))
        {
            $name_error = 'Name must be required!';
        }
        else if(!(strlen($name) >= 3 and strlen($name) <=15))
        {
            $name_error = 'Name must be between 3 and 15.';
        }
        else if(!(is_string($name))){
            $name_error = 'Name must be string.';
        }
        else if(!(preg_match('/[^a-zA-Z0-9]*$/', $name)))
        {
            $name_error = 'Name must be alphabets and numbers.';
        }

        // Check remark
        if(empty($remark))
        {
            $remark_error = 'Remark must be required!';
        }
        else if(!(strlen($remark) >= 5 and strlen($remark) <=50))
        {
            $remark_error = 'Remark must be between 5 and 50.';
        }
        else if(!(is_string($remark)))
        {
            $remark_error = 'Remark must be string.';
        }

        // Insert Data
        if((!($error_name) and !($error_remark)))
        {
            include("confs/config.php");

            // Check the name is exist

            $result = $conn->prepare('SELECT id FROM categories WHERE name = :name');
		    $result->execute(array( 'name' => $name ));
            while($row = $result->fetch()) {
                $exist = $row['id'];
                
            }

            if(!($exist == $id))
            {
                if($exist)
                {
                    $error_name = 'Name is already existed!';
                }
                else
                {
                    $sql = "INSERT INTO categories (name, remark, created_date, modified_date) VALUES (:name, :remark, now(), now())";
                    $pre = $conn->prepare( $sql );
                    $pre->execute( array(':name' => ucfirst($name), ':remark' => ucfirst($remark)) );
                    
                    header("location: cat-list.php");
                    exit();
                }
            }
            else
            {
                // $sql = "INSERT INTO categories (name, remark, created_date, modified_date) VALUES (:name, :remark, now(), now())";
                // $pre = $conn->prepare( $sql );
                // $pre->execute( array(':name' => ucfirst($name), ':remark' => ucfirst($remark)) );
                $name = ucfirst($name);
                $remark = ucfirst($remark);
                $result = $conn->query("UPDATE categories SET name='$name', remark='$remark' WHERE id='$id'");
                    
                header("location: cat-list.php");
                exit();
            }

        }

    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category edit</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="asset/css/all.min.css">
    <link rel="stylesheet" href="asset/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="cart-edit">
        <div class="row">
            <div class="col-md-6 offset-3">
                <div class="card mt-5">
                    <div class="card-header">
                        <h1>Edit Category</h1>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">

                            <!-- CSRF -->
                            <input type="hidden" name="token" value="<?php echo strip_tags($token) ?>">

                            <input type="hidden" name="id" value="<?php
                                if(isset($category_id))
                                {
                                    if($category_id)
                                    {
                                        echo strip_tags($category_id);
                                    }
                                }
                            ?>">
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php
                                    if(isset($old_name))
                                    {
                                        if($old_name)
                                        {
                                            echo strip_tags($old_name);
                                        }
                                    }
                                    else
                                    {
                                        if(isset($category_name))
                                        {
                                            if($category_name)
                                            {
                                                echo strip_tags($category_name);
                                            }
                                        }
                                    }
                                ?>">
                            </div>

                            <?php if(isset($error_name)) : ?>
                                <?php if($error_name) : ?>
                                    <div class="alert alert-danger">
                                        <?php echo strip_tags($error_name) ?>
                                    </div>
                                <?php endif ; ?>
                            <?php endif ; ?>

                            <div class="form-group">
                                <label for="remark">Remark</label>
                                <textarea name="remark" id="remark" cols="30" rows="5" class="form-control"><?php
                                    if(isset($old_remark))
                                    {
                                        if($old_remark)
                                        {
                                            echo strip_tags($old_remark);
                                        }
                                    }
                                    else
                                    {
                                        if(isset($category_remark))
                                        {
                                            if($category_remark)
                                            {
                                                echo strip_tags($category_remark);
                                            }
                                        } 
                                    }
                                ?></textarea>
                            </div>

                            <?php if(isset($error_remark)) : ?>
                                <?php if($error_remark) : ?>
                                    <div class="alert alert-danger">
                                        <?php echo strip_tags($error_remark) ?>
                                    </div>
                                <?php endif ; ?>
                            <?php endif ; ?>

                            <input type="submit" class="btn btn-primary" value="Update Category" name="submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jquery js -->
    <script src="asset/js/jquery.js"></script>

    <!-- bootstrap js -->
    <script src="asset/js/all.min.js"></script>
    <script src="asset/js/bootstrap.min.js"></script>
    <script src="asset/js/bootstrap.bundle.min.js"></script>
</body>
</html>