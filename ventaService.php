<?php
class VentaService {
    private int $maxIntentos = 5;

    public function __construct(private InterfazStockConnector $conector, private InterfazEstadisticasConnector $estadisticas, private VentaRepository $repositorio) {}

    public function procesar(DTOVenta $venta) {
        $this->validarVenta($venta);

        if (!$this->repositorio->guardar($venta)) {
            echo "Esta venta ya fue procesada anteriormente." . PHP_EOL;

            return $this->repositorio->obtener($venta);
        }

        $this->procesarStock($venta);
        $this->procesarEstadisticas($venta);

        return $this->repositorio->obtener($venta);
    }

    private function validarVenta(DTOVenta $venta) {
        if (empty($venta->productos)) throw new Exception("La venta no puede estar vacia");

        foreach ($venta->productos as $producto) {
            if (!isset($producto['codigo'], $producto['cantidad'])) throw new Exception("Cada producto debe tener un codigo y una cantidad");

            if ($producto['cantidad'] <= 0) throw new Exception("La cantidad debe ser mayor a cero");
        }
    }

    private function procesarStock(DTOVenta $venta) {
        $this->repositorio->registrarIntento($venta, 'stock');

        $intentos = $this->repositorio->obtener($venta)['intentosStock'];

        try {
            $productos = array_map(
                function(array $producto) {
                    return [
                        'codigoProducto' => $producto['codigo'],
                        'cantidad' => $producto['cantidad']
                    ];
                },
                $venta->productos
            );

            $movimiento = new DTOMovimiento(
                tipoMovimiento: 'SALIDA',
                fecha: $venta->fecha,
                referencia: 'VENTA-' . $venta->proveedor . '-' . $venta->ventaId,
                productos: $productos
            );

            $this->conector->registrarMovimiento($movimiento);
            $this->repositorio->actualizarEstado($venta, 'stock', 'PROCESADO');

            echo "Stock procesado correctamente." . PHP_EOL;
        } catch (Throwable $e) {
            $estado = $intentos >= $this->maxIntentos ? 'FALLIDO' : 'PENDIENTE';

            $this->repositorio->actualizarEstado($venta, 'stock', $estado);

            echo "Error en stock: " . $e->getMessage() . PHP_EOL;
        }
    }

    private function procesarEstadisticas(DTOVenta $venta) {

        $this->repositorio->registrarIntento($venta, 'estadisticas');

        $intentos = $this->repositorio->obtener($venta)['intentosEstadisticas'];

        try {
            $this->estadisticas->enviarEstadisticas($venta);
            $this->repositorio->actualizarEstado($venta, 'estadisticas', 'PROCESADO');

            echo "Estadisticas procesadas correctamente." . PHP_EOL;
        } catch (Throwable $e) {
			$estado = $intentos >= $this->maxIntentos ? 'FALLIDO' : 'PENDIENTE';

            $this->repositorio->actualizarEstado($venta, 'estadisticas', $estado);

            echo "Error en estadisticas: " . $e->getMessage() . PHP_EOL;
        }
    }

	public function reintentarPendientes() {
		$pendientesStock = $this->repositorio->obtenerPendientes('stock');

        foreach ($pendientesStock as $registro) {
            $venta = $registro['venta'];

            if ($registro['intentosStock'] < $this->maxIntentos) $this->procesarStock($venta);
        }

        $pendientesEstadisticas = $this->repositorio->obtenerPendientes('estadisticas');

        foreach ($pendientesEstadisticas as $registro) {
            $venta = $registro['venta'];

            if ($registro['intentosEstadisticas'] < $this->maxIntentos) $this->procesarEstadisticas($venta);
        }
    }

    public function consultarVentas() {
        return $this->repositorio->obtenerTodas();
    }
}
?>
