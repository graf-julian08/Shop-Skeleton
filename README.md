# PHP E-Commerce Shop Skeleton

## Übersicht
Das Repository **Shop-Skeleton** stellt eine modulare PHP-Backend-Struktur für E-Commerce-Plattformen bereit. Es beinhaltet Komponenten für Produktverwaltung, Retourenabwicklung und Weiterleitungs-Systeme.

## Projektstruktur & Architektur
- `index.php`: Hauptausführungsskript für den Webserver-Einstieg.
- `retoure.php`: Schnittstelle zur Abwicklung von Kundenretouren.
- `redirect_handler.php` & `redirects.htaccess`: URL-Weiterleitungsmechanismen.
- `admin/`: Administrationsbereich zur Datenpflege.
- `api/`: RESTful Endpunkte für Datenabfragen.
- `database/`: Datenbank-Schemata und SQLite-Datenbasis.
- `Yves-Frontend/`: Frontend-Komponenten der Shoplösung.

## Hauptfunktionalitäten
- **Retouren-Management**: Verarbeitungslogik für Rücksendungen.
- **Weiterleitungs-Engine**: Regelbasierte URL-Umlenkungen über `.htaccess` und PHP.
- **Modularer Aufbau**: Trennung in Administration, API und Frontend.

## Ausführung & Nutzung
Das System erfordert einen Webserver mit PHP-Unterstützung (z.B. Apache oder Nginx) und SQLite-Datenbankanbindung.

## Lizenz
Dieses Projekt steht unter der MIT-Lizenz.
