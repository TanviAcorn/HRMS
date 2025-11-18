# Manual Testing Checklist - HR Analytics Dashboard

## Test Environment Setup
- [ ] Ensure you have both admin and employee user accounts
- [ ] Clear browser cache and session storage
- [ ] Test on latest versions of Chrome, Firefox, Safari, and Edge
- [ ] Prepare mobile device or browser dev tools for responsive testing

---

## 1. Admin User - Toggle Button Visibility

### Test Steps:
1. Login as admin user (role = ROLE_ADMIN)
2. Navigate to dashboard
3. Look for toggle button in breadcrumb area

### Expected Results:
- [ ] Toggle button is visible
- [ ] Button shows two options: "Welcome" and "Analytics"
- [ ] "Welcome" button is active by default
- [ ] Button styling is consistent with application theme

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 2. Employee User - Toggle Button Hidden

### Test Steps:
1. Logout from admin account
2. Login as regular employee user
3. Navigate to dashboard
4. Check for toggle button presence

### Expected Results:
- [ ] Toggle button is NOT visible
- [ ] Only Welcome view is displayed
- [ ] No analytics-related elements are visible

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 3. View Switching Functionality

### Test Steps:
1. Login as admin user
2. Click "Analytics" toggle button
3. Observe view transition
4. Click "Welcome" toggle button
5. Observe view transition back

### Expected Results:
- [ ] Clicking "Analytics" hides Welcome view and shows Analytics view
- [ ] Clicking "Welcome" hides Analytics view and shows Welcome view
- [ ] Transition is smooth without page reload
- [ ] Active button has visual indicator (active class)
- [ ] No JavaScript errors in console

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 4. All Charts Load and Display

### Test Steps:
1. Login as admin user
2. Switch to Analytics view
3. Wait for all charts to load
4. Verify each chart renders correctly

### Expected Results:
- [ ] Employee Count by Designation chart loads (horizontal bar)
- [ ] Employee Count by Department chart loads (pie chart)
- [ ] Additions/Attritions chart loads (line chart)
- [ ] Employee Count by Status chart loads (doughnut chart)
- [ ] Annual CTC by Department chart loads (horizontal bar)
- [ ] Gender Distribution chart loads (pie chart)
- [ ] Years of Service Distribution chart loads (bar chart)
- [ ] Age Distribution chart loads (bar chart)
- [ ] All charts display data correctly
- [ ] Data tables appear below charts
- [ ] No "Unable to load chart" errors

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 5. Year Filter Functionality

### Test Steps:
1. In Analytics view, locate year filter dropdown
2. Note current year selection
3. Observe Additions/Attritions chart data
4. Change year to previous year
5. Observe chart update

### Expected Results:
- [ ] Year dropdown shows years from 2020 to current year
- [ ] Current year is selected by default
- [ ] Changing year triggers AJAX request
- [ ] Loading indicator appears during data fetch
- [ ] Additions/Attritions chart updates with new data
- [ ] Other charts remain unchanged
- [ ] No JavaScript errors

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 6. View Preference Persistence

### Test Steps:
1. Login as admin user
2. Switch to Analytics view
3. Refresh the page (F5 or Ctrl+R)
4. Observe which view is displayed
5. Switch to Welcome view
6. Refresh the page again
7. Observe which view is displayed

### Expected Results:
- [ ] After switching to Analytics and refreshing, Analytics view is displayed
- [ ] After switching to Welcome and refreshing, Welcome view is displayed
- [ ] View preference is stored in sessionStorage
- [ ] Preference persists across page refreshes within same session

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 7. Performance Testing

### Test Steps:
1. Open browser developer tools (F12)
2. Go to Network tab
3. Clear network log
4. Switch to Analytics view
5. Measure time from click to all charts rendered

### Expected Results:
- [ ] Analytics data loads within 3 seconds
- [ ] AJAX request completes successfully
- [ ] No unnecessary duplicate requests
- [ ] Charts render smoothly without lag
- [ ] Page remains responsive during load

### Test Status: ⬜ PASS / ⬜ FAIL
**Load Time:** _____ seconds
**Notes:**

---

## 8. Browser Compatibility

### Chrome
- [ ] Toggle button works
- [ ] All charts render correctly
- [ ] View switching works
- [ ] No console errors

### Firefox
- [ ] Toggle button works
- [ ] All charts render correctly
- [ ] View switching works
- [ ] No console errors

### Safari
- [ ] Toggle button works
- [ ] All charts render correctly
- [ ] View switching works
- [ ] No console errors

### Edge
- [ ] Toggle button works
- [ ] All charts render correctly
- [ ] View switching works
- [ ] No console errors

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 9. Responsive Design - Mobile Testing

### Test Steps:
1. Open dashboard in mobile device or use browser dev tools
2. Set viewport to mobile size (375x667 - iPhone SE)
3. Login as admin user
4. Test toggle button
5. Switch to Analytics view
6. Scroll through all charts

### Expected Results:
- [ ] Toggle button is visible and clickable on mobile
- [ ] Charts are responsive and fit mobile screen
- [ ] Chart heights are appropriate for mobile
- [ ] Data tables are horizontally scrollable
- [ ] No horizontal overflow issues
- [ ] Touch interactions work smoothly
- [ ] Text is readable without zooming

### Test Devices/Sizes:
- [ ] iPhone SE (375x667)
- [ ] iPhone 12 Pro (390x844)
- [ ] iPad (768x1024)
- [ ] Android Phone (360x640)

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 10. Error Handling

### Test Steps:
1. Open browser developer tools
2. Go to Network tab
3. Enable network throttling or go offline
4. Switch to Analytics view
5. Observe error handling

### Expected Results:
- [ ] User-friendly error message is displayed
- [ ] No raw error messages or stack traces shown
- [ ] Retry option is available
- [ ] Application doesn't crash
- [ ] Error is logged to console for debugging

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## 11. Data Accuracy Verification

See separate data-verification-queries.sql file for database queries to verify accuracy.

### Test Status: ⬜ PASS / ⬜ FAIL
**Notes:**

---

## Summary

**Total Tests:** 11
**Passed:** _____
**Failed:** _____
**Pass Rate:** _____%

**Critical Issues Found:**

**Minor Issues Found:**

**Recommendations:**

**Tested By:** _________________
**Date:** _________________
**Browser Versions:**
- Chrome: _____
- Firefox: _____
- Safari: _____
- Edge: _____
