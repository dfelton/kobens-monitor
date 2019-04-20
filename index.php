<!DOCTYPE HTML>
<html>
<head>
    <title>xkcd.com/1732/</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/styles.css"/>
</head>
<body>
    <div class="page">
        <h2>Orders</h2>
        <div class="container-fluid form-group">
                    <label for="refresh-orders">Auto Refresh Order Data:</label>
                    <select id="refresh-orders" class="form-control-sm">
                        <option selected value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
        </div>
        <div class="container-fluid monitors clear-after" id="orders" >
            <img src="/img/loading.gif" class="spinner"/>
        </div>

        <h2>Resource Monitors</h2>
        <div class="container-fluid form-group">
            <label for="refresh_cpuMemory">Auto Refresh Resource Data:</label>
            <select id="refresh_cpuMemory" class="form-control-sm">
                <option selected value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <h3>CPU Usage</h3>
         <div class="container-fluid monitors clear-after" id="cpu" >
            <img src="/img/loading.gif" class="spinner"/>
        </div>

        <h2>Memory Usage</h2>
        <div class="container-fluid monitors clear-after" id="memory" >
            <img src="/img/loading.gif" class="spinner"/>
        </div>
    </div>
    <script src="/js/assets/jquery-3.3.1.min.js"></script>
    <script src="/js/assets/canvasjs.min.js"></script>
    <script src="/js/monitor-orders.js"></script>
    <script src="/js/monitor-cpu-memory.js"></script>
</body>
</html>
