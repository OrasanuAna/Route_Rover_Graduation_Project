<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafice</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="/Grafice/grafice.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1900px;
        }
        .chart-container {
            margin-bottom: 50px;
        }
        canvas {
            max-width: 100% !important;
            height: 400px !important;
            margin: 0 auto;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    </style>
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
                    <a class="nav-link" href="/Rapoarte/genereaza_raport.php"><i class="fas fa-chart-bar"></i> Rapoarte</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="/Grafice/grafice.php"><i class="fas fa-chart-pie"></i> Grafice</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link no-hover-effect" style="padding-top: 12px;" href="#" id="themeToggle"><i class="fas fa-sun"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-5">Grafice</h1>
        <div class="row">
            <div class="col-md-6 chart-container">
                <h5 class="text-center">Numărul de șoferi angajați de-a lungul timpului</h5>
                <canvas id="soferiAngajatiChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h5 class="text-center">Distribuția tipurilor de vehicule în funcție de combustibil</h5>
                <canvas id="tipuriVehiculeChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h5 class="text-center">Culoare vehicul</h5>
                <canvas id="culoareVehiculChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h5 class="text-center">An fabricație vehicul</h5>
                <canvas id="anFabricatieVehiculChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h5 class="text-center">Statusul ITP și asigurarea pentru vehicule</h5>
                <canvas id="statusITPAsigurareChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h5 class="text-center">Marca și model vehicul</h5>
                <canvas id="marcaModelVehiculChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h5 class="text-center">Numărul de documente încărcate lunar</h5>
                <canvas id="documenteIncarcateChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h5 class="text-center">Durata contractelor</h5>
                <canvas id="durataContracteChart"></canvas>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        // Schimbă tema la clic pe iconiță
        document.getElementById('themeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const themeIcon = this.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'dark');
            } else {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'light');
            }
        });

        // Setează tema inițială în funcție de preferința stocată
        window.addEventListener('DOMContentLoaded', () => {
            const storedTheme = localStorage.getItem('theme') || 'light';
            if (storedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                document.getElementById('themeToggle').querySelector('i').classList.add('fa-moon');
                document.getElementById('themeToggle').querySelector('i').classList.remove('fa-sun');
            }
        });
    </script>

    <!-- CSS pentru tema dark mode -->
    <style>
        .dark-mode {
            background-color: #1A2733;
            color: white;
        }

        .dark-mode .navbar {
            background-color: #0A0F19 !important;
        }

        .dark-mode .navbar-light .navbar-brand,
        .dark-mode .navbar-light .navbar-nav .nav-link {
            color: white !important;
        }

        .dark-mode .navbar-light .navbar-nav .nav-item .nav-link:not(.no-hover-effect):hover::after,
        .dark-mode .navbar-light .navbar-nav .nav-item .nav-link:not(.no-hover-effect):focus::after {
            background-color: #fff;
        }

        .dark-mode .navbar-light .navbar-nav .nav-item .nav-link:not(.no-hover-effect)::after {
            background-color: #fff; 
        }

        .dark-mode .navbar-light .navbar-nav .nav-link:hover,
        .dark-mode .navbar-light .navbar-nav .nav-link:focus {
            color: #ddd;
        }

        .dark-mode .table {
            background-color: #1A2733;
            color: white;
        }

        .dark-mode .table th {
            background-color: #0A0F19;
            border: none;
        }

        .dark-mode .table td {
            background-color: #1A2733;
        }

        .dark-mode .btn.custom-btn {
            background-color: #0A0F19;
            border-color: #fff;
            color: white;
        }
        
        .dark-mode .btn.custom-btn:hover {
            background-color: #05060A;
            border-color: #fff;
        }

        .dark-mode .confirm-icon {
            color: #3BD16F;
        }

        .dark-mode .confirm-icon:hover {
            color: #006400;
        }
    </style>

    <script>
        // Functie pentru a obtine datele din PHP
        function fetchChartData() {
            return $.ajax({
                url: 'fetch_chart_data.php',
                method: 'GET',
                dataType: 'json'
            });
        }

        // Functie pentru a crea graficele
        function createCharts(data) {
            console.log(data);  // Adaugă acest log pentru a verifica datele preluate

            // Grafic numărul de șoferi angajați de-a lungul timpului
            new Chart(document.getElementById('soferiAngajatiChart'), {
                type: 'line',
                data: {
                    labels: data.soferi.labels,
                    datasets: [{
                        label: 'Număr de șoferi angajați',
                        data: data.soferi.data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Luna' } },
                        y: { title: { display: true, text: 'Număr de șoferi' } }
                    }
                }
            });

            // Grafic distribuția tipurilor de vehicule
            new Chart(document.getElementById('tipuriVehiculeChart'), {
                type: 'pie',
                data: {
                    labels: data.vehicule.labels,
                    datasets: [{
                        label: 'Tipuri de vehicule',
                        data: data.vehicule.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Grafic numărul de documente încărcate lunar
            new Chart(document.getElementById('documenteIncarcateChart'), {
                type: 'bar',
                data: {
                    labels: data.documente.labels,
                    datasets: [{
                        label: 'Număr de documente încărcate',
                        data: data.documente.data,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Luna' } },
                        y: { title: { display: true, text: 'Număr de documente' } }
                    }
                }
            });

            // Grafic statusul ITP și asigurare pentru vehicule
            new Chart(document.getElementById('statusITPAsigurareChart'), {
                type: 'bar',
                data: {
                    labels: data.itpAsigurare.labels,
                    datasets: [{
                        label: 'ITP Valabil',
                        data: data.itpAsigurare.itp,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Asigurare Valabilă',
                        data: data.itpAsigurare.asigurare,
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Vehicul' } },
                        y: { title: { display: true, text: 'Număr' } }
                    }
                }
            });

            // Grafic durata contractelor (Gantt chart)
            new Chart(document.getElementById('durataContracteChart'), {
                type: 'bar',
                data: {
                    labels: data.contracte.labels,
                    datasets: [{
                        label: 'Durata Contractelor',
                        data: data.contracte.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Contracte' } },
                        y: { title: { display: true, text: 'Durata (zile)' } }
                    }
                }
            });

            // Grafic an fabricație vehicul
            new Chart(document.getElementById('anFabricatieVehiculChart'), {
                type: 'bar',
                data: {
                    labels: data.anFabricatie.labels,
                    datasets: [{
                        label: 'Număr de vehicule',
                        data: data.anFabricatie.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'An fabricație' } },
                        y: { title: { display: true, text: 'Număr de vehicule' } }
                    }
                }
            });

            // Grafic culoare vehicul
            new Chart(document.getElementById('culoareVehiculChart'), {
                type: 'pie',
                data: {
                    labels: data.culoareVehicul.labels,
                    datasets: [{
                        label: 'Număr de vehicule',
                        data: data.culoareVehicul.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Grafic marca și model vehicul
            new Chart(document.getElementById('marcaModelVehiculChart'), {
                type: 'bar',
                data: {
                    labels: data.marcaModel.labels,
                    datasets: [{
                        label: 'Număr de vehicule',
                        data: data.marcaModel.data,
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Marca și Model' } },
                        y: { title: { display: true, text: 'Număr de vehicule' } }
                    }
                }
            });
        }

    
        // Obține datele și creează graficele
        fetchChartData().done(createCharts);
    </script>
</body>
</html>
