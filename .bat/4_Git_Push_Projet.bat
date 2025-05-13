@echo off
cd /d %~dp0
git add .
git commit -m "Sauvegarde du projet"
git push origin main
pause