<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to the database
$conn = new mysqli("localhost", "root", "", "tp_sr");

// Handle the different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

// Get the resource requested
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

$resource = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);

// Initialize the response array
$response = array();

// functions for "taches"
function getAllTaches()
{
    $result = $GLOBALS['conn']->query("SELECT * FROM tache");
    while ($row = $result->fetch_assoc()) {
        $GLOBALS['response'][] = $row;
    }
}
function getTache($id)
{
    $result = $GLOBALS['conn']->query("SELECT * FROM tache WHERE id = $id");
    $GLOBALS['response'] = $result->fetch_assoc();
}
function ajouterTache()
{
    $description = $GLOBALS['input']['description'];
    $id_employe = $GLOBALS['input']['id_employe'];
    $result = $GLOBALS['conn']->query("INSERT INTO tache (description, id_employe) VALUES ('$description', '$id_employe')");
    if ($result) {
        $GLOBALS['response'] = array("message" => "Item added successfully.");
        http_response_code(201);
    } else {
        $GLOBALS['response'] = array("error" => "Failed to add item.");
        http_response_code(400);
    }
}
function editerTache($id)
{
    $description = $GLOBALS['input']['description'];
    $id_employe = $GLOBALS['input']['id_employe'];
    $result = $GLOBALS['conn']->query("UPDATE tache SET description = '$description', id_employe = '$id_employe' WHERE id = $id");
    if ($result) {
        $GLOBALS['response'] = array("message" => "Item updated successfully.");
        http_response_code(200);
    } else {
        $GLOBALS['response'] = array("error" => "Failed to update item.");
        http_response_code(400);
    }
}
function supprimerTache($id)
{
    $result = $GLOBALS['conn']->query("DELETE FROM tache WHERE id = $id");
    if ($result) {
        $GLOBALS['response'] = array("message" => "Item deleted successfully.");
        http_response_code(200);
    } else {
        $GLOBALS['response'] = array("error" => "Failed to delete item.");
        http_response_code(400);
    }
}

// functions for "employes"
function getAllEmployes()
{
    $result = $GLOBALS['conn']->query("SELECT * FROM employe");
    while ($row = $result->fetch_assoc()) {
        $GLOBALS['response'][] = $row;
    }
}
function getEmploye($id)
{
    $result = $GLOBALS['conn']->query("SELECT * FROM employe WHERE id = $id");
    $GLOBALS['response'] = $result->fetch_assoc();
}
function ajouterEmploye()
{
    $nom = $GLOBALS['input']['nom'];
    $prenom = $GLOBALS['input']['prenom'];
    $adresse = $GLOBALS['input']['adresse'];
    $num_compte = $GLOBALS['input']['num_compte'];
    $grade = $GLOBALS['input']['grade'];
    $superieur = $GLOBALS['input']['superieur'];
    $result = null;
    if ($superieur) {
        $result = $GLOBALS['conn']->query("INSERT INTO employe (nom, prenom, adresse, num_compte, grade, superieur) VALUES ('$nom', '$prenom', '$adresse', '$num_compte', '$grade', '$superieur')");
    } else {
        $result = $GLOBALS['conn']->query("INSERT INTO employe (nom, prenom, adresse, num_compte, grade) VALUES ('$nom', '$prenom', '$adresse', '$num_compte', '$grade')");
    }
    if ($result) {
        $GLOBALS['response'] = array("message" => "Item added successfully.");
        http_response_code(201);
    } else {
        $GLOBALS['response'] = array("error" => "Failed to add item.");
        http_response_code(400);
    }
}
function editerEmploye($id)
{
    $nom = $GLOBALS['input']['nom'];
    $prenom = $GLOBALS['input']['prenom'];
    $adresse = $GLOBALS['input']['adresse'];
    $num_compte = $GLOBALS['input']['num_compte'];
    $grade = $GLOBALS['input']['grade'];
    $superieur = $GLOBALS['input']['superieur'];
    $result = $GLOBALS['conn']->query("UPDATE employe SET nom = '$nom', prenom = '$prenom', adresse='$adresse', num_compte='$num_compte', grade='$grade', superieur='$superieur' WHERE id = $id");
    if ($result) {
        $GLOBALS['response'] = array("message" => "Item updated successfully.");
        http_response_code(200);
    } else {
        $GLOBALS['response'] = array("error" => "Failed to update item.");
        http_response_code(400);
    }
}
function supprimerEmploye($id)
{
    $result = $GLOBALS['conn']->query("DELETE FROM employe WHERE id = $id");
    if ($result) {
        $GLOBALS['response'] = array("message" => "Item deleted successfully.");
        http_response_code(200);
    } else {
        $GLOBALS['response'] = array("error" => "Failed to delete item.");
        http_response_code(400);
    }
}

// Handle the different methods
switch ($method) {
    case 'GET':
        // SELECT
        if ($resource == "taches") {
            $id = array_shift($request);
            if (empty($id)) {
                getAllTaches();
            } else {
                getTache($id);
            }
        } else if ($resource == "employes") {
            $id = array_shift($request);
            if (empty($id)) {
                getAllEmployes();
            } else {
                getEmploye($id);
            }
        }
        http_response_code(200);
        break;
    case 'PUT':
        // UPDATE
        if ($resource == "taches") {
            $id = array_shift($request);
            if (empty($id)) {
                http_response_code(400);
                $response = array("error" => "No id provided.");
            } else {
                editerTache($id);
            }
        } else if ($resource == "employes") {
            $id = array_shift($request);
            if (empty($id)) {
                http_response_code(400);
                $response = array("error" => "No id provided.");
            } else {
                editerEmploye($id);
            }
        }
        http_response_code(200);
        break;
    case 'POST':
        // INSERT
        if ($resource == "taches") {
            ajouterTache();
        } else if ($resource == "employes") {
            ajouterEmploye();
        }
        break;
    case 'DELETE':
        // DELETE
        if ($resource == "taches") {
            $id = array_shift($request);
            if (empty($id)) {
                http_response_code(400);
                $response = array("error" => "No id provided.");
            } else {
                supprimerTache($id);
            }
        } else if ($resource == "employes") {
            $id = array_shift($request);
            if (empty($id)) {
                http_response_code(400);
                $response = array("error" => "No id provided.");
            } else {
                supprimerEmploye($id);
            }
        }
        http_response_code(200);
        break;
}

// Return the response in JSON format
echo json_encode($response);
