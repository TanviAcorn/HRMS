# HR Analytics Dashboard - Testing Documentation

## Overview

This directory contains comprehensive testing resources for the HR Analytics Dashboard feature. Use these files to perform thorough manual testing, data verification, and quality assurance.

---

## Files in This Directory

### 1. **manual-testing-checklist.md**
A detailed checklist for manual testing of all dashboard functionality.

**Use this for:**
- Systematic functional testing
- User acceptance testing (UAT)
- Regression testing
- Browser compatibility testing
- Responsive design testing

**How to use:**
1. Open the file
2. Follow each test case sequentially
3. Mark checkboxes as you complete tests
4. Document findings in Notes sections
5. Fill out the summary at the end

---

### 2. **data-verification-queries.sql**
SQL queries to verify data accuracy against the dashboard.

**Use this for:**
- Validating chart data accuracy
- Comparing database results with dashboard display
- Identifying data quality issues
- Spot-checking calculations

**How to use:**
1. Open your database client (MySQL Workbench, phpMyAdmin, etc.)
2. Connect to the HRMS database
3. Run each query section
4. Compare results with dashboard charts
5. Document any discrepancies

**Query Sections:**
- Employee Count by Designation
- Employee Count by Department
- Additions and Attritions
- Employee Count by Status
- Annual CTC by Department
- Gender Distribution
- Years of Service Distribution
- Age Distribution
- Overall Statistics Summary
- Data Quality Checks

---

### 3. **automated-test-helper.js**
JavaScript testing helper for browser console.

**Use this for:**
- Quick automated checks
- Verifying DOM elements exist
- Testing JavaScript functionality
- Checking AJAX endpoints
- Validating view switching

**How to use:**
1. Login as admin user
2. Navigate to dashboard
3. Open browser console (F12)
4. Copy and paste the entire script
5. Run the tests:
   ```javascript
   const tester = new AnalyticsDashboardTester();
   await tester.runAllTests();
   tester.generateReport();
   ```
6. Review results in console

**Tests Performed:**
- Toggle button exists
- View containers exist
- Chart.js loaded
- DashboardAnalytics class exists
- Chart canvases exist
- SessionStorage works
- Year filter exists
- View switching works
- AJAX endpoint accessible
- Responsive design check

---

### 4. **verify-data-accuracy.php**
PHP script for automated data verification.

**Use this for:**
- Automated data accuracy testing
- Quick verification of all analytics
- Continuous integration testing
- Pre-deployment validation

**How to use:**
```bash
# From Laravel project root
php .kiro/specs/hr-analytics-dashboard/verify-data-accuracy.php
```

**Output:**
- Summary of each analytics metric
- Total counts and statistics
- Warnings for data quality issues
- Pass/fail status for each test
- Overall pass rate

---

### 5. **testing-guide.md**
Comprehensive testing guide with detailed procedures.

**Use this for:**
- Understanding testing approach
- Detailed test procedures
- Troubleshooting common issues
- Test report templates

**Sections:**
- Prerequisites
- Testing approach (3 phases)
- Detailed testing procedures
- Data verification checklist
- Common issues and solutions
- Test report template

---

### 6. **data-accuracy-report-template.md**
Template for documenting data verification results.

**Use this for:**
- Formal data accuracy reporting
- Documentation for stakeholders
- Quality assurance records
- Audit trail

**How to use:**
1. Make a copy of the template
2. Fill in each section as you verify data
3. Document query results and dashboard display
4. Mark pass/fail for each verification
5. Complete summary and recommendations
6. Get sign-off from reviewers

---

## Quick Start Guide

### For Manual Testing
```
1. Open manual-testing-checklist.md
2. Prepare test accounts (admin and employee)
3. Follow each test case
4. Document results
```

### For Data Verification
```
1. Open data-verification-queries.sql
2. Run queries in database client
3. Open dashboard in browser
4. Compare results
5. Fill out data-accuracy-report-template.md
```

### For Automated Testing
```
1. Run verify-data-accuracy.php from command line
2. Run automated-test-helper.js in browser console
3. Review results
4. Document any failures
```

---

## Testing Workflow

### Phase 1: Functional Testing (Day 1)
1. ✓ Run manual-testing-checklist.md
2. ✓ Test admin user functionality
3. ✓ Test employee user restrictions
4. ✓ Test all browsers
5. ✓ Test responsive design
6. ✓ Document issues

### Phase 2: Data Verification (Day 2)
1. ✓ Run data-verification-queries.sql
2. ✓ Compare with dashboard
3. ✓ Verify calculations
4. ✓ Check data quality
5. ✓ Fill out data-accuracy-report-template.md

### Phase 3: Automated Testing (Day 3)
1. ✓ Run verify-data-accuracy.php
2. ✓ Run automated-test-helper.js
3. ✓ Review all results
4. ✓ Create final test report
5. ✓ Get sign-off

---

## Test Environments

### Development
- **Purpose:** Initial testing and debugging
- **Data:** Test data, can be modified
- **Users:** Developers only

### Staging
- **Purpose:** Pre-production testing
- **Data:** Copy of production data
- **Users:** QA team, stakeholders

### Production
- **Purpose:** Final validation after deployment
- **Data:** Live data
- **Users:** Limited testing, smoke tests only

---

## Test Data Requirements

### Minimum Data for Testing
- At least 50 employees across multiple departments
- At least 5 different designations
- Mix of employment statuses (Active, Probation, Relieved, etc.)
- Employees with various years of service (0-20+ years)
- Employees with various ages (20-65 years)
- Both male and female employees
- Some employees with missing data (null fields)
- Additions and attritions in current year

### Recommended Data
- 100+ employees for realistic testing
- 10+ departments
- 10+ designations
- Historical data for multiple years (2020-present)
- Complete salary information
- Complete demographic information

---

## Common Testing Scenarios

### Scenario 1: New Deployment
```
1. Run all manual tests
2. Run all data verification queries
3. Run automated tests
4. Document results
5. Get sign-off
```

### Scenario 2: Bug Fix Verification
```
1. Reproduce original bug
2. Apply fix
3. Run relevant tests from checklist
4. Verify fix works
5. Run regression tests
```

### Scenario 3: Data Update
```
1. Run data-verification-queries.sql before update
2. Perform data update
3. Run data-verification-queries.sql after update
4. Compare results
5. Verify dashboard reflects changes
```

### Scenario 4: Performance Testing
```
1. Clear cache
2. Measure load time (should be < 3 seconds)
3. Check database query performance
4. Monitor memory usage
5. Test with large datasets
```

---

## Issue Reporting

### When You Find an Issue

**Document:**
1. Issue description
2. Steps to reproduce
3. Expected behavior
4. Actual behavior
5. Screenshots/videos
6. Browser/environment details
7. Severity (Critical/High/Medium/Low)

**Report:**
- Create ticket in issue tracking system
- Attach documentation
- Assign to appropriate developer
- Set priority based on severity

---

## Sign-Off Checklist

Before approving for production:

- [ ] All manual tests passed
- [ ] All data verification passed
- [ ] All automated tests passed
- [ ] No critical issues found
- [ ] Performance meets requirements (< 3 seconds)
- [ ] Works on all required browsers
- [ ] Responsive design works on mobile
- [ ] Admin access control verified
- [ ] Employee restrictions verified
- [ ] Documentation complete
- [ ] Stakeholder approval obtained

---

## Support

### Questions or Issues?
- Review testing-guide.md for detailed procedures
- Check common issues section in testing-guide.md
- Contact development team
- Review requirements.md and design.md for specifications

### Need More Test Data?
- Use database seeders
- Import sample data
- Clone from staging environment

### Automated Tests Failing?
- Check database connection
- Verify Laravel environment is bootstrapped
- Check for missing dependencies
- Review error messages in output

---

## Version History

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 1.0 | [Date] | Initial testing documentation | [Name] |

---

## Next Steps

After completing all testing:

1. ✓ Compile final test report
2. ✓ Document all issues found
3. ✓ Verify all critical issues resolved
4. ✓ Get stakeholder sign-off
5. ✓ Schedule production deployment
6. ✓ Plan post-deployment validation
7. ✓ Archive test results for audit trail

---

**Remember:** Thorough testing ensures a successful deployment and happy users!
