const MONTHS = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

const DAYS = [
  'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
];

let events = {};

async function getEvents() {
  const res = await fetch('/api/event', { method: 'GET' });

  if (!res.ok) {
    alert('Unable to fetch data from server');
    return;
  }

  const eventsData = await res.json();

  for (const event of eventsData) {
    const day = new Date(event.event_date).getDate();
    
    events[day] ??= [];
    events[day].push(event);
  }

  console.log(events)
}

function getDaysInMonth(year, month) {
  return new Date(year, month + 1, 0).getDate();
}

function getFirstDayOfMonth(year, month) {
  const day = new Date(year, month, 1).getDay();
  return day === 0 ? 6 : day - 1;
}

function isToday(day, month, year) {
  const today = new Date();
  return day === today.getDate() &&
         month === today.getMonth() &&
         year === today.getFullYear();
}

function isWeekend(dayOfWeek) {
  return dayOfWeek === 5 || dayOfWeek === 6;
}

function renderCalendar() {
  const today = new Date();
  const displayMonth = today.getMonth();
  const displayYear = today.getFullYear();
  const daysInMonth = getDaysInMonth(displayYear, displayMonth);
  const firstDay = getFirstDayOfMonth(displayYear, displayMonth);

  const grid = document.getElementById('calendar-grid');
  const list = document.getElementById('calendar-list');
  const dayTemplate = document.getElementById('day-template');
  const listItemTemplate = document.getElementById('list-item-template');


  grid.innerHTML = "";
  list.innerHTML = "";
  document.getElementById('calendar-title').textContent = `${MONTHS[displayMonth]} ${displayYear}`;

  for (let i = 0; i < firstDay; i++) {
    const emptyClone = dayTemplate.content.cloneNode(true);
    const emptyCell = emptyClone.querySelector('.calendar-day');

    emptyCell.classList.add('calendar-day-empty');
    grid.appendChild(emptyClone);
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const dayOfWeek = new Date(displayYear, displayMonth, day).getDay();
    const adjustedDayOfWeek = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
    const isTodayDate = isToday(day, displayMonth, displayYear);
    const isWeekendDay = isWeekend(adjustedDayOfWeek);

    const dayClone = dayTemplate.content.cloneNode(true);
    const dayCell = dayClone.querySelector('.calendar-day');

    if (isTodayDate) {
      dayCell.classList.add('calendar-day-today');
    }

    if (isWeekendDay) {
      dayCell.classList.add('calendar-day-weekend')
    }

    dayCell.insertBefore(document.createTextNode(day), dayCell.firstChild);

    if (events[day]) {
      const hasJoined = events[day].some(e => e.joined);
      const gridDot = dayCell.querySelector('.event-dot');
      gridDot.classList.add('event-dot-visible');
      if (!hasJoined) gridDot.classList.add('event-dot-unjoined');
      dayCell.style.cursor = 'pointer';
      dayCell.onclick = () => openEventsModal(day);
    }

    grid.appendChild(dayClone);

    const listClone = listItemTemplate.content.cloneNode(true);
    const listItem = listClone.querySelector('.calendar-list-item');
    const listDay = listClone.querySelector('.calendar-list-day');
    const listName = listClone.querySelector('.calendar-list-name');

    if (isTodayDate) {
      listItem.classList.add('calendar-list-item-today');
    }

    if (isWeekendDay) {
      listItem.classList.add('calendar-list-item-weekend');
    }

    listDay.textContent = day;
    listName.textContent = DAYS[adjustedDayOfWeek];

    if (events[day]) {
      const hasJoined = events[day].some(e => e.joined);
      const listDot = listItem.querySelector('.event-dot');
      listDot.classList.add('event-dot-visible');
      if (!hasJoined) listDot.classList.add('event-dot-unjoined');
      listItem.style.cursor = 'pointer';
      listItem.onclick = () => openEventsModal(day);
    }

    list.appendChild(listClone);
  }
}

async function joinEvent(id, day) {
  const res = await fetch(`/api/event/join?event_id=${id}`, { method: 'POST' });

  if (!res.ok) {
    alert("Could not join event");
    return;
  }

  events = {};
  await getEvents();
  renderCalendar();
  openEventsModal(day);
}

function openEventsModal(day) {
  const dayEvents = events[day];
  const modal = document.getElementById('view-events-modal');
  const title = document.getElementById('view-events-title');
  const list = document.getElementById('view-events-list');
  const template = document.getElementById('event-item-template');

  title.textContent = `Events on ${day} ${MONTHS[new Date().getMonth()]}`;
  list.innerHTML = '';

  for (const event of dayEvents) {
    const clone = template.content.cloneNode(true);
    clone.querySelector('.event-item-title').textContent = event.title;

    clone.querySelector('.event-item-creator').textContent = `Created by: ${event.creator_first_name} ${event.creator_surname}`;
    clone.querySelector('.event-item-description').textContent = event.description;

    const names = event.users.map(u => `${u.first_name} ${u.surname}`);
    clone.querySelector('.event-item-users').textContent = names.length ? names.join(', ') : 'No participants';

    if (!event.joined) {
      const joinBtn = clone.querySelector('.event-item-join');
      joinBtn.classList.add('event-item-join-visible');
      joinBtn.onclick = () => joinEvent(event.id, day);
    }

    list.appendChild(clone);
  }

  modal.classList.add('modal-overlay-visible');
}

document.getElementById('view-events-close').onclick = () => {
  document.getElementById('view-events-modal').classList.remove('modal-overlay-visible');
}

document.getElementById('add-event').onclick = () => {
  document.getElementById('add-event-modal').classList.add('modal-overlay-visible');
}

document.getElementById('cancel-action').onclick = () => {
  document.getElementById('add-event-modal').classList.remove('modal-overlay-visible');
}

async function onload() {
  await getEvents();
  renderCalendar();
}
onload();

async function addEvent() {
  const form = document.querySelector('#add-event-modal .form');

  if (!form.checkValidity()) {
    return;
  }

  const title = document.getElementById('title-input').value;
  const description = document.getElementById('description-input').value;
  const event_date = document.getElementById('event-date-input').value;

  const res = await fetch('/api/event', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      title,
      description,
      event_date
    })
  });

  if (!res.ok) {
    alert("Could not add event");
  }

  events = {};

  await getEvents()
  renderCalendar();

  document.getElementById('add-event-modal').classList.remove('modal-overlay-visible');
}

document.getElementById('add-event-action').onclick = addEvent;