# inmuebles
Administracion de inmobiliarias

## Expedientes PDF

La agrupación de documentos de cliente y ubicación requiere `qpdf` disponible en el `PATH` del proceso PHP
y `proc_open` habilitado. Los PDF se unen en el orden configurado en `envia_correo_bd`; un fallo o advertencia
de `qpdf`, incluido un PDF cifrado sin contraseña, impide el envío y se informa como error.

El expediente combinado es un documento nuevo: conserve los PDF originales, ya que las firmas digitales de
estos no se trasladan como firmas válidas del expediente combinado.
