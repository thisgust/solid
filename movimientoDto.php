<?php
class DTOMovimiento {
        public function __construct(public string $tipoMovimiento, public string $fecha, public string $referencia, public array $productos) {}

        public function convertirAArreglo() {
            return ['tipoMovimiento' => $this->tipoMovimiento,
                    'fecha' => $this->fecha,
                    'referencia' => $this->referencia,
                    'productos' => $this->productos];
        }
}
?>
