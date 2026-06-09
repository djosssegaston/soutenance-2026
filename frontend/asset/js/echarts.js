$(function (e) {
    'use strict'

    /*-----echart2-----*/
    var chartdata = [{
        name: 'sales',
        type: 'bar',
        data: [110, 160, 95, 185, 105, 155]
    }, {
        name: 'profit',
        type: 'line',
        smooth: true,
        data: [85, 55, 125, 105, 95, 155]
    }, {
        name: 'growth',
        type: 'bar',
        data: [125, 145, 105, 155, 115, 185]
    }];
    
    var chart = document.getElementById('echart1');
    var barChart = echarts.init(chart);
    var option = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: ['2019', '2020', '2021', '2022', '2023', '2024'],
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        tooltip: {
            show: true,
            showContent: true,
            alwaysShowContent: true,
            triggerOn: 'mousemove',
            trigger: 'axis',
            axisPointer: {
                label: {
                    show: false,
                }
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata,
        color: ['#0d9c1e', '#09ad95', '#007F6E',]
    };
    barChart.setOption(option);
    window.addEventListener('resize',function(){
        barChart.resize();
    })

    /*-----echart1-----*/
    var chartdata2 = [{
        name: 'sales',
        type: 'line',
        smooth: true,
        data: [130, 260, 130, 370, 130, 400], 
        color: ['#0d9c1e']
    }, {
        name: 'Profit',
        type: 'line',
        smooth: true,
        size: 10,
        data: [90, 130, 290, 110, 110, 130], 
        color: ['#007F6E']
    }];
    
    var chart2 = document.getElementById('echart2');
    var barChart2 = echarts.init(chart2);
    var option2 = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: ['2014', '2015', '2016', '2017', '2018', '2024'],
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata2
    };
    barChart2.setOption(option2);
    window.addEventListener('resize',function(){
        barChart2.resize();
    })

    /*-----echart3-----*/
    var option3 = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '32',
        },
        xAxis: {
            type: 'value',
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        yAxis: {
            type: 'category',
            data: ['2014', '2015', '2016', '2017', '2018', '2024'],
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: '#c0dfd8'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata,
        color: ['#0d9c1e', '#09ad95', '#007F6E',]
    };
    var chart3 = document.getElementById('echart3');
    var barChart3 = echarts.init(chart3);
    barChart3.setOption(option3);
    window.addEventListener('resize',function(){
        barChart3.resize();
    })

    /*-----echart4-----*/
    var option4 = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '32',
        },
        xAxis: {
            type: 'value',
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        yAxis: {
            type: 'category',
            data: ['2015', '2016', '2017', '2018', '2024'],
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata2,
        color: ['#1170e4', '#d43f8d', '#09ad95']
    };
    var chart4 = document.getElementById('echart4');
    var barChart4 = echarts.init(chart4);
    barChart4.setOption(option4);
    window.addEventListener('resize',function(){
        barChart4.resize();
    })

    /*-----echart5-----*/
    var chartdata3 = [{
        name: 'sales',
        type: 'bar',
        stack: 'Stack',
        data: [150, 190, 220, 160, 300, 220, 260, 160, 260] 
    }, {
        name: 'Profit',
        type: 'bar',
        stack: 'Stack',
        data: [130, 150, 170, 520, 260, 270, 120, 220, 320] 
    }];
    
    var option5 = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: [ '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2024'],
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata3,
        color: ['#0d9c1e', '#007F6E']
    };
    var chart5 = document.getElementById('echart5');
    var barChart5 = echarts.init(chart5);
    barChart5.setOption(option5);
    window.addEventListener('resize',function(){
        barChart5.resize();
    })

    /*-----echart6-----*/
    var option6 = {
        grid: {
            top: '6',
            right: '10',
            bottom: '17',
            left: '32',
        },
        xAxis: {
            type: 'value',
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        yAxis: {
            type: 'category',
            data: [ '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2024'],
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata3,
        color: ['#0d9c1e', '#007F6E']
    };
    var chart6 = document.getElementById('echart6');
    var barChart6 = echarts.init(chart6);
    barChart6.setOption(option6);
    window.addEventListener('resize',function(){
        barChart6.resize();
    })

    /*-----echart7-----*/
    var chartdata4 = [{
        name: 'data',
        type: 'line',
        data: [25, 20, 36, 18, 15, 22, 30, 27]
    }];
    var option7 = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: [ '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2024'],
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata4,
        color: ['#0d9c1e']
    };
    var chart7 = document.getElementById('echart7');
    var lineChart = echarts.init(chart7);
    lineChart.setOption(option7);
    window.addEventListener('resize',function(){
        lineChart.resize();
    })

    /*-----echart8-----*/
    var chartdata5 = [{
        name: 'data',
        type: 'line',
        smooth: true,
        data: [21, 22, 36, 18, 15, 23, 25, 24]
    }];
    var option8 = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: [ '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2024'],
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLine: {
                lineStyle: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9ba6b5'
            }
        },
        series: chartdata5,
        color: ['#007F6E']
    };
    var chart8 = document.getElementById('echart8');
    var lineChart2 = echarts.init(chart8);
    lineChart2.setOption(option8);
    window.addEventListener('resize',function(){
        lineChart2.resize();
    })
});