function monitorMemory()
{
	$.ajax({
		dataType: 'json',
		url: '/monitors/memory.php',
		success: function( data ) {
			for (var key in data) {
				var chartData = [];
				for (var pid in data[key]) {
					var processData = [];
					for (var i in data[key][pid]) {
						processData.push({
							x: new Date(data[key][pid][i][0]),
							y: data[key][pid][i][1]
						});
					}
					chartData.push({
						type: 'area',
						yValueFormatString: '#,###',
						dataPoints: processData
					});
				}
				if ($('#memory_'+key).get(0) == undefined) {
					$('#memory').append('<div id="memory_'+key+'" class="monitor memory"></div>');
				}
				var chart = new CanvasJS.Chart('memory_' + key, {
				    title: {
				    	text: key.replace( /_/g, ':' )
			    	},
				    zoomEnabled: true,
					axisY: {
						title: 'Memory Usage',
						titleFontSize: 24,
						suffix: 'MB'
					},
				    data: chartData
				});
				chart.render();
			}
		},
		error: function() {
			$('#memory').html('<p class="offline"><strong>Monitoring Offline</strong></p>');
		}
	});
	setTimeout(function() { monitorMemory(); }, 60000);
}
$(function() { monitorMemory(); });