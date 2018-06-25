define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
		index: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['提现额度', '充值额度', '投资额度']
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Orderdata.column
                },
                yAxis: {

                },
                grid: [
                	{
                        left: 'left',
                        top: 'top',
                        right: '10',
                        bottom: 30
                    }
            	],
                series: [
                	{
                        name: '提现额度',
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                            	
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderdata.withdrawdata
                    },
                    {
                        name: '充值额度',
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                            	
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderdata.chargedata
                    },
                    {
                        name: '投资额度',
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                            	
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderdata.investdata
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {
//                Orderdata.column.push((new Date()).toLocaleTimeString().replace(/^\D*/, ''));
//                var amount = Math.floor(Math.random() * 200) + 20;
//                Orderdata.chargedata.push(amount);
//                Orderdata.withdrawdata.push(Math.floor(Math.random() * amount) + 1);

                //按自己需求可以取消这个限制
                if (Orderdata.column.length >= 20) {
                    //移除最开始的一条数据
                    Orderdata.column.shift();
                    Orderdata.withdrawdata.shift();
                    Orderdata.chargedata.shift();
                    Orderdata.investdata.shift();
                }
                myChart.setOption({
                    xAxis: {
                        data: Orderdata.column
                    },
                    series: [
                    	{
                            name: '提现额度',
                            data: Orderdata.withdrawdata
                        },
                        {
                            name: '充值额度',
                            data: Orderdata.chargedata
                        },
                        {
                            name: '投资额度',
                            data: Orderdata.investdata
                        }
                    ]
                });
            }, 2000);
            $(window).resize(function () {
                myChart.resize();
            });
        }
    };

    return Controller;
});