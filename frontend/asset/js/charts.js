 (function($) {
     "use strict";

     /*chart-employment*/
     var chart = c3.generate({
        bindto: '#chart-employment', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 25, 30, 35, 40, 45, 50], 
                ['data2', 20, 35, 30, 40, 50, 55], 
                ['data3', 30, 40, 45, 50, 60, 65]
            ],
            type: 'line', // default type of chart
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E',
                data3: '#09ad95'
            },
            names: {
                // name of each serie
                'data1': '2013',
                'data2': '2014',
                'data3': '2015'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['2016', '2017', '2018', '2019', '2020', '2021']
            },
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-temperature*/
     var chart = c3.generate({
        bindto: '#chart-temperature', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 8.0, 8.5, 9.0, 10.0, 12.0, 15.0, 14.5, 16.0, 18.0, 17.5, 16.0, 16.5], // Modified data for 'data1'
                ['data2', 4.9, 4.8, 5.2, 5.0, 5.5, 6.0, 6.2, 6.5, 6.0, 6.5, 7.0, 7.5]  // Modified data for 'data2'
            ],
            labels: true,
            type: 'line', // default type of chart
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E'
            },
            names: {
                // name of each serie
                'data1': 'India',
                'data2': 'USA'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr']
            },
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    
    

     /*chart-area*/
     var chart = c3.generate({
         bindto: '#chart-area', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 12, 8, 16, 19, 20, 18],
                 ['data2', 12, 5, 6, 8, 10, 13]
             ],
             type: 'area', // default type of chart
             colors: {
                 data1: '#0d9c1e',
                 data2: '#007F6E'
             },
             names: {
                 // name of each serie
                 'data1': 'Maximum',
                 'data2': 'Minimum'
             }
         },
         axis: {
             x: {
                 type: 'category',
                 // name of each category
                 categories: ['May', 'June', 'July', 'Aug', 'Sep', 'Oct']
             },
         },
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });

     /*chart-area-spline*/
     var chart = c3.generate({
         bindto: '#chart-area-spline', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 10, 8, 10, 12, 20, 18],
                 ['data2', 8, 12, 8, 20, 10, 13]
             ],
             type: 'area-spline', // default type of chart
             colors: {
                 data1: '#0d9c1e',
                 data2: '#007F6E'
             },
             names: {
                 // name of each serie
                 'data1': 'data1',
                 'data2': 'data2'
             }
         },
         axis: {
             x: {
                 type: 'category',
                 // name of each category
                 categories: ['May', 'June', 'July', 'Aug', 'Sep', 'Oct']
             },
         },
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });

     /*chart-area-spline-sracked*/
     var chart = c3.generate({
        bindto: '#chart-area-spline-sracked', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 14, 10, 18, 22, 25, 20],
                ['data2', 8, 12, 10, 14, 16, 18]
            ],
            type: 'area-spline', // default type of chart
            groups: [
                ['data1', 'data2']
            ],
            colors: {
                data1: '#ff5733', // Changed color for data1
                data2: '#33ff57'  // Changed color for data2
            },
            names: {
                // name of each serie
                'data1': 'Maximum',
                'data2': 'Minimum'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            },
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    
    

     /*chart-area-spline-sracked*/
     var chart = c3.generate({
         bindto: '#chart-sracked', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 5, 12, 18, 20, 28, 32, 26, 19, 8],
             ],
             type: 'area-spline', // default type of chart
             groups: [
                 ['data1', 'data2']
             ],
             colors: {
                 data1: '#0d9c1e'
             },
             names: {
                 // name of each serie
                 'data1': 'Maximum'
             }
         },
         axis: {
             x: {
                 type: 'category',
                 // name of each category
                 categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
             },
         },
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });

     /*chart-spline*/
     var chart = c3.generate({
        bindto: '#chart-spline', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 0, 0, 0.1, 0.3, 0.5, 1.0, 2.0, 2.5, 3.5, 4.0, 4.5, 5.0, 5.5, 6.0, 7.0, 5.0, 6.0, 5.5, 10.0, 6.0, 5.0, 12.0], // Modified data for 'data1'
                ['data2', 0, 0, 0, 0, 0.2, 0.1, 0.3, 0.5, 1.0, 1.5, 1.7, 2.0, 1.5, 2.5, 2.7, 2.0, 1.5, 1.5, 2.0, 3.5, 5.0, 9.0, 11.0] // Modified data for 'data2'
            ],
            labels: true,
            type: 'spline', // default type of chart
            colors: {
                data1: '#ff5733', // Changed color for data1
                data2: '#33ff57'  // Changed color for data2
            },
            names: {
                // name of each serie
                'data1': 'USA',
                'data2': 'India'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['May', 'June', 'July', 'Aug', 'Sep', 'Oct']
            },
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-spline-rotated*/
     var chart = c3.generate({
        bindto: '#chart-spline-rotated', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 10, 20, 30, 40, 50, 60, 70], // Updated data for 'data1' with an increasing pattern
                ['data2', 70, 60, 50, 40, 30, 20, 10]  // Updated data for 'data2' with a decreasing pattern
            ],
            type: 'spline', // default type of chart
            colors: {
                data1: '#ffa500', // Orange color for 'data1'
                data2: '#00bfff'  // Deep sky blue color for 'data2'
            },
            names: {
                // name of each serie
                'data1': 'Increasing',
                'data2': 'Decreasing'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']
            },
            rotated: true,
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    
    

     /*chart-step*/
     var chart = c3.generate({
         bindto: '#chart-step', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 10, 20, 20, 30, 30, 40], 
                 ['data2', 5, 10, 15, 20, 25, 30] 
             ],
             type: 'step', // default type of chart
             colors: {
                 data1: '#0d9c1e',
                 data2: '#007F6E'
             },
             names: {
                 // name of each serie
                 'data1': 'Maximum',
                 'data2': 'Minimum'
             }
         },
         axis: {
             x: {
                 type: 'category',
                 // name of each category
                 categories: ['May', 'June', 'July', 'Aug', 'Sep', 'Oct']
             },
         },
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });

     /*chart-area-step*/
     var chart = c3.generate({
         bindto: '#chart-area-step', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 12, 10, 16, 18, 22, 25], // Modified data for 'data1'
                 ['data2', 8, 8, 10, 12, 18, 20] 
             ],
             type: 'area-step', // default type of chart
             colors: {
                 data1: '#0d9c1e',
                 data2: '#007F6E'
             },
             names: {
                 // name of each serie
                 'data1': 'Maximum',
                 'data2': 'Minimum'
             }
         },
         axis: {
             x: {
                 type: 'category',
                 // name of each category
                 categories: ['May', 'June', 'July', 'Aug', 'Sep', 'Oct']
             },
         },
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });
     
     /*chart-bar*/
     var chart = c3.generate({
        bindto: '#chart-bar', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 20, 15, 25, 30, 35, 40], // Updated data for 'data1'
                ['data2', 10, 5, 15, 20, 25, 30] // Updated data for 'data2'
            ],
            type: 'bar', // default type of chart
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E'
            },
            names: {
                // name of each serie
                'data1': 'Maximum',
                'data2': 'Minimum'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            },
        },
        bar: {
            width: 16
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-bar*/
     var chart = c3.generate({
        bindto: '#chart-monthly', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 25, 32, 20, 45, 30, 35, 40, 55, 50, 45, 35, 40] // Updated data values
            ],
            type: 'bar', // default type of chart
            colors: {
                data1: '#0d9c1e'
            },
            names: {
                // name of each serie
                'data1': 'Maximum'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
        },
        bar: {
            width: 30
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-bar-rotated*/
     var chart = c3.generate({
        bindto: '#chart-bar-rotated', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 20, 15, 25, 30, 35, 40], // Updated data for 'data1'
                ['data2', 10, 5, 15, 20, 25, 30] // Updated data for 'data2'
            ],
            type: 'bar', // default type of chart
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E'
            },
            names: {
                // name of each serie
                'data1': 'Maximum',
                'data2': 'Minimum'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            },
            rotated: true,
        },
        bar: {
            width: 15
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        }
     });

     /*chart-bar-stacked*/
     var chart = c3.generate({
        bindto: '#chart-bar-stacked', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 20, 15, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70], // Updated data for 'data1'
                ['data2', 10, 5, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60] // Updated data for 'data2'
            ],
            type: 'bar', // default type of chart
            groups: [
                ['data1', 'data2']
            ],
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E'
            },
            names: {
                // name of each serie
                'data1': 'Maximum',
                'data2': 'Minimum'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
        },
        bar: {
            width: 16
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-pie*/
     var chart = c3.generate({
        bindto: '#chart-pie', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 30], // Updated value for 'data1'
                ['data2', 25], // Updated value for 'data2'
                ['data3', 20], // Updated value for 'data3'
                ['data4', 25]  // Updated value for 'data4'
            ],
            type: 'pie', // default type of chart
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E',
                data3: '#09ad95',
                data4: '#1170e4'
            },
            names: {
                // name of each serie
                'data1': 'Electronics', // Realistic name for 'data1'
                'data2': 'Clothing',    // Realistic name for 'data2'
                'data3': 'Home Decor',  // Realistic name for 'data3'
                'data4': 'Books'        // Realistic name for 'data4'
            }
        },
        axis: {},
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-pie*/
     var chart = c3.generate({
        bindto: '#chart-pie2', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 40], // Updated value for 'data1'
                ['data2', 25], // Updated value for 'data2'
                ['data3', 15], // Updated value for 'data3'
                ['data4', 10], // Updated value for 'data4'
                ['data5', 20], // Updated value for 'data5'
                ['data6', 30]  // Updated value for 'data6'
            ],
            type: 'pie', // default type of chart
            colors: {
                'data1': '#0d9c1e',
                'data2': '#007F6E',
                'data3': '#09ad95',
                'data4': '#1170e4',
                'data5': '#f82649',
                'data6': '#f7b731',
            },
            names: {
                // name of each serie
                'data1': 'Electronics', // Realistic name for 'data1'
                'data2': 'Clothing',    // Realistic name for 'data2'
                'data3': 'Home Decor',  // Realistic name for 'data3'
                'data4': 'Books',       // Realistic name for 'data4'
                'data5': 'Jewelry',     // Realistic name for 'data5'
                'data6': 'Accessories'  // Realistic name for 'data6'
            }
        },
        axis: {},
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-pie*/
     var chart = c3.generate({
        bindto: '#chart-pie3', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 45], // Updated value for 'data1'
                ['data2', 30], // Updated value for 'data2'
                ['data3', 25]  // Updated value for 'data3'
            ],
            type: 'pie', // default type of chart
            colors: {
                'data1': '#0d9c1e',
                'data2': '#007F6E',
                'data3': '#09ad95'
            },
            names: {
                // name of each serie
                'data1': 'Technology', // Realistic name for 'data1'
                'data2': 'Fashion',    // Realistic name for 'data2'
                'data3': 'Health'      // Realistic name for 'data3'
            }
        },
        axis: {},
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-pie*/
     var chart = c3.generate({
        bindto: '#chart-pie4', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 50], // Updated value for 'data1'
                ['data2', 40], // Updated value for 'data2'
                ['data3', 30], // Updated value for 'data3'
                ['data4', 20]  // Updated value for 'data4'
            ],
            type: 'pie', // default type of chart
            colors: {
                'data1': '#0d9c1e',
                'data2': '#007F6E',
                'data3': '#09ad95',
                'data4': '#1170e4'
            },
            names: {
                // name of each serie
                'data1': 'Technology', // Realistic name for 'data1'
                'data2': 'Fashion',    // Realistic name for 'data2'
                'data3': 'Health',     // Realistic name for 'data3'
                'data4': 'Finance'     // Realistic name for 'data4'
            }
        },
        axis: {},
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-donut*/
     var chart = c3.generate({
         bindto: '#chart-donut', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 60],
                 ['data2', 75],
                 ['data3', 45],
             ],
             type: 'donut', // default type of chart
             colors: {
                 data1: '#0d9c1e',
                 data2: '#007F6E',
                 data3: '#09ad95',
             },
             names: {
                 // name of each serie
                 'data1': 'sales1',
                 'data2': 'sales2',
                 'data3': 'sales3'
             }
         },
         axis: {},
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });

     /*chart-donut*/
     var chart = c3.generate({
        bindto: '#chart-donut2', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 60], // Modified value for 'data1'
                ['data2', 75], // Modified value for 'data2'
                ['data3', 45], // Modified value for 'data3'
                ['data4', 35], // Modified value for 'data4'
                ['data5', 55], // Modified value for 'data5'
                ['data6', 20], // Modified value for 'data6'
            ],
            type: 'donut', // default type of chart
            colors: {
                'data1': '#0d9c1e',
                'data2': '#007F6E',
                'data3': '#09ad95',
                'data4': '#1170e4',
                'data5': '#f82649',
                'data6': '#f7b731',
            },
            names: {
                // name of each serie
                'data1': 'Electronics',
                'data2': 'Clothing',
                'data3': 'Home Decor',
                'data4': 'Books',
                'data5': 'Cosmetics',
                'data6': 'Toys',
            }
        },
        axis: {},
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-donut*/
     var chart = c3.generate({
        bindto: '#chart-donut3', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 65], // Updated value for 'data1'
                ['data2', 85], // Updated value for 'data2'
            ],
            type: 'donut', // default type of chart
            colors: {
                'data1': '#0d9c1e',
                'data2': '#007F6E',
            },
            names: {
                // name of each serie
                'data1': 'Product A', // Updated name for 'data1'
                'data2': 'Product B', // Updated name for 'data2'
            }
        },
        axis: {},
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-donut*/
     var chart = c3.generate({
        bindto: '#chart-donut4', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 70], // Updated value for 'data1'
                ['data2', 90], // Updated value for 'data2'
                ['data3', 30], // Updated value for 'data3'
                ['data4', 50]  // Updated value for 'data4'
            ],
            type: 'donut', // default type of chart
            colors: {
                'data1': '#0d9c1e',
                'data2': '#007F6E',
                'data3': '#09ad95',
                'data4': '#1170e4',
            },
            names: {
                // name of each serie
                'data1': 'Product Alpha', // Updated name for 'data1'
                'data2': 'Product Beta',  // Updated name for 'data2'
                'data3': 'Product Gamma', // Updated name for 'data3'
                'data4': 'Product Delta', // Updated name for 'data4'
            }
        },
        axis: {},
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    
    

     /*chart-donut*/
     var chart = c3.generate({
         bindto: '#chart-donut5', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 78],
                 ['data2', 95],
                 ['data3', 25],
                 ['data4', 45]
             ],
             type: 'donut', // default type of chart
             colors: {
                 'data1': '#0d9c1e',
                 'data2': '#007F6E',
                 'data3': '#09ad95',
                 'data4': '#1170e4',
             },
             names: {
                 // name of each serie
                 'data1': 'USA',
                 'data2': 'Canada',
                 'data3': 'India',
                 'data4': 'France',
             }
         },
         axis: {},
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });

     /*chart-scatter*/
     var chart = c3.generate({
         bindto: '#chart-scatter', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 11, 8, 15, 18, 19, 17],
                 ['data2', 7, 7, 5, 7, 9, 12]
             ],
             type: 'scatter', // default type of chart
             colors: {
                 data1: 'green',
                 data2: 'red'
             },
             names: {
                 // name of each serie
                 'data1': 'Maximum',
                 'data2': 'Minimum'
             }
         },
         axis: {
             x: {
                 type: 'category',
                 // name of each category
                 categories: ['May', 'Jun', 'July', 'Aug', 'Sep', 'Oct']
             },
         },
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });

     /*chart-combination*/
     var chart = c3.generate({
        bindto: '#chart-combination', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 80, 110, 130, 220, 110, 200], // Updated data for 'data1'
                ['data2', 200, 150, 180, 300, 200, 250], // Updated data for 'data2'
                ['data3', 150, 180, 200, 280, 170, 240] // Updated data for 'data3'
            ],
            type: 'bar', // default type of chart
            types: {
                'data1': "line",
                'data2': "spline",
            },
            groups: [
                ['data3']
            ],
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E',
                data3: '#09ad95'
            },
            color: function(color, d) {
                // d will be 'id' when called for legends
                return d.id && d.id === 'data3' ? d3.rgb(98, 89, 202) : color;
            },
            names: {
                // name of each serie
                'data1': 'Marketing',
                'data2': 'Development',
                'data3': 'Sales'
            }
        },
        axis: {
            x: {
                type: 'category',
                // name of each category
                categories: ['2007-2008', '2009-2010', '2011-2012', '2013-2014', '2015-2016', '2017-2018']
            },
        },
        bar: {
            width: 50
        },
        legend: {
            show: false, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
    

     /*chart-wrapper*/
     var chart = c3.generate({
         bindto: '#chart-wrapper', // id of chart wrapper
         data: {
             columns: [
                 // each columns data
                 ['data1', 7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
                 ['data2', 3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
             ],
             labels: true,
             type: 'line', // default type of chart
             colors: {
                 data1: 'purple',
                 data2: 'pink'
             },
             names: {
                 // name of each serie
                 'data1': 'Tokyo',
                 'data2': 'London'
             }
         },
         axis: {
             x: {
                 type: 'category',
                 // name of each category
                 categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
             },
         },
         legend: {
             show: false, //hide legend
         },
         padding: {
             bottom: 0,
             top: 0
         },
     });
     // CHART TASKS
     var chart = c3.generate({
        bindto: '#chart-tasks', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', 0, 0, 2, 3, 20, 8, 15, 10, 30, 12, 60, 9, 10, 5, 3, 2, 0], // Updated data for 'data1'
                ['data2', 0, 0, 2, 3, 7, 5, 8, 6, 23, 7, 11, 6, 7, 4, 2, 2, 0], // Updated data for 'data2'
                ['data3', 0, 0, 2, 0, 2, 0, 2, 0, 3, 3, 0, 3, 3, 2, 1, 0, 0] // Updated data for 'data3'
            ],
            classes: {
                data1: 'filled',
                data2: 'filled',
                data3: 'filled'
            },
            type: 'area-spline', // default type of chart
            groups: [
                ['data1', 'data2', 'data3']
            ],
            colors: {
                data1: '#0d9c1e',
                data2: '#007F6E ',
                data3: '#09ad95'
            },
            names: {
                // name of each serie
                'data1': 'Income',
                'data2': 'Revenue',
                'data3': 'Profit'
            }
        },
        axis: {
            y: {
                padding: {
                    bottom: 0,
                },
                show: false,
                tick: {
                    outer: false
                }
            },
            x: {
                padding: {
                    left: 0,
                    right: 0
                },
                show: false
            }
        },
        legend: {
            position: 'inset',
            padding: 0,
            inset: {
                anchor: 'top-left',
                x: 20,
                y: 8,
                step: 10
            }
        },
        tooltip: {
            format: {
                title: function (x) {
                    return '';
                }
            }
        },
        padding: {
            bottom: 0,
            left: -1,
            right: -1
        },
        point: {
            show: false
        }
    });
    
     
 })(jQuery);