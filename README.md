# BoerNel.nl — Gronings Klaverjasblok

Een webapplicatie voor het bijhouden van scores, statistieken en ELO-ratings van een Gronings klaverjasgroep.

## Functionaliteit

- **Scorebord** — voer per hand de score in tijdens een pot; het systeem berekent automatisch totalen, roem, nat, pit en verzaken
- **Spelhistorie** — bekijk gespeelde potten met hand-voor-hand breakdown en ELO-berekeningen
- **Ranglijst** — actieve ranglijst (gespeeld afgelopen jaar) en all-time ranglijst met ELO-scores
- **Statistieken per speler** — ELO-ontwikkeling over tijd, gespeeld vs. gehaald percentage, bod-verdeling
- **Dark mode** — schakelbaar licht/donker thema, opgeslagen in localStorage en detecteert automatisch systeemvoorkeur

## Technische stack

| Onderdeel | Technologie |
|-----------|------------|
| Backend | PHP 8.x |
| Database | MySQL (via `mysqli`) |
| Frontend | Bootstrap 3.3.6, jQuery 2.2.0 |
| Grafieken | Highcharts |
| Tabellen | DataTables 1.10.16 |

## Bestandsstructuur

```
public_html/
├── index.php          # Startpagina met carousel (laatste winnaars, top 3, beste prestatie)
├── scoreboard.php     # Scoreblad invoerscherm (live tijdens het spel)
├── game.php           # Speldetail per pot met Highcharts scoregrafieken
├── stats.php          # Actieve ranglijst (gespeeld afgelopen jaar)
├── stats2.php         # All-time ranglijst
├── charts.php         # ELO-ontwikkeling & statistieken per speler
├── charts2.php        # Bodstatistieken per speler
├── bestanden/
│   ├── bernard.css    # Custom stylesheet incl. dark mode
│   ├── app.js         # Spellogica en scoreberekening (client-side)
│   ├── bootstrap.min.css/js
│   ├── jquery.min.js
│   └── jquery.cookie.js
├── config/
│   ├── put.php        # API-endpoint: sla ronde op in database
│   ├── del.php        # API-endpoint: verwijder laatste ronde
│   └── stats.php      # API-endpoint: statistieken ophalen
└── fotos/             # Spelersprofielfoto's (Naam.png)
```

## Database

De applicatie verwacht een MySQL-database met de volgende tabellen:

- `spelrondes` — individuele handen per pot (scores, roem, status)
- `Boernel_spel_totaal` — totalen per pot per speler inclusief ELO-berekeningen
- `Spelerdata` — spelersinformatie (naam, actief, ELO)

Databaseverbinding wordt ingesteld in `config/put.php` en de individuele PHP-pagina's.

## ELO-systeem

Het ELO-ratingsysteem berekent de verwachte winkans op basis van de gecombineerde ELO van twee teams en past de scores na elke pot aan. Startwaarde per speler is **1200**.

## Installatie

1. Kopieer de bestanden naar de `public_html` map van je webserver
2. Maak een MySQL-database aan en importeer de databasestructuur
3. Pas de databaseverbindingsgegevens aan in de PHP-bestanden
4. Zorg dat PHP 8.0 of hoger is geïnstalleerd
5. Voeg spelersprofielfoto's toe als `fotos/Naam.png`

## Dark mode

De licht/donker-knop (☽/☀) staat rechtsboven in de header van elke pagina. De keuze wordt opgeslagen in `localStorage` en blijft actief bij het navigeren tussen pagina's. Pagina's met Highcharts-grafieken herladen automatisch bij het wisselen van thema.
