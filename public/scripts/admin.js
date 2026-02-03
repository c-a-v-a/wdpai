async function getUsers() {
  const res = await fetch('/api/user', { method: 'GET' });

  if (!res.ok) {
    alert('Could not get data from server');
    return;
  }

  return await res.json();
}

async function renderTemplates(users) {
  const template = document.getElementById('user-card-template');
  const container = document.getElementById('user-card-container');

  container.innerHTML = "";

  for (const user of users) {
    const clone = template.content.cloneNode(true);
    const nameSpan = clone.querySelector('.user-card-name');
    const actionButton = clone.querySelector('.user-card-action');

    nameSpan.textContent = `${user.first_name} ${user.surname} (${user.email})`;

    if (user.active) {
      actionButton.textContent = 'Disable';
      actionButton.classList.add('btn-danger');
      actionButton.onclick = () => { disableUser(user.id) };
    } else {
      actionButton.textContent = 'Enable';
      actionButton.classList.add('btn-primary');
      actionButton.onclick = () => { enableUser(user.id) };
    }

    container.appendChild(clone);
  }
}

async function enableUser(id) {
  const res = await fetch(`/api/user/enable?id=${id}`, { method: 'PUT' });

  if (!res.ok) {
    alert('Could not get data from server');
    return;
  }

  renderTemplates(await getUsers());
}

async function disableUser(id) {
  const res = await fetch(`/api/user/disable?id=${id}`, { method: 'PUT' });

  if (!res.ok) {
    alert('Could not get data from server');
    return;
  }

  renderTemplates(await getUsers());
}

async function addUser() {
  const form = document.querySelector('.modal .form');

  if (!form.checkValidity()) {
    return;
  }

  const first_name = document.getElementById('first-name-input').value;
  const surname = document.getElementById('surname-input').value;
  const email = document.getElementById('email-input').value;
  const password = document.getElementById('password-input').value;

  const res = await fetch('/api/user', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      first_name,
      surname,
      email,
      password
    })
  });

  document.getElementById('modal-overlay').classList.remove('modal-overlay-visible');

  if (!res.ok) {
    if (res.status === 409) {
      alert("User with this email already exists");
    } else {
      alert("Could not add user");
    }
  }

  renderTemplates(await getUsers());
}

async function onload() {
  renderTemplates(await getUsers());
};
onload();

document.getElementById('add-user-action').onclick = addUser;

document.getElementById('add-user').onclick = () => {
  document.getElementById('modal-overlay').classList.add('modal-overlay-visible');
}

document.getElementById('cancel-action').onclick = () => {
  document.getElementById('modal-overlay').classList.remove('modal-overlay-visible');
}
