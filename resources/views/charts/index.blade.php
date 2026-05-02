<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics & Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stock Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Stock Against Date</h3>
                    <canvas id="stockChart" height="80"></canvas>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Sales Against Date</h3>
                    <canvas id="saleChart" height="80"></canvas>
                </div>
            </div>

            <!-- Cost Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Supply Items Cost Against Date</h3>
                    <canvas id="costChart" height="80"></canvas>
                </div>
            </div>

            <!-- Vehicle KM Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Vehicle Running (km) Against Date</h3>
                    <canvas id="vehicleChart" height="80"></canvas>
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
        };

        const chartOptions = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Stock Chart
        fetch('/api/stock-data')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('stockChart').getContext('2d');
                new Chart(ctx, {
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
                            tension: 0.1
                        }]
                    },
                    options: chartOptions
                });
            });

        // Sales Chart
        fetch('/api/sale-data')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('saleChart').getContext('2d');
                new Chart(ctx, {
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
                            tension: 0.1
                        }]
                    },
                    options: chartOptions
                });
            });

        // Cost Chart
        fetch('/api/cost-data')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('costChart').getContext('2d');
                new Chart(ctx, {
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
                            tension: 0.1
                        }]
                    },
                    options: chartOptions
                });
            });

        // Vehicle KM Chart
        fetch('/api/vehicle-data')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('vehicleChart').getContext('2d');
                new Chart(ctx, {
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
                            tension: 0.1
                        }]
                    },
                    options: chartOptions
                });
            });
    </script>
</x-app-layout>
