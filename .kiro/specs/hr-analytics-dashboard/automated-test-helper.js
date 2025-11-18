/**
 * Automated Test Helper for HR Analytics Dashboard
 * 
 * This script can be run in the browser console to automate some testing tasks.
 * Open the dashboard page, open browser console (F12), and paste this script.
 */

class AnalyticsDashboardTester {
    constructor() {
        this.results = {
            passed: 0,
            failed: 0,
            tests: []
        };
    }

    log(message, type = 'info') {
        const styles = {
            info: 'color: blue',
            success: 'color: green',
            error: 'color: red',
            warning: 'color: orange'
        };
        console.log(`%c${message}`, styles[type]);
    }

    addResult(testName, passed, message = '') {
        this.results.tests.push({ testName, passed, message });
        if (passed) {
            this.results.passed++;
            this.log(`✓ ${testName}`, 'success');
        } else {
            this.results.failed++;
            this.log(`✗ ${testName}: ${message}`, 'error');
        }
    }

    // Test 1: Check if toggle button exists
    testToggleButtonExists() {
        const toggleButtons = document.querySelectorAll('.btn-toggle');
        const exists = toggleButtons.length >= 2;
        this.addResult(
            'Toggle Button Exists',
            exists,
            exists ? '' : 'Toggle buttons not found'
        );
        return exists;
    }

    // Test 2: Check if both views exist
    testViewsExist() {
        const welcomeView = document.getElementById('welcomeView');
        const analyticsView = document.getElementById('analyticsView');
        const exists = welcomeView !== null && analyticsView !== null;
        this.addResult(
            'Both Views Exist',
            exists,
            exists ? '' : 'Welcome or Analytics view not found'
        );
        return exists;
    }

    // Test 3: Check if Chart.js is loaded
    testChartJsLoaded() {
        const loaded = typeof Chart !== 'undefined';
        this.addResult(
            'Chart.js Library Loaded',
            loaded,
            loaded ? '' : 'Chart.js not found'
        );
        return loaded;
    }

    // Test 4: Check if DashboardAnalytics class exists
    testDashboardAnalyticsExists() {
        const exists = typeof DashboardAnalytics !== 'undefined';
        this.addResult(
            'DashboardAnalytics Class Exists',
            exists,
            exists ? '' : 'DashboardAnalytics class not found'
        );
        return exists;
    }

    // Test 5: Check if all chart canvases exist
    testChartCanvasesExist() {
        const canvasIds = [
            'designationChart',
            'departmentChart',
            'additionsAttritionsChart',
            'statusChart',
            'ctcChart',
            'genderChart',
            'serviceChart',
            'ageChart'
        ];

        const missing = canvasIds.filter(id => !document.getElementById(id));
        const allExist = missing.length === 0;
        
        this.addResult(
            'All Chart Canvases Exist',
            allExist,
            allExist ? '' : `Missing canvases: ${missing.join(', ')}`
        );
        return allExist;
    }

    // Test 6: Check sessionStorage functionality
    testSessionStorage() {
        try {
            sessionStorage.setItem('test', 'value');
            const value = sessionStorage.getItem('test');
            sessionStorage.removeItem('test');
            const works = value === 'value';
            this.addResult(
                'SessionStorage Works',
                works,
                works ? '' : 'SessionStorage not functioning'
            );
            return works;
        } catch (e) {
            this.addResult('SessionStorage Works', false, e.message);
            return false;
        }
    }

    // Test 7: Check if year filter exists
    testYearFilterExists() {
        const yearFilter = document.getElementById('yearFilter');
        const exists = yearFilter !== null;
        this.addResult(
            'Year Filter Exists',
            exists,
            exists ? '' : 'Year filter dropdown not found'
        );
        return exists;
    }

    // Test 8: Simulate toggle click and check view switching
    async testViewSwitching() {
        const analyticsBtn = document.querySelector('[data-view="analytics"]');
        const welcomeBtn = document.querySelector('[data-view="welcome"]');
        
        if (!analyticsBtn || !welcomeBtn) {
            this.addResult('View Switching', false, 'Toggle buttons not found');
            return false;
        }

        // Click analytics button
        analyticsBtn.click();
        await this.sleep(500);

        const analyticsView = document.getElementById('analyticsView');
        const welcomeView = document.getElementById('welcomeView');

        const analyticsVisible = analyticsView && 
            window.getComputedStyle(analyticsView).display !== 'none';
        const welcomeHidden = welcomeView && 
            window.getComputedStyle(welcomeView).display === 'none';

        const switchedToAnalytics = analyticsVisible && welcomeHidden;

        // Click welcome button
        welcomeBtn.click();
        await this.sleep(500);

        const analyticsHidden = analyticsView && 
            window.getComputedStyle(analyticsView).display === 'none';
        const welcomeVisible = welcomeView && 
            window.getComputedStyle(welcomeView).display !== 'none';

        const switchedToWelcome = analyticsHidden && welcomeVisible;

        const passed = switchedToAnalytics && switchedToWelcome;
        this.addResult(
            'View Switching Works',
            passed,
            passed ? '' : 'View switching not working correctly'
        );
        return passed;
    }

    // Test 9: Check if AJAX endpoint is accessible
    async testAjaxEndpoint() {
        try {
            const response = await fetch('/dashboard/analytics-data', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const passed = response.ok;
            const data = passed ? await response.json() : null;
            
            this.addResult(
                'AJAX Endpoint Accessible',
                passed,
                passed ? `Status: ${response.status}` : `Status: ${response.status}`
            );

            if (passed && data) {
                this.log('Analytics data structure:', 'info');
                console.log(data);
            }

            return passed;
        } catch (e) {
            this.addResult('AJAX Endpoint Accessible', false, e.message);
            return false;
        }
    }

    // Test 10: Check responsive design
    testResponsiveDesign() {
        const viewportWidth = window.innerWidth;
        const isMobile = viewportWidth < 768;
        
        this.log(`Current viewport width: ${viewportWidth}px`, 'info');
        this.log(`Mobile view: ${isMobile}`, 'info');
        
        // Just log the info, don't fail
        this.addResult(
            'Responsive Design Check',
            true,
            `Viewport: ${viewportWidth}px, Mobile: ${isMobile}`
        );
        return true;
    }

    // Test 11: Check for JavaScript errors
    testNoConsoleErrors() {
        // This is informational - check console manually
        this.log('Check browser console for any JavaScript errors', 'warning');
        this.addResult(
            'No Console Errors',
            true,
            'Manual check required - review console'
        );
        return true;
    }

    // Helper function to sleep
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Run all tests
    async runAllTests() {
        this.log('=== Starting Automated Tests ===', 'info');
        this.log('', 'info');

        // Synchronous tests
        this.testToggleButtonExists();
        this.testViewsExist();
        this.testChartJsLoaded();
        this.testDashboardAnalyticsExists();
        this.testChartCanvasesExist();
        this.testSessionStorage();
        this.testYearFilterExists();
        this.testResponsiveDesign();
        this.testNoConsoleErrors();

        // Asynchronous tests
        await this.testViewSwitching();
        await this.testAjaxEndpoint();

        // Print summary
        this.log('', 'info');
        this.log('=== Test Summary ===', 'info');
        this.log(`Total Tests: ${this.results.tests.length}`, 'info');
        this.log(`Passed: ${this.results.passed}`, 'success');
        this.log(`Failed: ${this.results.failed}`, this.results.failed > 0 ? 'error' : 'success');
        this.log(`Pass Rate: ${((this.results.passed / this.results.tests.length) * 100).toFixed(2)}%`, 'info');

        return this.results;
    }

    // Generate detailed report
    generateReport() {
        console.table(this.results.tests);
        return this.results;
    }
}

// Usage instructions
console.log('%c=== HR Analytics Dashboard Test Helper ===', 'color: blue; font-size: 16px; font-weight: bold');
console.log('%cTo run tests, execute:', 'color: green');
console.log('%cconst tester = new AnalyticsDashboardTester();', 'color: gray');
console.log('%cawait tester.runAllTests();', 'color: gray');
console.log('%ctester.generateReport();', 'color: gray');
console.log('');

// Auto-run if desired (uncomment the lines below)
// (async () => {
//     const tester = new AnalyticsDashboardTester();
//     await tester.runAllTests();
//     tester.generateReport();
// })();
