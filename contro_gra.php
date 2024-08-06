





<?php

// Supongamos que ya tienes una instancia del servicio de estadísticas y los datos obtenidos de Matomo
$records = Services::get('stats')->getRecords([
    'contextIds' => [1],
    'dateEnd' => '2020-03-31',
    'dateStart' => '2020-01-01',
    'assocTypes' => [ASSOC_TYPE_SUBMISSION],
]);

// Sumar los registros para obtener un recuento total de todos los envíos
$views = array_reduce(
    $records,
    [Services::get('stats'), 'sumMetric'],
    0
);

// Si deseas ver los datos por mes, asumiendo que los registros tienen un campo 'month/mes'
$metricsData = [];
foreach ($records as $record) {
    $month = $record['month'];
    if (!isset($metricsData[$month])) {
        $metricsData[$month] = 0;
    }
    $metricsData[$month] += $record['metric'];
}

// Transformar los datos a un formato adecuado para Chart.js

$labels = array_keys($metricsData);
$data = array_values($metricsData);
/*
class MetricsHelper {

    public static function getLastYearMonthlyVisits() {
        // Crea una instancia de PKPMetricsDAO
        $metricsDao = DAORegistry::getDAO('PKPMetricsDAO');

        // Define los parámetros
        $metricType = 'ojs::counter'; // Define tu tipo de métrica aquí
        $columns = [STATISTICS_DIMENSION_MONTH];
        $filters = [
            'day' => [
                'from' => date('Y-m-01', strtotime('first day of last year')),
                'to' => date('Y-m-t', strtotime('last day of last year'))
            ]
        ];
        $orderBy = [STATISTICS_DIMENSION_MONTH => STATISTICS_ORDER_ASC];
        $range = null;

        // Obtén las métricas
        $metrics = $metricsDao->getMetrics($metricType, $columns, $filters, $orderBy, $range);

        // Retorna las métricas
        return $metrics;
    }
}

*/


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gráfico de Métricas por Mes</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div>
    <canvas id="myChart" width="400" height="400"></canvas>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var labels = <?php echo json_encode($labels); ?>;
        var data = <?php echo json_encode($data); ?>;
       
        var myChart = new Chart(ctx, {
            type: 'bar', // Tipo de gráfico: barra
            data: {
                labels: labels, // Etiquetas del eje X (países)
                datasets: [{
                    label: 'Metrics', // Etiqueta del dataset
                    data: data, // Datos de la métrica por país
                    backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de fondo de las barras
                    borderColor: 'rgba(54, 162, 235, 1)', // Color del borde de las barras
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true // Comenzar el eje Y en cero
                    }
                }
            }
        });
    </script>
    </div>
</body>
</html>

