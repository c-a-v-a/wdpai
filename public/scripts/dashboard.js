const MONTHS = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

const DAYS = [
  'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
];

let events = {};
let allEvents = [];

async function getEvents() {
  const res = await fetch('/api/event/week', { method: 'GET' });

  if (!res.ok) {
    alert('Unable to fetch events');
    return;
  }

  const eventsData = await res.json();
  allEvents = eventsData;

  events = {};
  for (const event of eventsData) {
    const day = new Date(event.event_date).getDate();
    events[day] ??= [];
    events[day].push(event);
  }
}

async function getMessages() {
  const res = await fetch('/api/message/new', { method: 'GET' });

  if (!res.ok) {
    alert('Unable to fetch messages');
    return [];
  }

  return await res.json();
}

function getWeekDays() {
  const today = new Date();
  const currentDay = today.getDay();
  const mondayOffset = currentDay === 0 ? -6 : 1 - currentDay;
  const monday = new Date(today);
  monday.setDate(today.getDate() + mondayOffset);

  const days = [];
  for (let i = 0; i < 7; i++) {
    const date = new Date(monday);
    date.setDate(monday.getDate() + i);
    days.push(date);
  }
  return days;
}

function isToday(date) {
  const today = new Date();
  return date.getDate() === today.getDate() &&
         date.getMonth() === today.getMonth() &&
         date.getFullYear() === today.getFullYear();
}

function isWeekend(dayOfWeek) {
  return dayOfWeek === 5 || dayOfWeek === 6;
}

function renderWeekCalendar() {
  const grid = document.getElementById('week-grid');
  const list = document.getElementById('week-list');
  const dayTemplate = document.getElementById('week-day-template');
  const listTemplate = document.getElementById('week-list-item-template');

  const weekDays = getWeekDays();

  grid.innerHTML = '';
  list.innerHTML = '';

  // Add day labels to grid
  for (const dayName of DAYS) {
    const label = document.createElement('div');
    label.classList.add('week-day-label');
    label.textContent = dayName.slice(0, 3);
    grid.appendChild(label);
  }

  for (let i = 0; i < 7; i++) {
    const date = weekDays[i];
    const day = date.getDate();
    const adjustedDayOfWeek = i;
    const isTodayDate = isToday(date);
    const isWeekendDay = isWeekend(adjustedDayOfWeek);

    // Grid view
    const dayClone = dayTemplate.content.cloneNode(true);
    const dayCell = dayClone.querySelector('.week-day');

    if (isTodayDate) dayCell.classList.add('week-day-today');
    if (isWeekendDay) dayCell.classList.add('week-day-weekend');

    dayCell.insertBefore(document.createTextNode(day), dayCell.firstChild);

    if (events[day]) {
      const hasJoined = events[day].some(e => e.joined);
      const dot = dayCell.querySelector('.event-dot');
      dot.classList.add('event-dot-visible');
      if (!hasJoined) dot.classList.add('event-dot-unjoined');
    }

    grid.appendChild(dayClone);

    // List view
    const listClone = listTemplate.content.cloneNode(true);
    const listItem = listClone.querySelector('.week-list-item');
    const listDay = listClone.querySelector('.week-list-day');
    const listName = listClone.querySelector('.week-list-name');

    if (isTodayDate) listItem.classList.add('week-list-item-today');
    if (isWeekendDay) listItem.classList.add('week-list-item-weekend');

    listDay.textContent = day;
    listName.textContent = DAYS[adjustedDayOfWeek];

    if (events[day]) {
      const hasJoined = events[day].some(e => e.joined);
      const listDot = listItem.querySelector('.event-dot');
      listDot.classList.add('event-dot-visible');
      if (!hasJoined) listDot.classList.add('event-dot-unjoined');
    }

    list.appendChild(listClone);
  }
}

function renderEvents() {
  const container = document.getElementById('events-list');
  const template = document.getElementById('event-item-template');

  container.innerHTML = '';

  for (const event of allEvents) {
    const clone = template.content.cloneNode(true);
    const eventDate = new Date(event.event_date);
    const dayName = DAYS[eventDate.getDay() === 0 ? 6 : eventDate.getDay() - 1];
    clone.querySelector('.dashboard-card-title').textContent = `${event.title} - ${dayName}`;
    clone.querySelector('.dashboard-card-subtitle').textContent = event.description;

    if (!event.joined) {
      const joinBtn = clone.querySelector('.dashboard-card-action');
      joinBtn.classList.add('dashboard-card-action-visible');
      joinBtn.onclick = () => joinEvent(event.id);
    }

    container.appendChild(clone);
  }
}

async function joinEvent(id) {
  const res = await fetch(`/api/event/join?event_id=${id}`, { method: 'POST' });

  if (!res.ok) {
    alert("Could not join event");
    return;
  }

  events = {};
  allEvents = [];
  await getEvents();
  renderWeekCalendar();
  renderEvents();
}

function renderMessages(messages) {
  const container = document.getElementById('messages-list');
  const template = document.getElementById('message-item-template');

  container.innerHTML = '';

  for (const message of messages) {
    const clone = template.content.cloneNode(true);
    clone.querySelector('.dashboard-card-title').textContent = message.title;
    clone.querySelector('.dashboard-card-subtitle').textContent = `By: ${message.author_first_name} ${message.author_surname}`;

    container.appendChild(clone);
  }
}

async function onload() {
  await getEvents();
  const messages = await getMessages();

  renderWeekCalendar();
  renderEvents();
  renderMessages(messages);
}
onload();
