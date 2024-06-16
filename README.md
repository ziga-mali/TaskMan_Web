# TaskMan

Spletna aplikacija TaskMan in backend z API kodo za projekt pri predmetu Seminar iz načrtovanja in razvoja programske opreme v telekomunikacijah. Dodana je tudi podatkovna baza z uporabnik Admin in geslom admin z administratorskimi pravicami. V bazi je že ustvarjen en projekt z eno nalogo.

### Opis delovanja APIja

| Pot                | Metoda  | Medijski tip | Vsebina zahtevka | Opis                |
|--------------------|---------|--------------|-------------------|---------------------|
|/login  | POST | JSON       | { <br> ime, <br> geslo <br> } | Prijava uporabnika. Vrne userID za identifikacijo in token za avtentikacijo    |
|/userview/userID  | GET | JSON | - | Pridobi projekte, ki jih lahko vidi uporabnik z userID
|/users | POST | JSON | { <br> vzdevek, <br> geslo, <br> ime, <br> priimek, <br> mail, <br> admin (opcijsko) <br> } | Ustvari uporabnika
|/users/userID | GET | JSON | - | Pridobi podatke o uporabniku z userID
|/users/userID | PUT | JSON |  { <br> vzdevek, <br> geslo, <br> ime, <br> priimek, <br> mail, <br> admin(opcijsko) <br> } | Posodobi podatke uporabnika z userID
|/users/userID | DELETE | JSON | - | Izbriši uporabnika z userID
|/projects | GET | JSON | - | Pridobi vse projekte
|/projects | POST | JSON | { <br> ime, <br> opis, <br> userID, <br> dostop <br>} | Ustvari projekt
|/projects/projectID | GET | JSON | - | Pridobi podatke o projektu s projectID
|/projects/projectID | PUT | JSON | { <br> ime, <br> opis, <br> userID, <br> dostop <br>} | Posodobi podatke o projektu s projectID
|/projects/projectID | DELETE | JSON | - | Izbriši projekt s projectID
|/projects/projectID/tasks | GET | JSON | - | Pridobi vse naloge, ki pripadajo projektu s projectID
|/projects/projectID/tasks | POST | JSON | { <br> ime, <br> opis, <br> kon_cas <br>} | Ustvari nalogo, ki pripada projektu s projectID
|/projects/projectID/tasks/taskIDs | GET | JSON | - | Pridobi podatke naloge s taskID, ki pripadajo projektu s projectID
|/projects/projectID/tasks/taskIDs | PUT | JSON | { <br> ime, <br> opis, <br> kon_cas <br>, <br> koncano <br>} | Posodobi podatke naloge s taskID, ki pripadajo projektu s projectID
|/projects/projectID/tasks/taskIDs | DELETE | JSON | - | Nalogo s taskID, ki pripada projektu s projectID

### Opis delovanja spletne aplikacije

Aplikacija je namenjena admnistratorskemu dostopu do podatkov, ki jih vnašajo uporabniki mobilne aplikacije. Vstopna stran je login.html. Tam uporabnik vnese svoje uporabniško ime in geslo. Ob loginu se preveri, če je uporabnik administrator in če ni se izpiše, da nima ustreznih pravic.
Ob uspešni prijavi se prikaze nadzorna plošča, kjer lahko uporabnik vidi vse uporabnike in vse projekte, tudi tiste, ki so že končani. Uporabnik ima možnost da si izbere uporabnika ali projekt, ki ga nato lahko ureja. Pod vsakim seznamom je tudi gumb, ki omogoča ustvarjanje novih projektov ali pa uporabnikov. Zgoraj desno na modrem pasu je tudi gumb za izpis (logout).
Tako ob pritisku na gumb dodaj projekt kot tudi ob pritisku na gumb dodaj uporabnika, se prikaže obrazec s polji, ki jih je potrebno izpolniti za uspešno ustvarjanje projekta ali pa uporabnika.
Ob pritisku na gumb Uredi uporabnika se prikaže obrazec s polji, ki jih je potrebno izpolniti za uspešno urejanje uporabnika. Potreben je ponoven vnos gesla za uporabnika. Pod obrazcem je gumb potrdi spremembo in gumb, ki omogoča izbris uporabnika.
Ob pritisku na gumb Uredi projekt se prikaže obrazec s polji, ki jih je potrebno izpolniti za uspešno urejanje projekta. Potreben je ponoven Pod obrazcem je gumb potrdi spremembe in gumb, ki omogoča izbris projekta. Zraven je tudi gumb, ki omogoča pridobitev nalog, ki so vezane na ta projekt.
Ob pritisku na ta gumb se prikaže seznam nalog in pod seznamom so trije gumbi, ki omogočajo urejanje izbrane naloge, izbris izbrane naloge in dodajanje naloge. Ob pritisku na gumb, ki omogoča urejanje nalog se prikaže obrazec s polji, ki jih je potrebno izpolniti za uspešno urejanje naloge. Ob pritisku na gumb, ki omogoča dodajanje nalog se prikaže obrazec s polji, ki jih je potrebno izpolniti za uspešno dodajanje naloge.
