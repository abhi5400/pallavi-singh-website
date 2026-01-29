@echo off
cd /d "%~dp0"
echo ========================================
echo  Starting PHP Server for Pallavi Singh
echo ========================================
echo.
echo Opening browser at http://localhost:8000 in 2 seconds...
echo Keep the "PHP Server" window open. Close it to stop the server.
echo ========================================
start "PHP Server" php -S localhost:8000
timeout /t 2 /nobreak >nul
start http://localhost:8000
exit

