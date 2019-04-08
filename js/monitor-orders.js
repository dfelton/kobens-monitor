function monitorOrders()
{
	$.ajax({
		dataType: 'json',
		url: '/monitors/trade-repeater-orders.php',
		success: function( data ) {
			for (var exchange in data) {
				for (var symbol in data[exchange]) {
					var chartData = [];
					var buys = 0;
					var sells = 0;
					for (var side in data[exchange][symbol]) {
						var dataPoints = [];
						for (var i in data[exchange][symbol][side]) {
							if (side == 'buy') {
								buys++;
							} else {
								sells++;
							}
							dataPoints.push({
								x: data[exchange][symbol][side][i].price,
								y: data[exchange][symbol][side][i].amount
							});
						}
						chartData.push({
							type: 'scatter',
							toolTipContent: '<strong>Price:</strong> {x}</br><strong>Amount:</strong> {y}',
							name: side + ' orders',
							showInLegend: true,
							dataPoints: dataPoints
						});
					}
					var chartDomElement = 'orders_'+exchange+'_'+symbol;
					if ($('#'+chartDomElement).get(0) == undefined) {
						$('#orders').append('<div id="'+chartDomElement+'" class="monitor orders"></div>');
						$('#orders').append('<div id="'+chartDomElement+'_count" class="monitor orders"></div>');
					}
					(new CanvasJS.Chart(chartDomElement, {
						title: {
							text: 'active ' + exchange + ' ' + symbol +' orders'
						},
						axisX: {
							title: 'Price'
						},
						axisY: {
							title: 'Amount'
						},
						zoomEnabled: true,
						data: chartData
					})).render();
					(new CanvasJS.Chart(chartDomElement+'_count', {
						title: {
							text: 'active ' + exchange + ' ' + symbol + ' order count',
						},
						axisY: {
							title: 'Count'
						},
						data: [{
							type: 'pie',
							dataPoints: [
								{y: buys, label: 'buys', indexLabel: buys + ' orders'},
								{y: sells, label: 'sells', indexLabel: sells + ' orders'}
							] 
						}]
					})).render();
				}
			}
		},
		error: function() {
			$('#orders').html('<p class="offline"><strong>Monitoring Offline</strong></p>');
		}
	});
	setTimeout(function() { monitorOrders(); }, 15000);
}
$(function() { monitorOrders(); });