document.getElementById('password').addEventListener('input', checkValue);

function checkValue() {
    document.getElementById('action').disabled = document.getElementById('password').value.split(' ').join('').length === 0;
}