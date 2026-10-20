#!/usr/bin/env bash
# ==============================================================================
# PACTDIGITAL-BF — Exécution du Cron Périodique Moodle (ADM-17)
#
# Exécute les tâches d'arrière-plan du cœur Moodle et des plugins :
# - Calcul et validation des achèvements de cours
# - Envoi des relances et notifications de formateurs
# - Émission des badges et attestations différées
# ==============================================================================

set -euo pipefail

MOODLE_CONTAINER="moodle-local-moodle-1"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Démarrage du cron PACTDIGITAL-BF..."
docker exec "${MOODLE_CONTAINER}" php /var/www/html/admin/cli/cron.php
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Fin du cron PACTDIGITAL-BF."
