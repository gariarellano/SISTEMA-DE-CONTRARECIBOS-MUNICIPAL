# Sistema de Contrarecibos Municipal

Sistema web para la gestión de contrarecibos del municipio, desarrollado en PHP con una arquitectura MVC.

## Requisitos

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Instalación y Configuración

El proyecto incluye una configuración de Docker para facilitar el despliegue del entorno de desarrollo.

1.  **Clonar el repositorio** (si aún no lo has hecho):
    ```bash
    git clone <url-del-repositorio>
    cd sistema-de-facturacion
    ```

2.  **Iniciar los contenedores**:
    Ejecuta el siguiente comando para construir y levantar los servicios (aplicación PHP, base de datos MySQL y phpMyAdmin):
    ```bash
    docker-compose up -d --build
    ```

3.  **Acceder a la aplicación**:
    - **Sistema**: Abre tu navegador y visita `http://localhost:8080`.
    - **phpMyAdmin**: Para gestionar la base de datos, visita `http://localhost:8000` (Usuario: `root`, Contraseña: `root`).

## Estructura del Proyecto

El proyecto sigue el patrón de diseño Modelo-Vista-Controlador (MVC):

-   **controllers/**: Contiene la lógica de negocio y manejo de peticiones (e.g., `FacturasController.php`, `AdminController.php`).
-   **models/**: Clases que interactúan con la base de datos (e.g., `Factura.php`, `Usuario.php`).
-   **views/**: Archivos frontend (HTML/PHP) para la presentación.
-   **public/**: Archivos estáticos públicos.
-   **config.php**: Configuración de la base de datos y constantes globales.
-   **index.php**: Punto de entrada de la aplicación y enrutador simple.

## Configuración

La configuración de la conexión a la base de datos se encuentra en `config.php`. Por defecto, está configurado para funcionar con el servicio `mysql-db` definido en `docker-compose.yml`.

## Base de Datos

El archivo `huetamoc_backup.sql` en la raíz contiene un respaldo inicial o estructura de la base de datos que puede ser importado si es necesario.

## Créditos

Desarrollado por **HAYDEE BARRERA SANTACRUZ**, alumna de **INGENIERÍA EN SISTEMAS COMPUTACIONALES** en el **TECNOLOGICO SUPERIOR DE HUETAMO**.
