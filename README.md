# FamBoard

FamBoard to internetowa tablica ogłoszeń dla osób mieszkających pod jednym dachem.
Celem aplikacji jest ułatwienie komunikacji i planowania dla rodzin i współokatorów, którzy
poprzez różne harmonogramy mogą mieć problemy w przekazywaniu informacji.

Autor: Filip Cebula

## Funkcjonalność

Aplikacja umożliwia:
- Dodawanie wiadomości (np. listy zakupów, czy informacji o rachunkach)
- Dodawanie komentarzy do wiadomości
- Tworzenie wydarzeń (np. wspólne wyjście w góry czy na koncerty)
- Dołączanie do wydarzeń (aby inne osoby wiedziały kto dołącza)
- Dodawanie i włączanie/wyłączanie kont użytkowników (administrator)

### Logowanie

Logować mogą się tylko użytkownicy dodani przez administratora. Konto musi być włączone,
co daje administratorowi kontrolę nad dostępem bez konieczności usuwania użytkowników
— ich wiadomości i wydarzenia pozostają dostępne.

### Rejestracja

Rejestracja nie jest dostępna — użytkowników dodaje administrator. Eliminuje to
zbędny **attack surface** i pasuje do zamkniętego charakteru aplikacji.

### Dashboard

Pierwsza strona po zalogowaniu. Zawiera podgląd wydarzeń z bieżącego tygodnia
oraz najnowszych wiadomości. Umożliwia także dołączanie do wydarzeń.

### Message board

Strona do przeglądania i dodawania wiadomości oraz komentarzy.

### Kalendarz

Widok wydarzeń w bieżącym miesiącu. Kliknięcie kafelka z wydarzeniem pozwala
zobaczyć szczegóły i dołączyć.

Zielona kropka oznacza wydarzenie, w którym bierzemy udział. Biała — wydarzenie,
do którego jeszcze nie dołączyliśmy.

Użytkownicy mogą także tworzyć nowe wydarzenia.

### Admin panel

Panel do dodawania użytkowników oraz włączania/wyłączania ich kont.
Dostępna jedynie dla administratora.

## Architektura projektu

Projekt ma architekturę client/server.

Serwer wysyła pliki HTML, natomiast dane są pobierane i renderowane po stronie
klienta za pomocą zapytań do API.

### Struktura backend

- Router.php - routing
- Database.php - singleton do połączenia z bazą danych
- repositories - singletony do operacji na bazie danych
- middleware - dekoratory i funkcje sprawdzające metodę HTTP oraz uprawnienia użytkownika
- data - klasy opisujące strukturę danych z bazy i danych wejściowych
- controllers - kontrolery wysyłające pliki HTML
- controllers/api - kontrolery obsługujące endpointy `/api`

### Struktura frontend

- views - widoki HTML
- styles - style CSS
- scripts - skrypty JavaScript

JavaScript renderuje widoki przy wykorzystaniu danych z `/api` oraz elementów `<template>` z HTML.

W scripts i styles istnieje plik shared, współdzielony między stronami.

### Diagram ERD

![Diagram ERD](images/ERD.png)

## Konfiguracja

Przykładowy plik .env

```
DB_NAME=FamBoard
DB_USER=docker
DB_PASSWORD=docker
```

Jeżeli korzystasz z przykładowej bazy danych stworzonej do testowania aplikacji poniżej znajdziesz email i haslo do przykładowych kont:

**Admin**
test@test.com
testtest123

**Zwykły użytkownik**
enabled@user.com
123123123

## ✅ Checklist

- [X] Dokumentacja w `README.md`
- [X] Architektura aplikacji (MVC / Frontend–Backend / inna)
- [X] Kod napisany obiektowo (część backendowa)
- [X] Diagram ERD
- [X] Repozytorium Git (historia commitów, struktura)
- [X] Realizacja tematu projektu
- [X] HTML
- [X] PostgreSQL
- [1/2] Złożoność bazy danych
- [X] Eksport bazy danych do pliku `.sql`
- [X] PHP
- [X] JavaScript
- [X] Fetch API (AJAX)
- [X] Design (estetyka interfejsu)
- [X] Responsywność
- [X] Logowanie użytkownika
- [X] Sesja użytkownika
- [X] Uprawnienia użytkowników
- [X] Role użytkowników (co najmniej dwie)
- [X] Wylogowywanie
- [1/2] Widoki, wyzwalacze, funkcje, transakcje
- [ ] Akcje na referencjach (klucze obce)
- [X] Bezpieczeństwo aplikacji
- [X] Brak replikacji kodu (DRY)
- [X] Czystość i przejrzystość kodu
