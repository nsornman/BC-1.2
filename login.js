const eyepassword = document.querySelector('#eyepassword');
const password = document.querySelector('#password');

eyepassword.addEventListener('click', function(e){
    const type = password.getAttribute('type') === 'password' ? 'text' :'password'
    password.setAttribute('type', type)

    this.classList.toggle('fa-eye-slash')
});