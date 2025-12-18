@echo off
title Laravel + Ngrok Launcher
echo 🚀 Lancement du serveur Laravel...
start /B php artisan serve --host=127.0.0.1 --port=8000

echo 🌍 Démarrage de Ngrok...
timeout /t 3 >nul
ngrok http 8000
pause
