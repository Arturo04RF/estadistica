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

    // Obtener la fecha actual y calcular la fecha de 9 meses atrás
    $currentDate = new DateTime();
    $pastDate = new DateTime();
    $pastDate->modify('-9 months');

    // Formatear las fechas para la consulta SQL
    $currentMonth = $currentDate->format('Ym');
    $pastMonth = $pastDate->format('Ym');

    // Construir la consulta SQL
    $selectClause = "SELECT " . STATISTICS_DIMENSION_MONTH . ", SUM(" . STATISTICS_METRIC . ") AS total_visits";
    $groupByClause = "GROUP BY " . STATISTICS_DIMENSION_MONTH;
    $whereClause = 'WHERE ' . STATISTICS_DIMENSION_METRIC_TYPE . ' IN (' . implode(', ', array_fill(0, count($metricType), '?')) . ')';
    $whereClause .= ' AND ' . STATISTICS_DIMENSION_MONTH . ' BETWEEN ? AND ?';
    $orderByClause = 'ORDER BY ' . STATISTICS_DIMENSION_MONTH . ' ASC';

    // Preparar la consulta
    $sql = "$selectClause FROM metrics $whereClause $groupByClause $orderByClause";
    $stmt = $conn->prepare($sql);

    // Vincular parámetros
    $params = array_merge($metricType, [$pastMonth, $currentMonth]);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
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
$colors = [];

$colorsArray = [
    'rgba(255, 99, 132, 0.7)', // Rojo claro
    'rgba(54, 162, 235, 0.7)', // Azul claro
    'rgba(255, 206, 86, 0.7)', // Amarillo claro
    'rgba(75, 192, 192, 0.7)', // Verde claro
    'rgba(153, 102, 255, 0.7)', // Púrpura claro
    'rgba(255, 159, 64, 0.7)', // Naranja claro
    'rgba(255, 99, 132, 0.7)', // Rojo claro (duplicado)
    'rgba(54, 162, 235, 0.7)', // Azul claro (duplicado)
    'rgba(255, 206, 86, 0.7)'  // Amarillo claro (duplicado)
];

foreach ($monthlyVisits as $index => $visit) {
    $labels[] = $visit[STATISTICS_DIMENSION_MONTH];
    $data[] = $visit['total_visits'];
    $colors[] = $colorsArray[$index % count($colorsArray)];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitas Mensuales a la revista</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #2c3e50;
            color: #ecf0f1;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            color: #ecf0f1;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .chart-container {
            width: 100%;
            max-width: 800px;
            padding: 20px;
            background: #34495e;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .buttons-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .button {
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #3498db;
        }
        .chart-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* Aspect ratio 16:9 */
            height: 0;
        }
        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="chart-container">
        <h2>Datos mensuales en 8 meses</h2>
        <div class="chart-wrapper">
            <canvas id="myChart"></canvas>
        </div>
        <div class="buttons-container">
            <button class="button" onclick="updateChartType('bar')">Barra</button>
            <button class="button" onclick="updateChartType('line')">Línea</button>
        </div>
    </div>

    <script>
        let chartType = 'bar';
        const ctx = document.getElementById('myChart').getContext('2d');
        let myChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Visitas en los últimos 8 meses',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: <?php echo json_encode($colors); ?>,
                    borderColor: 'rgba(0, 0, 0, 0.1)', // Color de borde negro claro
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Visitas Mensuales a la Revista'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });

        function updateChartType(newType) {
            myChart.config.type = newType;
            myChart.update();
        }
    </script>
</body>
</html>
