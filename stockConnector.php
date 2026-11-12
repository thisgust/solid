<?php
class StockConnector implements InterfazStockConnector {
    private string $url = 'http://localhost:3000/api/stock/movimiento';

    public function registrarMovimiento(DTOMovimiento $movimiento) {

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Idempotency-Key: ' . $movimiento->referencia]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($movimiento->convertirAArreglo()));

        $respuesta = curl_exec($ch);

        if ($respuesta === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new Exception("Error al comunicarse con el ERP: " . $error);
        }

        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($codigo < 200 || $codigo >= 300) throw new Exception("Error al registrar movimiento. HTTP " . $codigo);
    }
}
?>
