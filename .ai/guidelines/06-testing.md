# 06 · Pruebas

PHPUnit (no Pest). `php artisan test --compact`, con `--filter` para lo mínimo necesario.
**No elimines pruebas existentes**: son parte del sistema.

## Regla

Un cambio sin prueba que lo respalde no está terminado, y la prueba debe **fallar** si se
revierte el cambio. Verifícalo leyendo el assert, no el nombre.

## Prioridad real

1. **Feature sobre rutas**: cada ruta tocada, con y sin permiso de módulo. Es lo que más
   protege a esta base.
2. **Idempotencia** de sincronizaciones (Firebird y dispositivos).
3. **Fallo a mitad**: cortar en el ítem N y reanudar.
4. **Autorización**: sin permiso → 403, no 200 con el botón oculto.
5. **Unit** solo para lógica con reglas propias (`HorarioResolver`, `KardexCalculator`,
   conciliación, normalización de claves).

## Usa route() en las pruebas

```php
$this->actingAs($usuario)
    ->post(route('devices.sync', $device))   // route(), nunca la URL a mano
    ->assertForbidden();
```

Así la suite también vigila el contrato: si alguien borra la ruta, la prueba truena.

## Dobles obligatorios

Gateway de dispositivos y fuente Firebird siempre con doble. `Queue::fake()` para comprobar
el despacho; ejecución real para comprobar el efecto del Job.

## Base de pruebas

`RefreshDatabase` sobre una base dedicada. Revisa `phpunit.xml` / `.env.testing` antes de
correr nada: nunca apuntes la suite a desarrollo o producción.

## Prueba del contrato

`tests/Feature/ContratoDeRutasTest.php` compara las rutas actuales contra
`.ai/baseline/routes.json` y falla si desapareció alguna o cambió su URI. Es la red de
seguridad que corre sola en cada `php artisan test`.
