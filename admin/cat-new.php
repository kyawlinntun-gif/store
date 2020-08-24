<?php
    // $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
    // if($pageWasRefreshed)
    // {
    //     unset($_SESSION['error_name']);
    //     unset($_SESSION['error_remark']);
    // }

    // Create CSRF
    session_start();
	$token = md5(rand(1, 1000) . time());
    setcookie("csrf", $token);
    
    if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST['submit']))
    {
        // Check CSRF
        $cookie_token = $_COOKIE['csrf'];
		$form_token = $_POST['token'];
		if($cookie_token != $form_token) exit("Unauthorized Request!");

        // Error
        $name_error = '';
        $remark_error ='';

        // Post Data
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
        if((!($name_error) and !($remark_error)))
        {
            include("confs/config.php");

            // Check the name is exist

            $result = $conn->prepare('SELECT id FROM categories WHERE name = :name');
		    $result->execute(array( 'name' => $name ));
            while($row = $result->fetch()) {
                $exist = $row['id'];
            }

            if($exist)
            {
                $name_error = 'Name is already existed!';
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

    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="asset/css/all.min.css">
    <link rel="stylesheet" href="asset/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/css/style.css">

    <title>New Category</title>
</head>
<body>
    <div class="cat-new">
        <div class="row">
            <div class="col-md-6 offset-3">
                <div class="card mt-5">
                    <div class="card-header">
                        <h1>New Category</h1>
                    </div>
                    <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                        <!-- Csrf -->
                        <input type="hidden" name="token" value="<?= strip_tags($token) ?>">
            
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" name="name" id="name" class="form-control" 
                            value="<?php 
                                if(isset($old_name))
                                {
                                    if($old_name)
                                    {
                                        echo strip_tags($old_name);
                                    }
                                }
                            ?>">
                        </div>
                        
                        <?php if(isset($name_error)) : ?>
                            <?php if($name_error) : ?>
                                <div class="alert alert-danger">
                                    <?php echo strip_tags($name_error) ?>
                                </div>
                            <?php endif ; ?>
                        <?php endif ; ?>

                        <div class="form-group">
                            <label for="remark">Remark</label>
                            <textarea name="remark" id="remark" rows="5" class="form-control"><?php 
                                if(isset($old_remark))
                                {
                                    if($old_remark)
                                    {
                                        echo strip_tags($old_remark);
                                    }
                                }
                            ?></textarea>
                        </div>

                        <?php if(isset($remark_error)) : ?>
                            <?php if($remark_error) : ?>
                                <div class="alert alert-danger">
                                    <?php echo strip_tags($remark_error) ?>
                                </div>
                            <?php endif ; ?>
                        <?php endif ; ?>

                        <input type="submit" class="btn btn-primary" value="Add Category" name="submit">
                        
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