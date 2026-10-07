<?php

session_start();


// ==========================================
// ADMIN ACCESS PROTECTION
// ==========================================

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    http_response_code(401);
    exit("Unauthorized admin access.");
}


// ==========================================
// DATABASE
// ==========================================

require_once __DIR__ . "/db_connect.php";


// ==========================================
// ONLY POST REQUEST
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/admin-create-event.html");
    exit;
}


// ==========================================
// FORM DATA
// ==========================================

$title = trim($_POST["title"] ?? "");
$category = trim($_POST["category"] ?? "");
$eventDate = trim($_POST["event_date"] ?? "");
$eventTime = trim($_POST["event_time"] ?? "");
$location = trim($_POST["location"] ?? "");
$description = trim($_POST["description"] ?? "");

$errors = [];


// ==========================================
// VALIDATION
// ==========================================

if ($title === "") {
    $errors[] = "Event title is required.";
}

if ($category === "") {
    $errors[] = "Event category is required.";
}

if ($eventDate === "") {
    $errors[] = "Event date is required.";
}

if ($eventTime === "") {
    $errors[] = "Event time is required.";
}

if ($location === "") {
    $errors[] = "Event location is required.";
}

if ($description === "") {
    $errors[] = "Event description is required.";
}


// ==========================================
// IMAGE UPLOAD
// ==========================================

$imagePath = null;

if (
    isset($_FILES["event_image"]) &&
    $_FILES["event_image"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    if ($_FILES["event_image"]["error"] !== UPLOAD_ERR_OK) {
        $errors[] = "Image upload failed.";
    } else {

        $allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        $fileType = mime_content_type(
            $_FILES["event_image"]["tmp_name"]
        );

        if (!in_array($fileType, $allowedTypes, true)) {

            $errors[] =
                "Only JPG, PNG and WEBP images are allowed.";

        } else {

            // Maximum 5 MB
            if ($_FILES["event_image"]["size"] > 5 * 1024 * 1024) {

                $errors[] =
                    "Image size must be less than 5 MB.";

            } else {

                $uploadDirectory =
                    __DIR__ . "/../uploads/events/";

                if (!is_dir($uploadDirectory)) {

                    mkdir(
                        $uploadDirectory,
                        0777,
                        true
                    );
                }


                $extension = strtolower(
                    pathinfo(
                        $_FILES["event_image"]["name"],
                        PATHINFO_EXTENSION
                    )
                );


                $fileName =
                    "event_" .
                    time() .
                    "_" .
                    bin2hex(random_bytes(5)) .
                    "." .
                    $extension;


                $targetPath =
                    $uploadDirectory . $fileName;


                if (
                    move_uploaded_file(
                        $_FILES["event_image"]["tmp_name"],
                        $targetPath
                    )
                ) {

                    $imagePath =
                        "uploads/events/" . $fileName;

                } else {

                    $errors[] =
                        "Unable to save event image.";

                }
            }
        }
    }
}


// ==========================================
// SHOW ERRORS
// ==========================================

if ($errors) {

    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Create Event Error</title>";
    echo "</head>";
    echo "<body>";

    echo "<h2>Unable to Create Event</h2>";

    echo "<ul>";

    foreach ($errors as $error) {

        echo "<li>" .
            htmlspecialchars($error) .
            "</li>";

    }

    echo "</ul>";

    echo "<a href='../pages/admin-create-event.html'>";
    echo "Back to Create Event";
    echo "</a>";

    echo "</body>";
    echo "</html>";

    exit;
}


// ==========================================
// INSERT EVENT
// ==========================================

$sql = "INSERT INTO events
        (
            title,
            event_date,
            event_time,
            location,
            category,
            description,
            image_path
        )
        VALUES
        (
            :title,
            :event_date,
            :event_time,
            :location,
            :category,
            :description,
            :image_path
        )";


$stmt = $pdo->prepare($sql);


$stmt->execute([

    ":title" => $title,

    ":event_date" => $eventDate,

    ":event_time" => $eventTime,

    ":location" => $location,

    ":category" => $category,

    ":description" => $description,

    ":image_path" => $imagePath

]);


// ==========================================
// SUCCESS
// ==========================================

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";

echo "<meta charset='UTF-8'>";

echo "<meta name='viewport'
      content='width=device-width, initial-scale=1.0'>";

echo "<title>Event Created | StudentHub</title>";

echo "</head>";

echo "<body>";

echo "<h2>Event Created Successfully!</h2>";

echo "<p>";

echo "The event ";

echo "<strong>" .
    htmlspecialchars($title) .
    "</strong>";

echo " has been added successfully.";

echo "</p>";

echo "<br>";

echo "<a href='../pages/admin-create-event.html'>";
echo "Create Another Event";
echo "</a>";

echo "<br><br>";

echo "<a href='../pages/admin.html'>";
echo "Back to Admin Dashboard";
echo "</a>";

echo "</body>";

echo "</html>";

?>