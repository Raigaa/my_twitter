<?php
session_start();

if (isset($_SESSION['user_id']) && !empty($_SESSION)) {
    require_once './../models/Database.php';
    require_once './../models/User.php';
    if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] === 'deconnexion') {
        unset($_COOKIE['destinataireId']);
        setcookie('destinataireId', '', time() - 10);
        unset($_COOKIE['destinataireUsername']);
        setcookie('destinataireUsername', '', time() - 10);
    }

    $databaseInstance = Database::getInstance();
    $db = $databaseInstance->getConnection();
    $userInstance = User::getInstance($databaseInstance);

    if (isset($_POST['envoi_message'])) {
        if (isset($_POST['destinataire']) && !empty($_POST['destinataire'])) {
            $destinataire = htmlspecialchars($_POST['destinataire']);
            $id_user = $_SESSION['user_id'];
            $userData = $userInstance->getUserById($id_user);
            if ($destinataire != $userData['username']) {
                $destinataire = '%' . $destinataire . '%';
                $id_destinataire = $db->prepare('SELECT id, username FROM users WHERE username LIKE ? && ? != username');
                $id_destinataire->execute((array($destinataire, $userData['username'])));
                $id_destinataire = $id_destinataire->fetchAll(PDO::FETCH_CLASS);
                if (empty($id_destinataire)) {
                    $errorDestinataire = "Recipient not found.";
                }
            } else {
                $errorAutoAppellation = 'You cannot message yourself!';
            }
        } else {
            $error = "Please enter a username!";
        }
    }
    ?>

    <!DOCTYPE html>

    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Private messaging</title>
</head>

<body class="bg-gray-100 text-gray-800">
    <header class="p-4 bg-blue-500 text-white">
    <button onclick="window.location.href='timeline.html'" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Back</button>

<script>
function goBack() {
    window.history.back();
}
</script>
        <form method="POST" class="space-y-4">
            <label class="font-bold">Research followers</label>
            <input name="destinataire" type="text" placeholder="Enter the username here!" class="border p-2 rounded w-full text-black" />
            <div>
                <input type="submit" value="Search" name="envoi_message" class="mt-2 bg-blue-500 text-white p-2 rounded">
            </div>
            <?php if (isset($error)) {
                echo '<span class="text-red-500">' . $error . '</span>';
            } ?>
            <?php if (isset($errorDestinataire)) {
                echo '<span class="text-red-500">' . $errorDestinataire . '</span>';
            } ?>
            <?php if (isset($errorAutoAppellation)) {
                echo '<span class="text-red-500">' . $errorAutoAppellation . '</span>';
            } ?>
        </form>
    </header>
    <main class="p-4">
        <?php if (isset($id_destinataire) && !empty($id_destinataire[0]->id)) {
            for ($i = 0; $i < count($id_destinataire); $i++) {
                $valueId = md5($id_destinataire[$i]->id);
                $valueUsername = md5($id_destinataire[$i]->username);
                ?>
                <a href="<?= "ChatBox.html?id=$valueId&username=$valueUsername" ?>" class="underline text-blue-500">
                    <?= $id_destinataire[$i]->username ?>
                </a>
            <?php }
        } ?>
    </main>
</body>

    </html>
<?php } else {
    echo "You are not authorised to access this page.";
    header('Location:./../view/login.html');
} ?>