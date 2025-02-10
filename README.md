# 🚀 API de Instrumentos de DONKI - NASA

Este proyecto es una API desarrollada en **Laravel** que consume la API **DONKI** de la **NASA** para obtener información sobre instrumentos utilizados en eventos solares y su porcentaje de uso.

## 📌 Requisitos Previos

Antes de instalar el proyecto, asegúrate de tener instalado:

- **PHP 8.2**
- **Composer**
- **Laravel 11.31**
- **Git**
- **Postman** (para probar las rutas con la colección de pruebas)

---

## 🔧 **Instalación del Proyecto**

```sh
# 1️⃣ Clona este repositorio  
git clone https://github.com/apizarro1204/API_NASA.git
cd API_NASA

# 2️⃣ Instala las dependencias con Composer  
composer install

# 3️⃣ Copia el archivo de entorno y configura la API KEY  
cp .env.example .env

# 4️⃣ Genera la clave de la aplicación  
php artisan key:generate

# 5️⃣ Obtén tu API Key de la NASA en:  
# 🔗 https://api.nasa.gov/

# 6️⃣ Una vez te llegue tu API KEY a tu correo, agregalo como variable de entorno en .env  
NASA_API_KEY=TU_API_KEY_AQUI

# 7️⃣ Ejecuta el servidor local  
php artisan serve
```

## 🚀 Ahora podrás realizar consultas por medio de Postman.

```sh
# Importa el archivo "API NASA.postman_collection.json"

