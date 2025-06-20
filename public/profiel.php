<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../public/login.php');
}
require_once('../src/customer.php');
require_once('../src/image.php');
require_once('../src/courses.php');
$customerService = new Customer();
$imageService = new Image();
$courseService = new Courses();

$customerUsername = $_SESSION['username'];
$customer = $customerService->getCustomerByUsername($customerUsername)[0];
$imageId = $customerService->getCustomerImage($customer['customerid'])[0]['imageId'];
$filename = $imageService->getImageUrlQuery($imageId)[0]['filename'];

//this was partually written by AI but not entirely
if ($_SERVER["REQUEST_METHOD"] == "POST") {//checks if POST was used
    if (isset($_FILES["upload"]) && $_FILES["upload"]["error"] == 0) { //check for errors
        $targetDir = "../assets/uploads/"; // Get the directiory
        $targetFile = $targetDir . basename($_FILES["upload"]["name"]); //gets the file

        //Validate the file type or size
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); //get the extesion like gif
        $allowedTypes = ["jpg", "png", "gif", "webp", "svg"]; //an array of extensions that are allowed
        if (in_array($fileType, $allowedTypes)) {//checks if the file is allowed
            if (move_uploaded_file($_FILES["upload"]["tmp_name"], $targetFile)) {//moves the file to the correct folder
                //from here it was all made by me again
                $result = "The file  has been uploaded successfully!";//tells the user that the file was uploaded successfully
                $imageService->storeImage(basename($_FILES["upload"]["name"])); //sends the filename to the database
                $imageId = $imageService->getImageByFilename(basename(basename($_FILES["upload"]["name"]))); //gets the image id
                $customerService->updateCustomerImage($customer['customerid'], $imageId[0]["imageId"]); //gives the customer the image
                header('location: profiel.php'); //redirects to the profile page so there is no confirm form resubmitting
            } else { //errors
                $result = "Sorry, there was an error uploading your file.";
            }
        } else {
            $result = "Sorry, only JPG, PNG, GIF, SVG, and webp files are allowed.";
        }
    } else {
        $result = "No file was uploaded or there was an error.";
    }


    //other data !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(nl|com)$/", $email)
    if (isset($_POST['saveData'])) {
        $courseid = $_POST['course'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];

        $customerService->updateCustomer($customer['customerid'], $courseid, $age, $gender, $email);
        header('location: profiel.php'); //redirects to the profile page so there is no confirm form resubmitting
    }

    if (isset($_POST['saveInterests'])) {
        $outing = $_POST['outing'];
        $song = $_POST['song'];
        $film = $_POST['film'];
        $meal = $_POST['meal'];
        $professor = $_POST['professor'];

        $customerService->updateCustomerHobbies($outing, $song, $film, $meal, $professor, $customer['customerid']);
        header('location: profiel.php'); //redirects to the profile page so there is no confirm form resubmitting
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/profiel.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <title>Profiel</title>
</head>

<body>
    <div>
        <nav>
            <img src="../assets/Images/VriendeNetwerk.png" alt="LogoVriendenNetwerk">
            <form action="vriendenzoek.php" method="post">
                <input type="text" name="search" id="search" placeholder="Zoek Nieuwe vrienden.">
                <input type="submit" value="Zoeken" name="searchBtn"><br>
            </form>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="profiel.php">Profiel</a></li>
                <li><a href="friends.php">Vrienden</a></li>
            </ul>
        </nav>
    </div>
    <main>
        <div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="algemeneInfo">
                    <div class="imageSection">
                        <img src="<?php echo "../assets/uploads/$filename"; ?>" alt="profile picture">
                        <div>
                            <input type="file" name="upload" id="upload" class="picture">
                            <input type="submit" value="Upload a picture" name="picture" class="picture">
                            <?php if (isset($_POST["picture"])) {
                                echo $result;
                            } ?>
                        </div>
                    </div>
                    <div>
                        <?php echo $customer['firstName'] . ' ' . $customer['prefix'] . ' ' . $customer['lastName'] ?><br>
                        <label>username: <input type="text" name="username" value="<?php echo $customer['username']; ?>" readonly></label><br>
                        <label>opleiding: <select name="course">
                                <?php
                                $rows = $courseService->getAllCourses();
                                foreach ($rows as $row) {
                                    if ($row['opleidingid'] == $customer['courseid'])
                                    {
                                        echo "<option value=" . $row['opleidingid'] . " selected>" . $row['name'] . "</option>";
                                    }
                                    else
                                    {
                                        echo "<option value=" . $row['opleidingid'] . ">" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select></label><br>
                        <label>leeftijd: <input type="number" name="age" value="<?php echo $customer['age']; ?>"></label><br>
                        <label>geslacht: 
                                <select name="gender">
                                    <option value="F" <?php if ($customer['gender'] == "F") { echo "selected"; } ?>>Vrouw</option>
                                    <option value="M" <?php if ($customer['gender'] == "M") { echo "selected"; } ?>>Man</option>
                                </select>
                        </label><br>
                        <label>email <input type="text" name="email" pattern="/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(nl|com)$/" value="<?php echo $customer['email']; ?>"></label><br>
                        <input type="submit" value="Bewerk" name="saveData" class="save">
                    </div>
                </div>
                <div class="interesses">
                    <h2>interesses</h2>
                    <label> Favoriete Uitgaansgelegenheid: <input type="text" name="outing" value="<?php echo $customer['outing']; ?>"></label><br>
                    <label> Favoriet liedje: <input type="text" name="song" value="<?php echo $customer['song']; ?>"></label><br>
                    <label> Favoriete film: <input type="text" name="film" value="<?php echo $customer['film']; ?>"></label><br>
                    <label> Favoriete eten: <input type="text" name="meal" value="<?php echo $customer['meal']; ?>"></label><br>
                    <label> Favoriete Docent: <input type="text" name="professor" value="<?php echo $customer['professor']; ?>"></label><br>
                    <input type="submit" value="Bewerk Interesses" name="saveInterests" class="save">
                </div>
            </form>
        </div>
    </main>
</body>

</html>