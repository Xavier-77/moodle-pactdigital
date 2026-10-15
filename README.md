# Moodle Local - Pact Digital

Environnement de développement local Moodle et développements sur mesure pour Pact Digital.

## Structure du projet

- `compose.yml` : Configuration Docker Compose (MariaDB 11.4, Mailpit, Moodle PHP-Apache 8.3).
- `moodle/theme/pactdigital/` : Thème Moodle sur mesure Pact Digital (layouts, styles SCSS, templates Mustache).
- `moodle/local/pactdigital/` : Pages et fonctionnalités personnalisées (catalogue, dashboard, formateur).

## Démarrage rapide

```bash
docker compose up -d
```

- Moodle : [http://localhost:8080](http://localhost:8080)
- Mailpit (Web UI) : [http://localhost:8025](http://localhost:8025)
