function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const showPasswordCheckbox = document.getElementById('show-password');
    if (showPasswordCheckbox.checked) {
        passwordInput.type = 'text';
    } else {
        passwordInput.type = 'password';
    }
}


$(".closealertbutton").click(function (e) {
    // $('.alertbox')[0].hide()
    // e.preventDefault();
    pid = $(this).parent().parent().hide(500)
    // $(".alertbox", this).hide()
})

// var closeButton = document.querySelectorAll('.closealertbutton');
// closeButton.forEach(element => {
//     element.addEventListener('click', function (e) {
//         var parent = this.parentElement.parentElement;
//         parent.style.transition = "all 500ms cubic-bezier(0.25, 0.46, 0.45, 0.94)";
//         parent.style.transform = "translateX(100%)";
//         setTimeout(function () {
//             parent.style.display = 'none';
//         }, 500);
//     });
// });