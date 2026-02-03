const menuOpen = document.getElementById('menu');
const menuClose = document.getElementById('close-menu');
const sidebar = document.getElementById('sidebar');
const logoutButton = document.getElementById('log-out');

menuOpen.onclick = () => sidebar.classList.add('sidebar-open');
menuClose.onclick = () => sidebar.classList.remove('sidebar-open');

logoutButton.onclick = async () => {
  await fetch('/api/logout', { method: 'POST' });

  window.location.href = "/login";
}