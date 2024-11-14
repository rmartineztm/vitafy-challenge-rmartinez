# Instrucciones para Poner en Marcha el Proyecto y Realizar Pruebas

## Tabla de Contenidos

- [Instalación](#instalación)
- [Configuración](#configuración)
- [Ejecución del Proyecto](#ejecución-del-proyecto)
- [Pruebas](#pruebas)
- [Diseño e Implementación](#diseño-e-implementación)

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/rmartineztm/vitafy-challenge-rmartinez.git
   cd vitafy-challenge-rmartinez

2. **Instalar dependencias**
   ```bash
   composer install

## Configuración

1. **Configurar el archivo .env**
Edita el archivo .env para configurar los detalles de la base de datos y otros parámetros del entorno.
    ```bash
    cp .env.example .env

2. **Generar la clave de la aplicación**
    ```bash
    php artisan key:generate

3. **Configurar la base de datos**
    ```bash
    php artisan migrate

## Ejecución del Proyecto

1. **Iniciar el servidor**
   ```bash
   php artisan serve

## Pruebas
Utiliza Postman para probar los endpoints del CRUD. Importa la colección de Postman incluida para probar los siguientes endpoints:

Crear Lead: POST /api/leads

    {
    "name": "Ramiro Martinez",
    "email": "ramiro@mail.com",
    "phone": "1234567890"
    }    

Cada lead se crea con un cliente asociado y se le asigna un score mediante el servicio LeadScoringService.    

## Diseño e Implementación

1. **CRUD para Leads**
El create de leads se implementó en LeadController, gestionando la operación de alta requerida.

2. **Asignación de UUIDs**
Cada lead y cliente tiene un UUID generado automáticamente usando el trait HasUuid. El trait se usa para cada instancia del modelo Lead.

3. **Creación Automática de Clientes**
Al crear un lead, se genera automáticamente un cliente vinculado mediante lead_id como clave foránea. Esta lógica está en el LeadController y asegura que cada lead esté asociado a un cliente.

4. **Servicio de Scoring**
LeadScoringService se encarga de asignar un score a cada lead creado. El servicio es independiente, lo que permite una fácil modificación en la lógica de scoring o en futuras integraciones con servicios externos. Actualmente devuelve un valor random para simular la respuesta de la API externa.