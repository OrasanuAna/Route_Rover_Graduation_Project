<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <link href="/Rapoarte/genereaza_raport.css" rel="stylesheet">
        <title>Generează un raport</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/MeniuPrincipal/meniu_principal.php">
                <img src="/Imagini/Logo.png" class="navbar-logo" alt="Logo" style="max-height: 50px;">
                Route Rover
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto custom-navbar">
                    <li class="nav-item">
                        <a class="nav-link" href="/MeniuPrincipal/meniu_principal.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Profil/profil.php"><i class="fas fa-user"></i> Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Soferi/soferi.php"><i class="fas fa-users"></i> Șoferi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Vehicule/vehicule.php"><i class="fas fa-truck"></i> Vehicule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Documente/documente.php"><i class="fas fa-file-alt"></i> Documente</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Contracte/contracte.php"><i class="fas fa-file-contract"></i> Contracte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Sarcini/adauga_task.php"><i class="fas fa-tasks"></i> Task nou</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container my-5">
            <h1 class="text-center">Generează un raport</h1>
            <div class="row">
                <div class="col-md-3 mb-4 radio-group">
                    <h2>Selectează tabelul:</h2>
                    <form id="reportForm">
                        <div class="form-group">
                            <div>
                                <input type="radio" id="soferi" name="tabel" value="Soferi">
                                <label for="soferi">Șoferi</label>
                            </div>
                            <div>
                                <input type="radio" id="vehicule" name="tabel" value="Vehicule">
                                <label for="vehicule">Vehicule</label>
                            </div>
                            <div>
                                <input type="radio" id="documente" name="tabel" value="Documente">
                                <label for="documente">Documente</label>
                            </div>
                            <div>
                                <input type="radio" id="contracte" name="tabel" value="Contracte">
                                <label for="contracte">Contracte</label>
                            </div>
                            <div>
                                <input type="radio" id="sarcini" name="tabel" value="Sarcini">
                                <label for="sarcini">Sarcini</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-9 mb-4 filter-order-group">
                    <div class="row">
                        <div class="col-md-4 filter">
                            <h2>Filtrează</h2>
                            <div id="fieldSelection" class="form-group">
                                <!-- Checkbox-urile pentru câmpuri vor fi generate dinamic -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h2>Ordonează</h2>
                            <div class="form-group">
                                <div>
                                    <input type="radio" id="ascending" name="ordoneaza" value="Crescător">
                                    <label for="ascending">Crescător</label>
                                </div>
                                <div>
                                    <input type="radio" id="descending" name="ordoneaza" value="Descrescător">
                                    <label for="descending">Descrescător</label>
                                </div>
                                <div>
                                    <input type="radio" id="alphabetic" name="ordoneaza" value="Alfabetic">
                                    <label for="alphabetic">Alfabetic</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn custom-btn float-right">Generează raport</button>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script>
            // Obținerea elementelor necesare din DOM
            const reportForm = document.getElementById('reportForm');
            const fieldSelection = document.getElementById('fieldSelection');
            
            // Obiect pentru a mapa tabelele la câmpurile lor
            const tableFields = {
                'Soferi': ['Nume', 'Prenume', 'DataNasterii', 'DataAngajarii', 'Telefon', 'Email'],
                'Vehicule': ['NumarInmatriculare', 'MarcaModel', 'AnFabricatie', 'Culoare', 'TipCombustibil'],
                'Documente': ['NumeDocument', 'TipDocument', 'DataIncarcareDocument'],
                'Contracte': ['NumeContract', 'TipContract', 'DataInceputContract', 'DataSfarsitContract'],
                'Sarcini': ['NumeSarcina', 'DescriereSarcina', 'TermenLimitaSarcina']
            };

            // Funcție pentru a actualiza opțiunile de filtrare
            function updateFieldSelection(table) {
                // Șterge câmpurile curente
                fieldSelection.innerHTML = '';
                
                // Adaugă noi câmpuri bazate pe tabelul selectat
                tableFields[table].forEach(function(field) {
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.className = 'form-check-input';
                    input.id = field;
                    input.name = 'fields[]';
                    input.value = field;
                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = field;
                    label.textContent = field;
                    div.appendChild(input);
                    div.appendChild(label);
                    fieldSelection.appendChild(div);
                });
            }

            // Eveniment pentru schimbarea selecției tabelului
            reportForm.addEventListener('change', function(event) {
                if (event.target.name === 'tabel') {
                    updateFieldSelection(event.target.value);
                }
            });
        </script>

    </body>
</html>