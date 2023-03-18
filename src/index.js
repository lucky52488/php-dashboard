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
    console.log(pid)
    // $(".alertbox", this).hide()
})