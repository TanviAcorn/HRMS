# HR Analytics Dashboard - Testing Guide

## Overview

This guide provides comprehensive instructions for testing the HR Analytics Dashboard feature. It covers manual testing procedures, data verification, and automated testing helpers.

---

## Prerequisites

### Test Accounts Required
1. **Admin Account**: User with `role = ROLE_ADMIN` in session
2. **Employee Account**: Regular user without admin privileges

### Test Environment
- Development or staging environment with test data
- Access to database for running verification queries
- Modern browsers: Chrome, Firefox, Safari, Edge (latest versions)
- Mobile device or browser developer tools for responsive testing

### Tools Needed
- Browser Developer Tools (F12)
- Database client (MySQL Workbench, phpMyAdmin, etc.)
- Network throttling capability (built into browser dev tools)

---

## Testing Approach

### Phase 1: Functional Testing (Manual)
Use the **manual-testing-checklist.md** file to systematically test all functionality.

**Steps:**
1. Open `.kiro/specs/hr-analytics-dashboard/manual-testing-checklist.md`
2. Follow each test case in order
3. Mark checkboxes as you complete each test
4. Document any issues found in the Notes section
5. Record pass/fail status for each test

**Key Areas to Test:**
- Toggle button visibility (admin vs employee)
- View switching functionality
- Chart rendering and display
- Year filter functionality
- View preference persistence
- Performance (< 3 seconds load time)
- Browser compatibility
- Responsive design
- Error handling

---

### Phase 2: Data Accuracy Verification

Use the **data-verification-queries.sql** file to verify data accuracy.

**Steps:**
1. Open your database client
2. Connect to the HRMS database
3. Run each query section from `data-verification-queries.sql`
4. Compare results with what's displayed in the Analytics dashboard
5. Document any discrepancies

**Queries Included:**
1. Employee Count by Designation
2. Employee Count by Department
3. Additions and Attritions (by year)
4. Employee Count by Status
5. Annual CTC by Department
6. Gender Distribution
7. Years of Service Distribution
8. Age Distribution
9. Overall Statistics Summary
10. Data Quality Checks

**Verification Process:**
```
For each chart:
1. Run the corresponding SQL query
2. Open Analytics dashboard in browser
3. Switch to Analytics view
4. Compare query results with chart data
5. Verify counts, labels, and calculations match
6. Check data table below chart matches query results
```

---

### Phase 3: Automated Testing (Browser Console)

Use the **automated-test-helper.js** script for quick automated checks.

**Steps:**
1. Login as admin user
2. Navigate to dashboard
3. Open browser console (F12)
4. Copy contents of `automated-test-helper.js`
5. Paste into console and press Enter
6. Run tests:
   ```javascript
   const tester = new AnalyticsDashboardTester();
   await tester.runAllTests();
   tester.generateReport();
   ```

**Tests Performed:**
- Toggle button existence
- View containers existence
- Chart.js library loaded
- DashboardAnalytics class exists
- All chart canvases exist
- SessionStorage functionality
- Year filter existence
- View switching functionality
- AJAX endpoint accessibility
- Responsive design check
- Console error check

---

## Detailed Testing Procedures

### 1. Admin User Testing

**Login as Admin:**
```
1. Navigate to login page
2. Enter admin credentials
3. Verify session role = ROLE_ADMIN
4. Navigate to dashboard
```

**Toggle Button Test:**
```
✓ Toggle button is visible in breadcrumb area
✓ Button shows "Welcome" and "Analytics" options
✓ "Welcome" is active by default
✓ Button styling matches application theme
```

**View Switching Test:**
```
1. Click "Analytics" button
   ✓ Welcome view hides
   ✓ Analytics view shows
   ✓ No page reload occurs
   ✓ "Analytics" button becomes active
   
2. Click "Welcome" button
   ✓ Analytics view hides
   ✓ Welcome view shows
   ✓ "Welcome" button becomes active
```

**Chart Loading Test:**
```
1. Switch to Analytics view
2. Wait for charts to load
3. Verify each chart:
   ✓ Employee Count by Designation (horizontal bar)
   ✓ Employee Count by Department (pie chart)
   ✓ Additions/Attritions (line chart)
   ✓ Employee Count by Status (doughnut chart)
   ✓ Annual CTC by Department (horizontal bar)
   ✓ Gender Distribution (pie chart)
   ✓ Years of Service Distribution (bar chart)
   ✓ Age Distribution (bar chart)
4. Verify data tables appear below charts
```

**Year Filter Test:**
```
1. Locate year filter dropdown
2. Verify years from 2020 to current year
3. Current year should be selected by default
4. Change to previous year
   ✓ Loading indicator appears
   ✓ Additions/Attritions chart updates
   ✓ Other charts remain unchanged
5. Change back to current year
   ✓ Chart updates correctly
```

**Persistence Test:**
```
1. Switch to Analytics view
2. Refresh page (F5)
   ✓ Analytics view is still displayed
3. Switch to Welcome view
4. Refresh page (F5)
   ✓ Welcome view is still displayed
```

---

### 2. Employee User Testing

**Login as Employee:**
```
1. Logout from admin account
2. Login with employee credentials
3. Verify session role != ROLE_ADMIN
4. Navigate to dashboard
```

**Visibility Test:**
```
✓ Toggle button is NOT visible
✓ Only Welcome view is displayed
✓ No analytics-related elements visible
✓ Cannot access analytics via URL manipulation
```

---

### 3. Performance Testing

**Load Time Test:**
```
1. Open browser DevTools (F12)
2. Go to Network tab
3. Clear network log
4. Switch to Analytics view
5. Measure time from click to all charts rendered
   ✓ Should complete within 3 seconds
6. Check Network tab:
   ✓ Single AJAX request to /dashboard/analytics-data
   ✓ No duplicate requests
   ✓ Response size is reasonable
```

**Memory Test:**
```
1. Open DevTools Performance/Memory tab
2. Take heap snapshot
3. Switch to Analytics view
4. Take another heap snapshot
5. Switch to Welcome view
6. Take final heap snapshot
   ✓ No significant memory leaks
   ✓ Chart instances are properly destroyed
```

---

### 4. Browser Compatibility Testing

**Test Matrix:**
```
Browser          | Version | Toggle | Charts | Switching | Errors
-----------------|---------|--------|--------|-----------|-------
Chrome           | Latest  |   ✓    |   ✓    |     ✓     |   ✓
Firefox          | Latest  |   ✓    |   ✓    |     ✓     |   ✓
Safari           | Latest  |   ✓    |   ✓    |     ✓     |   ✓
Edge             | Latest  |   ✓    |   ✓    |     ✓     |   ✓
```

**For Each Browser:**
```
1. Open dashboard
2. Login as admin
3. Test toggle button
4. Switch to Analytics view
5. Verify all charts render
6. Test year filter
7. Check console for errors
8. Test view switching
9. Test persistence
```

---

### 5. Responsive Design Testing

**Device/Viewport Sizes:**
```
Device           | Size        | Test Results
-----------------|-------------|-------------
iPhone SE        | 375x667     | 
iPhone 12 Pro    | 390x844     | 
iPad             | 768x1024    | 
Desktop          | 1920x1080   | 
```

**For Each Size:**
```
1. Set viewport size in DevTools
2. Login as admin
3. Check toggle button:
   ✓ Visible and clickable
   ✓ Properly positioned
4. Switch to Analytics view
5. Check charts:
   ✓ Responsive and fit screen
   ✓ Appropriate height for viewport
   ✓ No horizontal overflow
6. Check data tables:
   ✓ Horizontally scrollable if needed
   ✓ Readable text
7. Test touch interactions (on actual device)
```

---

### 6. Error Handling Testing

**Network Error Test:**
```
1. Open DevTools Network tab
2. Enable "Offline" mode
3. Switch to Analytics view
   ✓ User-friendly error message displayed
   ✓ No raw error messages or stack traces
   ✓ Retry option available
   ✓ Application doesn't crash
4. Disable offline mode
5. Click retry
   ✓ Data loads successfully
```

**Server Error Test:**
```
1. Temporarily modify backend to return 500 error
2. Switch to Analytics view
   ✓ Error message displayed
   ✓ Graceful degradation
3. Restore backend
```

**Empty Data Test:**
```
1. Test with database having no employees
   ✓ "No data available" message shown
   ✓ Charts show empty state
   ✓ No JavaScript errors
```

---

## Data Verification Checklist

### Employee Count by Designation
```
□ Run query from data-verification-queries.sql
□ Compare with chart data
□ Verify counts match
□ Verify designation names match
□ Verify sorting (descending by count)
□ Verify relieved employees excluded
```

### Employee Count by Department
```
□ Run query from data-verification-queries.sql
□ Compare with chart data
□ Verify counts match
□ Verify department names match
□ Verify "Unassigned" for null departments
□ Verify relieved employees excluded
```

### Additions and Attritions
```
□ Run query for current year
□ Compare with chart data
□ Verify all 12 months displayed
□ Verify addition counts match
□ Verify attrition counts match
□ Test with different years
□ Verify year filter updates correctly
```

### Employee Count by Status
```
□ Run query from data-verification-queries.sql
□ Compare with chart data
□ Verify all statuses included
□ Verify counts match
□ Verify colors are distinct
```

### Annual CTC by Department
```
□ Run query from data-verification-queries.sql
□ Compare with chart data
□ Verify CTC totals match
□ Verify currency formatting
□ Verify relieved employees excluded
□ Spot-check individual employee CTCs
```

### Gender Distribution
```
□ Run query from data-verification-queries.sql
□ Compare with chart data
□ Verify counts match
□ Verify "Not Specified" for null gender
□ Verify percentages are correct
□ Verify relieved employees excluded
```

### Years of Service Distribution
```
□ Run query from data-verification-queries.sql
□ Compare with chart data
□ Verify range counts match
□ Verify calculation logic (joining date to current)
□ Spot-check individual employee service years
□ Verify relieved employees excluded
```

### Age Distribution
```
□ Run query from data-verification-queries.sql
□ Compare with chart data
□ Verify range counts match
□ Verify calculation logic (birth date to current)
□ Spot-check individual employee ages
□ Verify null birth dates excluded
□ Verify relieved employees excluded
```

---

## Common Issues and Solutions

### Issue: Charts not loading
**Symptoms:** Blank chart areas, no data displayed
**Check:**
- Browser console for JavaScript errors
- Network tab for failed AJAX requests
- Chart.js library loaded correctly
- Canvas elements exist in DOM

### Issue: Incorrect data counts
**Symptoms:** Numbers don't match database queries
**Check:**
- Employment status filter (Relieved excluded)
- Soft delete flag (t_is_deleted = 0)
- Date range filters for additions/attritions
- NULL handling for optional fields

### Issue: Toggle button not visible
**Symptoms:** Admin user can't see toggle
**Check:**
- Session role value
- Blade conditional rendering logic
- CSS display properties
- Browser cache (clear and reload)

### Issue: View preference not persisting
**Symptoms:** View resets on page refresh
**Check:**
- SessionStorage enabled in browser
- JavaScript storing preference correctly
- Page load script reading preference
- Browser privacy settings

### Issue: Performance slow
**Symptoms:** Load time > 3 seconds
**Check:**
- Database query performance (use EXPLAIN)
- Indexes on filtered columns
- Network latency
- Chart rendering optimization
- Caching implementation

---

## Test Report Template

```markdown
# HR Analytics Dashboard - Test Report

**Tested By:** [Your Name]
**Date:** [Date]
**Environment:** [Dev/Staging/Production]
**Browser:** [Browser and Version]

## Summary
- Total Tests: __
- Passed: __
- Failed: __
- Pass Rate: __%

## Functional Tests
- [ ] Admin toggle button visibility: PASS/FAIL
- [ ] Employee toggle button hidden: PASS/FAIL
- [ ] View switching: PASS/FAIL
- [ ] All charts load: PASS/FAIL
- [ ] Year filter: PASS/FAIL
- [ ] View persistence: PASS/FAIL

## Data Accuracy Tests
- [ ] Designation counts: PASS/FAIL
- [ ] Department counts: PASS/FAIL
- [ ] Additions/Attritions: PASS/FAIL
- [ ] Status counts: PASS/FAIL
- [ ] CTC calculations: PASS/FAIL
- [ ] Gender distribution: PASS/FAIL
- [ ] Service distribution: PASS/FAIL
- [ ] Age distribution: PASS/FAIL

## Performance Tests
- [ ] Load time < 3 seconds: PASS/FAIL
- [ ] No memory leaks: PASS/FAIL

## Browser Compatibility
- [ ] Chrome: PASS/FAIL
- [ ] Firefox: PASS/FAIL
- [ ] Safari: PASS/FAIL
- [ ] Edge: PASS/FAIL

## Responsive Design
- [ ] Mobile (375px): PASS/FAIL
- [ ] Tablet (768px): PASS/FAIL
- [ ] Desktop (1920px): PASS/FAIL

## Issues Found
1. [Issue description]
   - Severity: Critical/High/Medium/Low
   - Steps to reproduce:
   - Expected behavior:
   - Actual behavior:

## Recommendations
[Any recommendations for improvements]

## Sign-off
Tested and verified by: _______________
Date: _______________
```

---

## Conclusion

This testing guide provides a comprehensive approach to validating the HR Analytics Dashboard feature. Follow each section systematically to ensure complete test coverage.

**Remember:**
- Test with realistic data volumes
- Document all issues with screenshots
- Verify fixes after issues are resolved
- Perform regression testing after changes
- Get sign-off from stakeholders before deployment
