<?php
session_start();
$message = ''; // Mesaj de succes
$error = ''; // Variabilă pentru a stoca mesajele de eroare

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../db_connect.php'; // Include your database connection file

    // Colectează datele de la formular și le curăță
    $nume = mysqli_real_escape_string($conn, trim($_POST['nume']));
    $prenume = mysqli_real_escape_string($conn, trim($_POST['prenume']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $telefon = mysqli_real_escape_string($conn, trim($_POST['telefon']));
    $parola = mysqli_real_escape_string($conn, trim($_POST['parola']));
    $confirmare_parola = mysqli_real_escape_string($conn, trim($_POST['confirmare_parola']));

    // Verifică dacă toate câmpurile sunt completate
    if (empty($nume) || empty($prenume) || empty($username) || empty($email) || empty($telefon) || empty($parola) || empty($confirmare_parola)) {
        $error = 'Toate câmpurile sunt obligatorii.';
    } elseif ($parola != $confirmare_parola) {
        $error = 'Parolele nu corespund.';
    } else {
        // Verifică dacă numele de utilizator există deja în baza de date
        $sql = "SELECT * FROM Utilizatori WHERE NumeUtilizator = '$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $error = 'Nume de utilizator deja existent.';
        } else {
            // Înserează noul utilizator în baza de date
            $hashed_password = password_hash($parola, PASSWORD_DEFAULT);
            $insertSql = "INSERT INTO Utilizatori (Nume, Prenume, NumeUtilizator, Email, Telefon, Parola) VALUES ('$nume', '$prenume', '$username', '$email', '$telefon', '$hashed_password')";
            if ($conn->query($insertSql) === TRUE) {
                $message = 'Datele au fost salvate cu succes. Redirecționare...';
                header("refresh:3;url=/Autentificare/autentificare.php"); // Redirect după 3 secunde
            } else {
                $error = 'A apărut o eroare la înregistrare.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inregistrare</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Inregistrare/inregistrare.css">
</head>
<body>
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-lg-7 col-md-7 col-sm-6 p-0">
                <img src="/Imagini/RouteRoverCover.jpg" class="img-fluid img-full-height" alt="Route Rover Cover">
            </div>
            <div class="col-lg-5 col-md-5 col-sm-6 d-flex">
                <div class="full-height-form w-100">
                    <form method="POST">
                        <!-- Locul pentru mesaj de succes -->
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-success text-center" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <!-- Locul pentru erori -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <input type="text" class="form-control" name="nume" autocomplete="off" placeholder="Nume">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="prenume" autocomplete="off" placeholder="Prenume">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" autocomplete="off" placeholder="Nume de utilizator">
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" autocomplete="off" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="telefon" autocomplete="off" placeholder="Nr de telefon">
                        </div>
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="parola" placeholder="Parola" id="parola">
                            <i toggle="#parola" class="fas fa-fw fa-eye-slash field-icon toggle-password" style="color: #495057;"></i>
                        </div>
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="confirmare_parola" placeholder="Confirmare parolă" id="confirmare_parola">
                            <i toggle="#confirmare_parola" class="fas fa-fw fa-eye-slash field-icon toggle-password" style="color: #495057;"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-primary mb-3">Înainte</button>
                            <span class="text-center">---SAU---</span>
                            <a href="/Autentificare/autentificare.php" class="btn btn-primary mt-3">Ai deja un cont? Conectează-te</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="/Inregistrare/inregistrare.js"></script>
</body>
</html>
