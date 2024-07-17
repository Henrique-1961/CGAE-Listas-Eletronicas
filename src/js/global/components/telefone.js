document.getElementById('telefone').addEventListener('blur', function (e) {
    let x = e.target.value.replace(/\D/g, '').match(/(\d{2})(\d{0,})/);
    
    try { e.target.value = '(' + x[1] + ') ' + x[2]; }
    catch { e.target.value = e.target.value.replace(/\D/g, ''); }
});

document.getElementById('telefone').addEventListener('input', function(e) {
    if (e.target.value.charAt(e.target.value.length - 1).match(/(\d{1})/) === null) {
        e.target.value = e.target.value.substring(0, e.target.value.length - 1);
    }
});