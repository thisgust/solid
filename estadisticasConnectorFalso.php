<?php
class EstadisticasConnectorFalso implements InterfazEstadisticasConnector {

    private bool $disponible = true;

    private array $ventasRecibidas = [];

    public function setDisponible(bool $disponible) {
        $this->disponible = $disponible;
    }

    public function enviarEstadisticas(DTOVenta $venta) {

        if (!$this->disponible) throw new Exception("El sistema de estadisticas no esta disponible");

        $clave = $venta->proveedor . '-' . $venta->ventaId;

        if (isset($this->ventasRecibidas[$clave])) return;

        $datos = [
            'ventaId' => $venta->ventaId,
            'cantidadProductos' => array_sum(array_column($venta->productos, 'cantidad')),
            'importeTotal' => $venta->importeTotal
        ];

        $this->ventasRecibidas[$clave] = $datos;

        echo "Estadisticas recibidas: ";
        echo json_encode($datos) . PHP_EOL;
    }

    public function obtenerVentasRecibidas() {
        return $this->ventasRecibidas;
    }
}
?>
