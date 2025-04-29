# Financiacel - Prueba Técnica

Este repositorio contiene el código fuente para la prueba técnica de Financiacel, que incluye un backend desarrollado con Laravel y un frontend desarrollado con Angular.

## Contenido del Repositorio

El repositorio está organizado de la siguiente manera:

* `backend/`: Contiene el código fuente del backend de la aplicación, desarrollado con Laravel.
* `frontend/`: Contiene el código fuente del frontend de la aplicación, desarrollado con Angular.

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu sistema:

* **PHP** (versión 8.1 o superior recomendada)
* **Composer** (para la gestión de dependencias de PHP)
* **Node.js** (versión 18 o superior recomendada)
* **npm** (gestor de paquetes de Node.js, generalmente se instala con Node.js)
* **Git** (para la clonación del repositorio)
* **Una base de datos compatible con Laravel** (MySQL)

## Instalación

Sigue estos pasos para instalar y configurar la aplicación:

1.  **Clonar el Repositorio:**
    ```bash
    git clone https://github.com/aserratodev/financiacel-prueba-tecnica
    cd financiacel-prueba-tecnica
    ```

2.  **Instalar las Dependencias del Backend (Laravel):**
    ```bash
    cd backend
    composer install
    ```

3.  **Configurar la Base de Datos del Backend:**
    
    * Abre el archivo `.env` y configura las credenciales de tu base de datos (DB\_CONNECTION, DB\_HOST, DB\_PORT, DB\_DATABASE, DB\_USERNAME, DB\_PASSWORD).

4.  **Generar la Key de la Aplicación Laravel:**
    ```bash
    php artisan key:generate
    ```

5.  **Ejecutar las Migraciones de la Base de Datos:**
    ```bash
    php artisan migrate
    ```
    * **Nota importante sobre las migraciones:** La tabla `credit_applications` se crea en dos pasos. La migración inicial crea la tabla con los campos básicos, y una migración posterior (`add_monthly_payment_to_credit_applications_table.php`) añade la columna `monthly_payment`. Laravel ejecutará estas migraciones en el orden correcto según su fecha de creación. Asegúrate de que ambas migraciones se ejecuten para tener la estructura completa de la tabla `credit_applications`.

6.  **Ejecutar los Seeders de la Base de Datos:**
    ```bash
    php artisan db:seed --class=ClientSeeder
    php artisan db:seed --class=PhoneSeeder
    ```
    * Estos comandos insertarán datos de prueba en las tablas `clients` y `phones`. También puedes ejecutar `php artisan db:seed` para ejecutar todos los seeders definidos en `DatabaseSeeder.php`.

7.  **Instalar las Dependencias del Frontend (Angular):**
    ```bash
    cd ../frontend
    npm install
    ```

## Ejecución

Sigue estos pasos para ejecutar la aplicación:

1.  **Iniciar el Servidor de Desarrollo del Backend (Laravel):**
    ```bash
    cd ../backend
    php artisan serve
    ```
    Esto iniciará el servidor de desarrollo de Laravel en `http://127.0.0.1:8000`.

2.  **Iniciar la Aplicación Frontend (Angular):**
    ```bash
    cd ../frontend
    ng serve -o
    ```
    Esto construirá y abrirá la aplicación Angular en tu navegador (generalmente en `http://localhost:4200`). Asegúrate de que el servidor del backend esté también en ejecución. La interfaz permitirá seleccionar un cliente, elegir un modelo de celular y un plazo, ver la simulación de la cuota mensual y confirmar la solicitud de crédito.

## Documentación de Endpoints (Backend - Laravel)

Los endpoints principales del backend:

* `GET /api/clients`: Retorna una lista de todos los clientes.
* `GET /api/phones`: Retorna una lista de todos los modelos de celular (incluyendo su `id`, `model`, y `price`).
* `POST /api/credits`: Recibe los datos de la solicitud de crédito (`client_id`, `phoneId`, `term`) y la guarda en la base de datos, calculando la `monthly_payment` en el backend. Retorna una respuesta JSON indicando el éxito o el error de la creación, incluyendo un mensaje específico si el cliente ya tiene una solicitud activa. El estado inicial de la solicitud se podría incluir en la respuesta de éxito.
* `GET /api/credits/{id}`: Retorna los detalles de una solicitud de crédito específica, incluyendo su estado, monto, plazo y las referencias a cliente y teléfono.
* `GET /api/credits/{credit_application_id}/installments`: Retorna una lista de las cuotas asociadas a una solicitud de crédito específica, incluyendo el número de cuota, el monto y la fecha de vencimiento.

## Tests



## Notas Adicionales

* Se implementó una validación en el backend para evitar que un cliente cree múltiples solicitudes de crédito activas.
* La simulación de la cuota mensual se realiza tanto en el frontend (para una vista previa) como en el backend (para el cálculo final al guardar la solicitud).

