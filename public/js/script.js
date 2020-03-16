$(function () {
    $('#tab-login-button').on('click', function () {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('#tab-login').addClass('active');
        history.pushState({'reg': 1}, null, '/login.php');
    });
    $('#tab-register-button').on('click', function () {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('#tab-register').addClass('active');
        history.pushState({'reg': 1}, null, '/login.php?reg=1');
    });

    $('input').on('click input', function () {
        $(this).closest('.form-row').removeClass('has-error');
    });

    $('#tab-login form').on('submit', function () {
        var hasError = false,
            $emailField = $('#login-form-email'),
            $passwordField = $('#login-form-password');

        if (!validateEmail($emailField.val())) {
            $emailField.closest('.form-row').addClass('has-error');
            hasError = true;
        }

        if (String($passwordField.val()).trim().length < 6) {
            $passwordField.closest('.form-row').addClass('has-error');
            hasError = true;
        }

        return !hasError;
    });

    $('#tab-register form').on('submit', function () {
        var hasError = false,
            $emailField = $('#register-form-email'),
            $nameField = $('#register-form-name'),
            $photoField = $('#register-form-photo'),
            $passwordField = $('#register-form-password'),
            $passwordRepeatField = $('#register-form-password-repeat');

        if (!validateEmail($emailField.val())) {
            $emailField.closest('.form-row').addClass('has-error');
            hasError = true;
        }

        if (String($nameField.val()).trim().length < 2) {
            $nameField.closest('.form-row').addClass('has-error');
            hasError = true;
        }

        if ($photoField.val()) {
            var photoExt = $photoField.val().split('.').pop().toLowerCase();
            if($.inArray(photoExt, ['gif','png','jpg','jpeg']) == -1 || $photoField[0].files[0].size > 8 * 1024 * 1024) {
                $photoField.closest('.form-row').addClass('has-error');
                hasError = true;
            }
        }

        if (String($passwordField.val()).trim().length < 6) {
            $passwordField.closest('.form-row').addClass('has-error');
            hasError = true;
        }

        if (String($passwordRepeatField.val()).trim() !== String($passwordRepeatField.val()).trim()) {
            $passwordRepeatField.closest('.form-row').addClass('has-error');
            hasError = true;
        }

        return !hasError;
    });
});

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}