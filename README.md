# Sistema de Recursos Humanos - Grupo Ballesteros

## 🏢 Descripción
Sistema integral de RRHH diseñado específicamente para el Grupo Ballesteros, con arquitectura multi-tenant que permite gestionar empleados en múltiples empresas del grupo con planillas independientes.

## ✅ Módulos Implementados

### 1. 👥 Gestión Multi-Empresa
- **BusinessGroup**: Grupo Ballesteros principal
- **Companies**: Múltiples empresas del grupo
- **EmployeeCompany**: Empleados trabajando en múltiples empresas
- Dashboard consolidado del grupo

### 2. 👤 Gestión de Empleados
- Empleados globales con ID único
- Asignación a múltiples empresas
- Códigos de empleado por empresa
- Salarios independientes por empresa
- Empresa principal designada

### 3. ⏰ Control de Asistencia
- Registro de entrada/salida
- Control de horas trabajadas
- Reportes de asistencia por empresa

### 4. 🏖️ Gestión de Licencias
- Solicitudes de vacaciones
- Permisos médicos
- Aprobaciones por empresa
- Balance de días disponibles

### 5. 💰 Sistema de Nómina
- Cálculo independiente por empresa
- Conceptos de pago configurables
- Descuentos y bonificaciones
- Cumplimiento legal peruano
- Reportes consolidados

### 6. 📊 Evaluaciones de Desempeño
- Evaluaciones periódicas
- Métricas personalizables
- Seguimiento de objetivos
- Reportes de rendimiento

### 7. 🎯 Sistema de Reclutamiento
- Publicación de vacantes
- Gestión de candidatos
- Proceso de entrevistas
- Seguimiento de aplicaciones

### 8. 📈 Reportería Avanzada
- Reportes por empresa
- Consolidados del grupo
- Métricas de RRHH
- Exportación a Excel/PDF

## 🛠️ Tecnologías Utilizadas
- **Backend**: Laravel 10 + PHP 8.1+
- **Frontend**: Livewire + Alpine.js + Tailwind CSS
- **Base de Datos**: MySQL/MariaDB/PostgreSQL
- **Autenticación**: Laravel Breeze
- **Colas**: Laravel Queues para nómina
- **Reportes**: Laravel Excel

## 🚀 Características Especiales
- ✅ **Multi-tenant**: Un empleado en múltiples empresas
- ✅ **Planillas separadas**: Cada empresa mantiene su nómina
- ✅ **Compliance peruano**: Cumple normativas laborales
- ✅ **Escalable**: Arquitectura preparada para crecimiento
- ✅ **Seguro**: Roles y permisos por empresa
- ✅ **Moderno**: Interfaz responsive y intuitiva

## 📦 Instalación

### Requisitos
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL/MariaDB/PostgreSQL
- Laragon (recomendado para desarrollo)

### Pasos de Instalación
\`\`\`bash
# 1. Clonar repositorio
git clone [repositorio] sistema-rrhh-ballesteros
cd sistema-rrhh-ballesteros

# 2. Instalar dependencias PHP
composer install

# 3. Configurar base de datos
cp .env.example .env
# Editar .env con datos de tu BD

# 4. Generar key y migrar
php artisan key:generate
php artisan migrate:fresh --seed

# 5. Instalar dependencias frontend
npm install
npm run build

# 6. Iniciar servidor
php artisan serve
\`\`\`

### Datos de Prueba
- **Usuario Admin**: admin@ballesteros.com
- **Contraseña**: password
- **Empresas**: 10+ empresas del Grupo Ballesteros
- **Empleados**: 50+ empleados de prueba

## 🎯 Casos de Uso Principales

### Empleado Multi-Empresa
Juan Pérez trabaja en:
- **Constructora Ballesteros** (Principal) - Código: EMP001 - Salario: S/4,500
- **Logística Ballesteros** (Secundaria) - Código: LOG015 - Salario: S/1,500

### Nómina Separada
- Cada empresa calcula su planilla independientemente
- Reportes consolidados a nivel grupo
- Cumplimiento tributario por empresa

### Dashboard Ejecutivo
- Vista consolidada de todas las empresas
- Métricas globales del grupo
- Empleados activos por empresa
- Costos de nómina consolidados

## 📊 Próximas Funcionalidades
- [ ] Integración con SUNAT
- [ ] App móvil para marcado
- [ ] Inteligencia artificial para RRHH
- [ ] Integración con bancos
- [ ] Portal del empleado

## 🤝 Soporte
Para soporte técnico o consultas sobre el sistema, contactar al equipo de desarrollo.

---
**Desarrollado para el Grupo Ballesteros** 🏢
