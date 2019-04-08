function monitorCPU()
{
	$.ajax({
		dataType: 'json',
		url: '/monitors/cpu.php',
		success: function( data ) {
			if ($('#cpu > .offline').get(0) != undefined) {
				$('#cpu > .offline').remove();
			}
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
						yValueFormatString: '###.##',
						dataPoints: processData
					});
				}
				if ($('#cpu_'+key).get(0) == undefined) {
					$('#cpu').append('<div id="cpu_'+key+'" class="monitor cpu"></div>');
				}
				var chart = new CanvasJS.Chart('cpu_' + key, {
				    title:{
				    	text: key.replace( /_/g, ':' )
			    	},
				    zoomEnabled: true,
					axisY: {
						title: 'CPU Usage',
						titleFontSize: 24,
						suffix: '%'
					},
				    data: chartData
				});
				chart.render();
			}
		},
		error: function() {
			$('#cpu').html('<p class="offline"><strong>Monitoring Offline</strong></p>');
		}
	});
	setTimeout(function() { monitorCPU(); }, 60000);
}
$(function() { monitorCPU(); });