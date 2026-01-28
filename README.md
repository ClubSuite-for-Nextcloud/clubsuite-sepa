# ClubSuite SEPA

[![Nextcloud Version](https://img.shields.io/badge/Nextcloud-28--32-blue.svg)](https://nextcloud.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.1--8.3-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-AGPL%20v3-green.svg)](LICENSE)

> 🏦 Automatisierter Beitragseinzug per SEPA-Lastschrift.

## 📋 Übersicht

ClubSuite SEPA automatisiert den Beitragseinzug für Vereine:

- **Mandate**: Verwaltung von SEPA-Lastschriftmandaten (CORE/B2B)
- **Sammelaufträge**: Erstellung von SEPA-XML (pain.008.001.02)
- **Vorschau**: Prüfung vor Bankeinreichung
- **Historie**: Protokollierung aller Einzüge
- **Rücklastschriften**: Handling fehlgeschlagener Einzüge

## 🚀 Installation

### Über den Nextcloud App Store
1. **ClubSuite Core** und **ClubSuite Finance** müssen installiert sein
2. Apps → Organisation → "ClubSuite SEPA" suchen
3. Installieren und aktivieren

### Manuelle Installation
```bash
cd /path/to/nextcloud/apps
git clone https://github.com/ClubSuite-for-Nextcloud/clubsuite-sepa.git
php occ app:enable clubsuite-sepa
```

## 📦 Anforderungen

| Komponente | Version |
|------------|--------|
| Nextcloud | 28 - 32 |
| PHP | 8.1 - 8.3 |
| **clubsuite-core** | erforderlich |
| **clubsuite-finance** | erforderlich |

## 🔒 DSGVO / Datenschutz

- IBAN/BIC werden verschlüsselt gespeichert
- Mandatsdaten DSGVO-konform verarbeitet
- Löschfristen nach gesetzlichen Vorgaben

## 📄 Lizenz

AGPL v3 – Siehe [LICENSE](LICENSE)

## 🐛 Bugs & Feature Requests

[GitHub Issues](https://github.com/ClubSuite-for-Nextcloud/clubsuite-sepa/issues)

---

© 2026 Stefan Schulz
