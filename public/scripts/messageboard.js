async function getMessages() {
  const res = await fetch('/api/message', { method: 'GET' });

  if (!res.ok) {
    alert('Could not connect to the server');
    return;
  }

  return await res.json();
}

function renderTemplates(messages) {
  const container = document.getElementById('messages-container');
  const template = document.getElementById('message-card-template');

  container.innerHTML = "";

  for (const message of messages) {
    const clone = template.content.cloneNode(true);
    const titleElement = clone.querySelector('.message-title');
    const authorElement = clone.querySelector('.message-author');
    const contentElement = clone.querySelector('.message-content');
    const commentsElement = clone.querySelector('.message-comments');

    titleElement.textContent = message.title;
    authorElement.textContent = `By: ${message.author_first_name} ${message.author_surname}`;
    contentElement.textContent = message.content;

    for (const comment of message.comments) {
      const commentElement = document.createElement('p');
      commentElement.textContent = `${comment.first_name} ${comment.surname}: ${comment.content}`;

      commentsElement.appendChild(commentElement);
    }

    const commentInput = clone.querySelector('.comment-input');
    const commentButton = clone.querySelector('.add-comment-container .btn');
    commentButton.onclick = () => addComment(message.id, commentInput);

    container.appendChild(clone);
  }
}

async function onload() {
  renderTemplates(await getMessages());
};
onload();

document.getElementById('add-message').onclick = () => {
  document.getElementById('modal-overlay').classList.add('modal-overlay-visible');
}

document.getElementById('cancel-action').onclick = () => {
  document.getElementById('modal-overlay').classList.remove('modal-overlay-visible');
}

async function addMessage() {
  const form = document.querySelector('#modal-overlay .form');

  if (!form.checkValidity()) {
    return;
  }

  const title = document.getElementById('title-input').value;
  const content = document.getElementById('content-input').value;

  const res = await fetch('/api/message', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      title,
      content
    })
  });

  if (!res.ok) {
    alert("Could not add message");
  }

  document.getElementById('modal-overlay').classList.remove('modal-overlay-visible');

  renderTemplates(await getMessages());
}

document.getElementById('add-message-action').onclick = addMessage;

async function addComment(messageId, inputElement) {
  const content = inputElement.value;

  if (!content) {
    return;
  }

  const res = await fetch('/api/comment', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      message_id: messageId,
      content
    })
  });

  if (!res.ok) {
    alert("Could not add comment");
  }

  renderTemplates(await getMessages());
}