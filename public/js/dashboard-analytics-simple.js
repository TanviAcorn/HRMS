/**
 * Simplified Dashboard Analytics - Direct Rendering Approach
 */

(function() {
    'use strict';

    let charts = {};
    let analyticsData = null;

    let chartsLoaded = false; // Track if charts have been loaded
    let scrollLocked = false;

    function lockScroll() {
        if (scrollLocked) return;
        scrollLocked = true;
        document.documentElement.style.scrollBehavior = 'auto';
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'relative';
        document.body.style.width = '100%';
    }

    function unlockScroll() {
        if (!scrollLocked) return;
        scrollLocked = false;
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard analytics loaded');
        initViewToggle();

        // If the page is already on Analytics (server-rendered view), initialize immediately
        const analyticsView = document.getElementById('analyticsView');
        const params = new URLSearchParams(window.location.search);
        console.log('Analytics page init check:', !!analyticsView, params.get('view'));
        if (analyticsView && (analyticsView.classList.contains('active') || params.get('view') === 'analytics')) {
            try {
                console.log('Initializing analytics page flow');
                // Ensure visible now
                analyticsView.classList.add('active');
                analyticsView.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; min-height: 500px !important; height: auto !important; overflow: visible !important; background: #f8f9fa !important;';
                ensureVisible(analyticsView);
                // Load data directly; do not rely only on buttons in this mode
                chartsLoaded = false;
                lockScroll();
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        console.log('Loading analytics data (page init)...');
                        loadAnalyticsData();
                    });
                });
            } catch (e) {
                console.error('Analytics init error:', e);
                unlockScroll();
            }
        }
    });

    function initViewToggle() {
        const welcomeBtn = document.getElementById('welcomeToggleBtn');
        const analyticsBtn = document.getElementById('analyticsToggleBtn');

        if (!welcomeBtn || !analyticsBtn) return;

        welcomeBtn.addEventListener('click', () => switchView('welcome'));
        analyticsBtn.addEventListener('click', () => switchView('analytics'));
    }

    function switchView(viewName) {
        const welcomeView = document.getElementById('welcomeView');
        const analyticsView = document.getElementById('analyticsView');
        const welcomeBtn = document.getElementById('welcomeToggleBtn');
        const analyticsBtn = document.getElementById('analyticsToggleBtn');

        console.log('Switching to view:', viewName);
        console.log('welcomeView element:', welcomeView);
        console.log('analyticsView element:', analyticsView);

        if (viewName === 'welcome') {
            // Show only welcome
            if (welcomeView) {
                welcomeView.classList.add('active');
                welcomeView.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
            }
            if (analyticsView) {
                analyticsView.classList.remove('active');
                analyticsView.style.cssText = 'display: none !important; visibility: hidden !important; height: 0 !important; overflow: hidden !important;';
            }
            if (welcomeBtn) welcomeBtn.classList.add('active');
            if (analyticsBtn) analyticsBtn.classList.remove('active');
            sessionStorage.setItem('dashboardView', 'welcome');
            if (welcomeBtn || analyticsBtn) { window.scrollTo(0, 0); }
            unlockScroll();
        } else {
            // Hide welcome completely
            if (welcomeView) {
                welcomeView.classList.remove('active');
                welcomeView.style.cssText = 'display: none !important; visibility: hidden !important; height: 0 !important; overflow: hidden !important;';
            }
            
            // FORCE analytics view to be visible with aggressive inline styles
            if (analyticsView) {
                analyticsView.classList.add('active');
                analyticsView.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; min-height: 500px !important; height: auto !important; overflow: visible !important; background: #f8f9fa !important;';
            }
            // Ensure all ancestor containers are visible and not collapsing
            if (analyticsView) ensureVisible(analyticsView);
            
            // Force all child elements to be visible
            if (analyticsView) {
                const allCards = analyticsView.querySelectorAll('.card, .card-body, canvas');
                allCards.forEach(el => {
                    el.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
                });
            }
            
            if (welcomeBtn) welcomeBtn.classList.remove('active');
            if (analyticsBtn) analyticsBtn.classList.add('active');
            sessionStorage.setItem('dashboardView', 'analytics');
            if (welcomeBtn || analyticsBtn) { window.scrollTo(0, 0); }
            lockScroll();
            
            console.log('After setting styles:');
            if (analyticsView) {
                console.log('analyticsView display:', window.getComputedStyle(analyticsView).display);
                console.log('analyticsView visibility:', window.getComputedStyle(analyticsView).visibility);
                console.log('analyticsView height:', analyticsView.offsetHeight);
                console.log('analyticsView innerHTML length:', analyticsView.innerHTML.length);
            }
            
            // Force browser to recalculate layout
            if (analyticsView) analyticsView.offsetHeight;
            
            // Destroy any existing charts first
            Object.keys(charts).forEach(key => {
                if (charts[key]) {
                    charts[key].destroy();
                    delete charts[key];
                }
            });
            
            // Only load data once when first switching to analytics
            if (!chartsLoaded) {
                chartsLoaded = true;
                // CRITICAL: Wait for browser to finish rendering the visible view
                // Then force a reflow, THEN load data
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        console.log('View should be visible now, loading data...');
                        loadAnalyticsData();
                    });
                });
            }
        }
    }

    // Walk up the DOM and unhide any ancestors that might be collapsing the analytics area
    function ensureVisible(node) {
        try {
            let el = node;
            let safety = 0;
            while (el && el !== document.body && safety < 10) {
                const cs = window.getComputedStyle(el);
                if (cs.display === 'none') {
                    el.style.setProperty('display', 'block', 'important');
                }
                if (cs.visibility === 'hidden') {
                    el.style.setProperty('visibility', 'visible', 'important');
                }
                if (parseInt(cs.height, 10) === 0 && el !== node) {
                    el.style.setProperty('min-height', '1px', 'important');
                    el.style.setProperty('height', 'auto', 'important');
                    el.style.setProperty('overflow', 'visible', 'important');
                }
                el = el.parentElement;
                safety++;
            }
        } catch (e) {
            // no-op
        }
    }

    async function loadAnalyticsData() {
        try {
            const year = document.getElementById('yearFilter')?.value || new Date().getFullYear();
            console.log('Fetching analytics data for year:', year);
            const cacheBust = Date.now();
            const response = await fetch(`/dashboard/analytics-data?year=${year}&_=${cacheBust}`);
            
            if (!response.ok) throw new Error('Failed to load data');
            
            analyticsData = await response.json();
            console.log('=== FULL DATA LOADED ===');
            console.log('employeeByDesignation:', analyticsData.employeeByDesignation);
            console.log('employeeByDepartment:', analyticsData.employeeByDepartment);
            console.log('additionsAttritions:', analyticsData.additionsAttritions);
            console.log('employeeByStatus:', analyticsData.employeeByStatus);
            console.log('ctcByDepartment:', analyticsData.ctcByDepartment);
            console.log('genderDistribution:', analyticsData.genderDistribution);
            console.log('serviceDistribution:', analyticsData.serviceDistribution);
            console.log('ageDistribution:', analyticsData.ageDistribution);
            console.log('========================');
            
            // Wait a bit more for layout to stabilize
            setTimeout(renderAllCharts, 100);
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load analytics data');
            unlockScroll();
        }
    }

    function renderAllCharts() {
        if (!analyticsData) {
            console.error('No analytics data available');
            return;
        }

        console.log('Starting to render all charts...');
        
        // Verify analytics view is visible
        const analyticsView = document.getElementById('analyticsView');
        console.log('Analytics view visible?', analyticsView && window.getComputedStyle(analyticsView).display !== 'none');
        console.log('Analytics view height:', analyticsView ? analyticsView.offsetHeight : 'N/A');

        renderChart('designationChart', analyticsData.employeeByDesignation, 'bar');
        renderChart('departmentChart', analyticsData.employeeByDepartment, 'pie');
        renderChart('statusChart', analyticsData.employeeByStatus, 'doughnut');
        renderChart('genderChart', analyticsData.genderDistribution, 'pie');
        renderChart('serviceChart', analyticsData.serviceDistribution, 'bar');
        renderChart('ageChart', analyticsData.ageDistribution, 'bar');
        renderChart('ctcChart', analyticsData.ctcByDepartment, 'bar');
        renderAdditionsAttritionsChart();
        
        console.log('All charts rendered. Total charts:', Object.keys(charts).length);
        // Unlock scroll after charts are rendered and layout has settled
        setTimeout(unlockScroll, 50);
    }

    function renderChart(canvasId, data, type) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.warn(`Canvas not found: ${canvasId}`);
            return;
        }
        
        if (!data || data.length === 0) {
            console.warn(`No data for ${canvasId}`);
            showNoDataMessage(canvasId);
            return;
        }

        // Get card body (parent)
        const cardBody = canvas.parentElement;
        const loading = cardBody.querySelector('.chart-loading');
        
        // Hide loading spinner
        if (loading) loading.style.display = 'none';
        
        // Destroy existing chart
        if (charts[canvasId]) {
            charts[canvasId].destroy();
        }
        
        // Force card and card-body to be visible
        const card = cardBody.closest('.card');
        if (card) {
            card.style.display = 'block';
            card.style.visibility = 'visible';
        }
        cardBody.style.display = 'block';
        cardBody.style.visibility = 'visible';
        cardBody.style.minHeight = '450px';
        
        // Get actual dimensions from parent
        const parentWidth = cardBody.clientWidth || 600;
        const chartHeight = 400;
        
        // Set canvas dimensions BEFORE Chart.js touches it
        canvas.width = parentWidth - 40;
        canvas.height = chartHeight;
        canvas.style.display = 'block';
        canvas.style.width = (parentWidth - 40) + 'px';
        canvas.style.height = chartHeight + 'px';
        
        console.log(`${canvasId} canvas size: ${canvas.width}x${canvas.height}`);
        
        console.log(`${canvasId} rendering...`);

        // Prepare data
        let labels, values, label;
        
        if (canvasId === 'designationChart') {
            labels = data.map(d => d.designation || 'Unassigned');
            values = data.map(d => parseInt(d.count) || 0);
            label = 'Employees';
        } else if (canvasId === 'departmentChart') {
            labels = data.map(d => d.department || 'Unassigned');
            values = data.map(d => parseInt(d.count) || 0);
            label = 'Employees';
        } else if (canvasId === 'statusChart') {
            labels = data.map(d => d.status || 'Unknown');
            values = data.map(d => parseInt(d.count) || 0);
            label = 'Employees';
        } else if (canvasId === 'genderChart') {
            labels = data.map(d => d.gender || 'Not Specified');
            values = data.map(d => parseInt(d.count) || 0);
            label = 'Employees';
        } else if (canvasId === 'serviceChart') {
            labels = data.map(d => d.service_range);
            values = data.map(d => parseInt(d.count) || 0);
            label = 'Employees';
        } else if (canvasId === 'ageChart') {
            labels = data.map(d => d.age_range);
            values = data.map(d => parseInt(d.count) || 0);
            label = 'Employees';
        } else if (canvasId === 'ctcChart') {
            labels = data.map(d => d.department || 'Unassigned');
            values = data.map(d => parseFloat(d.total_ctc) || 0);
            label = 'Total CTC';
        }

        console.log(`Rendering ${canvasId}:`, labels.length, 'items');

        // Colors
        let datasetColors = generateColors((labels || []).length);
        if (canvasId === 'genderChart') {
            // Force Male color as requested; keep others light
            datasetColors = labels.map(l => {
                const name = (l || '').toString().toLowerCase();
                if (name === 'male') return 'rgba(151, 38, 38, 0.85)';
                // fallback for other categories
                return 'rgba(255, 129, 129, 0.57)';
            });
        }

        // Create chart
        try {
            charts[canvasId] = new Chart(canvas, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: values,
                        backgroundColor: datasetColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    legend: {
                        display: type === 'pie' || type === 'doughnut',
                        position: 'right'
                    },
                    scales: type === 'bar' ? {
                        yAxes: [{
                            ticks: { beginAtZero: true }
                        }]
                    } : undefined
                }
            });
            
            // Ensure canvas visible without triggering resize loops
            canvas.style.display = 'block';
            
            console.log(`✓ ${canvasId} rendered successfully`);
        } catch (error) {
            console.error(`✗ Error rendering ${canvasId}:`, error);
        }
    }

    function renderAdditionsAttritionsChart() {
        const canvas = document.getElementById('additionsAttritionsChart');
        const data = analyticsData.additionsAttritions;
        if (!canvas) return;

        // Get card body (parent)
        const cardBody = canvas.parentElement;
        const loading = cardBody ? cardBody.querySelector('.chart-loading') : null;

        // Handle no data: show friendly message, hide canvas, stop spinner
        if (!data || data.length === 0) {
            if (loading) {
                loading.innerHTML = '<div class="alert alert-info mb-0">No additions/attritions data for the selected year.</div>';
                loading.style.display = 'block';
            }
            canvas.style.display = 'none';
            return;
        }

        // Hide loading spinner
        if (loading) loading.style.display = 'none';
        
        if (charts.additionsAttritionsChart) {
            charts.additionsAttritionsChart.destroy();
        }
        
        // Set canvas to visible BEFORE Chart.js touches it
        canvas.removeAttribute('style');
        canvas.style.cssText = 'display: block !important; width: 100% !important; height: 400px !important;';
        
        console.log(`additionsAttritionsChart rendering...`);

        const labels = data.map(d => d.month);
        const additions = data.map(d => parseInt(d.additions) || 0);
        const attritions = data.map(d => parseInt(d.attritions) || 0);

        charts.additionsAttritionsChart = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Additions',
                        data: additions,
                        borderColor: '#8c1d2b',
                        backgroundColor: 'rgba(140, 29, 43, 0.20)',
                        pointBackgroundColor: '#8c1d2b',
                        pointBorderColor: '#8c1d2b',
                        fill: true
                    },
                    {
                        label: 'Attritions',
                        data: attritions,
                        borderColor: '#b91c1c',
                        backgroundColor: 'rgba(185, 28, 28, 0.18)',
                        pointBackgroundColor: '#b91c1c',
                        pointBorderColor: '#b91c1c',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true }
                    }]
                }
            }
        });
        // Ensure canvas visible without triggering resize loops
        canvas.style.display = 'block';
        
        console.log('✓ additionsAttritionsChart rendered successfully');
    }

    function generateColors(count) {
        // Burgundy/maroon scale from light to dark
        const burgundy = [
            'rgba(254, 226, 226, 0.85)', // very light rose
            'rgba(252, 165, 165, 0.85)',
            'rgba(248, 113, 113, 0.85)',
            'rgba(239, 68, 68, 0.85)',
            'rgba(220, 38, 38, 0.85)',
            'rgba(185, 28, 28, 0.85)', // theme red
            'rgba(153, 27, 27, 0.85)',
            'rgba(127, 29, 29, 0.85)',
            'rgba(99, 28, 35, 0.85)',  // burgundy
            'rgba(76, 17, 25, 0.85)',
            'rgba(64, 12, 20, 0.85)',
            'rgba(52, 9, 16, 0.85)'
        ];
        // Repeat shades if more items than palette length
        const out = [];
        for (let i = 0; i < count; i++) {
            out.push(burgundy[i % burgundy.length]);
        }
        return out;
    }

    function showNoDataMessage(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        const cardBody = canvas.parentElement;
        const loading = cardBody.querySelector('.chart-loading');
        
        if (loading) {
            loading.innerHTML = '<div class="alert alert-info">No data available for this chart</div>';
            loading.style.display = 'block';
        }
        
        canvas.style.display = 'none';
    }

    // Year filter
    document.addEventListener('change', function(e) {
        if (e.target.id === 'yearFilter') {
            // Show loading again and destroy any existing charts to avoid stale visuals
            const loadingDivs = document.querySelectorAll('.chart-loading');
            loadingDivs.forEach(d => d.style.display = 'block');
            Object.keys(charts).forEach(key => { try { charts[key].destroy(); } catch(_){} delete charts[key]; });
            chartsLoaded = false;
            loadAnalyticsData();
        }
    });

})();
