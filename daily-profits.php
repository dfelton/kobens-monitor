<!DOCTYPE HTML>
<html>
<head>
<title>xkcd.com/1732/</title>
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/styles.css"/>
<style type="text/css">.monitor-box{height:1500px}</style>
</head>
<body>
<div class="page">
	<input type="checkbox" checked="checked" id="refresh"/><label for="refresh">Auto Refresh</label>
    <div class="container-fluid monitors clear-after">
		<div id="daily-profits" class="monitor-box clear-after">
			<img src="/img/loading.gif" class="spinner"/>
		</div>
	</div>
	<div class="container-fluid monitors clear-after">
		<div id="daily-profits-moving-average" class="monitor-box clear-after">
			<img src="img/loading.gif" class="spinner"/>
		</div>
	</div>
</div>
<script src="js/assets/jquery-3.3.1.min.js"></script>
<script src="js/assets/canvasjs.min.js"></script>
<script src="js/daily-profits.js"></script>
<script>
setTimeout(
  function() {
    if (document.querySelector('#refresh:checked') !== null) {
      window.location.reload(false);
    }
  },
  60000
);
</script>
</body>
</html>
