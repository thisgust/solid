<?php
class StockConnectorFalso implements InterfazStockConnector {

    private bool $disponible = true;

    private array $movimientos = [];

    public function setDisponible(bool $disponible) {
        $this->disponible = $disponible;
    }

    public function registrarMovimiento(DTOMovimiento $movimiento) {

        if (!$this->disponible) throw new Exception("No se pudo alcanzar al ERP");

        $clave = $movimiento->referencia;

        if (isset($this->movimientos[$clave])) {
            echo "Movimiento ya registrado: " . $clave . PHP_EOL;
            return;
        }

        $this->movimientos[$clave] = $movimiento->convertirAArreglo();

        echo "Movimiento registrado correctamente: ";
        echo json_encode($movimiento->convertirAArreglo()) . PHP_EOL;
    }

    public function obtenerMovimientos() {
        return $this->movimientos;
    }
}
?>
