<?php
class DTOVenta {

    public function __construct(public string $ventaId, public string $fecha, public ?int $cajeroId, public array $productos, public string $proveedor = 'A', public float $importeTotal = 0.0) {}
}
?>
