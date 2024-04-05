<?php

session_start();

if (isset ($_SESSION) && !empty ($_SESSION) && isset ($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) == "XMLHTTPREQUEST") {
    require_once './../models/Database.php';
    require_once './../models/User.php';

    $databaseInstance = Database::getInstance();
    $db = $databaseInstance->getConnection();
    $userInstance = User::getInstance($databaseInstance);
    $expediteurId = $_SESSION['user_id'];
    $userData = $userInstance->getUserById($expediteurId);
    if (isset ($_POST['refresh'])) {
        $idRecipientRefresh = $_POST['idDestinataire'];
        $hashMD5Id2 = 0;
        $b = true;
        while ($b === true) {
            if ($idRecipientRefresh === md5($hashMD5Id2)) {
                $idRecipientRefresh = $hashMD5Id2;
                $b = false;
            }
            $hashMD5Id2++;
        }
        try {
            $conversation = $db->prepare('SELECT * FROM `users_messages` WHERE ((sender_id = ? ||  sender_id = ?)& (recipient_id = ?|| recipient_id = ?));');
            $conversation->execute((array($expediteurId, $idRecipientRefresh, $expediteurId, $idRecipientRefresh)));
            $conversation = $conversation->fetchAll(PDO::FETCH_CLASS);
            $conversationArray = [];
            if (count($conversation) != 0) {
                for ($i = 0; $i < count($conversation); $i++) {
                    $tempArray = [];
                    array_push($tempArray, $conversation[$i]->messages, $conversation[$i]->created_at, $conversation[$i]->sender_id);
                    $conversationArray[$i] = $tempArray;
                }
                
                try {
                    for ($i = 0; $i < count($conversationArray); $i++) {
                        $id = $conversationArray[$i][2];
                        $username = $db->query("SELECT username FROM users WHERE id = $id");
                        $username = $username->fetchAll(PDO::FETCH_CLASS);
                        $conversationArray[$i][2] = $username[0]->username;
                    }
                } catch (PDOException $e) {
                    callBackRefresh($e->getMessage());
                }

                callBackRefresh($conversationArray);
            } else {
                error("Vous n'avez pas de message");
            }

        } catch (PDOException $e) {
            error($e->getMessage());
        }
    } else if (isset ($_POST['userData']) && !empty ($_POST['userData']) && isset ($_POST['idDestinataire']) && !empty ($_POST['idDestinataire'])) {
        $idRecipient = $_POST['idDestinataire'];
        $hashMD5Id = 0;
        $a = true;
        while ($a === true) {
            if ($idRecipient === md5($hashMD5Id)) {
                $idRecipient = $hashMD5Id;
                $a = false;
            }
            $hashMD5Id++;
        }
        try {
            $dataRecipient = $db->query("SELECT username FROM users WHERE id = $idRecipient");
            $dataRecipient = $dataRecipient->fetchAll(PDO::FETCH_CLASS);
            $dataRecipient = $dataRecipient[0]->username;
            callBackDataUsers($userData, $dataRecipient);
        } catch (PDOException $e) {
            error('error');
        }
    }
} else {

}

function callBackRefresh($result = null, )
{
    header('Content-Type: application/json');
    $response = [
        'result' => $result,
    ];
    echo json_encode($response);
};

function callBackDataUsers($resultUserSession = null, $resultUserRecipient = null)
{
    header('Content-Type: application/json');
    $response = [
        'resultDataUserSession' => $resultUserSession,
        'resultDataUserRecipient' => $resultUserRecipient,
    ];
    echo json_encode($response);
};

function error($error)
{
    header('Content-Type: application/json');
    $response = [
        'error' => $error,
    ];
    echo json_encode($response);
};