<?php
session_start(); // Start a new session or resume the existing one

$error = ''; // Variable to hold error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../db_connect.php'; // Include your database connection file

    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    if (empty($username) && empty($password)) {
        $error = 'Vă rugăm să completați câmpurile pentru nume de utilizator și parolă.';
    } elseif (empty($username)) {
        $error = 'Vă rugăm să completați câmpul pentru nume de utilizator.';
    } elseif (empty($password)) {
        $error = 'Vă rugăm să completați câmpul pentru parolă.';
    } else {
        // Prepare and execute the SQL statement
        $sql = "SELECT * FROM Utilizatori WHERE NumeUtilizator = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['Parola'])) {
                $_SESSION['user_id'] = $user['UtilizatorID'];
                $_SESSION['username'] = $user['NumeUtilizator'];
                header("Location: /MeniuPrincipal/meniu_principal.php"); // Redirect to a different page
                exit;
            } else {
                $error = 'Nume de utilizator sau parolă incorectă.';
            }
        } else {
            $error = 'Nume de utilizator sau parolă incorectă.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Autentificare/autentificare.css">
</head>
<body>
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-lg-7 col-md-7 col-sm-6 p-0">
                <img src="/Imagini/RouteRoverCover.jpg" class="img-fluid img-full-height" alt="Route Rover Cover">
            </div>
            <div class="col-lg-5 col-md-5 col-sm-6 d-flex">
                <div class="full-height-form w-100 d-flex flex-column">
                    <!-- Locul pentru erori -->
                    <div class="error mb-3">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger text-center" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <form method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" autocomplete="off" placeholder="Nume de utilizator">
                        </div>
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="password" placeholder="Parola" id="password">
                            <i toggle="#password" class="fas fa-fw fa-eye-slash field-icon toggle-password" style="color: #495057;"></i>    
                        </div>
                        <div class="float-right">
                            <a href="/ResetareParola/resetare_parola.php">Ai uitat parola?</a>
                        </div>
                        <div class="d-flex flex-column buttons">
                            <button type="submit" class="btn btn-primary mb-3">Conectare</button>
                            <span class="text-center">---SAU---</span>
                            <a href="/Inregistrare/inregistrare.php" class="btn btn-primary mt-3">Înscrie-te</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="/Autentificare/autentificare.js"></script>
</body>
</html>