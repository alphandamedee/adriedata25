@echo off
REM Configuration
set BACKUP_DIR=%~dp0backups
set DB_NAME=adriedata
set DATESTAMP=%date:~6,4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%
set MYSQL_BIN=C:\wamp64\bin\mysql\mysql8.0.30\bin

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
"%MYSQL_BIN%\mysqldump.exe" -u root %DB_NAME% > "%BACKUP_DIR%\backup_%DATESTAMP%.sql"

echo Sauvegarde terminée : backup_%DATESTAMP%.sql
pause