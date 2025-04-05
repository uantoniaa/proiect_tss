
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
