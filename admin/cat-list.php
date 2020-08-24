<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartegory list</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="asset/css/all.min.css">
    <link rel="stylesheet" href="asset/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/css/style.css">

</head>
<body>
    <div class="cart-list">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card mt-5">
                    <?php if(isset($_SESSION['category_exist'])) : ?>
                        <?php if($_SESSION['category_exist']) : ?>
                            <div class="alert alert-danger">
                                <?php $category_exist = strip_tags($_SESSION['category_exist']) ?>
                                <?php echo strip_tags($category_exist) ?>
                                <?php unset($_SESSION['category_exist']) ?>
                            </div>
                        <?php endif ; ?>
                    <?php endif ; ?>
                    <?php if(isset($_SESSION['delete_name'])) : ?>
                        <?php if($_SESSION['delete_name']) : ?>
                            <div class="alert alert-info">
                                <?php $delete_name = strip_tags($_SESSION['delete_name']) ?>
                                <?php echo strip_tags($delete_name) ?>
                                <?php unset($_SESSION['delete_name']) ?>
                            </div>
                        <?php endif ; ?>
                    <?php endif ; ?>
                    <div class="card-header">
                        <h1>Category List</h1>
                    </div>
                    <div class="card-body">
                        <?php
                            include('confs/config.php');

                            $rows = $conn->query( "SELECT * FROM categories" );
                        ?>
                        <ul>    
                            <?php foreach($rows as $row) : ?>
                                <li>
                                    [ <a href="cat-del.php?id=<?php echo $row['id'] ?>" class="del">del</a> ]
                                    [ <a href="cat-edit.php?id=<?php echo $row['id'] ?>">edit</a> ] 
                                    <?php echo strip_tags($row['name']) ?>
                                </li>
                            <?php endforeach ; ?>
                        </ul>
                        <a href="cat-new.php">New Category</a>
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