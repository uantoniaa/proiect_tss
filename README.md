
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
 
### Concluzie
 
Proiectul se află într-o stare intermediară stabilă (versiune „beta-beta”).  
Testele unitare sunt bine structurate, acoperă logica critică și demonstrează atât corectitudinea codului, cât și robustețea funcțională a aplicației.  
Arhitectura modulară permite extinderea ușoară și integrarea cu tehnologii moderne pentru persistență, autentificare și testare automată.
