function confirmation(form) {
        submit(form);
}
function closepopup() {

if (document.getElementById) { // DOM3 = IE5, NS6
document.getElementById('popupcenter').style.visibility = 'hidden';
}
else {
if (document.layers) { // Netscape 4
document.hidepage.visibility = 'hidden';
}
else { // IE 4
document.all.hidepage.style.visibility = 'hidden';
}
}
}
    function showpopup() {
    if (document.getElementById) { // DOM3 = IE5, NS6
    document.getElementById('respon').style.visibility = 'visible';
    }
    
}

function showid(show) {
if (document.getElementById) { // DOM3 = IE5, NS6
document.getElementById(show).style.display = 'block';
}
else {
if (document.layers) { // Netscape 4
document.hidepage.display = 'block';
}
else { // IE 4
document.all.hidepage.style.display = 'block';
}
}
}
function hideid(show) {
if (document.getElementById) { // DOM3 = IE5, NS6
document.getElementById(show).style.display = 'none';
}
else {
if (document.layers) { // Netscape 4
document.hidepage.display = 'none';
}
else { // IE 4
document.all.hidepage.style.display = 'none';
}
}
}