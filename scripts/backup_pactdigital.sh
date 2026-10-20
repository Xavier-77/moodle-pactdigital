#!/usr/bin/env bash
# ==============================================================================
# PACTDIGITAL-BF — Script de Sauvegarde Automatisée & Résilience (ADM-09)
#
# Ce script réalise :
# 1. Un export complet (dump SQL compressé) de la base de données MariaDB
# 2. Une archive sécurisée des fichiers moodledata (hors caches temporaires)
# 3. La rotation automatique des sauvegardes (purge des archives > 7 jours)
# ==============================================================================

set -euo pipefail

BACKUP_DIR="${BACKUP_DIR:-$HOME/moodle-backups}"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_CONTAINER="moodle-local-db-1"
MOODLE_CONTAINER="moodle-local-moodle-1"
RETENTION_DAYS=7

mkdir -p "${BACKUP_DIR}"

LOG_FILE="${BACKUP_DIR}/backup_${TIMESTAMP}.log"
exec > >(tee -a "${LOG_FILE}") 2>&1

echo "================================================================="
echo "  PACTDIGITAL-BF — Démarrage de la sauvegarde du $(date)"
echo "================================================================="

# 1. Sauvegarde MariaDB
DB_FILE="${BACKUP_DIR}/db_pactdigital_${TIMESTAMP}.sql.gz"
echo "📦 [1/3] Sauvegarde de la base de données MariaDB..."
docker exec "${DB_CONTAINER}" mariadb-dump -u moodle -pmoodlepassword --single-transaction --quick moodle | gzip > "${DB_FILE}"
echo "   ✓ Base de données sauvegardée : ${DB_FILE} ($(du -h "${DB_FILE}" | cut -f1))"

# 2. Sauvegarde des données Moodledata (hors caches)
DATA_FILE="${BACKUP_DIR}/moodledata_pactdigital_${TIMESTAMP}.tar.gz"
echo "📁 [2/3] Sauvegarde des fichiers Moodledata (filedir & documents)..."
docker exec "${MOODLE_CONTAINER}" tar -czf - \
    --exclude='cache/*' \
    --exclude='localcache/*' \
    --exclude='sessions/*' \
    --exclude='temp/*' \
    -C /var/www/moodledata . > "${DATA_FILE}"
echo "   ✓ Moodledata sauvegardé : ${DATA_FILE} ($(du -h "${DATA_FILE}" | cut -f1))"

# 3. Rotation des anciennes sauvegardes
echo "🧹 [3/3] Rotation automatique des archives (> ${RETENTION_DAYS} jours)..."
find "${BACKUP_DIR}" -type f \( -name "*.sql.gz" -o -name "*.tar.gz" -o -name "*.log" \) -mtime +${RETENTION_DAYS} -exec rm -v {} \;

echo "================================================================="
echo "  ✅ Sauvegarde PACTDIGITAL terminée avec succès le $(date)"
echo "  Répertoire de stockage : ${BACKUP_DIR}"
echo "================================================================="
