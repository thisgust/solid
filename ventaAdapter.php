<?php
class VentaAdapter {
	public function adaptarA(array $datos) {

        if (!isset($datos['ventaId'], $datos['fecha'], $datos['cajeroId'], $datos['productos'])) throw new Exception("El formato de la venta tipo A es invalido");

        return new DTOVenta(
            ventaId: (string) $datos['ventaId'],
            fecha: $datos['fecha'],
            cajeroId: (int) $datos['cajeroId'],
            productos: $datos['productos'],
            proveedor: 'A',
            importeTotal: (float) ($datos['importeTotal'] ?? 0)
        );
    }

	public function adaptarA2(array $datos) {

        if (!isset($datos['id'], $datos['timestamp'], $datos['items'])) throw new Exception("El formato de la venta tipo A2 es invalido");

        $productos = [];

        foreach ($datos['items'] as $item) {
            if (!isset($item['sku'], $item['units'])) throw new Exception("El producto tipo A2 es invalido");

            $productos[] = [
                'codigo' => $item['sku'],
                'cantidad' => (int) $item['units']
            ];
        }

        return new DTOVenta(
            ventaId: (string) $datos['id'],
            fecha: $datos['timestamp'],
            cajeroId: null,
            productos: $productos,
            proveedor: 'A2',
            importeTotal: (float) ($datos['importeTotal'] ?? 0)
        );
    }
}
?>
