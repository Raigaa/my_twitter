document.addEventListener("DOMContentLoaded", function () {
  var navbar = document.createElement("nav");
  navbar.innerHTML = `
  <nav-bar class="bg-white shadow">
  <ul class="flex items-center justify-between p-5">
    <div class="flex items-center">
      <label for="logo" class="font-bold text-xl mr-3">BARK</label>
      <img id="logo" src="../public/images/dog-placeholder-logo.png" class="h-10 w-10">
    </div>
    <li><a href="../view/user-profile.html" class="text-blue-500 hover:text-blue-800">Profile</a></li>
    <li><a href="../view/timeline.html" class="text-blue-500 hover:text-blue-800">Timeline</a></li>
    <li><a href="../view/messagerie-privée.php" class="text-blue-500 hover:text-blue-800">Messages</a></li>
  </ul>
</nav-bar>
  `;
  document.body.prepend(navbar);
})