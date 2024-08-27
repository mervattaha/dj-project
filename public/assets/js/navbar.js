// navbar.js

document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.querySelector('.navbar-nav .dropdown-toggle');
    const dropdownMenu = document.querySelector('.navbar-nav .dropdown-menu');

    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });
    }
});
document.addEventListener("DOMContentLoaded", function() {
    var navbarHeight = document.querySelector(".navbar").offsetHeight;
    document.body.style.paddingTop = navbarHeight + "px";
});
