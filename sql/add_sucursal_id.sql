-- Script: add_sucursal_id.sql
-- Agrega la columna sucursal_id a tablas relevantes y crea índices y FK opcionales.
-- Recomendado: ejecutar en entorno de pruebas antes de producción. Hacer backup.

BEGIN;

-- Agregar columnas (permite NULL inicialmente)
ALTER TABLE tipo ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;
ALTER TABLE categoria ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;
ALTER TABLE escala ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;
ALTER TABLE dimension ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;
ALTER TABLE venta ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;
ALTER TABLE deuda ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;
ALTER TABLE pago ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;

-- Si existe tabla categoria_articulo (antigua), agregar también
ALTER TABLE IF EXISTS categoria_articulo ADD COLUMN IF NOT EXISTS sucursal_id INTEGER;

-- Índices para acelerar consultas por sucursal
CREATE INDEX IF NOT EXISTS idx_tipo_sucursal_id ON tipo(sucursal_id);
CREATE INDEX IF NOT EXISTS idx_categoria_sucursal_id ON categoria(sucursal_id);
CREATE INDEX IF NOT EXISTS idx_escala_sucursal_id ON escala(sucursal_id);
CREATE INDEX IF NOT EXISTS idx_dimension_sucursal_id ON dimension(sucursal_id);
CREATE INDEX IF NOT EXISTS idx_venta_sucursal_id ON venta(sucursal_id);
CREATE INDEX IF NOT EXISTS idx_deuda_sucursal_id ON deuda(sucursal_id);
CREATE INDEX IF NOT EXISTS idx_pago_sucursal_id ON pago(sucursal_id);
CREATE INDEX IF NOT EXISTS idx_categoria_articulo_sucursal_id ON categoria_articulo(sucursal_id);

COMMIT;


-- Opcional: popular registros existentes con una sucursal por defecto (reemplazar 1)
-- UPDATE tipo SET sucursal_id = 1 WHERE sucursal_id IS NULL;
-- UPDATE categoria SET sucursal_id = 1 WHERE sucursal_id IS NULL;
-- UPDATE escala SET sucursal_id = 1 WHERE sucursal_id IS NULL;
-- UPDATE dimension SET sucursal_id = 1 WHERE sucursal_id IS NULL;
-- UPDATE venta SET sucursal_id = (SELECT sucursal_id FROM usuario WHERE usuario.id = venta.usuario_id) WHERE sucursal_id IS NULL;
-- UPDATE deuda SET sucursal_id = (SELECT us.sucursal_id FROM usuario us JOIN venta v ON us.id = v.usuario_id WHERE v.id = deuda.id_venta) WHERE sucursal_id IS NULL;
-- UPDATE pago SET sucursal_id = (SELECT us.sucursal_id FROM usuario us JOIN venta v ON us.id = v.usuario_id WHERE v.id = pago.id_venta) WHERE sucursal_id IS NULL;
-- UPDATE categoria_articulo SET sucursal_id = 1 WHERE sucursal_id IS NULL;

-- Opcional: establecer NOT NULL y DEFAULT
-- ALTER TABLE tipo ALTER COLUMN sucursal_id SET DEFAULT 1;
-- ALTER TABLE tipo ALTER COLUMN sucursal_id SET NOT NULL;

-- Opcional: agregar FK si existe tabla sucursal(id)
-- ALTER TABLE tipo ADD CONSTRAINT fk_tipo_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursal(id);
-- Repetir para categoria, escala, dimension si se desea.
