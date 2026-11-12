<?php
class VentaController {
    public function __construct(private VentaService $servicio, private VentaAdapter $adapter) {}

    public function procesar() {
        header('Content-Type: application/json');
        $accion = $_GET['accion'] ?? 'procesar';

        try {
            if ($accion === 'estado') {
                echo json_encode($this->servicio->consultarVentas(), JSON_PRETTY_PRINT);

                return;
            }

            if ($accion === 'reintentar') {
                $this->servicio->reintentarPendientes();

                echo json_encode([
                    'mensaje' => 'Reintentos finalizados',
                    'ventas' => $this->servicio->consultarVentas()
                ]);

                return;
            }

            $datos = json_decode(file_get_contents("php://input"), true);

            if (!is_array($datos)) throw new Exception("JSON invalido");

            if (isset($datos['ventaId'])) {
                $venta = $this->adapter->adaptarA($datos);
            } elseif (isset($datos['id'])) {
                $venta = $this->adapter->adaptarA2($datos);
            } else {
                throw new Exception("No se pudo identificar el proveedor");
            }

            $resultado = $this->servicio->procesar($venta);

            http_response_code(200);

            echo json_encode([
                'mensaje' => 'Venta recibida',
                'resultado' => $resultado
            ], JSON_PRETTY_PRINT);

        } catch (Throwable $e) {
            http_response_code(400);

            echo json_encode(['mensaje' => $e->getMessage()]);
        }
    }
}
?>
