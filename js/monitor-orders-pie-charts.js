function monitorOrdersPie(forceFetch)
{
    if (forceFetch == true || document.querySelector('#refresh:checked') !== null) {
        $.ajax({
            dataType: 'json',
            url: 'monitors/trade-repeater-orders.php',
            success: function( data ) {
                if ($('#orders > .offline').get(0) != undefined) {
                    $('#orders > .offline').remove();
                }
                if ($('#orders > .spinner').get(0) != undefined) {
                    $('#orders > .spinner').remove();
                }
                for (var exchange in data) {
                    for (var symbol in data[exchange]) {
                        var buys = 0;
                        var sells = 0;
                        for (var side in data[exchange][symbol]) {
                            for (var i in data[exchange][symbol][side]) {
                                if (side == 'buy') {
                                    buys++;
                                } else {
                                    sells++;
                                }
                            }
                        }
                        var chartDomElement = 'orders_' + exchange + '_' + symbol;
                        if ($('#' + chartDomElement).get(0) == undefined) {
                            $('#orders').append('<div class="monitor orders"><div class="monitor-box clear-after" id="' + chartDomElement + '_count"></div></div>');
                        }
                        (new CanvasJS.Chart(chartDomElement + '_count', {
                            title: {
                                text: 'active ' + exchange + ' ' + symbol + ' order count',
                            },
                            axisY: {
                                title: 'Count'
                            },
                            data: [{
                                type: 'pie',
                                dataPoints: [
                                    { y: buys,  label: 'buys',  indexLabel: buys  + ' buy orders' },
                                    { y: sells, label: 'sells', indexLabel: sells + ' sell orders' }
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
    }
    setTimeout(function() { monitorOrdersPie(); }, 15000);
}
$(function() { monitorOrdersPie(true); });
