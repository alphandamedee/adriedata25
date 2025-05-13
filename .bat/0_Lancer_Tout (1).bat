@echo off
echo =======================================
echo     LANCEMENT DU PROJET COMPLET
echo =======================================

REM Lancer WAMP
echo ➤ Lancement de WAMP...
start "" "C:\wamp64\wampmanager.exe"
timeout /t 5 > nul

REM Ouvrir Visual Studio Code dans le dossier courant
echo ➤ Ouverture de VS Code...
start code .

REM Lancer le serveur Symfony (PHP natif)
echo ➤ Démarrage du serveur Symfony (localhost:8000)...
start cmd /k "php -S localhost:8000 -t public"

REM Ouvrir le navigateur sur le projet
echo ➤ Ouverture du navigateur...
start http://localhost:8000

echo ✅ Projet lancé avec succès !