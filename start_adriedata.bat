@echo off
echo Démarrage du serveur Symfony sur 192.168.1.74:8000...
cd /d F:\ALPHAND\DatAdrie\ADRIEDATA  REM ⚠️ Modifie ce chemin !
symfony server:start --allow-http --no-tls --port=8000 --bind=192.168.1.74
php -S 192.168.1.74:8000 -t public public/index.php

pause
