<?php
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

    // Consulta SQL para obtener los datos
    $sql = "SELECT month, SUM(metric) as total_visits
            FROM metrics
            WHERE metric_type = 'ojs::counter'
            AND YEAR(day) = YEAR(CURDATE()) - 1
            GROUP BY month
            ORDER BY month ASC";

    $result = $conn->query($sql);
    $labels = [];
    $data = [];

    if ($result->num_rows > 0) {
        // Obtener datos de la tabla
        while($row = $result->fetch_assoc()) {
            $labels[] = $row['month'];
            $data[] = $row['total_visits'];
        }
    } else {
        echo "No se encontraron resultados";
    }

    $conn->close();
?>

JavaScript

<script>
    let chartType = 'bar';
    const ctx = document.getElementById('myChart').getContext('2d');
    let myChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Visitas en 2024',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
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
                            return value + 'k';
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

