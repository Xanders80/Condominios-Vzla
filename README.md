<p style="text-align: center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo de Laravel"></a></p>

<p style="text-align: center">
Framework PHP para Artesanos Web
</p>

<h2 style="text-align: center"> Master Principal </h2>
<h3 style="text-align: center">( Sistema de Gestión de Condominios usando Generador de Crud )</h3>
<p style="text-align: center">
Master Principal es un un sistema de administración de condomionios usando un generador de CRUD para proyectos de Laravel. Este proyecto fue creado para facilitar a los desarrolladores la creación de proyectos en Laravel. Este proyecto está construido con Laravel 11 y Bootstrap 5.
</p>
<p style="text-align: center">
Hecho con ❤️ por <a href="https://www.linkedin.com/in/xanders-san-a477ab310/" target="_blank">Xanders80</a>
</p>

## Requisitos

- Laravel 11 o superior
- PHP 8.2 o superior
- MySQL 5.7 o superior o cualquier otra base de datos
- Composer 2.2.* o superior

## Características Principales
- [x] Inicio de sesión con autenticación (correo electrónico y contraseña)
- [x] CRUD con solicitud ajax
- [x] gestión de roles y permisos
- [x] Notificación en la barra lateral
- [x] Notificación en el encabezado
- [x] Crear un seeder de menú y acceder al menú utilizando el comando `app:convert-menu` de php artisan.
- [x] Archivo Morph
- [x] Menú Predeterminado
    - [x] Tablero
    - [x] Menú con submenú (multi nivel)
    - [x] Gestión de Roles
      - [x] Grupo de Acceso
      - [x] Nivel de Acceso
      - [x] Acceso al Menú
    - [x] Preguntas Frecuentes
    - [x] gestión de usuarios
    - [x] Anuncio

## Cómo instalar
```bash
# Desde Packagist
$ composer create-project arwp/main-master {tu-nombre-de-proyecto}
# ---- O -----
# Clona el repositorio
$ git clone https://github.com/arwahyu01/main-master.git {tu-nombre-de-proyecto}
$ cd main-master
$ composer install
$ cp .env.example .env
$ php artisan key:generate
$ php artisan migrate --seed
$ php artisan serve # o usa valet
```

## Script Personalizado
#### Para Datatables
- usa este script para enviar múltiples datos a 'datatable.blade.js'
```
    <script type="application/javascript">
        fetch("{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}", {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({id: "{{ $id }}"})
        })
        .then(e => e.text())
        .then(r => {
            Function('"use strict";\n' + r)();
        }).catch(e => console.log(e));
    </script>
```
- `JSON.stringify({'id': "{{ $id }}",'id2': "{{ $id2 }}"})` fpara solicitud múltiple
- `JSON.stringify({id: "{{ $id }}"})` para solicitud única
- Agrega $id, en el archivo datatable.blade.js de esta manera :
```
    $('#datatable').DataTable({
        ajax: `{{ url(config('master.app.url.backend').'/'.$url.'/data?id='${id}') }}`,
    });
```

## Características para desarrolladores (Constructor de MVC) :
Instala este paquete en tu proyecto de laravel
```bash
composer require arwp/mvc
```
#### No olvides configurar la configuración, lee más [aquí](https://github.com/arwahyu01/mvc-builder)
### Cómo usar este paquete :
  - Ejecuta php artisan make:mvc [nombre] en tu terminal para crear un módulo
    - [x] Controlador (con función CRUD)
    - [x] Modelo (con fillable y relación)
    - [x] Migración (con tabla y relación)
    - [x] vistas (con función CRUD)
    - [x] rutas 
  - Ejecuta php artisan migrate para crear la tabla
    - agrega un nuevo menú en la tabla de menú
    - agrega acceso al menú en la tabla de acceso al menú
  - Ejecuta `php artisan delete:mvc [name]` Para eliminar un módulo (eliminar todos los archivos y tablas en la base de datos)

## Licencia
- Paquete MVC Builder: Este paquete se ofrece sin licencia, lo que lo hace gratuito para usar en proyectos personales.
- Plantilla xanders80: La plantilla xanders80 utilizada para las vistas en este paquete no es gratuita. Necesitarás comprar una licencia para uso comercial [aquí](https://themeforest.net/item/admins -responsive-bootstrap-admin-template-dashboard/29365133).
- Derechos de autor y atribución: Por favor, respeta los derechos de autor del paquete y sus contribuyentes. No elimines los créditos incluidos en los archivos.

#### ¡Espero que este Constructor de MVC haga que tu proceso de desarrollo sea más rápido y fácil! 😊
