@echo off
echo ========================================
echo  INSTALADOR SISTEMA RRHH - GRUPO BALLESTEROS
echo ========================================

echo.
echo 1. Creando directorio del proyecto...
cd C:\laragon\www
mkdir sistema-rrhh-ballesteros
cd sistema-rrhh-ballesteros

echo.
echo 2. Inicializando proyecto Laravel...
composer create-project laravel/laravel . --prefer-dist

echo.
echo 3. Configurando permisos...
mkdir storage\logs
mkdir bootstrap\cache

echo.
echo 4. Instalando dependencias adicionales...
composer require livewire/livewire
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

echo.
echo 5. Instalando dependencias frontend...
npm install
npm install -D tailwindcss postcss autoprefixer @tailwindcss/forms
npx tailwindcss init -p

echo.
echo ========================================
echo  INSTALACION COMPLETADA
echo ========================================
echo.
echo Siguiente paso: Configurar base de datos
pause
