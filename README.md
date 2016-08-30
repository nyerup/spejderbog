# Spejderbog

Bruger du også hele efteråret på at lære navne og ansigter på dine spejdere?
Skriv dem op i en Spejderbog – så kan du øve dig hver aften, kigge i smug på
spejdermøderne, og i øvrigt bruge den til at snyde når det passer dig.

Kriterierne for Spejderbog har været:

* Implementation i PHP uden behov for databaseforbindelser eller eksotiske
  libraries, så den nemt kan lægges op på de gængse webhoteller, som mange
  spejdergrupper bruger.
* Mulighed for oprettelse af spejdere på en smartphone, inkl. upload af
  billeder.
* Fornuftigt udskriftsformat i en browser, så bogen kan bruges til at danne en
  PDF til rundsendelse blandt lederne.

Det kunne være sjovt en dag at lave:

* Integration med Blåt Medlem.
* Et udseende, som ikke er faldet ned fra 90'erne.

## Eksempel

Se en Spejderbog [her](https://jespernyerup.dk/spejderbog/). Du er velkommen til
at redigere alt hvad du har lyst til. Når den bliver for rodet, tømmer jeg den
for data, og starter forfra.

## Installation

Upload `index.php`, `edit.php` og mappen `img` (inklusive billedet `tom.png`,
som ligger i den) til en webserver, som kan fortolke PHP. Det fungerer bedst med
en nogenlunde nylig PHP-version, og har ikke behov for andet end libgd og
sqlite, som er en del af de fleste PHP-installationer.
