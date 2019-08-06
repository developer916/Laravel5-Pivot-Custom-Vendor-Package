function isInt(value) {
    return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
}


function draw_mid_chart(json) {
    for (var i = 1; i <= 25; i++) {
        var qnumber = ('Q' + i);
        var chartdata = [];
        var previousdata = [];
        var linevalue = [];
        var catagories = [];
        var labels = [];
        var column_count = 0;


        for (var label in json[qnumber]['series']) {
            //console.log('1 adding ' + label);
            catagories.push(label);
            chartdata.push({y: json[qnumber]['series'][label], color: json[qnumber]['color'][label]});
            column_count += 1;
        }

        for (var label in json[qnumber]['previous_series']) {
            previousdata.push({
                y: json[qnumber]['previous_series'][label],
                color: "#d4d5d6"
            });
            column_count += 1;
        }




        var series = [
            {
                type: 'column',
                data: previousdata,
                pointWidth: 460 / column_count,
                color: "#d4d5d6",
                groupPadding: ".30",
            },
            {
                type: 'column',
                data: chartdata,
                pointWidth: 360 / column_count,
                groupPadding: ".30",
            }

        ];

        for(var item in series)
        {
            console.log(item);
        }

        $('#chart' + qnumber).highcharts({

            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },

            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        formatter: function () {
                            if (this.y == 0) {
                                return 'N/A';
                            } else {
                                return '';
                            }
                        }
                    }
                }
            },

            xAxis: [
                {
                    type: 'category',
                    categories: catagories,
                    labels: {
                        rotation: 0,
                        style: {
                            fontSize: '10px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                {

                },

            ],
            yAxis: [
                {
                    min: 0,
                    max: 5,
                    tickInterval: 0.5,
                    title: {
                        text: ''
                    },
                    labels: {
                        formatter: function () {
                            return Number(this.value).toFixed(1);
                        }
                    },
                    plotLines: [{
                        color: '#F7AC5F',
                        dashStyle: 'ShortDot',
                        width: 4,
                        value: json[qnumber]['linevalue'],
                        zIndex: 4
                    }]
                },
            ],
            legend: {
                enabled: false
            },
            tooltip: {
                formatter: function () {
                    return json['tooltips'][this.x] + ' : ' + Number(this.y).toFixed(1);
                }
            },
            series: series
        });
    }
}

$(function () {
    $('#tab-content .tab-pane:not(:first-child)').removeClass('active');

    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});