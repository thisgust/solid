<?php
require_once 'ventaDto.php';
require_once 'movimientoDto.php';
require_once 'stockConnectorInterfaz.php';
require_once 'stockConnector.php';
require_once 'stockConnectorFalso.php';
require_once 'estadisticasConnectorInterfaz.php';
require_once 'estadisticasConnector.php';
require_once 'estadisticasConnectorFalso.php';
require_once 'ventaAdapter.php';
require_once 'ventaRepository.php';
require_once 'ventaService.php';
require_once 'ventaController.php';

// $conectorStock = new StockConnector();
$conectorStock = new StockConnectorFalso();
$conectorStock->setDisponible(true);

// $conectorEstadisticas = new EstadisticasConnector();
$conectorEstadisticas = new EstadisticasConnectorFalso();

$repositorio = new VentaRepository();
$adaptador = new VentaAdapter();
$servicio = new VentaService($conectorStock, $conectorEstadisticas, $repositorio);
$controlador = new VentaController($servicio, $adaptador);
$controlador->procesar();
?>
