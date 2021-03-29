var dailyProfitsChart;
var movingAverageChart;

function toogleDataSeries(e) {
    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        e.dataSeries.visible = false;
    } else{
        e.dataSeries.visible = true;
    }
    dailyProfitsChart.render();
}

function toogleDataSeriesAvg(e) {
    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        e.dataSeries.visible = false;
    } else{
        e.dataSeries.visible = true;
    }
    movingAverageChart.render();
}

function dailyProfits()
{
    $.ajax({
        dataType: 'json',
        url: 'monitors/daily-profits.php',
        success: function( data ) {
            var chartData = [];
			var chartDataMovingAverages = [];
            for (var symbol in data) {
                var isVisible = false;
                if (symbol == 'usd' || symbol == 'total_notional') {
                    isVisible = true;
				}
                var symbolData = {
                    type:"line",
                    axisYType: "secondary",
                    xValueType: "number",
                    name: symbol.replace('_' , ' '),
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###.########",
                    visible: isVisible
                };
				var symbolDataMovingAverage = {
                    type:"line",
                    axisYType: "secondary",
                    xValueType: "number",
                    name: symbol.replace('_', ' ') + ' 7 day moving average',
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###.########",
                    visible: isVisible
                };
				var symbolDataMovingAverage30 = {
                    type:"line",
                    axisYType: "secondary",
                    xValueType: "number",
                    name: symbol.replace('_', ' ') + ' 30 day moving average',
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###.########",
                    visible: isVisible
                };
                var dataPoints = [];
				var dataPointsMovingAverage = [];
				var dataPointsMovingAverage30 = [];
				var movingAverage = [];
				var movingAverage30 = [];
                for (var i in data[symbol]) {
                    dataPoints.push({ x: new Date(data[symbol][i].date), y: parseFloat(data[symbol][i].amount) });
					if (movingAverage.length == 7) {
						movingAverage.shift();
						movingAverage.push(parseFloat(data[symbol][i].amount));
					} else {
						movingAverage.push(parseFloat(data[symbol][i].amount));
					}
					if (movingAverage.length == 7) {
						var total = 0;
						for (var j in movingAverage) {
							total += movingAverage[j];
						}
						dataPointsMovingAverage.push({ 
							x: new Date(data[symbol][i].date), 
							y: parseFloat((total / 7).toFixed(8)) 
						});
					}
					
					if (movingAverage30.length == 30) {
						movingAverage30.shift();
						movingAverage30.push(parseFloat(data[symbol][i].amount));
					} else {
						movingAverage30.push(parseFloat(data[symbol][i].amount));
					}
					if (movingAverage30.length == 30) {
						var total = 0;
						for (var j in movingAverage30) {
							total += movingAverage30[j];
						}
						dataPointsMovingAverage30.push({ 
							x: new Date(data[symbol][i].date), 
							y: parseFloat((total / 30).toFixed(8)) 
						});
					}
                }
                symbolData.dataPoints = dataPoints;
                chartData.push(symbolData);

				symbolDataMovingAverage.dataPoints = dataPointsMovingAverage;
				symbolDataMovingAverage30.dataPoints = dataPointsMovingAverage30;
				chartDataMovingAverages.push(symbolDataMovingAverage);
				chartDataMovingAverages.push(symbolDataMovingAverage30);
            }

			dailyProfitsChart = new CanvasJS.Chart(
		        "daily-profits", 
		        {
		            title: { text: "Daily Profits" },
		            axisX: {
		                title: "Date",
		                gridThickness: 2
		            },
		            axisY: {
		                title: "Amount",
		                gridThickness: 2
		            },
		            toolTip: {
		                shared: true
		            },
		            legend: {
		                cursor: "pointer",
		                verticalAlign: "bottom",
		                horizontalAlign: "left",
		                dockInsidePlotArea: false,
		                itemclick: toogleDataSeries
		            },
		            data: chartData
		        }
		    );
			movingAverageChart = new CanvasJS.Chart(
		        "daily-profits-moving-average", 
		        {
		            title: { text: "Daily Profits Moving Averages" },
		            axisX: {
		                title: "Date",
		                gridThickness: 2
		            },
		            axisY: {
		                title: "Amount",
		                gridThickness: 2
		            },
		            toolTip: {
		                shared: true
		            },
		            legend: {
		                cursor: "pointer",
		                verticalAlign: "bottom",
		                horizontalAlign: "left",
		                dockInsidePlotArea: false,
		                itemclick: toogleDataSeriesAvg
		            },
		            data: chartDataMovingAverages
		        }
		    );
			
			dailyProfitsChart.render();
			movingAverageChart.render();
			
			setTimeout(
				function() {
			    	if (document.querySelector('#refresh:checked') !== null) {
			      		dailyProfits();
		    		}
			  	},
			  	60000
			);
        },
        error: function() {
            $('#daily-profits').html('<p class="offline"><strong>Monitoring Offline</strong></p>');
        }
    });
}
$(function() { 
	dailyProfits(); 
});
