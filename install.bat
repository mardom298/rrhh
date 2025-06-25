@echo off
echo ========================================
echo INSTALADOR SISTEMA RRHH BALLESTEROS
echo ========================================

echo.
echo 1. Instalando dependencias PHP...
composer install

echo.
echo 2. Instalando dependencias Node.js...
npm install

echo.
echo 3. Configurando entorno...
copy .env.example .env
php artisan key:generate

echo.
echo 4. Ejecutando migraciones...
php artisan migrate:fresh --seed

echo.
echo 5. Compilando assets...
npm run build

echo.
echo ========================================
echo INSTALACION COMPLETADA
echo ========================================
echo.
echo Para iniciar el sistema:
echo 1. npm run dev
echo 2. php artisan serve (en otra terminal)
echo.
echo URL: http://127.0.0.1:8000
echo Usuario: admin@ballesteros.com
echo Password: password
echo ========================================
pause
