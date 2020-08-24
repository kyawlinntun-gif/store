<?php
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
        $error_title = '';
        $error_author = '';
        $error_summary = '';
        $error_price = '';
        $error_category_id = '';
        $error_cover = '';

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
    <title>New Book</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="asset/css/all.min.css">
    <link rel="stylesheet" href="asset/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>

    <div class="book-new">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-3">
                    <div class="card mt-5">
                        <div class="card-header">
                            <h1>New Book</h1>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="$_POST">

                                <!-- CSRF -->
                                <input type="hidden" name="token" value="<?php echo strip_tags($token) ?>">

                                <div class="form-group">
                                    <label for="title">Book Title</label>
                                    <input type="text" id="title" name="title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="author">Author</label>
                                    <input type="text" id="author" name="author" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="summary">Summary</label>
                                    <textarea name="summary" id="summary" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="text" id="price" name="price" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="category_id">Category</label>
                                    
                                    <select name="category_id" id="category_id" class="form-control">
                                        <?php
                                            include('confs/config.php');
                                            $rows = $conn->query("SELECT * FROM categories");
                                            foreach( $rows as $row ) {
                                                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cover">Cover</label>
                                    <input type="file" id="cover" name="cover" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Book</button>
                            </form>
                        </div>
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