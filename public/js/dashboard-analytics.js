/**
 * Dashboard Analytics View Toggle and Chart Management
 */

(function() {
    'use strict';

    let dashboardAnalytics = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard analytics JS loaded');
        initViewToggle();
        restoreViewPreference();
    });

    /**
     * Initialize view toggle functionality
     */
    function initViewToggle() {
        const welcomeBtn = document.getElementById('welcomeToggleBtn');
        const analyticsBtn = document.getElementById('analyticsToggleBtn');

        if (!welcomeBtn || !analyticsBtn) {
            return; // Toggle buttons not present (non-admin user)
        }

        // Add click event listeners
        welcomeBtn.addEventListener('click', function() {
            switchView('welcome');
        });

        analyticsBtn.addEventListener('click', function() {
            switchView('analytics');
        });
    }

    /**
     * Switch between Welcome and Analytics views
     * @param {string} viewName - 'welcome' or 'analytics'
     */
    function switchView(viewName) {
        const welcomeView = document.getElementById('welcomeView');
        const analyticsView = document.getElementById('analyticsView');
        const welcomeBtn = document.getElementById('welcomeToggleBtn');
        const analyticsBtn = document.getElementById('analyticsToggleBtn');

        if (!welcomeView || !analyticsView) {
            console.error('Dashboard views not found');
            return;
        }

        if (viewName === 'welcome') {
            // Show Welcome view
            welcomeView.classList.add('active');
            analyticsView.classList.remove('active');
            welcomeBtn.classList.add('active');
            analyticsBtn.classList.remove('active');

            // Store preference
            sessionStorage.setItem('dashboardView', 'welcome');

            // Cleanup charts to prevent memory leaks
            if (dashboardAnalytics) {
                dashboardAnalytics.destroyCharts();
            }
        } else if (viewName === 'analytics') {
            // Show Analytics view FIRST (before loading data)
            welcomeView.classList.remove('active');
            analyticsView.classList.add('active');
            welcomeBtn.classList.remove('active');
            analyticsBtn.classList.add('active');

            // Store preference
            sessionStorage.setItem('dashboardView', 'analytics');

            // Initialize analytics if not already done
            if (!dashboardAnalytics) {
                dashboardAnalytics = new DashboardAnalytics();
            }

            // Use setTimeout to ensure DOM has updated and canvas elements are visible
            setTimeout(() => {
                dashboardAnalytics.loadAnalyticsData();
            }, 100);
        }
    }

    /**
     * Restore view preference from sessionStorage on page load
     */
    function restoreViewPreference() {
        const savedView = sessionStorage.getItem('dashboardView');
        
        if (savedView === 'analytics') {
            // Switch to analytics view if it was previously selected
            switchView('analytics');
        }
        // If no saved view or saved view is 'welcome', do nothing
        // The welcome view is already active by default in the HTML
    }

    /**
     * DashboardAnalytics Class
     * Manages chart rendering and data fetching for analytics dashboard
     */
    class DashboardAnalytics {
        constructor() {
            this.charts = {};
            this.data = null;
            this.currentYear = new Date().getFullYear();
            this.initYearFilter();
        }

        /**
         * Initialize year filter event listener
         */
        initYearFilter() {
            const yearFilter = document.getElementById('yearFilter');
            if (yearFilter) {
                yearFilter.addEventListener('change', (e) => {
                    this.currentYear = e.target.value;
                    this.loadAdditionsAttritionsData();
                });
            }
        }

        /**
         * Load all analytics data via AJAX
         */
        async loadAnalyticsData() {
            try {
                console.log('Loading analytics data for year:', this.currentYear);
                // Show loading indicators
                this.showLoadingIndicators();

                const response = await fetch(`/dashboard/analytics-data?year=${this.currentYear}`);
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                this.data = await response.json();
                console.log('Analytics data loaded:', this.data);

                // Hide loading indicators and render all charts
                this.hideLoadingIndicators();
                this.renderAllCharts();

            } catch (error) {
                console.error('Error loading analytics data:', error);
                this.handleError('Failed to load analytics data. Please try again.');
            }
        }

        /**
         * Load only additions/attritions data for year filter
         */
        async loadAdditionsAttritionsData() {
            try {
                // Show loading indicator for additions/attritions chart
                const chartContainer = document.getElementById('additionsAttritionsChart').closest('.card-body');
                const loadingDiv = chartContainer.querySelector('.chart-loading');
                const canvas = document.getElementById('additionsAttritionsChart');
                
                if (loadingDiv) loadingDiv.style.display = 'block';
                if (canvas) canvas.style.display = 'none';

                const response = await fetch(`/dashboard/analytics-data?year=${this.currentYear}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                this.data.additionsAttritions = data.additionsAttritions;

                // Hide loading and render chart
                if (loadingDiv) loadingDiv.style.display = 'none';
                if (canvas) canvas.style.display = 'block';
                
                this.renderAdditionsAttritionsChart(this.data.additionsAttritions);

            } catch (error) {
                console.error('Error loading additions/attritions data:', error);
                this.handleError('Failed to load additions/attritions data.');
            }
        }

        /**
         * Show loading indicators for all charts
         */
        showLoadingIndicators() {
            const loadingDivs = document.querySelectorAll('.chart-loading');
            loadingDivs.forEach(div => div.style.display = 'block');
        }

        /**
         * Hide loading indicators and show charts
         */
        hideLoadingIndicators() {
            console.log('Hiding loading indicators');
            const loadingDivs = document.querySelectorAll('.chart-loading');
            const canvases = document.querySelectorAll('#analyticsView canvas');
            
            console.log('Found', loadingDivs.length, 'loading divs and', canvases.length, 'canvases');
            
            loadingDivs.forEach(div => div.style.display = 'none');
            canvases.forEach(canvas => {
                canvas.style.display = '';  // Remove inline style to let CSS handle it
                canvas.style.width = '';
                canvas.style.height = '';
            });
        }

        /**
         * Render all charts
         */
        renderAllCharts() {
            if (!this.data) {
                console.error('No data available to render charts');
                return;
            }

            // Check if Chart.js is available
            if (typeof Chart === 'undefined') {
                console.error('Chart.js library is not loaded!');
                this.handleError('Chart.js library is not loaded. Please refresh the page.');
                return;
            }

            console.log('Rendering all charts with data:', this.data);

            this.renderDesignationChart(this.data.employeeByDesignation);
            this.renderDepartmentChart(this.data.employeeByDepartment);
            this.renderAdditionsAttritionsChart(this.data.additionsAttritions);
            this.renderStatusChart(this.data.employeeByStatus);
            this.renderCtcChart(this.data.ctcByDepartment);
            this.renderGenderChart(this.data.genderDistribution);
            this.renderServiceChart(this.data.serviceDistribution);
            this.renderAgeChart(this.data.ageDistribution);
        }

        /**
         * Render Employee Count by Designation Chart (Horizontal Bar)
         */
        renderDesignationChart(data) {
            console.log('renderDesignationChart called with data:', data);
            
            if (!data || data.length === 0) {
                console.warn('No designation data available');
                this.showNoDataMessage('designationChart', 'No designation data available');
                return;
            }

            const ctx = document.getElementById('designationChart');
            console.log('Canvas element found:', ctx);
            if (!ctx) {
                console.error('Canvas element designationChart not found!');
                return;
            }

            // Make canvas visible FIRST
            ctx.style.display = 'block';
            
            // Force canvas to have proper dimensions
            const cardBody = ctx.parentElement;
            if (cardBody) {
                const width = cardBody.clientWidth > 0 ? cardBody.clientWidth - 40 : 600;
                ctx.width = width;
                ctx.height = 400;
                ctx.style.width = width + 'px';
                ctx.style.height = '400px';
                console.log('Canvas dimensions set to:', ctx.width, 'x', ctx.height);
                
                // Hide loading indicator
                const loadingDiv = cardBody.querySelector('.chart-loading');
                if (loadingDiv) {
                    loadingDiv.style.display = 'none';
                }
            } else {
                console.error('Parent element not found for canvas');
                ctx.width = 600;
                ctx.height = 400;
            }

            // Destroy existing chart
            if (this.charts.designation) {
                this.charts.designation.destroy();
            }

            const labels = data.map(item => item.designation || 'Unassigned');
            const counts = data.map(item => parseInt(item.count) || 0);

            console.log('Creating chart with labels:', labels, 'counts:', counts);

            try {
                this.charts.designation = new Chart(ctx, {
                    type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Employee Count',
                        data: counts,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Employees: ' + context.parsed.x;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
                });
                console.log('Chart created successfully:', this.charts.designation);
            } catch (error) {
                console.error('Error creating designation chart:', error);
                this.showNoDataMessage('designationChart', 'Error creating chart: ' + error.message);
                return;
            }

            // Render data table
            this.renderDataTable('designationTable', data, [
                { key: 'designation', label: 'Designation' },
                { key: 'count', label: 'Count', format: 'number' }
            ]);
        }

        /**
         * Render Employee Count by Department Chart (Pie)
         */
        renderDepartmentChart(data) {
            if (!data || data.length === 0) {
                this.showNoDataMessage('departmentChart', 'No department data available');
                return;
            }

            const ctx = document.getElementById('departmentChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.charts.department) {
                this.charts.department.destroy();
            }

            const labels = data.map(item => item.department || 'Unassigned');
            const counts = data.map(item => parseInt(item.count) || 0);
            const colors = this.generateColors(data.length);

            this.charts.department = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: window.innerWidth < 768 ? 'bottom' : 'right',
                            labels: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                },
                                padding: window.innerWidth < 768 ? 8 : 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Render data table
            this.renderDataTable('departmentTable', data, [
                { key: 'department', label: 'Department' },
                { key: 'count', label: 'Count', format: 'number' }
            ]);
        }

        /**
         * Render Additions and Attritions Chart (Line)
         */
        renderAdditionsAttritionsChart(data) {
            if (!data || data.length === 0) {
                this.showNoDataMessage('additionsAttritionsChart', 'No additions/attritions data available');
                return;
            }

            const ctx = document.getElementById('additionsAttritionsChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.charts.additionsAttritions) {
                this.charts.additionsAttritions.destroy();
            }

            const labels = data.map(item => item.month);
            const additions = data.map(item => parseInt(item.additions) || 0);
            const attritions = data.map(item => parseInt(item.attritions) || 0);

            this.charts.additionsAttritions = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Additions',
                            data: additions,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Attritions',
                            data: attritions,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                },
                                padding: window.innerWidth < 768 ? 8 : 10
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 9 : 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });
        }

        /**
         * Render Employee Count by Status Chart (Doughnut)
         */
        renderStatusChart(data) {
            if (!data || data.length === 0) {
                this.showNoDataMessage('statusChart', 'No status data available');
                return;
            }

            const ctx = document.getElementById('statusChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.charts.status) {
                this.charts.status.destroy();
            }

            const labels = data.map(item => item.status || 'Unknown');
            const counts = data.map(item => parseInt(item.count) || 0);
            const total = counts.reduce((a, b) => a + b, 0);
            
            const statusColors = {
                'Active': 'rgba(75, 192, 192, 0.8)',
                'Probation': 'rgba(255, 206, 86, 0.8)',
                'Notice Period': 'rgba(255, 159, 64, 0.8)',
                'Relieved': 'rgba(255, 99, 132, 0.8)',
                'Suspended': 'rgba(201, 203, 207, 0.8)'
            };
            
            const colors = labels.map(label => statusColors[label] || 'rgba(153, 102, 255, 0.8)');

            this.charts.status = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: window.innerWidth < 768 ? 'bottom' : 'right',
                            labels: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                },
                                padding: window.innerWidth < 768 ? 8 : 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Render data table
            this.renderDataTable('statusTable', data, [
                { key: 'status', label: 'Status' },
                { key: 'count', label: 'Count', format: 'number' }
            ]);
        }

        /**
         * Render Annual CTC by Department Chart (Horizontal Bar)
         */
        renderCtcChart(data) {
            if (!data || data.length === 0) {
                this.showNoDataMessage('ctcChart', 'No CTC data available');
                return;
            }

            const ctx = document.getElementById('ctcChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.charts.ctc) {
                this.charts.ctc.destroy();
            }

            const labels = data.map(item => item.department || 'Unassigned');
            const ctcValues = data.map(item => parseFloat(item.total_ctc) || 0);

            this.charts.ctc = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Annual CTC',
                        data: ctcValues,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'CTC: ₹' + context.parsed.x.toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + (value / 100000).toFixed(1) + 'L';
                                },
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });

            // Render data table with currency formatting
            this.renderDataTable('ctcTable', data, [
                { key: 'department', label: 'Department' },
                { key: 'total_ctc', label: 'Total CTC', format: 'currency' }
            ]);
        }

        /**
         * Render Gender Distribution Chart (Pie)
         */
        renderGenderChart(data) {
            if (!data || data.length === 0) {
                this.showNoDataMessage('genderChart', 'No gender data available');
                return;
            }

            const ctx = document.getElementById('genderChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.charts.gender) {
                this.charts.gender.destroy();
            }

            const labels = data.map(item => item.gender || 'Not Specified');
            const counts = data.map(item => parseInt(item.count) || 0);
            
            const genderColors = {
                'Male': 'rgba(54, 162, 235, 0.8)',
                'Female': 'rgba(255, 99, 132, 0.8)',
                'Other': 'rgba(153, 102, 255, 0.8)',
                'Not Specified': 'rgba(201, 203, 207, 0.8)'
            };
            
            const colors = labels.map(label => genderColors[label] || 'rgba(153, 102, 255, 0.8)');

            this.charts.gender = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: window.innerWidth < 768 ? 'bottom' : 'right',
                            labels: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                },
                                padding: window.innerWidth < 768 ? 8 : 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Render data table
            this.renderDataTable('genderTable', data, [
                { key: 'gender', label: 'Gender' },
                { key: 'count', label: 'Count', format: 'number' }
            ]);
        }

        /**
         * Render Years of Service Distribution Chart (Bar)
         */
        renderServiceChart(data) {
            if (!data || data.length === 0) {
                this.showNoDataMessage('serviceChart', 'No service data available');
                return;
            }

            const ctx = document.getElementById('serviceChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.charts.service) {
                this.charts.service.destroy();
            }

            const labels = data.map(item => item.service_range);
            const counts = data.map(item => parseInt(item.count) || 0);
            const colors = this.generateGradientColors(data.length, 'rgba(75, 192, 192, ', 'rgba(54, 162, 235, ');

            this.charts.service = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Employee Count',
                        data: counts,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Employees: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 9 : 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });

            // Render data table
            this.renderDataTable('serviceTable', data, [
                { key: 'service_range', label: 'Years of Service' },
                { key: 'count', label: 'Count', format: 'number' }
            ]);
        }

        /**
         * Render Age Distribution Chart (Bar)
         */
        renderAgeChart(data) {
            if (!data || data.length === 0) {
                this.showNoDataMessage('ageChart', 'No age data available');
                return;
            }

            const ctx = document.getElementById('ageChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.charts.age) {
                this.charts.age.destroy();
            }

            const labels = data.map(item => item.age_range);
            const counts = data.map(item => parseInt(item.count) || 0);

            this.charts.age = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Employee Count',
                        data: counts,
                        backgroundColor: 'rgba(153, 102, 255, 0.7)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Employees: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 9 : 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });

            // Render data table
            this.renderDataTable('ageTable', data, [
                { key: 'age_range', label: 'Age Range' },
                { key: 'count', label: 'Count', format: 'number' }
            ]);
        }

        /**
         * Render data table below charts
         * @param {string} containerId - ID of the container element
         * @param {Array} data - Array of data objects
         * @param {Array} columns - Array of column definitions
         */
        renderDataTable(containerId, data, columns) {
            const container = document.getElementById(containerId);
            if (!container || !data || data.length === 0) return;

            // Wrap table in responsive container for mobile scrolling
            let tableHtml = '<div class="table-responsive">';
            tableHtml += '<table class="table table-sm table-striped table-bordered mt-3">';
            tableHtml += '<thead class="thead-light"><tr>';
            
            // Table headers
            columns.forEach(col => {
                tableHtml += `<th>${col.label}</th>`;
            });
            tableHtml += '</tr></thead><tbody>';

            // Table rows
            data.forEach(row => {
                tableHtml += '<tr>';
                columns.forEach(col => {
                    let value = row[col.key];
                    
                    // Format value based on type
                    if (col.format === 'currency') {
                        value = '₹' + parseFloat(value || 0).toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    } else if (col.format === 'number') {
                        value = parseInt(value || 0).toLocaleString('en-IN');
                    }
                    
                    tableHtml += `<td>${value || '-'}</td>`;
                });
                tableHtml += '</tr>';
            });

            tableHtml += '</tbody></table></div>';
            container.innerHTML = tableHtml;
        }

        /**
         * Show "No data available" message
         */
        showNoDataMessage(canvasId, message) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            const container = canvas.closest('.card-body');
            if (container) {
                const loadingDiv = container.querySelector('.chart-loading');
                if (loadingDiv) {
                    loadingDiv.innerHTML = `<p class="text-muted">${message}</p>`;
                }
            }
        }

        /**
         * Handle AJAX errors
         */
        handleError(message) {
            // Hide all loading indicators
            const loadingDivs = document.querySelectorAll('.chart-loading');
            loadingDivs.forEach(div => {
                div.innerHTML = `<div class="alert alert-danger">${message}</div>`;
            });

            // Show error toast if available
            if (typeof toastr !== 'undefined') {
                toastr.error(message);
            } else {
                alert(message);
            }
        }

        /**
         * Generate distinct colors for charts
         */
        generateColors(count) {
            const colors = [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)',
                'rgba(199, 199, 199, 0.7)',
                'rgba(83, 102, 255, 0.7)',
                'rgba(255, 99, 255, 0.7)',
                'rgba(99, 255, 132, 0.7)'
            ];

            return colors.slice(0, count);
        }

        /**
         * Generate gradient colors
         */
        generateGradientColors(count, startColor, endColor) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const opacity = 0.7 - (i * 0.1);
                colors.push(startColor + Math.max(opacity, 0.3) + ')');
            }
            return colors;
        }

        /**
         * Destroy all chart instances to prevent memory leaks
         */
        destroyCharts() {
            Object.keys(this.charts).forEach(key => {
                if (this.charts[key]) {
                    this.charts[key].destroy();
                    delete this.charts[key];
                }
            });
            this.data = null;
        }
    }

    // Make DashboardAnalytics available globally if needed
    window.DashboardAnalytics = DashboardAnalytics;

})();
