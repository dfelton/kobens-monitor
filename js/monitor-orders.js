(() => {
    window.gemini = {
        domElementId: 'canvas_chart',
        endpoint: 'monitors/trade-repeater-orders.php',
        chart: null,
        isExecuting: false,
        isInit: false,
        chartDefaults: {
            axisX: {title: 'Price'},
            axisY: {title: 'Amount'},
            zoomEnabled: true,
        },
        tooltipContent: '<strong>ID:</strong> {id}</br>' +
            '<strong>Buy Price:</strong> {buy_price}</br>' +
            '<strong>Buy Amount:</strong> {buy_amount}<br/>' +
            '<br/>' +
            '<strong>Sell Price:</strong> {sell_price}</br>' +
            '<strong>Sell Amount:</strong> {sell_amount}<br/>' +
            '<strong>Sell Price Increase:</strong> {sell_price_increase}</br>' +
            '<strong>Sell Price Increase Percent:</strong> {sell_price_increase_percent}</br>' +
            '<br/>' +
            '<strong>Buy Quote Subtotal:</strong> {buy_quote_subtotal}</br>' +
            '<strong>Buy Quote Fees:</strong> {buy_quote_fees}</br>' +
            '<strong>Buy Quote Total:</strong> {buy_quote_total}</br>' +
            '<br/>' +
            '<strong>Sell Quote Subtotal:</strong> {sell_quote_subtotal}</br>' +
            '<strong>Sell Quote Fees:</strong> {sell_quote_fees}</br>' +
            '<strong>Sell Quote Total:</strong> {sell_quote_total}</br>' +
            '<br/>' +
            '<strong>Profit Base:</strong> {profit_base}</br>' +
            '<strong>Profit Quote:</strong> {profit_quote}</br>' +
            '',            
        init: async function() {
            if (this.isInit === false) {
                this.isInit = true
                this.chart = new CanvasJS.Chart(this.domElementId, this.chartDefaults)
                this.update()
            }
        },
        error: () => {
            console.log('error...')
            this.complete()
        },
        complete: function() {
            this.isExecuting = false
            document.getElementById('symbol').disabled = false
            document.getElementById('symbol').focus()
        },
        update: function() {
            if (this.isExecuting === true || this.isInit === false) {
                return
            }
            this.isExecuting = true
            document.getElementById('symbol').disabled = true
            this.fetch()
        },
        getAjaxUrl: function() {
            return this.endpoint + this.getAjaxArgs()
        },
        getAjaxArgs: function() {
            let str = ''
            if (this.getSymbol() !== '') {
                str += '?symbol=' + this.getSymbol()
            }
            if (this.getMax() && this.getMax() !== '0') {
                str += (str === '' ? '?' : '&') +
                    'price_max=' + this.getMax()
            }
            if (this.getMin() && this.getMin() !== '0') {
                str += (str === '' ? '?' : '&') +
                    'price_min=' + this.getMin()
            }
            return str
        },
        getSymbol: () => {
            let select = document.getElementById('symbol')
            return select.options[select.selectedIndex].value
        },
        getMax: () => {
            return document.getElementById('price-max').value
        },
        getMin: () => {
            return document.getElementById('price-min').value
        },
        resetMinMax: () => {
            document.getElementById('price-min').value = '';
            document.getElementById('price-max').value = '';
        },
        updateChart: function(data) {
            parsed = this.parseData(data);
            this.chart.options.title = { text: this.getSymbol() }
            this.chart.options.data = parsed.orders
            this.chart.render()
            document.getElementById('total-orders').innerHTML = parsed.meta.total_orders
            document.getElementById('total-buy-orders').innerHTML = parsed.meta.total_buy_orders
            document.getElementById('total-buy-usd').innerHTML = parsed.meta.total_buy_usd
            document.getElementById('total-sell-orders').innerHTML = parsed.meta.total_sell_orders
            document.getElementById('total-sell-usd').innerHTML = parsed.meta.total_sell_usd
            document.getElementById('total-base-label').innerHTML = parsed.meta.base_symbol
            document.getElementById('total-base-purchased').innerHTML = parsed.meta.total_base_purchased
            document.getElementById('avg-base-label').innerHTML = parsed.meta.base_symbol
            document.getElementById('avg-base-price').innerHTML = parsed.meta.average_base_price
            document.getElementById('current-value').innerHTML = parsed.meta.base_current_value
            this.complete()
        },
        parseData: function(data) {
            let chartData = []
            for (let status in data.orders) {
                let dataPoints = []
                for (let i in data.orders[status]) {
                    dataPoints.push({
                        x: data.orders[status][i].price,
                        y: data.orders[status][i].amount,
                        id: data.orders[status][i].id,
                        color: data.orders[status][i].color,
                        buy_amount: data.orders[status][i].buy_amount,
                        buy_price: data.orders[status][i].buy_price,
                        buy_quote_subtotal: data.orders[status][i].buy_quote_subtotal,
                        buy_quote_fees: data.orders[status][i].buy_quote_fees,
                        buy_quote_total: data.orders[status][i].buy_quote_total,
                        sell_amount: data.orders[status][i].sell_amount,
                        sell_price: data.orders[status][i].sell_price,
                        sell_price_increase: data.orders[status][i].sell_price_increase,
                        sell_price_increase_percent: data.orders[status][i].sell_price_increase_percent,
                        sell_quote_subtotal: data.orders[status][i].sell_quote_subtotal,
                        sell_quote_fees: data.orders[status][i].sell_quote_fees,
                        sell_quote_total: data.orders[status][i].sell_quote_total,
                        profit_base: data.orders[status][i].profit_base,
                        profit_quote: data.orders[status][i].profit_quote
                    })
                }
                chartData.push({
                    type: 'scatter',
                    toolTipContent: this.tooltipContent,
                    name: status,
                    legendText: status,
                    legendMarkerColor: data.orders[status][0].color,
                    showInLegend: true,
                    dataPoints: dataPoints
                })
            }
            return {
                'orders': chartData,
                'meta': data.meta
            }
        },
        fetch: async function() {
            $.ajax({
                dataType: 'json',
                url: this.getAjaxUrl(),
                success: (data) => this.updateChart(data),
                error: () => this.error(),
                complete: () => this.complete()
            })
        }
    }
    window.gemini.init()
})()
