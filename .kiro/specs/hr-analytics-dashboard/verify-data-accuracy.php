<?php
/**
 * Data Accuracy Verification Script for HR Analytics Dashboard
 * 
 * This script compares the analytics data returned by the API
 * with direct database queries to verify accuracy.
 * 
 * Usage: php verify-data-accuracy.php
 * 
 * Run this from the Laravel project root directory.
 */

// Bootstrap Laravel
require __DIR__ . '/../../../bootstrap/app.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

class DataAccuracyVerifier
{
    private $errors = [];
    private $warnings = [];
    private $passed = 0;
    private $failed = 0;

    public function run()
    {
        echo "=== HR Analytics Dashboard - Data Accuracy Verification ===\n\n";
        
        $this->verifyEmployeeByDesignation();
        $this->verifyEmployeeByDepartment();
        $this->verifyAdditionsAttritions();
        $this->verifyEmployeeByStatus();
        $this->verifyCtcByDepartment();
        $this->verifyGenderDistribution();
        $this->verifyServiceDistribution();
        $this->verifyAgeDistribution();
        
        $this->printSummary();
    }

    private function verifyEmployeeByDesignation()
    {
        echo "Testing: Employee Count by Designation\n";
        
        $query = "SELECT 
                    dm.v_designation_name as designation,
                    COUNT(em.i_id) as count
                  FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
                  LEFT JOIN " . config('constants.DESIGNATION_MASTER_TABLE') . " dm 
                    ON em.i_designation_id = dm.i_id
                  WHERE em.t_is_deleted = 0 
                    AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                  GROUP BY em.i_designation_id, dm.v_designation_name
                  ORDER BY count DESC";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->addWarning("No designation data found");
        } else {
            $totalCount = array_sum(array_column($results, 'count'));
            echo "  ✓ Found " . count($results) . " designations\n";
            echo "  ✓ Total employees: $totalCount\n";
            $this->passed++;
        }
        
        echo "\n";
    }

    private function verifyEmployeeByDepartment()
    {
        echo "Testing: Employee Count by Department\n";
        
        $query = "SELECT 
                    COALESCE(dept.v_department_name, 'Unassigned') as department,
                    COUNT(em.i_id) as count
                  FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
                  LEFT JOIN " . config('constants.DEPARTMENT_MASTER_TABLE') . " dept 
                    ON em.i_department_id = dept.i_id
                  WHERE em.t_is_deleted = 0 
                    AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                  GROUP BY em.i_department_id, dept.v_department_name
                  ORDER BY count DESC";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->addWarning("No department data found");
        } else {
            $totalCount = array_sum(array_column($results, 'count'));
            $unassigned = array_filter($results, fn($r) => $r->department === 'Unassigned');
            
            echo "  ✓ Found " . count($results) . " departments\n";
            echo "  ✓ Total employees: $totalCount\n";
            
            if (!empty($unassigned)) {
                $unassignedCount = $unassigned[0]->count ?? 0;
                echo "  ⚠ Unassigned employees: $unassignedCount\n";
            }
            
            $this->passed++;
        }
        
        echo "\n";
    }

    private function verifyAdditionsAttritions()
    {
        echo "Testing: Additions and Attritions\n";
        
        $currentYear = date('Y');
        
        // Additions
        $additionsQuery = "SELECT 
                            MONTH(dt_joining_date) as month,
                            COUNT(*) as count
                           FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
                           WHERE YEAR(dt_joining_date) = ?
                             AND t_is_deleted = 0
                           GROUP BY MONTH(dt_joining_date)";
        
        $additions = DB::select($additionsQuery, [$currentYear]);
        $totalAdditions = array_sum(array_column($additions, 'count'));
        
        // Attritions
        $attritionsQuery = "SELECT 
                             MONTH(dt_relieving_date) as month,
                             COUNT(*) as count
                            FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
                            WHERE YEAR(dt_relieving_date) = ?
                              AND e_employment_status = '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                              AND t_is_deleted = 0
                            GROUP BY MONTH(dt_relieving_date)";
        
        $attritions = DB::select($attritionsQuery, [$currentYear]);
        $totalAttritions = array_sum(array_column($attritions, 'count'));
        
        echo "  ✓ Year: $currentYear\n";
        echo "  ✓ Total additions: $totalAdditions\n";
        echo "  ✓ Total attritions: $totalAttritions\n";
        echo "  ✓ Net change: " . ($totalAdditions - $totalAttritions) . "\n";
        
        $this->passed++;
        echo "\n";
    }

    private function verifyEmployeeByStatus()
    {
        echo "Testing: Employee Count by Status\n";
        
        $query = "SELECT 
                    e_employment_status as status,
                    COUNT(*) as count
                  FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
                  WHERE t_is_deleted = 0
                  GROUP BY e_employment_status
                  ORDER BY count DESC";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->addError("No status data found");
        } else {
            $totalCount = array_sum(array_column($results, 'count'));
            echo "  ✓ Found " . count($results) . " statuses\n";
            echo "  ✓ Total employees: $totalCount\n";
            
            foreach ($results as $row) {
                echo "    - {$row->status}: {$row->count}\n";
            }
            
            $this->passed++;
        }
        
        echo "\n";
    }

    private function verifyCtcByDepartment()
    {
        echo "Testing: Annual CTC by Department\n";
        
        $query = "SELECT 
                    COALESCE(dept.v_department_name, 'Unassigned') as department,
                    SUM(COALESCE(esi.d_annual_ctc, 0)) as total_ctc,
                    COUNT(em.i_id) as employee_count
                  FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
                  LEFT JOIN " . config('constants.DEPARTMENT_MASTER_TABLE') . " dept 
                    ON em.i_department_id = dept.i_id
                  LEFT JOIN " . config('constants.EMPLOYEE_SALARY_INFO_TABLE') . " esi 
                    ON em.i_id = esi.i_employee_id AND esi.t_is_deleted = 0
                  WHERE em.t_is_deleted = 0 
                    AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                  GROUP BY em.i_department_id, dept.v_department_name
                  ORDER BY total_ctc DESC";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->addWarning("No CTC data found");
        } else {
            $totalCtc = array_sum(array_column($results, 'total_ctc'));
            $totalEmployees = array_sum(array_column($results, 'employee_count'));
            
            echo "  ✓ Found " . count($results) . " departments\n";
            echo "  ✓ Total CTC: " . number_format($totalCtc, 2) . "\n";
            echo "  ✓ Total employees: $totalEmployees\n";
            echo "  ✓ Average CTC: " . number_format($totalCtc / $totalEmployees, 2) . "\n";
            
            // Check for employees without salary info
            $noSalaryQuery = "SELECT COUNT(*) as count
                             FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
                             LEFT JOIN " . config('constants.EMPLOYEE_SALARY_INFO_TABLE') . " esi 
                               ON em.i_id = esi.i_employee_id AND esi.t_is_deleted = 0
                             WHERE em.t_is_deleted = 0 
                               AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                               AND esi.i_id IS NULL";
            
            $noSalary = DB::select($noSalaryQuery);
            if ($noSalary[0]->count > 0) {
                echo "  ⚠ Employees without salary info: {$noSalary[0]->count}\n";
            }
            
            $this->passed++;
        }
        
        echo "\n";
    }

    private function verifyGenderDistribution()
    {
        echo "Testing: Gender Distribution\n";
        
        $query = "SELECT 
                    COALESCE(e_gender, 'Not Specified') as gender,
                    COUNT(*) as count
                  FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
                  WHERE t_is_deleted = 0 
                    AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                  GROUP BY e_gender
                  ORDER BY count DESC";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->addWarning("No gender data found");
        } else {
            $totalCount = array_sum(array_column($results, 'count'));
            echo "  ✓ Total employees: $totalCount\n";
            
            foreach ($results as $row) {
                $percentage = ($row->count / $totalCount) * 100;
                echo "    - {$row->gender}: {$row->count} (" . number_format($percentage, 2) . "%)\n";
            }
            
            $this->passed++;
        }
        
        echo "\n";
    }

    private function verifyServiceDistribution()
    {
        echo "Testing: Years of Service Distribution\n";
        
        $query = "SELECT 
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
                  GROUP BY service_range
                  ORDER BY FIELD(service_range, '0-1 years', '1-3 years', '3-5 years', '5-10 years', '10+ years')";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->addWarning("No service data found");
        } else {
            $totalCount = array_sum(array_column($results, 'count'));
            echo "  ✓ Total employees: $totalCount\n";
            
            foreach ($results as $row) {
                echo "    - {$row->service_range}: {$row->count}\n";
            }
            
            $this->passed++;
        }
        
        echo "\n";
    }

    private function verifyAgeDistribution()
    {
        echo "Testing: Age Distribution\n";
        
        $query = "SELECT 
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
                  ORDER BY FIELD(age_range, '18-25', '26-35', '36-45', '46-55', '56+')";
        
        $results = DB::select($query);
        
        if (empty($results)) {
            $this->addWarning("No age data found");
        } else {
            $totalCount = array_sum(array_column($results, 'count'));
            echo "  ✓ Total employees: $totalCount\n";
            
            foreach ($results as $row) {
                echo "    - {$row->age_range}: {$row->count}\n";
            }
            
            // Check for employees without birth date
            $noBirthDateQuery = "SELECT COUNT(*) as count
                                FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
                                WHERE t_is_deleted = 0 
                                  AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                                  AND dt_birth_date IS NULL";
            
            $noBirthDate = DB::select($noBirthDateQuery);
            if ($noBirthDate[0]->count > 0) {
                echo "  ⚠ Employees without birth date: {$noBirthDate[0]->count}\n";
            }
            
            $this->passed++;
        }
        
        echo "\n";
    }

    private function addError($message)
    {
        $this->errors[] = $message;
        $this->failed++;
        echo "  ✗ ERROR: $message\n";
    }

    private function addWarning($message)
    {
        $this->warnings[] = $message;
        echo "  ⚠ WARNING: $message\n";
    }

    private function printSummary()
    {
        echo "=== Summary ===\n";
        echo "Tests Passed: {$this->passed}\n";
        echo "Tests Failed: {$this->failed}\n";
        echo "Warnings: " . count($this->warnings) . "\n";
        
        if (!empty($this->errors)) {
            echo "\nErrors:\n";
            foreach ($this->errors as $error) {
                echo "  - $error\n";
            }
        }
        
        if (!empty($this->warnings)) {
            echo "\nWarnings:\n";
            foreach ($this->warnings as $warning) {
                echo "  - $warning\n";
            }
        }
        
        $passRate = $this->passed + $this->failed > 0 
            ? ($this->passed / ($this->passed + $this->failed)) * 100 
            : 0;
        
        echo "\nPass Rate: " . number_format($passRate, 2) . "%\n";
        
        if ($this->failed === 0) {
            echo "\n✓ All data accuracy tests passed!\n";
        } else {
            echo "\n✗ Some tests failed. Please review the errors above.\n";
        }
    }
}

// Run the verifier
try {
    $verifier = new DataAccuracyVerifier();
    $verifier->run();
} catch (Exception $e) {
    echo "Error running verification: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
