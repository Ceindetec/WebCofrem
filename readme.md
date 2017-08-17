<h1>Proyecto Tarjeta Cofrem</h1>

<h3>Intalaciòn de repositorio</h3>

Luego de tener clonado el repositorio, es necesario ejecutar los siguientes pasasos:

<h4> Actualizar composer </h4>

<pre>composer update</pre>

<h4>Generar el archivo de configuracion .env</h4>

<pre>copy .env.example .env</pre>

<h4>Generar la key de laravel </h4>

<pre>php artisan key:generate</pre>

<h4>Correr migraciones</h4>

<pre>php artisan migrate --seed</pre>




