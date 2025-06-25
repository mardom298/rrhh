# Sistema RRHH Grupo Ballesteros

Sistema completo de Recursos Humanos para el Grupo Ballesteros, desarrollado en Laravel 11 con PHP 8.3.

## 🚀 Características

- ✅ **Multi-tenant**: Soporte para múltiples empresas del grupo
- ✅ **Empleados compartidos**: Un empleado puede trabajar en varias empresas
- ✅ **Gestión completa**: Empleados, departamentos, posiciones
- ✅ **Dashboard ejecutivo**: Métricas y estadísticas en tiempo real
- ✅ **Responsive**: Interfaz moderna con Tailwind CSS

## 📋 Requisitos

- PHP 8.2+
- Laravel 11
- MySQL/MariaDB
- Composer
- Node.js & NPM

## 🔧 Instalación

1. **Clonar/Descomprimir el proyecto**
\`\`\`bash
# Colocar en C:\laragon\www\sistema-rrhh-ballesteros
\`\`\`

2. **Instalar dependencias**
\`\`\`bash
composer install
npm install
\`\`\`

3. **Configurar entorno**
\`\`\`bash
cp .env.example .env
php artisan key:generate
\`\`\`

4. **Configurar base de datos**
\`\`\`bash
# Editar .env con datos de tu BD
DB_DATABASE=sistema_rrhh_ballesteros
DB_USERNAME=root
DB_PASSWORD=
\`\`\`

5. **Ejecutar migraciones**
\`\`\`bash
php artisan migrate:fresh --seed
\`\`\`

6. **Compilar assets**
\`\`\`bash
npm run dev
\`\`\`

7. **Iniciar servidor**
\`\`\`bash
php artisan serve
\`\`\`

## 🎯 Acceso al Sistema

- **URL**: http://127.0.0.1:8000
- **Usuario**: admin@ballesteros.com
- **Contraseña**: password

## 📊 Módulos Incluidos

- **Dashboard**: Métricas y estadísticas
- **Empleados**: Gestión multi-empresa
- **Empresas**: Administración del grupo
- **Departamentos**: Organización por áreas
- **Posiciones**: Cargos y salarios

## 🏢 Empresas de Ejemplo

1. **Ballesteros Construcción** (RUC: 20111111111)
2. **Ballesteros Logística** (RUC: 20222222222)
3. **Ballesteros Tecnología** (RUC: 20333333333)

## 👥 Usuarios de Prueba

- **Carlos Ballesteros**: Gerente en todas las empresas
- **María García**: RRHH en Construcción y Tecnología
- **Luis Rodríguez**: Consultor en Tecnología

## 🛠️ Tecnologías

- **Backend**: Laravel 11, PHP 8.3
- **Frontend**: Blade, Tailwind CSS
- **Base de Datos**: MySQL/MariaDB
- **Herramientas**: Vite, Composer, NPM

## 📞 Soporte

Para soporte técnico, contactar al equipo de desarrollo.

---

**© 2024 Grupo Ballesteros - Sistema RRHH**
