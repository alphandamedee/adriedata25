@echo off
echo Fusion de la branche dev vers main...
cd /d F:\ALPHAND\DatAdrie\ADRIEDATA

echo 1. Mise à jour de la branche dev...
git checkout dev
git pull origin dev

echo 2. Passage à la branche main...
git checkout main
git pull origin main

echo 3. Fusion de dev vers main...
git merge dev

echo 4. Si tout s'est bien passé, envoi vers le dépôt distant...
git push origin main

echo Fusion terminée ! Vérifiez qu'il n'y a pas eu d'erreurs.
pause