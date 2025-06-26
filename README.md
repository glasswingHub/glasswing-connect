# Glasswing Connect

Una aplicación web moderna para la gestión de beneficiarios y escuelas, construida con Laravel y Vue.js.

## 🛠 Stack Tecnológico

### Backend
- **Laravel 12** - Framework PHP para el backend
- **PHP 8.2+** - Lenguaje de programación
- **Laravel Sanctum** - Autenticación API
- **Laravel Socialite** - OAuth con Google
- **Inertia.js** - Renderizado del lado del servidor para SPA

### Frontend
- **Vue.js 3** - Framework JavaScript progresivo
- **Inertia.js** - Cliente para Laravel
- **Tailwind CSS** - Framework CSS utilitario
- **Vite** - Build tool y bundler
- **Axios** - Cliente HTTP

### Herramientas de Desarrollo
- **Laravel Breeze** - Kit de autenticación
- **Laravel Sail** - Entorno Docker para desarrollo
- **Laravel Pint** - Formateador de código PHP
- **PHPUnit** - Testing framework
- **Faker** - Generación de datos de prueba

## 📋 Requisitos Previos

- PHP 8.2 o superior
- Composer
- Node.js 18+ y npm
- Git

## 🚀 Configuración del Entorno de Desarrollo

### 1. Clonar el repositorio
```bash
git clone https://github.com/glasswingHub/glasswing-connect
cd glasswing-connect
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Instalar dependencias JavaScript
```bash
npm install
```

### 4. Configurar variables de entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar la base de datos
```bash
# Crear la base de datos SQLite (se crea automáticamente)
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate

# Opcional: Ejecutar seeders para datos de prueba
php artisan db:seed
```

### 6. Configurar OAuth de Google (opcional)
Si deseas usar autenticación con Google, agrega las siguientes variables a tu archivo `.env`:
```env
GOOGLE_CLIENT_ID=tu-client-id
GOOGLE_CLIENT_SECRET=tu-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## 🏃‍♂️ Ejecutar el Proyecto

### Opción 1: Comando único (recomendado)
```bash
composer run dev
```
Este comando ejecuta simultáneamente:
- Servidor de desarrollo Laravel (puerto 8000)
- Cola de trabajos
- Logs en tiempo real
- Vite dev server

### Opción 2: Comandos separados
```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Vite dev server
npm run dev

# Terminal 3: Cola de trabajos (opcional)
php artisan queue:work
```

### 3. Acceder a la aplicación
Abre tu navegador y ve a: `http://localhost:8000`

## 🧪 Testing

```bash
# Ejecutar todos los tests
composer test

# Ejecutar tests específicos
php artisan test --filter=NombreDelTest
```

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Crear un nuevo controlador
php artisan make:controller NombreController

# Crear un nuevo modelo con migración
php artisan make:model NombreModelo -m

# Crear un nuevo seeder
php artisan make:seeder NombreSeeder

# Ejecutar migraciones
php artisan migrate

# Revertir migraciones
php artisan migrate:rollback

# Ver rutas disponibles
php artisan route:list
```

## 📝 Notas de Desarrollo

- El proyecto usa **Inertia.js** para crear una SPA sin necesidad de una API separada
- **Tailwind CSS** está configurado para estilos utilitarios
- **Laravel Sanctum** maneja la autenticación


