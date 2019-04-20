function monitorCpuAndMemory()
{
    if ($('#refresh_cpuMemory').val() == '0') {
        setTimeout(function() { monitorCpuAndMemory(); }, 60000);
        return;
    }
    $.ajax({
        dataType: 'json',
        url: '/monitors/cpu-memory.php',
        success: function( data ) {
            var monitorTypes = ['cpu', 'memory'];
            for (var i in monitorTypes) {
            if ($('#' + monitorTypes[i] + ' > .offline').get(0) != undefined) {
                $('#' + monitorTypes[i] + ' > .offline').remove();
            }
            if ($('#' + monitorTypes[i] + ' > .spinner').get(0) != undefined) {
                $('#' + monitorTypes[i] + ' > .spinner').remove();
            }
            }
            for (var key in data) {
                var chartDataMemory = [];
                var chartDataCpu = [];
                for (var pid in data[key]) {
                    var processDataMemory = [];
                    var processDataCpu = [];
                    for (var i in data[key][pid]) {
                        var date = new Date(data[key][pid][i][0]);
                        processDataCpu.push({
                            x: date,
                            y: data[key][pid][i][1]
                        });
                        processDataMemory.push({
                            x: date,
                            y: data[key][pid][i][2]
                        });
                    }
                    chartDataCpu.push({
                        type: 'area',
                        yValueFormatString: '###.##',
                        dataPoints: processDataCpu
                    });
                    chartDataMemory.push({
                        type: 'area',
                        yValueFormatString: '#,###',
                        dataPoints: processDataMemory
                    });
                }
                for (var i in monitorTypes) {
                    if ($('#' + monitorTypes[i] + '_' + key).get(0) == undefined) {
                        $('#' + monitorTypes[i]).append('<div class="monitor ' + monitorTypes[i] + '"><div class="monitor-box clear-after" id="' + monitorTypes[i] + '_' + key + '"></div></div>');
                    }
                }
                var titleText = key.replace( /_/g, ':' );
                (new CanvasJS.Chart('cpu_' + key, {
                    title: {
                    text: titleText
                    },
                    zoomEnabled: true,
                    axisY: {
                        title: 'CPU Usage',
                        titleFontSize: 24,
                        suffix: '%'
                    },
                    data: chartDataCpu
                })).render();
                (new CanvasJS.Chart('memory_' + key, {
                    title: {
                        text: titleText
                    },
                    zoomEnabled: true,
                    axisY: {
                        title: 'Memory Usage',
                        titleFontSize: 24,
                        suffix: 'KB'
                    },
                    data: chartDataMemory
                })).render();
            }
        },
        error: function() {
            $('#memory').html('<p class="offline"><strong>Monitoring Offline</strong></p>');
            $('#cpu').html('<p class="offline"><strong>Monitoring Offline</strong></p>');
        }
    });
    setTimeout(function() { monitorCpuAndMemory(); }, 60000);
}
$(function() { monitorCpuAndMemory(); });
