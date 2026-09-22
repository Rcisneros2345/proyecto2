---
decision: D-004
modulo: HUELLAS
fecha: 2026-09-19
estado: DECIDIDA
decidido_por: team-lead
---

# D-004 · copyFingerprint: ¿solo hardware o hardware + estado local?

## El problema en una frase

`copyFingerprint` copia una huella de un dispositivo a otro vía `ZktecoService::uploadFingerprint`. El servicio NO escribe BD — solo lee templates y los sube al hardware. Pero si no escribimos estado local, la pestaña de huellas del dispositivo destino no muestra la copia hasta la próxima extracción.

## Opciones

### Opción A — Solo hardware (simple)
- **Qué implica:** llamar `uploadFingerprint`, devolver success/error. Sin tocar BD.
- **Cuesta:** ~30 min
- **Gana:** consistente con el contrato del servicio; sin riesgo de template mal almacenado
- **Pierde:** la UI del destino no muestra la copia hasta el próximo sync
- **Reversible:** sí, trivial

### Opción B — Hardware + estado local (consistente)
- **Qué implica:** tras éxito en hardware, upsert `Fingerprint` con `device_id = destino` + actualizar `fingerprint_count` del pivot destino, en `DB::transaction()`.
- **Cuesta:** ~1 hora
- **Gana:** consistente con el patrón de Oleada 1 (deleteFingerprint, removeFromDevice); la UI refleja la copia inmediatamente
- **Riesgo:** template guardado con header del origen (se reescribe en cada upload vía `forDevice`, así que funciona pero es dato "sucio")
- **Reversible:** sí

## Decisión

**B.** copyFingerprint mueve el mismo tipo de dato entre las mismas tablas que deleteFingerprint. Omitir el estado local repite el bug de fondo que motivó toda la Oleada 1 — la UI dice "copiado" y el dato local no está ahí.

## Implementación

Tras éxito en `$service->uploadFingerprint($employee, $fingerprint)`:
1. Upsert `Fingerprint` con `device_id = destino`, `employee_id`, `finger`, `template_hash`
2. Actualizar `fingerprint_count` del pivot `device_employee` destino
3. Todo dentro de `DB::transaction()`
