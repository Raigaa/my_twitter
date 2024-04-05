//Récupération des données de l'utilisateur pour les afficher
document.addEventListener("DOMContentLoaded", function () {
  fetch("../../controller/ProfileData.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      $("#email").val(data.email);
      $("#username").val(data.username);
      $("#firstname").val(data.firstname);
      $("#lastname").val(data.lastname);
      $("#dob").val(data.birthdate);

      $("#submit-private-form").click(function (event) {
        event.preventDefault();
        const birthdate_info = document.getElementById("birthdate");
        const mail_info = document.getElementById("mail_info");

        let email = $("#email").val();
        let username = $("#username").val();
        let firstname = $("#firstname").val();
        let lastname = $("#lastname").val();
        let birthdate = $("#dob").val();

        let formPrivateData = {
          id: data.id,
          username: username,
          email: email,
          firstname: firstname,
          lastname: lastname,
          dob: birthdate,
        };


        //verif lastname
        let regexLastname = /^[a-z]*$/gmi;
        if (regexLastname.test(lastname) === true && lastname.length < 30 && lastname != "") {
          statementLastName = 1;
          formPrivateData.lastname = lastname;
        } else {
          formPrivateData.lastname = data.lastname;
        }
        //.
         
        //verif firstname
        let regexFirstName = /^[a-z]*$/gmi;
        if (regexFirstName.test(firstname) === true && firstname.length < 30 && firstname != "") {
          statementFirstName = 1;
          formPrivateData.firstname = firstname;
        } else {
          formPrivateData.firstname = data.firstname;
        }
        //.

        //verif username
        let regexUserName = /^[a-z]*$/gmi;
        if (regexUserName.test(username) === true && username.length < 30 && username != "") {
          statementUserName = 1
          formPrivateData.username = username;
        } else {
          formPrivateData.username = data.username;
        }
        //.

        //Vérification birthdate
        
        let date = birthdate.split('-');
        let date_final = [];

        date.forEach(function (value, index) {
          date_final.push(parseInt(value));
        });

        let Date_Naissance_Int = new Date(date_final[0], date_final[1], date_final[2]);

        let Date_15_ans = new Date(Date_Naissance_Int.getTime() + 15 * 365 * 24 * 60 * 60 * 1000);

        let Date_150_ans = new Date(Date_Naissance_Int.getTime() + 150 * 365 * 24 * 60 * 60 * 1000);

        new Date() >= Date_15_ans && new Date() <= Date_150_ans ? statementBirthDate = 1 : statementBirthDate = null;

        if (statementBirthDate === 1) {
          formPrivateData.dob = birthdate;
          birthdate_info.innerHTML = ""
        } else if (birthdate != "" && statementBirthDate === null) {
          formPrivateData.dob = data.birthdate;
          birthdate_info.innerHTML = `Vous devez avoir au minimum 15 ans et maximum 150 ans`
          birthdate_info.style.fontSize = "12px";
          birthdate_info.style.color = "red";
          birthdate_info.style.textDecoration = "underline";
        }

        //15 ans min 
        //150 ans
        //.

        //Verif Mail
        let regexMAil = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        if (email === null || email === "") {
          formPrivateData.email = data.email
          mail_info.innerHTML = "Veuillez remplir ce champs";
          mail_info.style.fontSize = "12px";
          mail_info.style.color = "red";
          mail_info.style.textDecoration = "underline";
        } else {
          mail_info.innerHTML = "";
        }

        if (regexMAil.test(email) === true) {
          statementMail = 1;
          formPrivateData.email = email;
          mail_info.innerHTML = "";
        } else if (regexMAil.test(email) === false && email != "" && email != null) {
          formPrivateData.email = data.email;
          mail_info.innerHTML = "Adresse email invalide !";
          mail_info.style.fontSize = "12px";
          mail_info.style.color = "red";
          mail_info.style.textDecoration = "underline";
        }
        //.

        $.ajax({
          type: "POST",
          url: "../../controller/UpdatePrivateData.php",
          data: { formPrivateData: formPrivateData },
          dataType: "json",
          success: function (response) {
            requestDataUser();
            if (response.status === true || response.status === "true") {
              alert(response.message);
            } else {
              alert(response.message);
            }
          },
          error: function (xhr, status, error) {
            requestDataUser();;
            alert("An error occurred while updating the personal data.");
          },
        });
      });
    })
    .catch((error) => {
      console.error("Error fetching profile data:", error);
    });
});

function requestDataUser () {
  fetch("../../controller/ProfileData.php")
  .then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    return response.json();
  })
  .then((data) => {
    $("#email").val(data.email);
    $("#username").val(data.username);
    $("#firstname").val(data.firstname);
    $("#lastname").val(data.lastname);
    $("#dob").val(data.birthdate);
  });

}