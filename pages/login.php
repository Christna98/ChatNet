<?php

session_start();

// $_SESSION["CurrentUser"] = [];

if (array_key_exists("CurrentUser", $_SESSION)) {
    header("location: ../index.php", true);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="garden">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatNet - Login</title>
    <link rel="stylesheet" href="../styles/output.css">
</head>

<body>
    <nav class="navbar flex justify-between">
        <a href="/" class="flex font-bold text-2xl">
            <span class="text-accent">Chat</span>
            <span>Net</span>
        </a>

        <ul class="flex gap-4">
            <li>
                <a href="./login.php">Login</a>
            </li>
            <li>
                <a href="./register.php">Register</a>
            </li>
        </ul>
    </nav>

    <main class="flex flex-col justify-center items-center">

        <?php if (isset($_GET["error"])) : ?>
            <p class="text-error text-lg mt-8"><?= $_GET["error"]; ?></p>
        <?php endif; ?>

        <h2 class="text-2xl my-8">Login To Your Account</h2>

        <form action="../traitement/login.php" method="POST" class="flex flex-col gap-4">
            <div class="form-control">
                <label class="input-group">
                    <span>Username</span>
                    <input type="text" name="userName" placeholder="Enter your username" class="input input-bordered" />
                </label>
            </div>
            <div class="form-control">
                <label class="input-group">
                    <span>Password</span>
                    <input type="text" name="password" placeholder="Enter your password" class="input input-bordered" />
                </label>
            </div>

            <button type="submit" class="btn btn-active btn-primary">Login</button>
        </form>
    </main>
</body>

</html>