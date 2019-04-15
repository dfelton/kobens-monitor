<!DOCTYPE HTML>
<html>
<head>
    <title>xkcd.com/1732/</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/styles.css"/>
</head>
<body>
<div id="chartContainer"></div>
    <div class="page">
        <h2>Orders</h2>
        <div id="orders" class="container-fluid monitors">
            <img src="/img/loading.gif" class="spinner"/>
        </div>
        <div class="container">
            <div class="row form-group">
                <div class="col-x-sm">
                    <label for="refresh-orders">Auto Refresh</label>
                    <select id="refresh-orders" class="form-control-sm">
                        <option selected value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
        </div>

        <h2>Resource Monitors</h2>

        <h3>CPU Usage</h3>
        <div id="cpu" class="container-fluid monitors">
            <img src="/img/loading.gif" class="spinner"/>
        </div>

        <h2>Memory Usage</h2>
        <div id="memory" class="container-fluid monitors">
            <img src="/img/loading.gif" class="spinner"/>
        </div>
        <div class="container">
            <div class="row form-group">
                <label for="refresh_cpuMemory">Auto Refresh</label>
                <select id="refresh_cpuMemory" class="form-control-sm">
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>
    </div>
    <script src="/js/assets/jquery-3.3.1.min.js"></script>
    <script src="/js/assets/canvasjs.min.js"></script>
    <script src="/js/monitor-orders.js"></script>
    <script src="/js/monitor-cpu-memory.js"></script>
</body>
</html>
