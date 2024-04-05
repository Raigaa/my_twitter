<?php
session_start();

require_once './../models/Database.php';
require_once './../models/User.php';

$databaseInstance = Database::getInstance();
$userInstance = User::getInstance($databaseInstance);


if (isset ($_SESSION) && !empty ($_SESSION) && isset ($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) == "XMLHTTPREQUEST") {

    $idUserLog = $_SESSION['user_id'];
    $id_destinataire = $_POST['idDestinataire'];
    $message = htmlentities($_POST['message']);
    $expediteurId = $_SESSION['user_id'];

    $db = $databaseInstance->getConnection();
    $userData = $userInstance->getUserById($expediteurId);
    
    $hashMD5Id = 0;
    $a = true;
    while ($a === true) {
        if ($id_destinataire === md5($hashMD5Id)) {
            $id_destinataire = $hashMD5Id;
            $a = false;
        }
        $hashMD5Id++;
    }

    try {
        $insertQuery = $db->query("SELECT id FROM users_messages ORDER BY id DESC LIMIT 1");
        $insertQuery = $insertQuery->fetchAll(PDO::FETCH_CLASS);
        $ResultIdLast = $insertQuery[0]->id + 1;

    } catch (PDOException $e) {
        CallBackStatementInsertion(array($idUserLog, $id_destinataire, $message));
    }

    try {
        $insertQuery = $db->prepare("INSERT INTO users_messages (id, sender_id, recipient_id, messages) VALUES (:id, :idExpediteur, :idDestinataire, :messages)");
        $insertQuery->execute(array(':id' => $ResultIdLast, ':idExpediteur' => $idUserLog, ':idDestinataire' => $id_destinataire, ':messages' => $message));
        CallBackStatementInsertion('Votre message a bien été envoyé');
    } catch (PDOException $e) {
        CallBackStatementInsertion(array($idUserLog, $id_destinataire, $message));
    }

}

function CallBackStatementInsertion($statement)
{
    header('Content-Type: application/json');
    $response = [
        'statement' => $statement,
    ];
    echo json_encode($response);
}
;