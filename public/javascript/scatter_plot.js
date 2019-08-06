function draw_scatter_plot (json) {
	var count = 0;
	var series = [];
	for (var label in json['series']['data']) {
		series.push({
			    lineWidth:2,
				name: json['series']['data'][label]['name'],
				data: json['series']['data'][label]['values'],
				color: json['series']['data'][label]['color'],
				marker: {
					symbol: json['series']['data'][label]['symbol']
				}
		});
		count++;
	}
	
	$('#container').highcharts({
        chart: {
            type: 'scatter',
            zoomType: 'xy',
            marginTop:100
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        credits: {
            enabled: false
        },
        exporting: {
        	enabled: false
        },
        xAxis: {
            title: {
                enabled: true,
                text: ''
            },
            showLastLabel: true,
            gridLineColor: '#ADAFB3',
            gridLineWidth: 1,
            min: 1,
            max: 25,
            tickInterval: 1,
            plotLines: [{
                color: 'red', // Color value
                dashStyle: 'dash', // Style of the plot line. Default to solid
                value: 5, // Value of where the line will appear
                width: 2 // Width of the line    
              },{
                color: 'red', // Color value
                dashStyle: 'dash', // Style of the plot line. Default to solid
                value: 10, // Value of where the line will appear
                width: 2 // Width of the line    
              },{
                color: 'red', // Color value
                dashStyle: 'dash', // Style of the plot line. Default to solid
                value: 15, // Value of where the line will appear
                width: 2 // Width of the line    
              },{
                color: 'red', // Color value
                dashStyle: 'dash', // Style of the plot line. Default to solid
                value: 20, // Value of where the line will appear
                width: 2 // Width of the line    
	          }],
          labels: {
	          useHTML: true,
	          formatter: function () {
          		return '<span class="tooltips" data-container="body" data-original-title="Q'+this.value+': '+ json['questions'][this.value-1]['question'] +'" data-toggle="tooltip" data-placement="bottom">'+this.value+'</span>';
	          }
  		  }
        },
        yAxis: {
        	min: 1,
            max: 5,
            tickInterval: 1,
            title: {
                text: ''
            }
        },
        legend: {
            layout: 'horizontal',
            align: 'center',
            verticalAlign: 'bottom',
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
            borderWidth: 1,
            padding: 12,
            margin: 30
        },
        plotOptions: {
        	series: {
                point: {
                    events: {
                        click: function () {
                            if (typeof(this.series.graph.visible) == 'undefined' || !this.series.graph.visible) {
                                this.series.graph.show();
                                this.series.graph.visible = true;
                            } else {
                                this.series.graph.hide();
                                this.series.graph.visible = false;
                            }
                        }
                    }
                }
            },
            scatter: {
                marker: {
                    radius: 7,
                    states: {
                        hover: {
                            enabled: true,
                            lineColor: 'rgb(100,100,100)'
                        }
                    }
                },
                states: {
                    hover: {
                        marker: {
                            enabled: false
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<b>{series.name}</b><br>',
                    pointFormat: 'Question {point.x} average score: {point.y}'
                },
                events: {
                	hide: function () {
                    	$('.tooltips').tooltip();
                    },
                    show: function () {
                    	$('.tooltips').tooltip();
                    }
                }
            }
        },
        series: series
    }, function (chart) {
    	setTimeout(function(){
    		for (var i = 0; i < count; i++) {
    			chart.series[i].graph.hide();
    		}
        },1);
    	
    	chart.renderer.text('<span style="font-size:15px;">Standard 1: </span><br/>'+json['standards'][0], 55,40).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'130px'
        }).add();
    	chart.renderer.text('<span style="font-size:15px;">Standard 2: </span><br/>'+json['standards'][1], 223,40).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'130px'
        }).add();
    	chart.renderer.text('<span style="font-size:15px;">Standard 3: </span><br/>'+json['standards'][2], 417,40).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'130px'
        }).add();
    	chart.renderer.text('<span style="font-size:15px;">Standard 4: </span><br/>'+json['standards'][3], 614,40).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'140px'
        }).add();
    	chart.renderer.text('<span style="font-size:15px;">Standard 5: </span><br/>'+json['standards'][4], 813,40).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'130px'
        }).add();
    });
}