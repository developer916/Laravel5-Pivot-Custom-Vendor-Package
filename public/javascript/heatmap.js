function show_heatmap(json) {
	var series = [{
		type: 'column',
        name: 'Average',
        data: json['stats'],
        pointPlacement: 'on'
	}];



	var lines_arr_length = json['lines'].length;
	var offset = lines_arr_length / 5;

	for (var i = 0; i < lines_arr_length; i++) {
		series.push(  {
				type:'line',
				name:json['lines'][i]['description'],
				data:[json['lines'][i]['value'],json['lines'][i]['value'],json['lines'][i]['value'],json['lines'][i]['value'],json['lines'][i]['value'],json['lines'][i]['value']],
				pointStart:json['lines'][i]['position'],
				color:json['lines'][i]['color'],
				dashStyle:json['lines'][i]['linestyle'],
				pointPlacement: 'between',
				visible:json['lines'][i]['visible'],
				lineWidth:json['lines'][i]['width'],
				marker:{
					states:{
						hover:{
							enabled:false
							}
						}
					}
		});
	}

    $('#container').highcharts({
        chart: {
            polar: true,
            events: {
	        	load: function() {
			        $.each(this.xAxis[0].ticks, function (i, tick) {
			        	if(i == 26) {
			        		return;
			        	}
			        	var distance = 2;
			            var label = tick.label;
			                angle = (((i - 1)/25)*360);

			            if (angle >= 90 && angle <= 270) {
			            	angle -= 180;
			            }

			            var y = tick.label.xy.y;
			            var x = tick.label.xy.x;


			            tick.label.attr({
			            	x: x,
			            	y: y,
			            	rotation: angle,
			            });
			        });
                    $.each(this.xAxis[1].ticks, function (i, tick) {
                        if(i == 26) {
                            return;
                        }
                        var distance = 2;
                        var label = tick.label;
                        angle = (((i - 1)/25)*360);

                        if (angle >= 90 && angle <= 270) {
                            angle -= 180;
                        }

                        var y = tick.label.xy.y;
                        var x = tick.label.xy.x;


                        tick.label.attr({
                            x: x,
                            y: y,
                            rotation: angle
                        });
                    });




	        	}
	        }
        },

        title: {
            text: ''
        },

        pane: {
            startAngle: 0,
            endAngle: 360,
            size: '60%'
        },
        credits: {
            enabled: false
        },

        xAxis: [
            {
                tickInterval: 1,
                min: 1,
                max: 26,
                gridLineColor: 'transparent',
                lineColor: 'transparent',
                labels: {
                    align: 'center',
                    x: 0,
                    y: 5,
                    formatter: function () {
                        if (this.value == 26) {
                            return;
                        }
                        return 'Q' + this.value;
                    },
                    distance: 2
                }
            },
            {
                tickInterval: 1,
                min: 1,
                max: 26,
                gridLineColor: 'transparent',
                lineColor: 'transparent',
                labels: {
                    useHTML: true,
                    align: 'center',
                    x: -.5,
                    y: 5,
                    formatter: function () {
                        if (this.value == 26) {
                            return;
                        }
                        return '<strong class="question-value-tick" style="position:relative;background-color:rgba(0, 0, 0, 0.2);padding:1px 7px">'+json['stats'][this.value - 1]+'</strong>';
                    },
                    distance: 33,
                }
            },
        ],

        yAxis: {
            min: 0,
            max:5,
            gridLineColor: 'transparent',
            zIndex:99,
            labels: {
                formatter: function () {
                    return '';
                }
            }
        },
        tooltip: {
            useHTML: true,
        	tickInterval: 1,
            min: 1,
            max: 26,
        	formatter: function () {
        		if (this.series.type == 'column') {
        			return 'Q' + this.x + '. ' + json['questions'][this.x - 1] + ' : ' + Number(this.y).toFixed(1);
        		} else {
        			return this.series.name + ': ' + Number(this.y).toFixed(1);
        		}
            },
            style: {
                "padding": "1px",
                "backgroundColor": "#ffffff"
            }
        },
        legend: {
        	enabled: false
        },

        plotOptions: {
            series: {
                pointStart: 1,
                pointInterval: 1
            },
            column: {
                pointPadding: 0.14,
                groupPadding: 0,
                color:'#9CAFD0'
            },
            line: {
                connectEnds:false,
                dashStyle:"ShortDot",
                marker: {
                	enabled:false
                }

            }
        },
        exporting: {
        	enabled: false
        },
        series: series
    }, function (chart) {
    	var hasboxtext = (typeof json['boxtext'] !== 'undefined');

    	chart.renderer.circle(570, 400, 250).attr({
            fill: 'transparent',
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();


    	chart.renderer.text('<span style="font-size:15px;">Standard 1</span> <br/>'+json['standards'][0], 750,40).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'130px'
        }).add();
    	chart.renderer.path(['M', 750,80, 'L', 870, 80]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M', 750,80, 'L', 710, 153]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(776, 110, 25).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.text(Number(json['lines'][offset*0]['value']).toFixed(1), 758,117).css({
            color: 'white',
            fontSize: '23px'
        }).add();
    	if (hasboxtext) {
    		chart.renderer.rect(811, 90, 50,40, 0).attr({
    			fill: '#4F4783',
                stroke: '#4F4783',
                'stroke-width': 1
    		}).add();
    		chart.renderer.text(Number(json['boxtext'][0]).toFixed(1), 819,117).css({
                color: 'white',
                fontSize: '23px'
            }).add();
    	}


    	chart.renderer.text('<span style="font-size:15px;">Standard 2</span> <br/>'+json['standards'][1], 919,450).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'130px'
        }).add();
    	chart.renderer.path(['M', 919,490, 'L', 1053, 490]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M', 919,490, 'L', 845, 455]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(945, 518, 25).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.text(Number(json['lines'][offset*1]['value']).toFixed(1), 927,525).css({
            color: 'white',
            fontSize: '23px'
        }).add();
    	if (hasboxtext) {
    		chart.renderer.rect(980, 498, 50,40, 0).attr({
    			fill: '#4F4783',
                stroke: '#4F4783',
                'stroke-width': 1
    		}).add();
    		chart.renderer.text(Number(json['boxtext'][1]).toFixed(1), 988,525).css({
                color: 'white',
                fontSize: '23px'
            }).add();
    	}


    	chart.renderer.text('<span style="font-size:15px;">Standard 3</span> <br/>'+json['standards'][2], 710,652).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'130px'
        }).add();
    	chart.renderer.path(['M', 700,709, 'L', 835, 709]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M', 700,709, 'L', 605, 685]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(736, 745, 25).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.text(Number(json['lines'][offset*2]['value']).toFixed(1), 718,753).css({
            color: 'white',
            fontSize: '23px'
        }).add();
    	if (hasboxtext) {
    		chart.renderer.rect(771, 725, 50,40, 0).attr({
    			fill: '#4F4783',
                stroke: '#4F4783',
                'stroke-width': 1
    		}).add();
    		chart.renderer.text(Number(json['boxtext'][2]).toFixed(1), 779,753).css({
                color: 'white',
                fontSize: '23px'
            }).add();
    	}


    	chart.renderer.text('<span style="font-size:15px;">Standard 4</span> <br/>'+json['standards'][3], 80,500).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'160px'
        }).add();
    	chart.renderer.path(['M',80,555, 'L', 227, 555]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M', 227,555, 'L', 310, 516]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(106, 588, 25).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.text(Number(json['lines'][offset*3]['value']).toFixed(1), 88,595).css({
            color: 'white',
            fontSize: '23px'
        }).add();
    	if (hasboxtext) {
    		chart.renderer.rect(141, 568, 50,40, 0).attr({
    			fill: '#4F4783',
                stroke: '#4F4783',
                'stroke-width': 1
    		}).add();
    		chart.renderer.text(Number(json['boxtext'][3]).toFixed(1), 149,595).css({
                color: 'white',
                fontSize: '23px'
            }).add();
    	}


    	chart.renderer.text('<span style="font-size:15px;">Standard 5</span> <br/>'+json['standards'][4], 160,110).css({
            color: '#4572A7',
            fontSize: '12px',
            width:'160px'
        }).add();
    	chart.renderer.path(['M',160,165, 'L', 309, 165]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M', 309,165, 'L', 370, 194]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(186, 198, 25).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.text(Number(json['lines'][offset*4]['value']).toFixed(1), 168,205).css({
            color: 'white',
            fontSize: '23px'
        }).add();
    	if (hasboxtext) {
    		chart.renderer.rect(221, 178, 50,40, 0).attr({
    			fill: '#4F4783',
                stroke: '#4F4783',
                'stroke-width': 1
    		}).add();
    		chart.renderer.text(Number(json['boxtext'][4]).toFixed(1), 229,205).css({
                color: 'white',
                fontSize: '23px'
            }).add();
    	}

    	chart.renderer.path(['M',570, 396, 'L', 525, 35]).attr({
            stroke: 'white',
            'stroke-width': 10
        }).add();
    	chart.renderer.path(['M',537, 172, 'L', 534, 152]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M',547, 171, 'L', 545, 151]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();

    	chart.renderer.path(['M',568, 398, 'L', 892, 246]).attr({
            stroke: 'white',
            'stroke-width': 10
        }).add();
    	chart.renderer.path(['M',776, 295, 'L', 793, 287]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M',780, 304, 'L', 797, 296]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();

    	chart.renderer.path(['M',573, 400, 'L', 791, 633]).attr({
            stroke: 'white',
            'stroke-width': 10
        }).add();
    	chart.renderer.path(['M',730, 561, 'L', 746, 578]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M',722, 567, 'L', 739, 585]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();

    	chart.renderer.path(['M',570, 398, 'L', 427, 658]).attr({
            stroke: 'white',
            'stroke-width': 10
        }).add();
    	chart.renderer.path(['M',466, 598, 'L', 453, 621]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M',456, 594, 'L', 444, 616]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();

    	chart.renderer.path(['M',571, 398, 'L', 275, 341]).attr({
            stroke: 'white',
            'stroke-width': 10
        }).add();
    	chart.renderer.path(['M',344, 349, 'L', 325, 346]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();
    	chart.renderer.path(['M',343, 359, 'L', 324, 356]).attr({
            stroke: '#BEC0C1',
            'stroke-width': 1
        }).add();

    	chart.renderer.circle(49, 760, 13).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(69, 760, 2).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(77, 760, 2).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(85, 760, 2).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.circle(93, 760, 2).attr({
            fill: '#F7AC5F',
            stroke: '#F7AC5F',
            'stroke-width': 1
        }).add();
    	chart.renderer.text('School average score per Standard', 105, 765).css({
            color: '#4572a7',
            fontSize: '15px'
        }).add();

    	if (hasboxtext) {
    		chart.renderer.rect(37, 778, 24,20, 0).attr({
    			fill: '#4F4783',
                stroke: '#4F4783',
                'stroke-width': 1
    		}).add();

    		chart.renderer.path(['M', 68, 788, 'L', 96, 788]).attr({
                stroke: '#4F4783',
                'stroke-width': 3
            }).add();
    		chart.renderer.text(json['legend_name']+' average score per Standard', 105, 794).css({
                color: '#4572a7',
                fontSize: '15px'
            }).add();

            //Teacher Last Survey Results Key
            chart.renderer.rect(538, 778, 24,20, 0).attr({
                fill: '#d4d5d6',
                stroke: '#d4d5d6',
                'stroke-width': 1
            }).add();

            chart.renderer.text(json['legend_name']+' Question average score', 600, 794).css({
                color: '#4572a7',
                fontSize: '15px'
            }).add();
    	}
    	chart.renderer.text('Scores range from 1 (strongly disagree)<br/>to 5(strongly agree)', 35, 714).css({
            color: '#4572a7',
            fontSize: '15px'
        }).add();
    });
}