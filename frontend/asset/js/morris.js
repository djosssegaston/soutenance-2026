/*---- morrisBar2----*/
$(function(e) {
    'use strict';

    new Morris.Area({
        element: 'morrisBar2',
        data: [{
            x: '2020 Q4',
            y: 3,
            z: 7
        }, {
            x: '2021 Q1',
            y: 3,
            z: 4
        }, {
            x: '2021 Q2',
            y: null,
            z: 1
        }, {
            x: '2021 Q3',
            y: 2,
            z: 5
        }, {
            x: '2021 Q4',
            y: 8,
            z: 2
        }, {
            x: '2022 Q1',
            y: 4,
            z: 4
        }],
        xkey: 'x',
        ykeys: ['y', 'z'],
        lineColors: ['#0d9c1e', '#007F6E'],
        labels: ['Y', 'Z'],
        resize: true
    }).on('click', function(i, row) {
        console.log(i, row);
    });

    /*---- morrisBar3----*/
    new Morris.Area({
        element: 'morrisBar3',
        behaveLikeLine: true,
        data: [{
            x: '2021 Q1',
            y: 3,
            z: 3
        }, {
            x: '2021 Q2',
            y: 2,
            z: 1
        }, {
            x: '2021 Q3',
            y: 2,
            z: 4
        }, {
            x: '2021 Q4',
            y: 3,
            z: 3
        }],
        xkey: 'x',
        ykeys: ['y', 'z'],
        lineColors: ['#0d9c1e', '#007F6E'],
        labels: ['Y', 'Z'],
        resize: true
    });

    /*---- morrisBar4----*/
    new Morris.Bar({
        element: 'morrisBar4',
        data: [{
            x: '2021 Q1',
            y: 0
        }, {
            x: '2021 Q2',
            y: 1
        }, {
            x: '2021 Q3',
            y: 2
        }, {
            x: '2021 Q4',
            y: 3
        }, {
            x: '2022 Q1',
            y: 4
        }, {
            x: '2022 Q2',
            y: 5
        }, {
            x: '2022 Q3',
            y: 6
        }, {
            x: '2022 Q4',
            y: 7
        }, {
            x: '2023 Q1',
            y: 8
        }],
        xkey: 'x',
        ykeys: ['y'],
        labels: ['Y'],
        barColors: function(row, series, type) {
            if (type === 'bar') {
                var red = Math.ceil(0 * row.y / this.ymax);
                return '#0d9c1e';
            } else {
                return '#000';
            }
        }
    });
    var day_data = [{
        "period": "Jan",
        "licensed": 3407,
        "sorned": 660
    }, {
        "period": "Feb",
        "licensed": 3351,
        "sorned": 629
    }, {
        "period": "Mar",
        "licensed": 3269,
        "sorned": 618
    }, {
        "period": "Apr",
        "licensed": 3246,
        "sorned": 661
    }, {
        "period": "May",
        "licensed": 3257,
        "sorned": 667
    }, {
        "period": "June",
        "licensed": 3248,
        "sorned": 627
    }, {
        "period": "July",
        "licensed": 3171,
        "sorned": 660
    }, {
        "period": "Aug",
        "licensed": 3171,
        "sorned": 676
    }, {
        "period": "Sept",
        "licensed": 3201,
        "sorned": 656
    }, {
        "period": "Oct",
        "licensed": 3215,
        "sorned": 622
    }];

    /*---- morrisBar5----*/
    new Morris.Bar({
        element: 'morrisBar5',
        data: day_data,
        xkey: 'period',
        ykeys: ['licensed', 'sorned'],
        labels: ['Licensed', 'SORN'],
        barColors: ['#0d9c1e', '#007F6E'],
        xLabelAngle: 0,
        resize: true

    });
    var nReloads = 0;

    function data(offset) {
        var ret = [];
        for (var x = 0; x <= 360; x += 10) {
            var v = (offset + x) % 360;
            ret.push({
                x: x,
                y: Math.sin(Math.PI * v / 180).toFixed(4),
                z: Math.cos(Math.PI * v / 180).toFixed(4)
            });
        }
        return ret;
    }
    /*---- morrisBar6----*/
    var graph = Morris.Line({
        element: 'morrisBar6',
        data: data(0),
        xkey: 'x',
        ykeys: ['y', 'z'],
        labels: ['data1', 'data2'],
        lineColors: ['#0d9c1e', '#007F6E'],
        parseTime: false,
        ymin: -1.0,
        ymax: 1.0,
        hideHover: true,
        resize: true
    });

    function update() {
        nReloads++;
        graph.setData(data(5 * nReloads));
        $('#reloadStatus').text(nReloads + ' reloads');
    }
    // setInterval(update, 100);

    /*---- morrisBar7----*/
    var day_data = [{
        "period": "2022-10-01",
        "licensed": 3407,
        "sorned": 660
    }, {
        "period": "2022-09-30",
        "licensed": 3351,
        "sorned": 629
    }, {
        "period": "2022-09-29",
        "licensed": 3269,
        "sorned": 618
    }, {
        "period": "2022-09-20",
        "licensed": 3246,
        "sorned": 661
    }, {
        "period": "2022-09-19",
        "licensed": 3257,
        "sorned": 667
    }, {
        "period": "2022-09-18",
        "licensed": 3248,
        "sorned": 627
    }, {
        "period": "2022-09-17",
        "licensed": 3171,
        "sorned": 660
    }, {
        "period": "2022-09-16",
        "licensed": 3171,
        "sorned": 676
    }, {
        "period": "2022-09-15",
        "licensed": 3201,
        "sorned": 656
    }, {
        "period": "2022-09-10",
        "licensed": 3215,
        "sorned": 622
    }];
    new Morris.Line({
        element: 'morrisBar7',
        data: day_data,
        xkey: 'period',
        ykeys: ['licensed', 'sorned'],
        labels: ['Licensed', 'SORN'],
        lineColors: ['#0d9c1e', '#007F6E'],
        resize: true,
    });
    /*---- morrisBar8----*/
    new Morris.Donut({
        element: 'morrisBar8',
        data: [{
            value: 50,
            label: 'Exciting'
        }, {
            value: 35,
            label: 'Amazing'
        }, {
            value: 10,
            label: 'Intriguing'
        }],
        backgroundColor: 'rgba(119, 119, 142, 0.2)',
        labelColor: '#77778e',
        colors: ['#0d9c1e', '#007F6E', '#09ad95'],
        resize: true,
        formatter: function(x) {
            return x + "%"
        }
    }).on('click', function(i, row) {
        console.log(i, row);
    });
    
    /*---- morrisBar9----*/
    new Morris.Donut({
        element: 'morrisBar9',
        data: [{
            value: 40,
            label: 'Awesome Category'
        }, {
            value: 20,
            label: 'Fantastic Category'
        }, {
            value: 15,
            label: 'Superb Category'
        }, {
            value: 10,
            label: 'Incredible Category'
        }, {
            value: 8,
            label: 'Marvelous Category'
        }, {
            value: 7,
            label: 'Amazing Category'
        }],
        backgroundColor: 'rgba(119, 119, 142, 0.2)',
        labelColor: '#77778e',
        resize: true,
        colors: ['#0d9c1e', '#007F6E', '#09ad95', '#1170e4', '#f82649', '#f7b731'],
        formatter: function(x) {
            return x + "%"
        }
    });
});
