(function () {
    'use strict';

    var modal = document.getElementById('auth-modal');
    if (!modal) return;

    var form = modal.querySelector('[data-auth-form]');
    var message = modal.querySelector('[data-auth-message]');
    var loginTab = modal.querySelector('[data-auth-tab="login"]');
    var signupTab = modal.querySelector('[data-auth-tab="signup"]');
    var loginFields = modal.querySelector('[data-auth-fields="login"]');
    var signupFields = modal.querySelector('[data-auth-fields="signup"]');
    var modeInput = form.querySelector('[name="mode"]');
    var returnInput = form.querySelector('[name="return_to"]');

    function setMode(mode) {
        var signup = mode === 'signup';
        modeInput.value = mode;
        loginTab.classList.toggle('is-active', !signup);
        signupTab.classList.toggle('is-active', signup);
        loginFields.hidden = signup;
        signupFields.hidden = !signup;
        loginFields.querySelectorAll('input').forEach(function (input) {
            input.required = !signup && input.type !== 'checkbox';
        });
        signupFields.querySelectorAll('input').forEach(function (input) {
            input.required = signup;
        });
        modal.querySelector('[data-auth-title]').textContent = signup ? 'Create your account' : 'Welcome back';
        modal.querySelector('[data-auth-subtitle]').textContent = signup
            ? 'Save your progress and keep learning with a clear plan.'
            : 'Log in to continue to your learning destination.';
        form.querySelector('.auth-submit').textContent = signup ? 'Create account' : 'Log in';
        message.hidden = true;
        message.textContent = '';
    }

    function openModal(returnTo, mode) {
        returnInput.value = returnTo || 'index.php';
        setMode(mode || 'login');
        modal.hidden = false;
        document.body.classList.add('modal-open');
        var firstInput = modal.querySelector('[data-auth-fields="' + (mode === 'signup' ? 'signup' : 'login') + '"] input');
        if (firstInput) firstInput.focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('modal-open');
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-auth-open]');
        if (trigger) {
            event.preventDefault();
            openModal(trigger.getAttribute('data-return-to'), trigger.getAttribute('data-auth-mode'));
        }
        if (event.target.closest('[data-auth-close]') || event.target === modal.querySelector('.auth-modal-backdrop')) {
            closeModal();
        }
        if (event.target.closest('[data-auth-tab="login"]')) setMode('login');
        if (event.target.closest('[data-auth-tab="signup"]')) setMode('signup');
        var passwordToggle = event.target.closest('[data-password-toggle]');
        if (passwordToggle) {
            var target = document.getElementById(passwordToggle.getAttribute('data-password-toggle'));
            target.type = target.type === 'password' ? 'text' : 'password';
            passwordToggle.textContent = target.type === 'password' ? 'Show' : 'Hide';
        }
        if (event.target.closest('[data-social-login]')) {
            event.preventDefault();
            message.hidden = false;
            message.className = 'auth-modal-message notice';
            message.textContent = 'Social login coming soon.';
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) closeModal();
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        message.hidden = true;
        var data = new FormData(form);
        fetch('auth_action.php', { method: 'POST', body: data, credentials: 'same-origin' })
            .then(function (response) {
                return response.json().then(function (body) {
                    if (!response.ok) throw new Error(body.message || 'Unable to continue.');
                    return body;
                });
            })
            .then(function (body) {
                window.location.href = body.redirect;
            })
            .catch(function (error) {
                message.hidden = false;
                message.className = 'auth-modal-message error-message';
                message.textContent = error.message;
                submit.disabled = false;
            });
    });
}());
