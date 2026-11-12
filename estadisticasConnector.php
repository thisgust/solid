<?php
class EstadisticasConnector implements InterfazEstadisticasConnector {

    private string $url;

    public function __construct(string $url = 'http://localhost:3001/api/estadisticas/ventas') {
        $this->url = $url;
    }

    public function enviarEstadisticas(DTOVenta $venta) {

        $clave = $venta->proveedor . '-' . $venta->ventaId;

        $datos = [
            'ventaId' => $venta->ventaId,
            'cantidadProductos' => array_sum(
                array_column($venta->productos, 'cantidad')
            ),
            'importeTotal' => $venta->importeTotal
        ];

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Idempotency-Key: ' . $clave]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));

        $respuesta = curl_exec($ch);

        if ($respuesta === false) {

			$error = curl_error($ch);

            curl_close($ch);

            throw new Exception("Error al comunicarse con estadisticas: " . $error);
        }

        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($codigo < 200 || $codigo >= 300) throw new Exception("Error al enviar estadisticas. HTTP " . $codigo);
    }
}
?>
