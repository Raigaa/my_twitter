document.addEventListener('DOMContentLoaded', (e) => {
    window.userData;

    let params = new URLSearchParams(window.location.search);
    window.idDestinataire = params.get('id');
    let usernameDestinataire = params.get('username');

    if (window.idDestinataire) {
        requestAjaxUserData(idDestinataire);
        setInterval(requestAjaxRefresh, 100);
    } else {
        Redirection();
    }


    document.getElementById('submit_mess').addEventListener('click', (e) => {
        e.preventDefault();

        valueMessage = document.getElementById('input_message').value;
        document.getElementById('input_message').value = "";
        let info = document.getElementById('info');
        if (valueMessage.length > 0) {
            info.innerHTML = '';
            requestInsertMessage(valueMessage, idDestinataire);
        } else {
            info.innerHTML = 'Veuillez entrer au minimum un caractère';
        }
    });
});

function requestAjaxRefresh() {
    let chatbox = document.getElementById('ChatBox');

    let Datas = new FormData();
    Datas.append("refresh", true);
    Datas.append("idDestinataire", window.idDestinataire)
    $.ajax({
        type: "POST",
        url: "../../controller/ChatBoxRefresh.php",
        data: Datas,
        dataType: "json",
        timeout: 120000,
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            chatbox.innerHTML = "";
            if ( response.error !="Vous n'avez pas de message") {
                for (let i = 0; i < response.result.length; i++) {
                    let li = document.createElement('li');
                    li.innerHTML = response.result[i][2] + ': ' + response.result[i][0];
                    if (response.result[i][2] === userData.username) {
                        li.classList.add('expediteur', 'bg-blue-500', 'mt-2', 'list-none', 'p-4', 'text-white', 'rounded', 'ml-8');
                        chatbox.appendChild(li);
                    } else {
                        li.classList.add('destinataire', 'bg-green-500', 'mt-2', 'list-none', 'mr-8', 'p-4', 'text-white', 'rounded');
                        chatbox.appendChild(li);
                    }
                }
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            alert("An error occurred during registration.");
        }
    });
}
function requestAjaxUserData(idDestinataire) {
    let Datas = new FormData();
    Datas.append("userData", true);
    Datas.append("idDestinataire", idDestinataire)
    $.ajax({
        type: "POST",
        url: "../../controller/ChatBoxRefresh.php",
        data: Datas,
        dataType: "json",
        timeout: 120000,
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            userData = response.resultDataUserSession;
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            alert("An error occurred during registration. userdata");
        }
    });

}

function requestInsertMessage(valueMessage, idDestinataire) {
    let Datas = new FormData();
    Datas.append("userInsert", true);
    Datas.append("message", valueMessage);
    Datas.append("idDestinataire", idDestinataire);
    $.ajax({
        type: "POST",
        url: "../../controller/ChatBoxInsert.php",
        data: Datas,
        dataType: "json",
        timeout: 120000,
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {

        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            alert("An error occurred during registration.");
        }
    });

}

function Redirection() {
    document.location.href = "./messagerie-privée.php";
}