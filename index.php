<!DOCTYPE HTML>
<html>
<head>
    <title>xkcd.com/1732/</title>
    <link rel="stylesheet" type="text/css" href="/css/styles.css"/>
</head>
<body>
<div id="chartContainer"></div>
    <div class="page">
        <div id="containers">
            <h2>Orders</h2>
            <div class="refresh">
                <label>Auto Refresh</label>
                <select id="refresh_orders">
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div id="orders" class="container">
                <img src="/img/loading.gif" class="spinner" alt=""/>
            </div>

            <h2>CPU Usage</h2>
            <div class="refresh">
                <label>Auto Refresh</label>
                <select id="refresh_cpu">
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div id="cpu" class="container">
                <img src="/img/loading.gif" class="spinner" alt=""/>
            </div>

            <h2>Memory Usage</h2>
            <div class="refresh">
                <label>Auto Refresh</label>
                <select id="refresh_memory">
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div id="memory" class="container">
                <img src="/img/loading.gif" class="spinner" alt=""/>
            </div>
        </div>
    </div>
    <script src="/js/assets/jquery-3.3.1.min.js"></script>
    <script src="/js/assets/canvasjs.min.js"></script>
    <script src="/js/monitor-orders.js"></script>
    <script src="/js/monitor-cpu.js"></script>
    <script src="/js/monitor-memory.js"></script>
</body>
</html>