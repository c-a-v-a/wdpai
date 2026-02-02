document.getElementById('login-action').onclick = async () => {
  const form = document.getElementById('login-form');

  if (!form.checkValidity()) {
    return;
  }

  const res = await fetch('/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      email: document.getElementById('email').value,
      password: document.getElementById('password').value
    })
  });

  if (!res.ok) {
    alert('Incorrect email or password');
    return;
  }

  const data = await res.json();

  if (data.success) {
    window.location.href = "/dashboard";
  }
}