# Moodle Local - Pact Digital

Environnement de développement local Moodle conteneurisé (Docker) avec les développements sur mesure pour **Pact Digital**.

---

## 📁 Contenu du dépôt

* `compose.yml` : Configuration Docker Compose (MariaDB 11.4, Moodle PHP-Apache 8.3, Mailpit).
* `backup_moodle.sql` : Export complet de la base de données (cours, utilisateurs, configurations, rôles).
* `moodle/theme/pactdigital/` : Thème sur mesure Pact Digital (layouts, styles SCSS, templates Mustache, logos).
* `moodle/local/pactdigital/` : Modules et pages locales personnalisées (catalogue, dashboard, formateur).
* `.env.example` : Modèle de variables d'environnement locales.

---

## 🚀 Guide d'installation rapide (pour un nouveau collaborateur)

### 1. Prérequis
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé et démarré.
* [Git](https://git-scm.com/) installé.

---

### 2. Cloner le projet
```bash
git clone https://github.com/Xavier-77/moodle-pactdigital.git
cd moodle-pactdigital
```

---

### 3. Configurer le cœur Moodle (Moodle Core)

Ce dépôt contient vos développements spécifiques (`pactdigital`). Pour lier le cœur Moodle :

1. Créez votre fichier de configuration local :
   ```bash
   cp .env.example .env
   ```
2. Ouvrez `.env` et adaptez la variable `MOODLE_DIR` avec le chemin vers votre dossier Moodle sur votre machine (par exemple `/Users/votre_nom/moodle-docker/moodle` ou `./moodle`).

> **Note :** Si vous n'avez pas encore le cœur Moodle 4.3+, vous pouvez le cloner :
> ```bash
> git clone -b MOODLE_403_STABLE git://git.moodle.org/moodle.git /chemin/vers/moodle
> ```
> Puis copiez ou liez les dossiers personnalisés de ce dépôt :
> * `moodle/theme/pactdigital` ➜ dans `<chemin_moodle>/theme/pactdigital`
> * `moodle/local/pactdigital` ➜ dans `<chemin_moodle>/local/pactdigital`

---

### 4. Démarrer les conteneurs

Lancez simplement :
```bash
docker compose up -d
```

> 💡 **Initialisation automatique de la base de données :**
> Au tout premier démarrage, MariaDB importe automatiquement le fichier `backup_moodle.sql`. Vos comptes, cours et réglages sont immédiatement disponibles !

*(Si besoin de réimporter manuellement la base de données plus tard)* :
```bash
docker compose exec -T db mariadb -u moodle -pmoodlepassword moodle < backup_moodle.sql
```

---

### 5. Accès aux services

| Service | URL | Identifiants par défaut |
| :--- | :--- | :--- |
| **Moodle** | [http://localhost:8080](http://localhost:8080) | Comptes configurés dans la base |
| **Mailpit (Serveur mail de test)** | [http://localhost:8025](http://localhost:8025) | Accès libre |
| **Base de données (MariaDB)** | `localhost:3306` | User: `moodle` / Pass: `moodlepassword` |

---

## 💾 Sauvegarder la base de données avant de pousser sur Git

Lorsque vous effectuez des changements dans Moodle (nouveau cours, modification de réglages) et que vous souhaitez mettre à jour la base sur GitHub :

```bash
docker compose exec db mariadb-dump -u moodle -pmoodlepassword moodle > backup_moodle.sql
git add backup_moodle.sql
git commit -m "chore: mise à jour du dump de la base de données"
git push
```
