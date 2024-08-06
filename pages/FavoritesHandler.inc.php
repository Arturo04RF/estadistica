<?php
// Definir constantes
define('STATISTICS_DIMENSION_MONTH', 'month');
define('STATISTICS_METRIC', 'metric');
define('STATISTICS_DIMENSION_METRIC_TYPE', 'metric_type');

function getMonthlyVisits($metricType) {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "jmcs";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Validar tipo de métrica
    if (!is_array($metricType)) $metricType = array($metricType);

    // Construir la consulta SQL
    $selectClause = "SELECT " . STATISTICS_DIMENSION_MONTH . ", SUM(" . STATISTICS_METRIC . ") AS total_visits";
    $groupByClause = "GROUP BY " . STATISTICS_DIMENSION_MONTH;
    $whereClause = 'WHERE ' . STATISTICS_DIMENSION_METRIC_TYPE . ' IN (' . implode(', ', array_fill(0, count($metricType), '?')) . ')';
    $orderByClause = 'ORDER BY ' . STATISTICS_DIMENSION_MONTH . ' ASC';

    // Preparar la consulta
    $sql = "$selectClause FROM metrics $whereClause $groupByClause $orderByClause";
    $stmt = $conn->prepare($sql);

    // Vincular parámetros
    $stmt->bind_param(str_repeat('s', count($metricType)), ...$metricType);
    $stmt->execute();
    $result = $stmt->get_result();

    // Recoger resultados
    $returner = [];
    while ($row = $result->fetch_assoc()) {
        $returner[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $returner;
}

// Obtener visitas mensuales
$metricType = 'ojs::counter';
$monthlyVisits = getMonthlyVisits($metricType);

// Preparar datos para Chart.js
$labels = [];
$data = [];

foreach ($monthlyVisits as $visit) {
    $labels[] = $visit[STATISTICS_DIMENSION_MONTH];
    $data[] = $visit['total_visits'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Visitas Mensuales</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Visitas en 2024',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
