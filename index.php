<?php
    require "functions.php"; 

    if(!isset($_SESSION["username"])){
        header("Location: login.php");
    }

    $ebookPerPage = 5;
    $totalEbook = count(query("SELECT * FROM ebooks"));
    $totalPage = ceil($totalEbook / $ebookPerPage);
    $activePage = isset($_GET["page"]) ? $_GET["page"] : 1;
    $index = $ebookPerPage * $activePage - $ebookPerPage;

    if(isset($_GET["search"]) || isset($_GET["keyword"])){
        $keyword = $_GET["keyword"];
        $ebooks = searchEbook($keyword);
        $totalPage = ceil(count($ebooks) / $ebookPerPage);
        $ebooks = searchEbook($keyword,$index,$ebookPerPage);
    }else{
        $ebooks = query("SELECT * FROM ebooks LIMIT $index,$ebookPerPage");
    }

    $totalEbook = count($ebooks);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/img/logo_ebook.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courgette&family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Home - eBook Apps</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img class="me-1" src="assets/img/logo_ebook.png" alt="Logo eBook"> eBook Apps
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <!-- TODO: Buat List eBook -->
                        <a class="nav-link" href="#">List eBook</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create.php">Add eBook</a>
                    </li>
                </ul>
                <form class="d-flex" action="" method="get">
                    <input class="form-control me-2" aria-label="Search" type="search" name="keyword" placeholder="Search eBooks" autocomplete="off" value="<?php if(isset($keyword)) echo $keyword ?>" autofocus>
                    <button class="btn btn-warning" type="submit" name="search">Search</button>
                </form>
                <a class="btn btn-danger mx-2 my-lg-0 my-2 fw-bold" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <h1 class="h3 mb-4">Total Ebooks: <?= $totalEbook ?></h1>
        <?php if($totalEbook === 0) : ?>
        <div class="not-found d-flex flex-column align-items-center justify-content-center">
            <i class="bi bi-search"></i>
            <h2 class="my-4">Oops couldn't find any ebooks!</h2>
        </div>
        <?php else: ?>
            <?php if($totalEbook === 1 || $totalEbook === 2) : ?>
            <div class="list-ebook full-height">
            <?php else: ?>
            <div class="list-ebook">
            <?php endif; ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-2 g-xl-3 g-2">
                    <?php foreach($ebooks as $ebook) : ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="row g-0">
                                <div class="col-xl-4 text-center">
                                    <img class="img-fluid rounded-start" src="assets/img/<?= $ebook["cover"] ?>" alt="<?= $ebook["cover"] ?>">
                                </div>
                                <div class="col-xl-8">
                                    <div class="card-body">
                                        <h5 class="card-title text-center fw-bold my-2"><?= $ebook["title"] ?></h5>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Author: <?= $ebook["author"] ?></li>
                                        <li class="list-group-item">Category: <?= $ebook["category"] ?></li>
                                        <li class="list-group-item">Price: 
                                            <?php if($ebook["price"] === "Free") : ?>
                                            <span class="badge bg-success">Free</span>
                                            <?php else : ?>
                                            <span class="badge bg-danger">Paid</span>
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                    <div class="card-body text-center">
                                        <div class="btn-group" role="group" aria-label="Update and Delete">
                                            <a class="btn btn-sm btn-outline-success" href="update.php?id=<?= $ebook["id"] ?>">Update</a>
                                            <a class="btn btn-sm btn-outline-danger delete-ebook" href="delete.php?id=<?= $ebook["id"] ?>&cover=<?= $ebook["cover"] ?>">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <nav class="my-4" aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($activePage <= 1) echo "disabled" ?>">
                    <a class="page-link" href="?page=<?= $activePage - 1 ?><?php if(isset($keyword)) echo "&keyword=$keyword" ?>">Previous</a>
                </li>
                <?php for($i = 1; $i <= $totalPage; $i++) : ?>
                    <li class="page-item <?php if($i == $activePage) echo "active" ?>" aria-current="page">
                        <a class="page-link" href="?page=<?= $i ?><?php if(isset($keyword)) echo "&keyword=$keyword" ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if($activePage >= $totalPage) echo "disabled" ?>">
                    <a class="page-link" href="?page=<?= $activePage + 1 ?><?php if(isset($keyword)) echo "&keyword=$keyword" ?>">Next</a>
                </li>
            </ul>
        </nav>
    </main>

    <footer>
        <div class="text-center text-light p-4 bg-dark">&copy; 2021 Copyright <a class="link-warning text-decoration-none fw-bold" href="https://github.com/madfauzy" target="_blank">Ahmad Fauzy</a>. All Rights Reserved.</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/script.js"></script>
    <?php if(isset($_GET["delete"])) : ?>
        <?php if($_GET["delete"] === "success") : ?>
        <script>
            Swal.fire(
                'Success!',
                'eBook has been deleted.',
                'success'
            );
        </script>
        <?php else : ?>
        <script>
            Swal.fire(
                'Error!',
                'Failed to delete eBook. Try again!',
                'error'
            );
        </script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>