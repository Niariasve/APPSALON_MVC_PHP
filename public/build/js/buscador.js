function iniciarApp(){buscarPorFecha()}function buscarPorFecha(){document.querySelector("#fecha").addEventListener("input",(function(n){const e=n.target.value;console.log(e),window.location=`admin?fecha=${e}`}))}document.addEventListener("DOMContentLoaded",(function(){iniciarApp()}));