const menuOpen = document.getElementById('menu');
const menuClose = document.getElementById('close-menu');
const sidebar = document.getElementById('sidebar');

menuOpen.onclick = () => sidebar.classList.add('sidebar-open');
menuClose.onclick = () => sidebar.classList.remove('sidebar-open');