<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Advanced Analytics & Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Time Range Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap gap-2 items-center mb-4">
                    <label class="font-semibold text-gray-700">Display Last:</label>
                    <select id="daysFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="7">7 Days</option>
                        <option value="14">14 Days</option>
                        <option value="30" selected>30 Days</option>
                        <option value="60">60 Days</option>
                        <option value="90">90 Days</option>
                    </select>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-lg sm:rounded-lg p-6 text-white">
                    <h3 class="text-sm font-semibold opacity-90 mb-2">Total Sales</h3>
                    <p class="text-3xl font-bold">₹{{ number_format($stats['total_sales'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-red-600 overflow-hidden shadow-lg sm:rounded-lg p-6 text-white">
                    <h3 class="text-sm font-semibold opacity-90 mb-2">Total Costs</h3>
                    <p class="text-3xl font-bold">₹{{ number_format($stats['total_cost'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-lg sm:rounded-lg p-6 text-white">
                    <h3 class="text-sm font-semibold opacity-90 mb-2">Total Profit</h3>
                    <p class="text-3xl font-bold">₹{{ number_format($stats['total_profit'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 overflow-hidden shadow-lg sm:rounded-lg p-6 text-white">
                    <h3 class="text-sm font-semibold opacity-90 mb-2">Vehicle Running</h3>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_vehicles_km'] ?? 0, 0) }} km</p>
                </div>
            </div>

            <!-- Profit vs Sales vs Cost Analysis -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">📊 Profit, Sales & Cost Analysis</h3>
                    <canvas id="profitLossChart" height="80"></canvas>
                    <div id="profitLossSummary" class="mt-4 grid grid-cols-4 gap-4 text-sm"></div>
                </div>
            </div>

            <!-- Stock Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">📦 Stock Quantity Over Time</h3>
                    <canvas id="stockChart" height="80"></canvas>
                    <div id="stockSummary" class="mt-4 grid grid-cols-4 gap-4 text-sm"></div>
                </div>
            </div>

            <!-- Stock by Product Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">📦 Top 10 Products by Stock</h3>
                    <canvas id="stockByProductChart" height="80"></canvas>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">💰 Sales Revenue Over Time</h3>
                    <canvas id="saleChart" height="80"></canvas>
                    <div id="saleSummary" class="mt-4 grid grid-cols-4 gap-4 text-sm"></div>
                </div>
            </div>

            <!-- Sales by Product Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">💰 Top 10 Products by Sales</h3>
                    <canvas id="saleByProductChart" height="80"></canvas>
                </div>
            </div>

            <!-- Cost Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">💳 Supply Items Cost Over Time</h3>
                    <canvas id="costChart" height="80"></canvas>
                    <div id="costSummary" class="mt-4 grid grid-cols-4 gap-4 text-sm"></div>
                </div>
            </div>

            <!-- Cost by Consumable Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">💳 Top 10 Consumables by Cost</h3>
                    <canvas id="costByConsumableChart" height="80"></canvas>
                </div>
            </div>

            <!-- Vehicle KM Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">🚗 Vehicle Running (km) Over Time</h3>
                    <canvas id="vehicleChart" height="80"></canvas>
                    <div id="vehicleSummary" class="mt-4 grid grid-cols-4 gap-4 text-sm"></div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const colors = {
            primary: 'rgb(75, 192, 192)',
            secondary: 'rgb(54, 162, 235)',
            tertiary: 'rgb(255, 206, 86)',
            quaternary: 'rgb(153, 102, 255)',
            success: 'rgb(76, 175, 80)',
            danger: 'rgb(255, 99, 132)',
        };

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: false,
                }
            }
        };

        let allCharts = {};

        function getDaysFromFilter() {
            return document.getElementById('daysFilter').value;
        }

        function renderCharts() {
            const days = getDaysFromFilter();
            
            // Destroy existing charts to prevent overlapping
            Object.values(allCharts).forEach(chart => {
                if (chart) chart.destroy();
            });
            allCharts = {};

            loadProfitLossChart(days);
            loadStockChart(days);
            loadStockByProductChart(days);
            loadSalesChart(days);
            loadSalesByProductChart(days);
            loadCostChart(days);
            loadCostByConsumableChart(days);
            loadVehicleChart(days);
        }

        // Profit Loss Chart
        function loadProfitLossChart(days) {
            fetch(`/api/profit-loss-data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('profitLossChart').getContext('2d');
                    allCharts.profitLoss = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: data.datasets
                        },
                        options: {
                            ...chartOptions,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                }
                            }
                        }
                    });

                    // Update summary
                    const summary = data.summary;
                    document.getElementById('profitLossSummary').innerHTML = `
                        <div class="p-3 bg-blue-50 rounded">
                            <p class="text-gray-600 text-xs">Total Sales</p>
                            <p class="font-bold text-blue-600">₹${summary.total_sales.toLocaleString()}</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded">
                            <p class="text-gray-600 text-xs">Total Cost</p>
                            <p class="font-bold text-red-600">₹${summary.total_cost.toLocaleString()}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded">
                            <p class="text-gray-600 text-xs">Total Profit</p>
                            <p class="font-bold text-green-600">₹${summary.total_profit.toLocaleString()}</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded">
                            <p class="text-gray-600 text-xs">Profit Margin</p>
                            <p class="font-bold text-purple-600">${summary.profit_margin}%</p>
                        </div>
                    `;
                })
                .catch(error => console.error('Error loading profit loss chart:', error));
        }

        // Stock Chart
        function loadStockChart(days) {
            fetch(`/api/stock-data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('stockChart').getContext('2d');
                    allCharts.stock = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Stock Quantity',
                                data: data.data,
                                borderColor: colors.primary,
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: colors.primary,
                            }]
                        },
                        options: chartOptions
                    });

                    if (data.summary) {
                        document.getElementById('stockSummary').innerHTML = `
                            <div class="p-3 bg-blue-50 rounded">
                                <p class="text-gray-600 text-xs">Total</p>
                                <p class="font-bold text-blue-600">${data.summary.total.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded">
                                <p class="text-gray-600 text-xs">Average</p>
                                <p class="font-bold text-green-600">${data.summary.average.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-purple-50 rounded">
                                <p class="text-gray-600 text-xs">Maximum</p>
                                <p class="font-bold text-purple-600">${data.summary.max.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-yellow-50 rounded">
                                <p class="text-gray-600 text-xs">Period</p>
                                <p class="font-bold text-yellow-600">${days} Days</p>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error('Error loading stock chart:', error));
        }

        // Stock by Product Chart
        function loadStockByProductChart(days) {
            fetch(`/api/stock-data-by-product?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('stockByProductChart').getContext('2d');
                    allCharts.stockByProduct = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Stock Quantity',
                                data: data.data,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            ...chartOptions,
                            indexAxis: 'y',
                        }
                    });
                })
                .catch(error => console.error('Error loading stock by product chart:', error));
        }

        // Sales Chart
        function loadSalesChart(days) {
            fetch(`/api/sale-data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('saleChart').getContext('2d');
                    allCharts.sales = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Sales Amount (Rs.)',
                                data: data.data,
                                borderColor: colors.secondary,
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: colors.secondary,
                            }]
                        },
                        options: chartOptions
                    });

                    if (data.summary) {
                        document.getElementById('saleSummary').innerHTML = `
                            <div class="p-3 bg-blue-50 rounded">
                                <p class="text-gray-600 text-xs">Total Amount</p>
                                <p class="font-bold text-blue-600">₹${data.summary.total_amount.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded">
                                <p class="text-gray-600 text-xs">Average/Day</p>
                                <p class="font-bold text-green-600">₹${data.summary.average_amount.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-purple-50 rounded">
                                <p class="text-gray-600 text-xs">Peak Day</p>
                                <p class="font-bold text-purple-600">₹${data.summary.max_amount.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-yellow-50 rounded">
                                <p class="text-gray-600 text-xs">Transactions</p>
                                <p class="font-bold text-yellow-600">${data.summary.transaction_count.toLocaleString()}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error('Error loading sales chart:', error));
        }

        // Sales by Product Chart
        function loadSalesByProductChart(days) {
            fetch(`/api/sale-data-by-product?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('saleByProductChart').getContext('2d');
                    allCharts.saleByProduct = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Sales Amount (Rs.)',
                                data: data.data,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            ...chartOptions,
                            indexAxis: 'y',
                        }
                    });
                })
                .catch(error => console.error('Error loading sales by product chart:', error));
        }

        // Cost Chart
        function loadCostChart(days) {
            fetch(`/api/cost-data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('costChart').getContext('2d');
                    allCharts.cost = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Cost Amount (Rs.)',
                                data: data.data,
                                borderColor: colors.tertiary,
                                backgroundColor: 'rgba(255, 206, 86, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: colors.tertiary,
                            }]
                        },
                        options: chartOptions
                    });

                    if (data.summary) {
                        document.getElementById('costSummary').innerHTML = `
                            <div class="p-3 bg-blue-50 rounded">
                                <p class="text-gray-600 text-xs">Total Cost</p>
                                <p class="font-bold text-blue-600">₹${data.summary.total_cost.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded">
                                <p class="text-gray-600 text-xs">Average/Day</p>
                                <p class="font-bold text-green-600">₹${data.summary.average_cost.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-purple-50 rounded">
                                <p class="text-gray-600 text-xs">Peak Day</p>
                                <p class="font-bold text-purple-600">₹${data.summary.max_cost.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-yellow-50 rounded">
                                <p class="text-gray-600 text-xs">Items Quantity</p>
                                <p class="font-bold text-yellow-600">${data.summary.total_items.toLocaleString()}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error('Error loading cost chart:', error));
        }

        // Cost by Consumable Chart
        function loadCostByConsumableChart(days) {
            fetch(`/api/cost-data-by-consumable?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('costByConsumableChart').getContext('2d');
                    allCharts.costByConsumable = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Cost (Rs.)',
                                data: data.data,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(199, 199, 199, 0.7)',
                                    'rgba(83, 102, 255, 0.7)',
                                    'rgba(255, 183, 77, 0.7)',
                                    'rgba(76, 175, 80, 0.7)',
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'right',
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error loading cost by consumable chart:', error));
        }

        // Vehicle Chart
        function loadVehicleChart(days) {
            fetch(`/api/vehicle-data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    const ctx = document.getElementById('vehicleChart').getContext('2d');
                    allCharts.vehicle = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Vehicle Running (km)',
                                data: data.data,
                                borderColor: colors.quaternary,
                                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: colors.quaternary,
                            }]
                        },
                        options: chartOptions
                    });

                    if (data.summary) {
                        document.getElementById('vehicleSummary').innerHTML = `
                            <div class="p-3 bg-blue-50 rounded">
                                <p class="text-gray-600 text-xs">Total KM</p>
                                <p class="font-bold text-blue-600">${data.summary.total_km.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded">
                                <p class="text-gray-600 text-xs">Average KM</p>
                                <p class="font-bold text-green-600">${data.summary.average_km.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-purple-50 rounded">
                                <p class="text-gray-600 text-xs">Max KM</p>
                                <p class="font-bold text-purple-600">${data.summary.max_km.toLocaleString()}</p>
                            </div>
                            <div class="p-3 bg-yellow-50 rounded">
                                <p class="text-gray-600 text-xs">Trip Count</p>
                                <p class="font-bold text-yellow-600">${data.summary.trip_count.toLocaleString()}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error('Error loading vehicle chart:', error));
        }

        // Initial render
        document.addEventListener('DOMContentLoaded', function() {
            renderCharts();
            
            // Add event listener for filter change
            document.getElementById('daysFilter').addEventListener('change', renderCharts);
        });
    </script>
</x-app-layout>
