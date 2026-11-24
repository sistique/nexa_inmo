# inmuebles
Administracion de inmobiliarias

## Relaciones de agentes

Las secciones `inm_rel_agente_proveedor` e `inm_rel_agente_empleado` administran las relaciones
de agentes con proveedores y empleados desde el menú Clientes. El instalador de inmuebles crea sus
tablas y permisos; antes deben existir las tablas `com_agente`, `gt_proveedor` y `em_empleado`.
Estas relaciones no limitan cuántos proveedores o empleados puede tener un agente, ni cuántos
agentes pueden vincularse a cada proveedor o empleado.

Para comprobar los formularios y consultas de listado con la base configurada, ejecutar
`NEXA_TEST_READONLY_DB=1 vendor/bin/phpunit tests/controllers/inm_rel_agenteTest.php`.
Esta prueba solo realiza consultas y requiere las tablas y permisos instalados.

## Expedientes PDF

La agrupación de documentos de cliente y ubicación requiere `qpdf` disponible en el `PATH` del proceso PHP
y `proc_open` habilitado. Los PDF se unen en el orden configurado en `envia_correo_bd`; un fallo o advertencia
de `qpdf`, incluido un PDF cifrado sin contraseña, impide el envío y se informa como error.

El expediente combinado es un documento nuevo: conserve los PDF originales, ya que las firmas digitales de
estos no se trasladan como firmas válidas del expediente combinado.
