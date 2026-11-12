<?php
class VentaRepository {

    private array $ventas = [];

    private function clave(DTOVenta $venta) {
        return $venta->proveedor . '-' . $venta->ventaId;
    }

    public function existe(DTOVenta $venta) {
        return isset($this->ventas[$this->clave($venta)]);
    }

    public function guardar(DTOVenta $venta) {

        $clave = $this->clave($venta);

        if (isset($this->ventas[$clave])) return false;

        $this->ventas[$clave] = [
            'venta' => $venta,
            'estadoStock' => 'PENDIENTE',
            'estadoEstadisticas' => 'PENDIENTE',
            'intentosStock' => 0,
            'intentosEstadisticas' => 0
        ];

        return true;
    }

    public function obtener(DTOVenta $venta) {
        return $this->ventas[$this->clave($venta)] ?? null;
    }

    public function obtenerTodas() {
        return $this->ventas;
    }

    public function registrarIntento(DTOVenta $venta, string $destino) {

        $clave = $this->clave($venta);

        if ($destino === 'stock') {
            $this->ventas[$clave]['intentosStock']++;
        } else {
            $this->ventas[$clave]['intentosEstadisticas']++;
        }
    }

    public function actualizarEstado(DTOVenta $venta, string $destino, string $estado) {

        $clave = $this->clave($venta);

        if ($destino === 'stock') {
            $this->ventas[$clave]['estadoStock'] = $estado;
        } else {
            $this->ventas[$clave]['estadoEstadisticas'] = $estado;
        }
    }

    public function obtenerPendientes(string $destino) {

        $campo = $destino === 'stock' ? 'estadoStock' : 'estadoEstadisticas';

        return array_filter($this->ventas, fn($registro) => $registro[$campo] === 'PENDIENTE');
    }
}
?>
