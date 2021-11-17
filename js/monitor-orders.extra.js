function getOrders()
{
    let urlParams = new URLSearchParams(window.location.search);
    let urlEndpoint = 'monitors/trade-repeater-orders.php';
    if (urlParams.has('symbol')) {
       urlEndpoint += '?symbol' + urlParams.get('symbol'); 
    }

    $.ajax({
        dataType: 'json',
        url: urlEndpoint,
        success: function( data ) {
            if ($('#orders > .offline').get(0) != undefined) {
                $('#orders > .offline').remove();
            }
            if ($('#orders > .spinner').get(0) != undefined) {
                $('#orders > .spinner').remove();
            }
            for (var exchange in data) {
                for (var symbol in data[exchange]) {
                    var chartData = [];
//                     var strategyDataDataPoints = { buys: [], sells: [] };
//                    var buys = 0;
//                    var sells = 0;
                    for (var side in data[exchange][symbol]) {
                        var dataPoints = [];
                        for (var i in data[exchange][symbol][side]) {
                            let color;
                            if (side == 'buy') {
                                //buys++;
                                color = 'green';
                            } else {
                                //sells++;
                                color = 'red';
                            }
                            dataPoints.push({
                                x: data[exchange][symbol][side][i].price,
                                y: data[exchange][symbol][side][i].amount,
                                color: color,
                                id: data[exchange][symbol][side][i].id,
                                buy_amount: data[exchange][symbol][side][i].buy_amount,
                                buy_price: data[exchange][symbol][side][i].buy_price,
                                buy_price_increase: data[exchange][symbol][side][i].buy_price_increase,
                                buy_price_increase_percent: data[exchange][symbol][side][i].buy_price_increase_percent,
                                sell_amount: data[exchange][symbol][side][i].sell_amount,
                                sell_price: data[exchange][symbol][side][i].sell_price,
                                sell_price_increase: data[exchange][symbol][side][i].sell_price_increase,
                                sell_price_increase_percent: data[exchange][symbol][side][i].sell_price_increase_percent,
                                save_amount: data[exchange][symbol][side][i].save_amount
                            });
//                             strategyDataDataPoints.buys.push({
//                                 x: data[exchange][symbol][side][i].buy_price,
//                                 y: data[exchange][symbol][side][i].buy_amount,
//                                 buy_amount: data[exchange][symbol][side][i].buy_amount,
//                                 buy_price: data[exchange][symbol][side][i].buy_price,
//                                 sell_amount: data[exchange][symbol][side][i].sell_amount,
//                                 sell_price: data[exchange][symbol][side][i].sell_price
//                             });
//                             strategyDataDataPoints.sells.push({
//                                 x: data[exchange][symbol][side][i].sell_price,
//                                 y: data[exchange][symbol][side][i].sell_amount,
//                                 buy_amount: data[exchange][symbol][side][i].buy_amount,
//                                 buy_price: data[exchange][symbol][side][i].buy_price,
//                                 sell_amount: data[exchange][symbol][side][i].sell_amount,
//                                 sell_price: data[exchange][symbol][side][i].sell_price
//                             });
                        }
                        chartData.push({
                            type: 'scatter',
                            toolTipContent: '<strong>ID:</strong> {id}</br>' +
                                '<strong>Buy Price:</strong> {buy_price}</br>' +
                                '<strong>Buy Price Increase:</strong> {buy_price_increase}</br>' +
                                '<strong>Buy Price Increase Percent:</strong> {buy_price_increase_percent}</br>' +
                                '<strong>Buy Amount:</strong> {buy_amount}<br/>' +
                                '<strong>Sell Price:</strong> {sell_price}</br>' +
                                '<strong>Sell Amount:</strong> {sell_amount}<br/>' +
                                '<strong>Sell Price Increase:</strong> {sell_price_increase}</br>' +
                                '<strong>Sell Price Increase Percent:</strong> {sell_price_increase_percent}</br>' +
                                '<strong>Save Amount:</strong> {save_amount}</br>',
                            name: side + ' orders',
                            showInLegend: true,
                            dataPoints: dataPoints
                        });
                    }
                    var chartDomElement = 'orders_' + exchange + '_' + symbol;
                    if ($('#' + chartDomElement).get(0) == undefined) {
                        $('#orders').append('<div class="monitor orders"><div class="monitor-box clear-after" id="' + chartDomElement + '"></div></div>');
//                         $('#orders').append('<div class="monitor orders"><div class="monitor-box clear-after" id="' + chartDomElement + '_count"></div></div>');
//                         $('#orders').append('<div class="monitor orders"><div class="monitor-box clear-after" id="' + chartDomElement + '_buy_strategy"></div></div>');
//                         $('#orders').append('<div class="monitor orders"><div class="monitor-box clear-after" id="' + chartDomElement + '_sell_strategy"></div></div>');
                    }
                    (new CanvasJS.Chart(chartDomElement, {
                        title: {
                            text: symbol
//                             text: 'active ' + exchange + ' ' + symbol + ' orders'
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
//                     (new CanvasJS.Chart(chartDomElement + '_count', {
//                         title: {
//                             text: 'active ' + exchange + ' ' + symbol + ' order count',
//                         },
//                         axisY: {
//                             title: 'Count'
//                         },
//                         data: [{
//                             type: 'pie',
//                             dataPoints: [
//                                 { y: buys,  label: 'buys',  indexLabel: buys  + ' buy orders' },
//                                 { y: sells, label: 'sells', indexLabel: sells + ' sell orders' }
//                             ]
//                         }]
//                     })).render();

//                     (new CanvasJS.Chart(chartDomElement + '_buy_strategy', {
//                         title: {
//                             text: exchange + ' ' + symbol + ' buy strategy'
//                         },
//                         axisX: {
//                             title: 'Price'
//                         },
//                         axisY: {
//                             title: 'Amount'
//                         },
//                         zoomEnabled: true,
//                         data: [{
//                             type: 'scatter',
//                             toolTipContent:
//                                 '<strong>Buy Price:</strong> {buy_price}</br>' +
//                                 '<strong>Buy Amount:</strong> {buy_amount}<br/>' +
//                                 '<strong>Sell Price:</strong> {sell_price}</br>' +
//                                 '<strong>Sell Amount:</strong> {sell_amount}<br/>',
//                             name: 'buy orders',
//                             dataPoints: strategyDataDataPoints.buys
//                         }]
//                     })).render();
//                     (new CanvasJS.Chart(chartDomElement + '_sell_strategy', {
//                         title: {
//                             text: exchange + ' ' + symbol + ' sell strategy'
//                         },
//                         axisX: {
//                             title: 'Price'
//                         },
//                         axisY: {
//                             title: 'Amount'
//                         },
//                         zoomEnabled: true,
//                         data: [{
//                             type: 'scatter',
//                             toolTipContent:
//                                 '<strong>Buy Price:</strong> {buy_price}</br>' +
//                                 '<strong>Buy Amount:</strong> {buy_amount}<br/>' +
//                                 '<strong>Sell Price:</strong> {sell_price}</br>' +
//                                 '<strong>Sell Amount:</strong> {sell_amount}<br/>',
//                             name: 'sell orders',
//                             dataPoints: strategyDataDataPoints.sells
//                         }]
//                     })).render();
                }
            }
        },
        error: function() {
            $('#orders').html('<p class="offline"><strong>Monitoring Offline</strong></p>');
        }
    });
}
$(function() {
    getOrders();
});
