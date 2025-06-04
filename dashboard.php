<!-- TO STOP ERRORS FROM SHOWING CAN BE CHANGED TO LOG ERRORS FOR REFERENCE -->
<?php 
  // ini_set('log_errors', 1);
  // ini_set('error_log', '/path/to/php-error.log');       // Replace with an actual path
?>
<?php
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  error_reporting(0);
?>

<!-- Security check cookies checking -->
<?php
// Start secure page protection
if (!isset($_COOKIE['user_email']) || empty($_COOKIE['user_email'])) {
    // No valid login cookie found – redirect to login
    header('Location: index.html'); // Or login.php
    exit();
}
?>

<?php
$scriptURL = "https://script.google.com/macros/s/AKfycbxsyCU4HB8-uMFepd5vNviIGXoO5_g7hjDzx5Xoew61wzet6wUBCA_CmuHTAcb_eEoi/exec";
$response = @@file_get_contents($scriptURL);
$stats = ($response !== false) ? json_decode($response, true) : [];
if (!is_array($stats)) $stats = [];

$rc = "https://script.google.com/macros/s/AKfycbxbyE9oUuqzkvgtDungbQIj_xK0Xr20eqiIHK1_k956mNkRH2IvVZ09O8xPznVhixlY8A/exec";
$respo = @@file_get_contents($rc);
$stat = ($respo !== false) ? json_decode($respo, true) : [];
if (!is_array($stat)) $stat = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Driver Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
  <style>
    body {
      background-color: #000;
      margin: 0;
      height: 100vh;
      color: #fff;
    }
    .green { color: #00c851; font-size: 2.5rem; }
    .red { color: #ff4444; font-size: 2.5rem; }
    .dark { color: rgb(0, 0, 0); font-size: 2.5rem; }
    .containers {
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      padding: 20px;
      align-items: center;
      justify-content: center;
      background-color: rgb(231, 229, 229);
      margin: 10px;
      border-radius: 10px;
    }
    .card {
      background: rgb(206, 205, 205);
      border-radius: 30px;
      padding: 30px;
      font-size: 1.1rem;
      text-align: center;
      width: 200px;
      height: 238px;
      color: black;
    }
    .card div {
      font-size: 45px;
      height: 150px;
    }
    .card span {
      font-size: 80px;
    }
    .dashboard {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-left: 10px;
      height: 60px;
    }
    .dashboard img {
      height: 60px;
    }
    .dashboard i {
      position: absolute;
      left: 10px;
      font-size: 40px;
    }
    .dot-button {
      position: absolute;
      top: 50%;
      right: 1%;
      transform: translate(-50%, -50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      cursor: pointer;
      z-index: 1000;
    }
    .main-container {
      position: relative;
    }
    .dot {
      width: 15px;
      height: 15px;
      background-color: #ff6b35;
      border-radius: 50%;
      margin: 5px 0;
      transition: background-color 0.3s;
    }
    .dot.active {
      background-color: #fff;
    }
    .toggle-container {
      display: none;
    }
    .card-graph {
      height: 278px;
      width: 440px;
      background: rgb(211, 211, 211);
      border-radius: 20px;
      padding: 10px;
    }
    .card-graph canvas {
      width: 100% !important;
      height: 100% !important;
    }

    
        /* Panel */
    #top-panel {
      position: fixed;
      top: -200px; /* Hidden initially */
      left: 60px;
      right: 10px;
      height: 60px;
      background-color: rgb(0, 0, 0);
      color: white;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      transition: top 0.3s ease;
      z-index: 1000;
      padding:2px;
      padding-left:5px;
      border-bottom-left-radius:10px;
      border-bottom-right-radius:10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    /* Show panel */
    #top-panel.open {
      position:absolute;
      top: 0;
    }

    .avatar  {
      height:50px;
      border-radius: 50%;
    }

    .links{
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap:20px;
      height:60px;
      margin-right:50px;
    }

    .links a{
      font-size:20px;
      color:white;
      text-decoration:none;
    }

  </style>
</head>

<body>

  <div class="dashboard">
    <a id="menu-icon"><i class="fa-solid fa-bars fa-2xl"  style="color: #ff6b35;"></i></a>
    <img src="ashwa-logo.png" alt="">
    <div id="top-panel">
      <img src="img_avatar.png" alt="Avatar" class="avatar">
      <div class="links">
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
      </div>
  </div>
  </div>
    <!-- Script for Top Panel -->
  <script>
    const menuIcon = document.getElementById('menu-icon');
    const topPanel = document.getElementById('top-panel');

    menuIcon.addEventListener('click', () => {
      topPanel.classList.toggle('open');
    });
  </script>

  <div class="main-container">
    <!-- Container 1 -->
    <div class="containers" id="dataContainer1">
      <div class="card"><div>Active Cars</div><span class="green"><?= $stats['active'] ?? 0 ?></span></div>
      <div class="card"><div>Active Driver</div><span class="green"><?= $stats['activedrivers'] ?? 0 ?></span></div>
      <div class="card"><div>Inactive Cars</div><span class="red"><?= $stats['inactive'] ?? 0 ?></span></div>
      <div class="card"><div>Waiting Drivers</div><span class="red"><?= $stats['waiting'] ?? 0 ?></span></div>
      <div class="card"><div>RC's On Duty</div><span class="red"><?= $stat['present'] ?? 0 ?></span></div>
      <div class="card"><div>Drivers Converted</div><span class="green"><?= $stats['converted'] ?? 0 ?></span></div>
      <div class="card"><div>Today’s Calls Made</div><span class="green"><?= $stats['callsToday'] ?? 0 ?></span></div>
      <div class="card"><div>Today’s Callbacks</div><span class="dark"><?= $stats['callbacks'] ?? 0 ?></span></div>
      <div class="card"><div>Alerts</div><span class="dark"><?= $stats['callbacks'] ?? 0 ?></span></div>
    </div>

    <!-- Container 2 -->
    <div class="containers toggle-container" id="dataContainer2">
      <div class="card-graph">
        <canvas id="barChart"></canvas>
      </div>

      <div class="card-graph">
        <canvas id="statusChart"></canvas>
      </div>

      <div class="card-graph">
        <canvas id="leadsChart"></canvas>
      </div>

      <div class="card-graph">
        <canvas id="fleetChart"></canvas>
      </div>
    </div>

    <!-- Toggle Button -->
    <div class="dot-button" onclick="toggleData()">
      <div class="dot active"></div>
      <div class="dot"></div>
    </div>

  </div>

  <!-- Script for Changing Container using dot-buttons -->
  <script>
    function toggleData() {
      const container1 = document.getElementById('dataContainer1');
      const container2 = document.getElementById('dataContainer2');
      const dots = document.querySelectorAll('.dot');
      const showingFirst = container1.style.display !== 'none';
      container1.style.display = showingFirst ? 'none' : 'flex';
      container2.style.display = showingFirst ? 'flex' : 'none';
      dots[0].classList.toggle('active', !showingFirst);
      dots[1].classList.toggle('active', showingFirst);
    }
    document.getElementById('dataContainer1').style.display = 'flex';
    document.getElementById('dataContainer2').style.display = 'none';
  </script>

  <!-- Funnel Bar Chart -->
<?php
// Fetch data from Google Script URL on the server side
$googleScriptUrl = 'https://script.google.com/macros/s/AKfycbzL3-5_BbqbDOag1N4H_m8HRIZ2MKtvpP4vgQ92jD4WUFavZPBAE3wwBcu75d-EWdkcEg/exec';
$jsonData = @file_get_contents($googleScriptUrl);
$data = json_decode($jsonData, true);

// Fallback if fetch fails
if (!$data) {
    $data = [
        'labels' => [],
        'values' => []
    ];
}
?>

<script>
  // Use PHP data embedded in JavaScript variable (hides the real URL)
  const labels = <?php echo json_encode($data['labels']); ?>;
  const values = <?php echo json_encode($data['values']); ?>;

  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        data: values,
        backgroundColor: [
          '#fdad93', '#ffa486', '#ff612c', '#e74b17', '#a83813', '#5e1d07'
        ].slice(0, labels.length),
        borderColor: 'rgba(255, 255, 255, 0)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          ticks: {
            color: '#000',
            callback: val => val + '%'
          },
          title: {
            display: true,
            color: '#000',
            font: { weight: 'bold' }
          }
        },
        x: {
          ticks: { color: '#000' }
        }
      },
      interaction: {
        mode: 'index',    // changed from 'nearest' to 'index'
          intersect: false ,  // tooltip only when hovering directly on bar
          responsive: true
      },
      
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          color: '#000',
          text: 'Funnel Stage Conversion (All Batches Combined)',
          padding: { bottom: 18 },
          font: { size: 17 }
        },
        tooltip: {
          enabled: true,
            mode: 'index',
            intersect: false,
          padding: 5,
            cornerRadius: 3,
            titleFont: { size: 14 },
            bodyFont: { size: 12 },
            backgroundColor: 'rgba(0,0,0,0.75)',
          callbacks: { label: ctx => ctx.parsed.y + '%' }
        }
      }
    }
  });
</script>


<!-- Status Pie Chart -->
   
<?php
// Fetch and decode JSON from the Google Apps Script
$scriptUrl = 'https://script.google.com/macros/s/AKfycbwYja7vM5ESmNZ9S-8KKoIrQgs4LkitjOkJrMeSdVZ24vbo31ujNiM-HU4K4pMgxZguEA/exec';
$json = @file_get_contents($scriptUrl);
$data = json_decode($json, true);

// Fallback if fetch fails
if (!is_array($data)) {
    $data = [];
}

// Split into labels and counts for the chart
$labels = array_column($data, 'status');
$counts = array_column($data, 'count');
?>

<script>
  // Embed PHP-generated data into JavaScript
  document.addEventListener("DOMContentLoaded", function() {
  // Chart code here

  const labels = <?php echo json_encode($labels); ?>;
  const data = <?php echo json_encode($counts); ?>;

  new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: ['#4dd0e1', '#29b6f6', '#0686c5', '#546e7a', '#5c6bc0', '#9c27b0', '#e57373'],
        radius: '90%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: {
              padding: 5
            },
      plugins: {
        legend: { display:false },
        title: {
              display: true,
              text: 'Driver Status Distribution (AGM)',
              weight:'bold',
              color: '#000',
              padding: {
                    bottom: 20
                },
                font: {
                  size: 20,
                }
            },
        datalabels: {
          color: '#000',
          clip: false,
          formatter: (value, context) => `${context.chart.data.labels[context.dataIndex]} ${value}`,
          anchor: 'end',
                align: 'end',
                offset:5,
                font: {
                  size: 12,
                  weight: 'bold'
                }
        }
      }
    },
    plugins: [ChartDataLabels]
  });});
</script>

  
<!-- Data Source Effectiveness  -->
 
<?php
$script_url = 'https://script.google.com/macros/s/AKfycbzL3-5_BbqbDOag1N4H_m8HRIZ2MKtvpP4vgQ92jD4WUFavZPBAE3wwBcu75d-EWdkcEg/exec';
$response = @file_get_contents($script_url);
$data = json_decode($response, true);

$labels = array_keys($data['grouped']);
$values = array_values($data['grouped']);
?>

<script>
    const ctx = document.getElementById('leadsChart').getContext('2d');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Total Leads',
          data: <?php echo json_encode($values); ?>,
          backgroundColor: 'rgb(78, 114, 223)',
          borderRadius: 1,
          barThickness: 30,  // fixed bar thickness for easier hover
          maxBarThickness: 40
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',    // changed from 'nearest' to 'index'
          intersect: true   // tooltip only when hovering directly on bar
        },
        plugins: {
          tooltip: {
            enabled: true,
            mode: 'index',
            intersect: true,
            padding: 5,
            cornerRadius: 3,
            titleFont: { size: 14 },
            bodyFont: { size: 12 },
            backgroundColor: 'rgba(0,0,0,0.75)'
          },
          title:{
            display:true,
            color:'#000',
            text:"Data Source Effectiveness",
            font:{
              size:20,
              weight:"bold"

            },
          },
          legend: { display: false }
        },
        scales: {
          x: {
            beginAtZero: true,
            ticks: { 
              color: '#000',
              precision: 0 },
            title: {
              display: true,
              color: '#000',
              font: { size: 14,
                weight:"bold"
               }
            }
          },
          y: {
            ticks:{
              color: '#000',
            },
            title: {
              display: true,
              color: '#000',
              font: { size: 14,
                weight:"bold"
               }
            }
          }
        }
      }
    });
</script>

<!-- Supplied Driver(Company Wise)  -->

<?php
// Fetch data from Google Script URL on the server side
$googleScriptUrl = 'https://script.google.com/macros/s/AKfycby2NtUjHgygpuV7H8JPlIYtZCQ3Qs1liCPEaBTr7RraTBp5JuQgEjwAZi6a9BDFwDVm1w/exec';
$jsonData = @file_get_contents($googleScriptUrl);
$data = json_decode($jsonData, true);

// Fallback if fetch fails
if (!$data) {
    $data = [
        'labels' => [],
        'data' => []
    ];
}
?>

<script>
  document.addEventListener("DOMContentLoaded", function() {
  

  // Fetch JSON data from the provided URL
  const labels = <?php echo json_encode($data['labels']); ?>;
  const data = <?php echo json_encode($data['data']); ?>;
  // Chart code here
      const ctx = document.getElementById('fleetChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Fleet Count',
            data: data,
            backgroundColor: 'rgb(4,18,52)',
            borderWidth: 1,
            borderRadius: 2,
        borderSkipped: false,
          }]
        },
        options: {
           maintainAspectRatio: false,
          interaction: {
          mode: 'index',    // changed from 'nearest' to 'index'
          intersect: true ,  // tooltip only when hovering directly on bar
          responsive: true
        },
          plugins: {
        tooltip: {
            enabled: true,
            mode: 'index',
            intersect: true,
            padding: 5,
            cornerRadius: 3,
            titleFont: { size: 14 },
            bodyFont: { size: 12 },
            backgroundColor: 'rgba(0,0,0,0.75)'
          },
        legend: { display: false },
        title: {
          display: true,
          color:'#000',
          text: 'Supplied Driver (Company Wise)',
          font: { size: 19 }
        }
      },
          indexAxis: 'y', // This makes the bars horizontal
          responsive: true,
          scales: {
            x: {
              beginAtZero: true,
          ticks: { 
            color:'#000',
            stepSize: 5,
           },
          title:{color:'#000'}
        },
        y: {
            ticks:{
              color: '#000',
            },
            title: {
              display: true,
              color: '#000',
              font: { size: 14,
                weight:"bold"
               },
            }
          }
          }
        }
      });
    }); 
</script>

</body>
</html>