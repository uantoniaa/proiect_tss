
Testarea unitară este o practică esențială în dezvoltarea aplicațiilor backend în PHP, contribuind la detectarea timpurie a erorilor, facilitarea refactorizării și îmbunătățirea arhitecturii.
Este adesea utilizată în combinație cu TDD (Test Driven Development) și CI/CD (Continuous Integration/Continuous Delivery).

## 1. Definiții esențiale
- **Test unitar**: verifică în mod izolat o funcție.
- **PHPUnit**: framework de testare unitară pentru PHP.
- **Mocking**: simularea comportamentului unor dependințe externe pentru a izola testele.
- **Test case**: verifică un comportament specific.
- **Fixture**: date sau obiecte de test folosite în scenarii specifice.

## 2. Framework-uri de testare
- **PHPUnit**: stabil, documentat, integrabil în CI/CD, standardul principal în PHP.
- **Pest**: alternativă modernă bazată pe PHPUnit, cu o sintaxă concisă.
- **Codeception**: framework complet pentru testare unitară, funcțională și end-to-end (E2E), bazat pe stilul **BDD (Behavior Driven Development)** – adică teste scrise într-un mod care reflectă comportamentul așteptat al aplicației.
- **Mockery / Prophecy**: biblioteci pentru mocking; Mockery e mai flexibil, Prophecy e integrat în PHPUnit.

## 3. Aplicația noastră - NoteApp 
Aplicația este un sistem de notițe personale, împărțit în 4 servicii:
- `NoteService`: gestiunea notițelor
- `TagService`: filtrarea și organizarea notițelor după etichete
- `FilterService`: căutare și sortare
- `AuthService`: autentificare și validare utilizator.

![Screenshot 2025-05-16 230950](https://github.com/user-attachments/assets/0f4b849c-a8b0-46d4-ad7a-1c04aa849987)

Pentru testarea logicii aplicației, echipa a folosit **PHPUnit**, datorită:
- suportului pentru teste izolate și mocking
- integrării facile cu Composer
- generării de rapoarte de acoperire a codului
Testarea unitară cu PHPUnit este potrivită și accesibilă chiar și pentru aplicații mici, cum este NoteApp.

**Aplicația poate fi modificată sau extinsă pe parcurs, în funcție de cum evoluează proiectul și de feedback-ul primit**.

## Implementarea versiunii intermediare (beta-beta)
 
### Studii de caz
 
#### Studiu de caz 1 – Crearea unei notițe cu date valide
- **Scenariu**: Utilizatorul `u1` creează o notiță cu titlul `Meeting` și conținutul `Discuție despre proiect`.
- **Input**: `userId = u1`, `title = Meeting`, `content = Discuție despre proiect`
- **Output așteptat**: `Notiță creată cu succes.`
- **Test folosit**: `testCreateNoteSuccess`
 
#### Studiu de caz 2 – Tentativă de creare a unei notițe cu titlu duplicat
- **Input**: `createNote("u1", "Meeting", "Text 1")` urmat de `createNote("u1", "Meeting", "Text 2")`
- **Output așteptat**: `Titlu deja folosit.`
- **Test folosit**: `testCreateNoteDuplicateTitle`
 
#### Studiu de caz 3 – Filtrare cu cuvânt cheie și dată
- **Scenariu**: Utilizatorul dorește să găsească notițe care conțin cuvântul „testing” în titlu, conținut sau tag.
- **Input**: listă cu 3 notițe, cu și fără „testing” și cu date diferite
- **Output așteptat**: 2 rezultate filtrate
- **Test folosit**: `testFilterByKeywordAndDate`
 
#### Studiu de caz 4 – Adăugarea unei etichete la o notiță existentă
- **Scenariu**: Utilizatorul `u1` dorește să adauge o etichetă (ex: `important`) la notița intitulată `Note1`.
- **Precondiții**: Notița `Note1` există deja pentru utilizatorul `u1`
- **Input**: `userId = u1`, `noteTitle = Note1`, `tag = important`
- **Output așteptat**: `Etichetă adăugată cu succes.`
- **Test folosit**: `testAddTagSuccess`
 
---
 
### Evaluări comparative
 
#### Căutare în conținut: `stripos()` vs `strpos()`
- **Alternativă**: `strpos()` – căutare case-sensitive
- **Decizie**: `stripos()` permite utilizatorului să caute fără diferențiere între litere mari/mici
- **Impact**: îmbunătățește accesibilitatea și experiența de utilizare
 
#### Procesarea etichetelor: `array_map()` și `array_filter()` vs `foreach`
- **Alternativă**: buclă clasică `foreach`
- **Avantaj actual**: expresivitate mai mare, cod mai concis și ușor de întreținut
 
#### Persistență: fișiere `.json` vs baze de date
- **Alegere actuală**: JSON pentru simplitate și acces rapid
- **Limitare**: nu este scalabil pentru mulți utilizatori sau volume mari de date
- **Posibilă extindere**: integrare cu SQLite sau MySQL pentru persistență robustă
 
---
 
### Analize aprofundate
 
#### Acoperirea testelor
- Testele unitare acoperă toate metodele din clasele: `NoteService`, `FilterService`, `TagService`, `AuthService`
- Sunt verificate cazuri pozitive, negative și de margine
 
#### Limitări identificate
- Funcția `updateNote()` nu validează dacă noul titlu este deja folosit → posibilitate de suprascriere accidentală
- Fișierele `notes.json` și `tags.json` pot deveni inconsistente dacă două procese scriu simultan
- Lipsa unei metode de autentificare cu sesiune expirabilă
 
#### Posibile îmbunătățiri
- Introducerea unui sistem de logare a modificărilor
- Adăugarea unui câmp `timestamp` în notițe pentru sortare și filtrare avansată
- Migrarea testelor către un framework CI (ex: GitHub Actions) pentru rulare automată
 
---
 
### Observații privind testarea
 
#### Testarea funcțională acoperă:
- Cazuri valide și invalide pentru utilizator
- Verificarea mesajelor de răspuns din metodele serviciilor
 
#### Testarea structurală acoperă:
- Toate ramurile logice din `FilterService::filterNotes`
- Acoperire completă a tuturor condiționalelor (`if`, `else`) și instrucțiunilor repetitve (`foreach`)
 
---

# Partea 3/3

## Configurație Tehnică

| Componentă         | Valoare                          |
|--------------------|----------------------------------|
| Limbaj             | PHP 8.2.28                       |
| Framework testare  | PHPUnit 11.5.20                  |
| Debugging Tool     | Xdebug v3.4.3                    |
| Mediu rulare       | VSCode                           |
| Mașină virtuală    | Nu a fost utilizată              |

# Evaluarea unui Instrument AI pentru Generarea Testelor Software
## Introducere
În contextul dezvoltării software moderne, testarea automată a aplicațiilor joacă un rol esențial în asigurarea calității și fiabilității codului. Testele unitare contribuie la detectarea rapidă a erorilor, la validarea comportamentului logic și la creșterea încrederii în aplicațiile livrate. În ultimii ani, instrumentele bazate pe inteligență artificială (AI) au început să ofere asistență reală în generarea și scrierea testelor unitare, accelerând procesul de testare și îmbunătățind acoperirea acestora.

Acest raport analizează utilizarea unui astfel de instrument AI — ChatGPT — în testarea unei aplicații PHP pentru gestionarea notițelor. Studiul constă într-o comparație între o suită de teste unitare scrisă manual și una generată automat cu ajutorul AI [5][6][7][8], urmărind diferențele de structură, calitate și acoperire funcțională. În cadrul procesului de testare a claselor, au fost evaluate două abordări distincte: testele scrise manual, bazate pe înțelegerea logicii aplicației, și testele generate automat de ChatGPT.

Scopul raportului este să evidențieze utilitatea, avantajele și eventualele limitări ale utilizării AI în procesul de testare software, precum și să ofere o perspectivă comparativă asupra celor două metode de generare a testelor.

##  4. Comparație între testele proprii și cele autogenerate

| **Criteriu**                     | **Teste proprii**                         | **Teste generate AI**                                   |
|----------------------------------|-------------------------------------------|----------------------------------------------------------|
| Număr de cazuri acoperite        | 4-5 în medie cazuri (de bază)                        | 6+ cazuri (acoperire completă + persistență)             |
| Persistență între instanțe       |    Nu este testată                        | Testată explicit                                      |
| Gestionare fișier JSON           |    Implicit în testele reale              | Separat, cu fișier de test dedicat                    |
| Izolare între teste              |    Nu există (același fișier users.json)  | Cu fișier temporar `test_users.json`                  |
| Verificare efecte secundare      | Doar prin metode                       | Verifică fișierul scris și conținutul său             |
| Claritate și structură           |  Simplu și clar                         |  Mai complet, dar mai complex structural               |

## 5. Captură de ecran și rulare cod

![Screenshot 2025-05-16 224620](https://github.com/user-attachments/assets/89e73bbb-a4d2-4830-a21a-64f441733d5c)

### 6. Analiză comparativă

| Criteriu                   | Teste Manuale                              | Teste Generate cu AI                             |
|---------------------------|--------------------------------------------|--------------------------------------------------|
| **Acoperire funcțională** | Medie, multe cazuri intuitive acoperite                | Extinsă – include filtrare parțială, dată, lipsă câmp |
| **Claritate cod**         | Foarte clar, concis                       | Mai detaliat, structurat           |
| **Efort de implementare** | Ridicat                                   | Minim (generare + ajustare)                     |
| **Generalizare testare**  | Mai specific                              | Mai variat (teste pozitive și negative)         |
| **Tratare excepții**      | Doar cazuri de filtrare eșuată            | Include și note fără câmp `date`, cu update cu titlu existent                |
| **Testarea unei actualizări cu un titlu deja existent** | Nu am acoperit manual | Sugerată de AI  |
| **Testarea mesajelor exacte returnate de metode**  | Nu am acoperit manual | Sugerată de AI  |

Testele generate de AI au o structură clară și sunt bine denumite, reflectând exact comportamentul testat. Fiecare metodă de test acoperă un singur caz specific, respectând principiul „testului atomic”. De asemenea, AI-ul propune o soluție elegantă de izolare a testelor prin utilizarea unui fișier dedicat test_notes.json, care este creat și șters automat înainte și după fiecare test.
În schimb, testele scrise manual folosesc fișierul notes.json implicit, resetat în metoda setUp(). Această abordare funcționează corect, dar poate introduce interferențe dacă testele sunt rulate în paralel. Totuși, tool-urile AI nu dau răspunsuri complexe din primul prompt, necesitând detalii și mai multe încercări.

## 7. Interpretare și concluzii

Testele generate manual sunt suficiente pentru cazuri intuitivă și oferă o bază solidă pentru funcționalitățile de bază ale claselor.

ChatGPT a generat o suită de teste mai complexă și cu o abordare tehnică riguroasă, care acoperă inclusiv:
- persistența datelor între instanțe,
- izolarea testelor printr-un fișier temporar,
- verificarea conținutului fișierului JSON.

AI-ul a sugerat inclusiv o metodă de **subclassing** pentru a nu modifica clasa originală — o abordare profesionistă pentru testare și a oferit:
- o suită completă de teste, bine organizate și ușor de citit;
- sugestii pentru cazuri de test mai puțin evidente;
- soluții pentru o testare mai curată și mai izolată;
- posibilitatea de a genera rapid cod repetitiv, economisind timp.

Totuși, testele generate nu pot înlocui complet gândirea critică și adaptarea unui dezvoltator. De exemplu, integrarea clasei FilterService și validarea contextului specific al aplicației au fost realizate eficient doar în testele scrise manual. Deși AI-ul poate propune rapid o structură de test, el tinde să opereze la un nivel generic sau de bază, fără a înțelege complet toate nuanțele logice sau constrângerile reale ale aplicației. În multe cazuri, acesta omite scenarii particulare, dependențe de context sau comportamente de margine (edge cases) care ar putea apărea doar într-un flux real de utilizare. Prin urmare, implicarea activă a dezvoltatorului rămâne esențială pentru a valida, ajusta și completa testele sugerate de AI.

**Concluzie:** AI-ul poate îmbunătăți semnificativ procesul de testare, mai ales în proiecte reale, unde persistența, izolarea și scalabilitatea sunt importante. Totuși, în fazele incipiente ale dezvoltării, testele proprii pot fi suficiente, iar implicarea umană va rămâne esențială.

## 8. Bibliografie

* [1]: Bergmann, Sebastian, PHPUnit – Documentație oficială, https://docs.phpunit.de/ Data ultimei accesări: 15 Mai 2025
* [2]: Johnson, Matthew, A Beginner's Guide to Test Driven Development With Symfony and Codeception, https://www.twilio.com/en-us/blog/beginners-guide-test-driven-development-symfony-codeception Data ultimei accesări: 5 aprilie 2025
* [3]: Laravel Team, Laravel Testing Documentation, https://laravel.com/docs/12.x/testing Data ultimei accesări: 5 aprilie 2025
* [4]: Johnson, Matthew, A Beginner's Guide to Test Driven Development With Symfony and Codeception, https://www.twilio.com/en-us/blog/beginners-guide-test-driven-development-symfony-codeception Data ultimei accesări: 5 aprilie 2025
* [5]: Chatgpt 4o, AuthService Test, https://chatgpt.com/share/682783f8-ef64-8008-9dc4-b26f0eb3cc24, Data generării: 16 Mai 2025
* [6]: Chatgpt 4o, FilterService Test, https://chatgpt.com/share/682784b4-86a8-8008-aad8-7d106c0404ea, Data generării: 16 Mai 2025
* [7]: Chatgpt 4o, NoteService Test, https://chatgpt.com/share/68278540-40a0-8008-821f-aea3629d62ef, Data generării: 16 Mai 2025
* [8]: Chatgpt 4o, TagService Test, https://chatgpt.com/share/682785a8-8d5c-8008-ae2b-24b5ab6da7bf, Data generării: 16 Mai 2025
* [9]: Derick Rethans, Xdebug 3, https://xdebug.org/docs/, Data ultimei accesări: 10 Mai 2025
