function draw_comparison_table (json) {
	
	$('#container').highcharts({

        chart: {
            type: 'heatmap',
            marginTop: 40,
            marginBottom: 80,
            events: {
    			redraw: function () {
    				$('.tooltips').tooltip();
                },
            }
        },


        title: {
            text: ''
        },

        xAxis: {
            categories: ['Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5'],
            opposite:true,
            lineColor:'#fff',
            tickColor:'transparent',
            labels: {
                style: {
                    fontSize: '15px',
                },
                useHTML: true,
            	formatter: function () {
            		switch (this.value) {
                	case 'Standard 1':
                		return '<div class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][0] +'" data-toggle="tooltip" data-placement="bottom">'+this.value+'</div>';
                	case 'Standard 2':
                		return '<div class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][1] +'" data-toggle="tooltip" data-placement="bottom">'+this.value+'</div>';
                	case 'Standard 3':
                		return '<div class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][2] +'" data-toggle="tooltip" data-placement="bottom">'+this.value+'</div>';
                	case 'Standard 4':
                		return '<div class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][3] +'" data-toggle="tooltip" data-placement="bottom">'+this.value+'</div>';
                	case 'Standard 5':
                		return '<div class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][4] +'" data-toggle="tooltip" data-placement="bottom">'+this.value+'</div>';
            		}
        		}
            },
        },

        yAxis: {
            categories: json['catagories'],
            title: null,
            labels: {
                style: {
                    fontSize: '15px',
                }
            }
        },

        colorAxis: {
            min: 1,
            max: 10,
            stops: [
                [0.1, '#DC9394'],
                [0.2, '#F3DBDB'],
                [0.3, '#B8CDE1'],
                [0.4, '#EAF0DF'],
                [0.5, '#C1D3A1'],
                [0.6, '#EDEDEE']
            ],
        },

        legend: {
            enabled:false
        },
        credits: {
            enabled: false
        },
        exporting: {
        	enabled: false
        },

        tooltip: {
            formatter: function () {
            	if (this.point.y > 0) {
            		return 'The average for ' + this.series.yAxis.categories[this.point.y] + ' in ' + this.series.xAxis.categories[this.point.x]+': '+json['standards'][this.point.x] + ' is '+json['standard_values'][this.point.x][this.series.yAxis.categories[this.point.y]];
            	} else {
            		return 'The school average for '+ this.series.xAxis.categories[this.point.x]+ ' is ' +  json['school_average'][this.point.x];
            	}
            }
        },

        series: [{
            name: 'Departments',
            borderWidth: 5,
            borderColor: '#fff',
            data: json['series']['data'],
            dataLabels: {
                enabled: true,
                color: '#000000',
                style: {
                	fontSize:15,
                	textShadow:'none',
                	fontWeight:'normal'
                },
                formatter: function () {
                	switch (this.point.value) {
                		case 1:
                			return 'Lowest';
                		case 2:
                			return 'Below';
                		case 3:
                			return 'Average';
                		case 4:
                			return 'Above';
                		case 5:
                			return 'Highest';
                		case 6:
                			return json['school_average'][this.point.x];
                	}
                }
            }
        }]

    });
    
    // Remove text garbage which is visible on printing
    var text = document.querySelectorAll('svg text');
    for (var i = 0; i < text.length; ++i) {
        if ( text[i].parentNode.tagName === 'svg' )
            text[i].remove();
    }
}