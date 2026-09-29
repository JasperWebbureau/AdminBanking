# Changelog

## 0.6.1 - 2026-09-21

- Dashboardbijdrage levert expliciete presentatiemetadata voor bankimport en nog af te letteren regels.

## 0.6.0 - 2026-09-21

- Optionele Dashboard-provider toegevoegd voor recente bankactiviteit, afschriftimport en nog af te letteren bankregels.
- Aandachtspunten linken direct naar het gefilterde Banking-overzicht.

## 0.5.1 - 2026-09-21

- Los bankierenpaneel verwijderd; de controller levert nu navigatiemetadata aan het gezamenlijke AdminCore-paneel.

## 0.5.0 - 2026-09-20

- Handmatig afletteren toegevoegd voor open bankregels zonder bruikbaar voorstel.
- Facturen zijn via AJAX doorzoekbaar op nummer, klant en klantreferentie; uitgaven op titel, leverancier en referentie.
- Resultaten tonen datum, totaal en waar relevant het actuele openstaande bedrag, met expliciete waarschuwingen bij bedragverschillen.
- Vrije doel-id's worden nooit vertrouwd: de module-eigen zoekprovider valideert tenant, valuta, beschikbaarheid en bedrag opnieuw binnen dezelfde transactie.
- Handmatige bevestiging gebruikt dezelfde idempotente Payment- en Expense-processors als een automatisch voorstel.
- Vanilla JavaScript verzorgt alleen debounce en `requestSubmit`; transport, loader, meldingen en containervervanging blijven bij Flexgrids centrale AJAX-laag.

## 0.4.0 - 2026-09-20

- Expliciet bevestigen van een matchvoorstel en bewust negeren van een bankregel toegevoegd.
- Bankregels worden met een row lock verwerkt en alleen na succesvolle doelverwerking als `matched` opgeslagen.
- Dezelfde bevestiging is idempotent; een afwijkend of verouderd voorstel wordt geweigerd.
- Bevestigde voorstellen blijven zichtbaar als gekozen auditspoor; genegeerde regels bewaren geen fictief doel.
- Factuurmatches registreren via AdminPayment een Payment en allocation en synchroniseren de factuurstatus binnen dezelfde transactie.
- Uitgavematches verifiëren de actuele Expense, valuta en het brutobedrag voordat Banking de publieke koppeling vastlegt.
- Echte MariaDB-tests bevestigen Payment-idempotentie, factuurstatus, Expense-hercontrole en Banking-statushydration.

## 0.3.0 - 2026-09-19

- Afletterscherm per banktransactie met declaratieve AJAX-herberekening toegevoegd.
- Consumer-owned kandidaatcontract, conventiegebaseerde providerdiscovery en atomische voorstelopslag toegevoegd.
- Voorstellen worden gededupliceerd, op betrouwbaarheid gesorteerd en tot tien kandidaten begrensd.
- Payment-adapter voor openstaande facturen en Expense-adapter voor bestaande uitgaven toegevoegd.
- Scores leggen herkenning van bedrag, referentie, naam en datum in leesbare redenen vast.
- Voorstellen zijn bewust niet automatisch definitief; acceptatie en negeren volgen als afzonderlijke auditbare handelingen.
- Frameworkvrije suites, modulegrenzen, PHP 7.3-linting en echte MariaDB-adaptertests slagen.

## 0.2.0 - 2026-09-19

- Tenantgebonden bankrekeningbeheer via Flexgrids declaratieve AJAX-laag toegevoegd.
- ABN-afschriftimport via bestand of geplakte tabregels toegevoegd; bronbestanden worden niet opgeslagen.
- Server-side zoekbaar, filterbaar, sorteerbaar en gepagineerd transactieoverzicht met gedeelde TableRenderer toegevoegd.
- Samenvattingskaarten voor aantallen, ontvangen, uitgegeven en nog af te letteren transacties toegevoegd.
- Vanilla JavaScript-filterclass en expliciete Flexgrid form-grid-resets toegevoegd.
- Schema, echte PDO-opslag, lijstqueries, presentatielaag, UI-contracten, PHP 7.3 en JavaScript-syntax geverifieerd.

## 0.1.0 - 2026-09-19

- BankAccount-, BankImport-, BankTransaction- en BankMatchProposal-domein toegevoegd.
- Exacte signed Money-bedragen en versioned bron-snapshots toegevoegd.
- Idempotente, transactionele ABN-tabimport met overlapdetectie toegevoegd.
- Tenantgebonden PDO-adapters en Autowire-records met unieke import- en transactiesleutels toegevoegd.
- Frameworkvrije domein-, parser-, import- en metadata-tests toegevoegd; schema/PDO-tests staan klaar voor na Autowire.
