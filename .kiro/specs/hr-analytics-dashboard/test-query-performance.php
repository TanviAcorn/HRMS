#!/usr/bin/env php
<?php

/**
 * Query Performance Testing Script
 * 
 * This script tests the performance of all analytics queries using EXPLAIN
 * to verify that indexes are being used properly.
 * 
 * Usage: php test-query-performance.php
 */

// Bootstrap Laravel
require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

echo "\n=== HR Analytics Query Performance Test ===\n\n";

// Test year
$testYear = date('Y');
$yearStart = $testYear . '-01-01';
$yearEnd = ($testYear + 1) . '-01-01';

$queries = [
    'Employee by Designation' => "EXPLAIN SELECT 
        COALESCE(lm.v_value, 'Unassigned') as designation,
        COUNT(em.i_id) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
    LEFT JOIN " . config('constants.LOOKUP_MASTER_TABLE') . " lm 
        ON em.i_designation_id = lm.i_id 
        AND lm.v_module_name = '" . config('constants.DESIGNATION_LOOKUP') . "'
        AND lm.t_is_deleted = 0
    WHERE em.t_is_deleted = 0 
        AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
    GROUP BY em.i_designation_id, lm.v_value
    ORDER BY count DESC",
    
    'Employee by Department' => "EXPLAIN SELECT 
        COALESCE(lm.v_value, 'Unassigned') as department,
        COUNT(em.i_id) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
    LEFT JOIN " . config('constants.LOOKUP_MASTER_TABLE') . " lm 
        ON em.i_department_id = lm.i_id 
        AND lm.v_module_name = 'department'
        AND lm.t_is_deleted = 0
    WHERE em.t_is_deleted = 0 
        AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
    GROUP BY em.i_department_id, lm.v_value
    ORDER BY count DESC",
    
    'Additions (Optimized)' => "EXPLAIN SELECT 
        MONTH(dt_joining_date) as month,
        COUNT(*) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
    WHERE dt_joining_date >= '$yearStart'
        AND dt_joining_date < '$yearEnd'
        AND t_is_deleted = 0
    GROUP BY MONTH(dt_joining_date)",
    
    'Attritions (Optimized)' => "EXPLAIN SELECT 
        MONTH(dt_relieving_date) as month,
        COUNT(*) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
    WHERE dt_relieving_date >= '$yearStart'
        AND dt_relieving_date < '$yearEnd'
        AND e_employment_status = '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
        AND t_is_deleted = 0
    GROUP BY MONTH(dt_relieving_date)",
    
    'Employee by Status' => "EXPLAIN SELECT 
        e_employment_status as status,
        COUNT(*) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
    WHERE t_is_deleted = 0
    GROUP BY e_employment_status
    ORDER BY count DESC",
    
    'CTC by Department' => "EXPLAIN SELECT 
        COALESCE(lm.v_value, 'Unassigned') as department,
        SUM(COALESCE(esi.d_annual_ctc, 0)) as total_ctc
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
    LEFT JOIN " . config('constants.LOOKUP_MASTER_TABLE') . " lm 
        ON em.i_department_id = lm.i_id 
        AND lm.v_module_name = 'department'
        AND lm.t_is_deleted = 0
    LEFT JOIN " . config('constants.EMPLOYEE_SALARY_MASTER_TABLE') . " esi 
        ON em.i_id = esi.i_employee_id 
        AND esi.t_is_deleted = 0
    WHERE em.t_is_deleted = 0 
        AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
    GROUP BY em.i_department_id, lm.v_value
    ORDER BY total_ctc DESC",
    
    'Gender Distribution' => "EXPLAIN SELECT 
        COALESCE(e_gender, 'Not Specified') as gender,
        COUNT(*) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
    WHERE t_is_deleted = 0 
        AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
    GROUP BY e_gender
    ORDER BY count DESC",
    
    'Years of Service (Optimized)' => "EXPLAIN SELECT 
        CASE
            WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 1 THEN '0-1 years'
            WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 3 THEN '1-3 years'
            WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 5 THEN '3-5 years'
            WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 10 THEN '5-10 years'
            ELSE '10+ years'
        END as service_range,
        COUNT(*) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
    WHERE t_is_deleted = 0 
        AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
        AND dt_joining_date IS NOT NULL
    GROUP BY service_range
    ORDER BY FIELD(service_range, '0-1 years', '1-3 years', '3-5 years', '5-10 years', '10+ years')",
    
    'Age Distribution' => "EXPLAIN SELECT 
        CASE
            WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 26 THEN '18-25'
            WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 36 THEN '26-35'
            WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 46 THEN '36-45'
            WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 56 THEN '46-55'
            ELSE '56+'
        END as age_range,
        COUNT(*) as count
    FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
    WHERE t_is_deleted = 0 
        AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
        AND dt_birth_date IS NOT NULL
    GROUP BY age_range
    ORDER BY FIELD(age_range, '18-25', '26-35', '36-45', '46-55', '56+')",
];

echo "Testing " . count($queries) . " queries...\n\n";

foreach ($queries as $name => $query) {
    echo "--- $name ---\n";
    
    try {
        $startTime = microtime(true);
        $results = DB::select($query);
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        echo "Execution Time: {$executionTime}ms\n";
        
        if (!empty($results)) {
            $result = $results[0];
            
            // Convert object to array for easier access
            $resultArray = (array) $result;
            
            echo "Type: " . ($resultArray['type'] ?? 'N/A') . "\n";
            echo "Key Used: " . ($resultArray['key'] ?? 'NONE') . "\n";
            echo "Rows Examined: " . ($resultArray['rows'] ?? 'N/A') . "\n";
            
            // Check for potential issues
            if (isset($resultArray['type']) && $resultArray['type'] === 'ALL') {
                echo "⚠️  WARNING: Full table scan detected!\n";
            }
            
            if (!isset($resultArray['key']) || $resultArray['key'] === null) {
                echo "⚠️  WARNING: No index used!\n";
            } else {
                echo "✓ Index is being used\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "\n=== Performance Test Complete ===\n";
echo "\nRecommendations:\n";
echo "- Look for queries with 'type: ALL' (full table scans)\n";
echo "- Ensure 'Key Used' shows an index name\n";
echo "- Lower 'Rows Examined' is better\n";
echo "- Execution time should be under 200ms for most queries\n\n";

echo "To run actual query timing test:\n";
echo "php artisan tinker\n";
echo "Then: DB::enableQueryLog(); // call analytics endpoint // DB::getQueryLog();\n\n";
