-- Data Verification Queries for HR Analytics Dashboard
-- Run these queries to manually verify the accuracy of analytics data

-- ============================================================================
-- 1. EMPLOYEE COUNT BY DESIGNATION
-- ============================================================================
-- This should match the "Employee Count by Designation" chart
SELECT 
    dm.v_designation_name as designation,
    COUNT(em.i_id) as count
FROM tbl_employee_master em
LEFT JOIN tbl_designation_master dm 
    ON em.i_designation_id = dm.i_id
WHERE em.t_is_deleted = 0 
    AND em.e_employment_status != 'Relieved'
GROUP BY em.i_designation_id, dm.v_designation_name
ORDER BY count DESC;

-- ============================================================================
-- 2. EMPLOYEE COUNT BY DEPARTMENT
-- ============================================================================
-- This should match the "Employee Count by Department" chart
SELECT 
    COALESCE(dept.v_department_name, 'Unassigned') as department,
    COUNT(em.i_id) as count
FROM tbl_employee_master em
LEFT JOIN tbl_department_master dept 
    ON em.i_department_id = dept.i_id
WHERE em.t_is_deleted = 0 
    AND em.e_employment_status != 'Relieved'
GROUP BY em.i_department_id, dept.v_department_name
ORDER BY count DESC;

-- ============================================================================
-- 3. ADDITIONS AND ATTRITIONS (Current Year)
-- ============================================================================
-- Replace YEAR(CURDATE()) with specific year if testing different years

-- Additions by month
SELECT 
    MONTH(dt_joining_date) as month,
    MONTHNAME(dt_joining_date) as month_name,
    COUNT(*) as additions
FROM tbl_employee_master
WHERE YEAR(dt_joining_date) = YEAR(CURDATE())
    AND t_is_deleted = 0
GROUP BY MONTH(dt_joining_date), MONTHNAME(dt_joining_date)
ORDER BY MONTH(dt_joining_date);

-- Attritions by month
SELECT 
    MONTH(dt_relieving_date) as month,
    MONTHNAME(dt_relieving_date) as month_name,
    COUNT(*) as attritions
FROM tbl_employee_master
WHERE YEAR(dt_relieving_date) = YEAR(CURDATE())
    AND e_employment_status = 'Relieved'
    AND t_is_deleted = 0
GROUP BY MONTH(dt_relieving_date), MONTHNAME(dt_relieving_date)
ORDER BY MONTH(dt_relieving_date);

-- Combined view for all 12 months
SELECT 
    m.month_num,
    m.month_name,
    COALESCE(a.additions, 0) as additions,
    COALESCE(at.attritions, 0) as attritions
FROM (
    SELECT 1 as month_num, 'Jan' as month_name UNION ALL
    SELECT 2, 'Feb' UNION ALL SELECT 3, 'Mar' UNION ALL
    SELECT 4, 'Apr' UNION ALL SELECT 5, 'May' UNION ALL
    SELECT 6, 'Jun' UNION ALL SELECT 7, 'Jul' UNION ALL
    SELECT 8, 'Aug' UNION ALL SELECT 9, 'Sep' UNION ALL
    SELECT 10, 'Oct' UNION ALL SELECT 11, 'Nov' UNION ALL
    SELECT 12, 'Dec'
) m
LEFT JOIN (
    SELECT MONTH(dt_joining_date) as month, COUNT(*) as additions
    FROM tbl_employee_master
    WHERE YEAR(dt_joining_date) = YEAR(CURDATE()) AND t_is_deleted = 0
    GROUP BY MONTH(dt_joining_date)
) a ON m.month_num = a.month
LEFT JOIN (
    SELECT MONTH(dt_relieving_date) as month, COUNT(*) as attritions
    FROM tbl_employee_master
    WHERE YEAR(dt_relieving_date) = YEAR(CURDATE()) 
        AND e_employment_status = 'Relieved' 
        AND t_is_deleted = 0
    GROUP BY MONTH(dt_relieving_date)
) at ON m.month_num = at.month
ORDER BY m.month_num;

-- ============================================================================
-- 4. EMPLOYEE COUNT BY STATUS
-- ============================================================================
-- This should match the "Employee Count by Status" chart
SELECT 
    e_employment_status as status,
    COUNT(*) as count
FROM tbl_employee_master
WHERE t_is_deleted = 0
GROUP BY e_employment_status
ORDER BY count DESC;

-- ============================================================================
-- 5. ANNUAL CTC BY DEPARTMENT
-- ============================================================================
-- This should match the "Annual CTC by Department" chart
SELECT 
    COALESCE(dept.v_department_name, 'Unassigned') as department,
    SUM(COALESCE(esi.d_annual_ctc, 0)) as total_ctc,
    COUNT(em.i_id) as employee_count,
    ROUND(AVG(COALESCE(esi.d_annual_ctc, 0)), 2) as avg_ctc
FROM tbl_employee_master em
LEFT JOIN tbl_department_master dept 
    ON em.i_department_id = dept.i_id
LEFT JOIN tbl_employee_salary_info esi 
    ON em.i_id = esi.i_employee_id AND esi.t_is_deleted = 0
WHERE em.t_is_deleted = 0 
    AND em.e_employment_status != 'Relieved'
GROUP BY em.i_department_id, dept.v_department_name
ORDER BY total_ctc DESC;

-- Verify individual CTC records
SELECT 
    em.v_employee_code,
    em.v_employee_full_name,
    dept.v_department_name,
    esi.d_annual_ctc
FROM tbl_employee_master em
LEFT JOIN tbl_department_master dept ON em.i_department_id = dept.i_id
LEFT JOIN tbl_employee_salary_info esi ON em.i_id = esi.i_employee_id AND esi.t_is_deleted = 0
WHERE em.t_is_deleted = 0 
    AND em.e_employment_status != 'Relieved'
ORDER BY dept.v_department_name, esi.d_annual_ctc DESC;

-- ============================================================================
-- 6. GENDER DISTRIBUTION
-- ============================================================================
-- This should match the "Gender Distribution" chart
SELECT 
    COALESCE(e_gender, 'Not Specified') as gender,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM tbl_employee_master 
        WHERE t_is_deleted = 0 AND e_employment_status != 'Relieved'), 2) as percentage
FROM tbl_employee_master
WHERE t_is_deleted = 0 
    AND e_employment_status != 'Relieved'
GROUP BY e_gender
ORDER BY count DESC;

-- ============================================================================
-- 7. YEARS OF SERVICE DISTRIBUTION
-- ============================================================================
-- This should match the "Years of Service Distribution" chart
SELECT 
    CASE
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 1 THEN '0-1 years'
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 3 THEN '1-3 years'
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 5 THEN '3-5 years'
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 10 THEN '5-10 years'
        ELSE '10+ years'
    END as service_range,
    COUNT(*) as count
FROM tbl_employee_master
WHERE t_is_deleted = 0 
    AND e_employment_status != 'Relieved'
GROUP BY service_range
ORDER BY FIELD(service_range, '0-1 years', '1-3 years', '3-5 years', '5-10 years', '10+ years');

-- Detailed view with actual years
SELECT 
    em.v_employee_code,
    em.v_employee_full_name,
    em.dt_joining_date,
    TIMESTAMPDIFF(YEAR, em.dt_joining_date, CURDATE()) as years_of_service,
    CASE
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 1 THEN '0-1 years'
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 3 THEN '1-3 years'
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 5 THEN '3-5 years'
        WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 10 THEN '5-10 years'
        ELSE '10+ years'
    END as service_range
FROM tbl_employee_master em
WHERE em.t_is_deleted = 0 
    AND em.e_employment_status != 'Relieved'
ORDER BY years_of_service DESC;

-- ============================================================================
-- 8. AGE DISTRIBUTION
-- ============================================================================
-- This should match the "Age Distribution" chart
SELECT 
    CASE
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 26 THEN '18-25'
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 36 THEN '26-35'
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 46 THEN '36-45'
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 56 THEN '46-55'
        ELSE '56+'
    END as age_range,
    COUNT(*) as count
FROM tbl_employee_master
WHERE t_is_deleted = 0 
    AND e_employment_status != 'Relieved'
    AND dt_birth_date IS NOT NULL
GROUP BY age_range
ORDER BY FIELD(age_range, '18-25', '26-35', '36-45', '46-55', '56+');

-- Detailed view with actual ages
SELECT 
    em.v_employee_code,
    em.v_employee_full_name,
    em.dt_birth_date,
    TIMESTAMPDIFF(YEAR, em.dt_birth_date, CURDATE()) as age,
    CASE
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 26 THEN '18-25'
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 36 THEN '26-35'
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 46 THEN '36-45'
        WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 56 THEN '46-55'
        ELSE '56+'
    END as age_range
FROM tbl_employee_master em
WHERE em.t_is_deleted = 0 
    AND em.e_employment_status != 'Relieved'
    AND em.dt_birth_date IS NOT NULL
ORDER BY age DESC;

-- ============================================================================
-- 9. OVERALL STATISTICS SUMMARY
-- ============================================================================
-- Quick summary to verify overall counts
SELECT 
    'Total Employees' as metric,
    COUNT(*) as count
FROM tbl_employee_master
WHERE t_is_deleted = 0
UNION ALL
SELECT 
    'Active Employees',
    COUNT(*)
FROM tbl_employee_master
WHERE t_is_deleted = 0 AND e_employment_status != 'Relieved'
UNION ALL
SELECT 
    'Relieved Employees',
    COUNT(*)
FROM tbl_employee_master
WHERE t_is_deleted = 0 AND e_employment_status = 'Relieved'
UNION ALL
SELECT 
    'Employees with Salary Info',
    COUNT(DISTINCT esi.i_employee_id)
FROM tbl_employee_salary_info esi
WHERE esi.t_is_deleted = 0
UNION ALL
SELECT 
    'Employees without Birth Date',
    COUNT(*)
FROM tbl_employee_master
WHERE t_is_deleted = 0 AND dt_birth_date IS NULL;

-- ============================================================================
-- 10. DATA QUALITY CHECKS
-- ============================================================================
-- Check for potential data issues

-- Employees without designation
SELECT COUNT(*) as employees_without_designation
FROM tbl_employee_master
WHERE t_is_deleted = 0 
    AND e_employment_status != 'Relieved'
    AND i_designation_id IS NULL;

-- Employees without department
SELECT COUNT(*) as employees_without_department
FROM tbl_employee_master
WHERE t_is_deleted = 0 
    AND e_employment_status != 'Relieved'
    AND i_department_id IS NULL;

-- Employees without salary info
SELECT 
    em.v_employee_code,
    em.v_employee_full_name,
    em.e_employment_status
FROM tbl_employee_master em
LEFT JOIN tbl_employee_salary_info esi 
    ON em.i_id = esi.i_employee_id AND esi.t_is_deleted = 0
WHERE em.t_is_deleted = 0 
    AND em.e_employment_status != 'Relieved'
    AND esi.i_id IS NULL;

-- Employees with future joining dates
SELECT 
    v_employee_code,
    v_employee_full_name,
    dt_joining_date
FROM tbl_employee_master
WHERE t_is_deleted = 0 
    AND dt_joining_date > CURDATE();

-- Employees with invalid age (< 18 or > 100)
SELECT 
    v_employee_code,
    v_employee_full_name,
    dt_birth_date,
    TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) as age
FROM tbl_employee_master
WHERE t_is_deleted = 0 
    AND dt_birth_date IS NOT NULL
    AND (TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 18 
         OR TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) > 100);
