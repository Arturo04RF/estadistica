<?php
// getCountryVisits.php

header('Content-Type: application/json');

// Asegúrate de que estás incluyendo y usando correctamente la clase PKPMetricsDAO
include_once('lib/pkp/classes/statistics/PKPMetricsDAO.inc.php');

try {
    $metricsDao = new PKPMetricsDAO();
    $metricType = 'ojs::counter'; // Cambia esto según corresponda
    $columns = [STATISTICS_DIMENSION_COUNTRY];
    $filters = [];  // Agrega filtros si es necesario
    $orderBy = [];

    // Obtén los datos de visitas por país
    $data = $metricsDao->getMetrics($metricType, $columns, $filters, $orderBy);

    if ($data) {
        $labels = [];
        $counts = [];

        foreach ($data as $row) {
            // Asegúrate de que $row contiene las claves esperadas
            if (isset($row[STATISTICS_DIMENSION_COUNTRY]) && isset($row['metric'])) {
                $labels[] = $row[STATISTICS_DIMENSION_COUNTRY];
                $counts[] = $row['metric'];
            } else {
                throw new Exception('Unexpected data format');
            }
        }

        // Devuelve los datos como JSON
        echo json_encode(['labels' => $labels, 'data' => $counts]);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
