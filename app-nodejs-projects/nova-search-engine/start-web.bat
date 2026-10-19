@echo off
REM ===========================================
REM NovaSearch AI - Web + API kalici baslatici
REM Bu dosya: surumde tek tikla web'i acar.
REM Oturum acilisinda da otomatik calisir (Startup
REM klasorundeki kisayol uzerinden).
REM ===========================================
cd /d "%~dp0"
echo NovaSearch AI baslatiliyor... http://novasearch.coremusic.net:3001
bun apps/api/src/index.ts
pause
