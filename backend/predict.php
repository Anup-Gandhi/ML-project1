<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"), true);
$id1 = $data['id1'];
$id2 = $data['id2'];

if (!$id1 || !$id2) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

// Load CSV file and get features
$csvFile = "drugbank_with_descriptors.csv";
if (!file_exists($csvFile)) {
    echo json_encode(["error" => "CSV file not found"]);
    exit;
}

$csv = array_map('str_getcsv', file($csvFile));
$headers = array_shift($csv);

$features = [];
foreach ($csv as $row) {
    $rowAssoc = array_combine($headers, $row);
    if ($rowAssoc["ID"] == $id1) {
        $features["X1"] = $rowAssoc;
    }
    if ($rowAssoc["ID"] == $id2) {
        $features["X2"] = $rowAssoc;
    }
}

if (!isset($features["X1"]) || !isset($features["X2"])) {
    echo json_encode(["error" => "One or both DrugBank IDs not found"]);
    exit;
}

$modelFile = "best_random_forest.pkl";
if (!file_exists($modelFile)) {
    echo json_encode(["error" => "Model file not found"]);
    exit;
}

// Prepare features for Python script
$inputFeatures = [
    "MolWt_X1" => $features["X1"]["MolWt"],
    "LogP_X1" => $features["X1"]["LogP"],
    "NumHDonors_X1" => $features["X1"]["NumHDonors"],
    "NumHAcceptors_X1" => $features["X1"]["NumHAcceptors"],
    "TPSA_X1" => $features["X1"]["TPSA"],
    "MolWt_X2" => $features["X2"]["MolWt"],
    "LogP_X2" => $features["X2"]["LogP"],
    "NumHDonors_X2" => $features["X2"]["NumHDonors"],
    "NumHAcceptors_X2" => $features["X2"]["NumHAcceptors"],
    "TPSA_X2" => $features["X2"]["TPSA"]
];

// Pass features to Python for prediction
$command = escapeshellcmd("python3 predict.py '" . json_encode($inputFeatures) . "'");
$prediction = shell_exec($command);

echo json_encode(["prediction" => trim($prediction)]);
?>
