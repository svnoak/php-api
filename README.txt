INSTRUKTIONER
=============

I denna övningen ska ni skapa ett sk. CRUD (Create, Read, Update, Delete) API,
dvs. ett API som gör det möjligt att kunna: hämta, läsa, uppdatera och radera
innehåll. Ert API ska bestå av 4 filer: create.php, read.php, update.php,
delete.php. Dessa filerna kommer hantera varsin HTTP-metod i ordningen: POST,
GET, PATCH och DELETE. Det är tillåtet att skapa fler filer utöver dessa (t.ex.
"functions.php"). Ni ska även utgå från en redan bestämd databas, se
"database.json" i denna mapp.

Samtliga sk. endpoints (t.ex. "http://localhost:7000/create.php") ska endast ta
emot och skicka tillbaka JSON (Content-Type), med undantag för GET (det räcker
att den skickar). Respektive endpoint ska endast tillåta sin "egen" HTTP-metod,
dvs. att en "POST" förfrågan till "delete.php" ska ge ett fel i stil med "Method
not allowed" (405).

I listan nedan hittar ni korta beskrivningar över respektive endpoint. Input och
output står för vilken data (dvs. vilka nycklar) den ska ta emot och vad den
skickar tillbaka. Nycklar skrivna inom []-paranteser är valfria.

> Tips innan ni börjar <
Gör era "tester" i programmet Insomnia först. Då har ni delvis testningen redan
på plats men också redan börjat fundera över hur ni ska programmera ert API.
Detta kommer ni ha nytta av inför projektet.

Create.php
==========

Input: { first_name, last_name, email, age }
Output: { id, first_name, last_name, email, age }

Tar emot information om en ny användare och lägger till denna i databasen, om
detta gick bra skickar vi tillbaka den nyskapade användaren.

Read.php
==========

Eftersom "read.php" hanterar GET-metod är vår input istället följande URL-parametrar:

- limit=N      (begränsa antal användare som skickas ut till `N`)
- id=X         (hämta en användare baserat på ID `X`)
- ids=A,B,C    (hämta användare baserat på en serie siffror)

Anropen med "limit" och "ids" ska skicka tillbaka en array över användare och
anrop med "id" skickar tillbaka en användare (dvs. ett objekt) direkt.

Update.php
==========

Input: { id, [first_name], [last_name], [email], [age] }
Output: { id, first_name, last_name, email, age }

Tar emot om en befintlig användare (baserat på `id`) och redigerar den utefter
de andra fält som också var medskickade (t.ex. "first_name"). Tänk på att det
ska gå att redigera flera fält på samma gång - så skickar jag med { id,
first_name, age } så innebär det att jag redigerar förnamn samt ålder.

Delete.php
==========

Input: { id }
Output: { id }

Tar emot en befintlig användares `id` och raderar sedan denna från databasen. Om
det lyckades svarar vi med användarens `id`.

ÖVRIGT
======

Kontrollera så att samtliga fält finns och inte är tomma när en användare ska
skapas. Om någonting inte stämmer svarar ni med något relevant felmeddelande
(t.ex. "One or more fields are either empty or missing").

Om en användare inte hittas (baserat på dess `id`) i någon av era endpoints
(GET, PATCH och DELETE) kan ni svara med ett felmeddelande i form av "Not found"
(404).

Det finns en del andra saker ni kan göra för att ert API inte ska krasha med ett      <--- Detta lämnar jag till
PHP-felmeddelande. Försök istället att allt eftersom ni programmerar, tänk efter           er som en bra övning.
- vad kan ske här? Om ni tror något kan gå fel så är det bättre att svara
användaren med JSON än att dom får ert känsliga PHP-felmeddelande.