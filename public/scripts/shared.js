const menuOpen = document.getElementById('menu');
const menuClose = document.getElementById('close-menu');
const sidebar = document.getElementById('sidebar');

menuOpen.onclick = () => sidebar.style.display = "flex";
menuClose.onclick = () => sidebar.style.display = "none";