function draw_bar_graph (json) {
	for (var label in json['question_blocks']) {
		var data = [];
		data.push({y:parseFloat(json['question_blocks'][label]['aggraverages'][0]), color:'#71699B'});
		data.push({y:parseFloat(json['question_blocks'][label]['aggraverages'][1]), color:'#B8B3CE'});
		data.push({y:parseFloat(json['question_blocks'][label]['aggraverages'][2]), color:'#9590BE'});
		data.push({y:parseFloat(json['question_blocks'][label]['aggraverages'][3]), color:'#DFDFEA'});
		data.push({y:parseFloat(json['question_blocks'][label]['aggraverages'][4]), color:'#3B375F'});
		
		$('#container_block'+json['question_blocks'][label]['qnumber']).highcharts({
			chart: {
				marginBottom: 70,
				events: {
	    			redraw: function () {
	    				$('.tooltips').tooltip();
	                },
	            }
			},
			title: {
	            text:''
	        },
			xAxis: {
					categories: ['Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5'],
					gridLineColor: '#ADAFB3',
		            lineColor: '#ADAFB3',
		            tickWidth:0,
		            labels: {
		            	style: {
		            		color: 'black',
		            		fontSize: 14
		            	},
						x: 0,
		            	useHTML: true,
		            	formatter: function () {
		            		switch (this.value) {
	                    	case 'Standard 1':
	                    		return '<span class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][0] +'" data-toggle="tooltip" data-placement="bottom" style="margin-left:-35px;">'+this.value+'</span>';
	                    	case 'Standard 2':
	                    		return '<span class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][1] +'" data-toggle="tooltip" data-placement="bottom" style="margin-left:-35px;">'+this.value+'</span>';
	                    	case 'Standard 3':
	                    		return '<span class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][2] +'" data-toggle="tooltip" data-placement="bottom" style="margin-left:-35px;">'+this.value+'</span>';
	                    	case 'Standard 4':
	                    		return '<span class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][3] +'" data-toggle="tooltip" data-placement="bottom" style="margin-left:-35px;">'+this.value+'</span>';
	                    	case 'Standard 5':
	                    		return '<span class="tooltips" data-container="body" data-original-title="'+this.value+': '+ json['standards'][4] +'" data-toggle="tooltip" data-placement="bottom" style="margin-left:-35px;">'+this.value+'</span>';
		            		}
	            		}
		            }
				},
			yAxis: {
	            min: 1,
	            max: 5,
	            gridLineColor: 'transparent',
	            lineColor: '#ADAFB3',
	            lineWidth: 1,
	            tickWidth:1,
	            tickInterval: 1,
	            title: {
	            	text:''
	            },
	            labels: {
	            	style: {
	            		color: '#71699B',
	            		fontSize: 14,
	            	},
	            	formatter: function () {
	                    switch (this.value) {
	                    	case 1:
	                    		return 'Strongly Disagree-1';
	                    	case 2:
	                    		return 'Disagree-2';
	                    	case 3:
	                    		return 'Neutral-3';
	                    	case 4:
	                    		return 'Agree-4';
	                    	case 5:
	                    		return 'Strongly Agree-5';
	                    }
	                }
	            }
			},
			tooltip: {
	        	tickInterval: 1,
	            min: 1,
	            max: 26,
	        	formatter: function () {
	        		if (this.series.type == 'column') {
	        			return json['data_type'] + ' average for ' + this.x + ' : ' + Number(this.y).toFixed(1);
	        		} else {
	        			return 'School average for ' + this.x + ' : ' + Number(this.y).toFixed(1);
	        		}
	                
	            }
	        },
			legend: {
	            enabled: false
	        },
	        credits: {
	            enabled: false
	        },
	        exporting: {
	        	enabled: false
	        },
			series: [{
				type: 'column',
				data: data
			},{
				type: 'line',
				color: '#F7AC5F',
				dashStyle: 'ShortDot',
                lineWidth: 0,
                marker: {
                	radius:12
                },
				data: [parseFloat(json['school_averages'][0]), parseFloat(json['school_averages'][1]), parseFloat(json['school_averages'][2]), parseFloat(json['school_averages'][3]), parseFloat(json['school_averages'][4])]
			}]
		});
	}
	
}

