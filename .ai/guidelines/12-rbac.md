# 12 · Permisos, módulos y menú

## Cómo funciona hoy

```
Usuario → grupos de permisos (se acumulan con OR) → permisos por módulo/acción
       → PermissionResolver (con caché) → middleware module_permission:<modulo>,<accion>
       → menú (NavigationItem + AdminLayoutComposer)
```

`EnsureAdmin` protege superficies administrativas y **no equivale** a permiso de módulo.

## Invariantes

1. Ocultar el botón **no es autorizar**. La comprobación va en el servidor.
2. Toda ruta de escritura declara su `module_permission` (o su Policy).
3. Al asignar o quitar grupos (desde usuarios, empleados o profesores) **se invalida la
   caché**. Si no, el menú y el middleware mienten hasta el siguiente ciclo.
4. Una clave de permiso es contrato público: `academia.ciclos,activo` aparece en la ruta,
   el seeder, el menú y la matriz de pruebas. Renombrarla rompe las cuatro a la vez.

## Checklist para una acción nueva (todo en el mismo cambio)

- [ ] Ruta con `->middleware('module_permission:<modulo>,<accion>')` y `->name()`
- [ ] Permiso sembrado y visible en la pantalla de permisos
- [ ] `NavigationItem` si va al menú, con su permiso
- [ ] Policy del recurso si aplica
- [ ] Prueba: con permiso 200 · sin permiso 403 · sin sesión redirige
- [ ] `php scripts/impacto.php` (menú, composer del layout, caché)
- [ ] `php scripts/verificar_referencias.php` (avisa de permisos de ruta sin rastro en seeders)

## Pruebas de referencia

`AcademiaRbacMatrixTest`, `RequireModulePermissionMiddlewareTest`,
`PermissionResolverCacheTest`, `UserGroupAssignmentTest`, `UserPermissionInheritanceTest`,
`ModulePermissionSeederTest`, `PermissionGroupPermissionsTest`.

## Deuda conocida

La auditoría del 2026-09-16 señala que faltan acciones finas y policies de subrecursos en
grupos, asistencia y horarios. Complétalas de forma incremental, con su prueba, no de golpe.
