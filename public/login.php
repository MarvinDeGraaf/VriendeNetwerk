<?php
session_start();
if (isset($_SESSION['login'])) {
    header('location: ../public');
}

if(isset($_POST['submit'])) {
    require_once('../src/auth.php');
    $auth = new Authenticate();
    $username = $_POST['username'];
    $password = $_POST['password'];
    $result = $auth->checkLogin($username, $password);
    if ($result) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        header('location: ../public');
    } else {
        $error = "Invalid username or password.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <title>Login</title>
</head>
<body>
    <img src="../assets/Images/VriendeNetwerk.png" alt="logo">
    <form action="" method="post">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" name="username" id="username" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>
        <button type="submit" name="submit">Login</button>
    </form>
    <form action="" method="post" class="register">
        <input type="submit" value="Registreren" name="register"><br>
        <?php
        if (isset($_POST['register'])) {
            header('location: signUp.php');
        }
        ?>
    </form>

        <?php if (isset($error)) {
            echo "<div class=\"error\">";
            echo "<p style='color:red;'>$error</p>";
            echo "</div>";
        } ?>
</body>
</html>