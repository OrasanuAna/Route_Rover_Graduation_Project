<?php

include '../db_connect.php';

// Initialize an empty array to store the data
$data = [];

// Numărul de șoferi angajați de-a lungul timpului
$soferiResult = $conn->query("SELECT DATE_FORMAT(DataAngajarii, '%Y-%m') AS luna, COUNT(*) AS numar FROM Soferi GROUP BY luna ORDER BY luna");
$soferiLabels = [];
$soferiData = [];
while ($row = $soferiResult->fetch_assoc()) {
    $soferiLabels[] = $row['luna'];
    $soferiData[] = $row['numar'];
}
$data['soferi'] = ['labels' => $soferiLabels, 'data' => $soferiData];

// Distribuția tipurilor de vehicule
$vehiculeResult = $conn->query("SELECT TipCombustibil, COUNT(*) AS numar FROM Vehicule GROUP BY TipCombustibil");
$vehiculeLabels = [];
$vehiculeData = [];
while ($row = $vehiculeResult->fetch_assoc()) {
    $vehiculeLabels[] = $row['TipCombustibil'];
    $vehiculeData[] = $row['numar'];
}
$data['vehicule'] = ['labels' => $vehiculeLabels, 'data' => $vehiculeData];

// Numărul de documente încărcate lunar
$documenteResult = $conn->query("SELECT DATE_FORMAT(DataIncarcareDocument, '%Y-%m') AS luna, COUNT(*) AS numar FROM Documente GROUP BY luna ORDER BY luna");
$documenteLabels = [];
$documenteData = [];
while ($row = $documenteResult->fetch_assoc()) {
    $documenteLabels[] = $row['luna'];
    $documenteData[] = $row['numar'];
}
$data['documente'] = ['labels' => $documenteLabels, 'data' => $documenteData];

// Statusul ITP și asigurare pentru vehicule
$itpResult = $conn->query("SELECT NumarInmatriculare, DataSfarsitITP >= CURDATE() AS itp_valabil, DataSfarsitAsigurare >= CURDATE() AS asigurare_valabila FROM Vehicule");
$itpLabels = [];
$itpData = [];
$asigurareData = [];
while ($row = $itpResult->fetch_assoc()) {
    $itpLabels[] = $row['NumarInmatriculare'];
    $itpData[] = $row['itp_valabil'];
    $asigurareData[] = $row['asigurare_valabila'];
}
$data['itpAsigurare'] = ['labels' => $itpLabels, 'itp' => $itpData, 'asigurare' => $asigurareData];

// Durata contractelor
$contracteResult = $conn->query("SELECT NumeContract, DATEDIFF(DataSfarsitContract, DataInceputContract) AS durata FROM Contracte");
$contracteLabels = [];
$contracteData = [];
while ($row = $contracteResult->fetch_assoc()) {
    $contracteLabels[] = $row['NumeContract'];
    $contracteData[] = $row['durata'];
}
$data['contracte'] = ['labels' => $contracteLabels, 'data' => $contracteData];

// Anul de fabricație al vehiculelor
$anFabricatieResult = $conn->query("SELECT AnFabricatie, COUNT(*) AS numar FROM Vehicule GROUP BY AnFabricatie ORDER BY AnFabricatie");
$anFabricatieLabels = [];
$anFabricatieData = [];
while ($row = $anFabricatieResult->fetch_assoc()) {
    $anFabricatieLabels[] = $row['AnFabricatie'];
    $anFabricatieData[] = $row['numar'];
}
$data['anFabricatie'] = ['labels' => $anFabricatieLabels, 'data' => $anFabricatieData];

// Culoare vehicul
$culoareVehiculResult = $conn->query("SELECT Culoare, COUNT(*) AS numar FROM Vehicule GROUP BY Culoare ORDER BY Culoare");
$culoareVehiculLabels = [];
$culoareVehiculData = [];
while ($row = $culoareVehiculResult->fetch_assoc()) {
    $culoareVehiculLabels[] = $row['Culoare'];
    $culoareVehiculData[] = $row['numar'];
}
$data['culoareVehicul'] = ['labels' => $culoareVehiculLabels, 'data' => $culoareVehiculData];

// Marca și model vehicul
$marcaModelResult = $conn->query("SELECT MarcaModel, COUNT(*) AS numar FROM Vehicule GROUP BY MarcaModel ORDER BY MarcaModel");
$marcaModelLabels = [];
$marcaModelData = [];
while ($row = $marcaModelResult->fetch_assoc()) {
    $marcaModelLabels[] = $row['MarcaModel'];
    $marcaModelData[] = $row['numar'];
}
$data['marcaModel'] = ['labels' => $marcaModelLabels, 'data' => $marcaModelData];

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);

?>
