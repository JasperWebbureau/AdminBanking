# AdminBanking

Versie: `0.5.1`

Zelfstandige tenantgebonden module voor bankrekeningen, idempotente afschriftimports, banktransacties en matchvoorstellen. De kern gebruikt alleen AdminCore en kent Payment, Expense, Invoice en de legacy-administratie niet.

## Beschikbaar

- Bankrekeningen gebruiken een stabiele rekeningreferentie en optioneel een gevalideerd IBAN.
- Banktransacties bewaren signed minor units, een versioned bron-snapshot en een unieke externe fingerprint.
- Imports zijn atomair en idempotent op tenant, bankrekening, formaat en SHA-256 van de broninhoud.
- De eerste parser ondersteunt het bestaande ABN AMRO tabgescheiden exportformaat, zonder oude code over te nemen.
- Matches verwijzen uitsluitend met `target_type + target_public_id`; er bestaan geen cross-module foreign keys.
- `/Flexgrid/AdminBanking/transactions` biedt rekeningbeheer, import via upload of geplakte ABN-tabregels en een AJAX-transactieoverzicht.
- Zoeken, rekening-, status-, richting- en jaarfilters, sortering en paginering draaien server-side en blijven tenantgebonden.
- De import bewaart het bronbestand niet: alleen genormaliseerde transacties, veilige snapshots en de SHA-256-controlehash worden opgeslagen.
- Alle formulieren gebruiken Flexgrids centrale `AjaxEvent`/`AjaxResponse`-laag; de kleine filterclass gebruikt uitsluitend vanilla JavaScript.
- Iedere open bankregel kan naar een afletterscherm met herberekenbare, opgeslagen controlevoorstellen.
- Payment stelt bijschrijvingen voor aan openstaande facturen; Expense stelt afschrijvingen voor aan bestaande uitgaven.
- Betrouwbaarheid en reden blijven zichtbaar. De gebruiker kan een voorstel expliciet bevestigen of een bankregel bewust negeren.
- Een bevestigde factuurmatch registreert idempotent een Payment en allocation; een uitgavematch valideert het actuele bedrag en bewaart de publieke koppeling in Banking.
- Op het detail van een open afschrijving maakt **Maak uitgave** direct een uitgave aan en lettert de bankregel in dezelfde databasetransactie af. Het vaste brutobedrag wordt standaard met 21% btw teruggerekend; het percentage en de categorie zijn in het formulier te kiezen. Een leverancier is optioneel en kan nieuw worden ingevuld of uit een eerdere uitgave worden overgenomen. Een factuur of bon kan veilig worden geüpload.
- Zonder betrouwbaar voorstel kan de gebruiker via AJAX veilig zoeken op factuurnummer, klant, leverancier of referentie. Resultaten tonen datum, totaal en actueel openstaand bedrag en worden vóór bevestiging opnieuw tenantgebonden gevalideerd.
- Deelbetalingen op facturen tonen een waarschuwing maar zijn toegestaan; overbetalingen en afwijkende uitgavebedragen blijven niet koppelbaar.

Schema, PDO-opslag, beide kandidaatadapters en de transactionele beslisketen zijn tegen MariaDB gecontroleerd. Automatische bevestiging blijft uitgesloten: alleen de gebruiker maakt een voorstel definitief.
