<div class="org-chart-vertical">
    <style>
        .org-chart-vertical {
            padding: 15px;
        }
        
        .org-node-vertical {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
            margin: 8px auto;
            max-width: 280px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            page-break-inside: avoid;
            overflow: visible;
        }

        .org-node-vertical:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            border-color: #8B1538;
        }

        .org-node-vertical.current-employee {
            border: 2px solid #28a745;
            background: #f8fff9;
        }

        .org-node-vertical.current-employee::before {
            content: 'YOU';
            position: absolute;
            top: -10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .org-node-header-vertical {
            display: flex;
            align-items: center;
        }

        .org-node-avatar-vertical {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8B1538 0%, #6d1029 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 10px;
            flex-shrink: 0;
            border: 2px solid #f5f5f5;
        }

        .org-node-avatar-vertical img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .org-node-info-vertical {
            flex: 1;
            min-width: 0;
        }

        .org-node-name-vertical {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            margin-bottom: 3px;
            line-height: 1.2;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .org-node-title-vertical {
            font-size: 11px;
            color: #666;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .org-node-team-vertical {
            font-size: 10px;
            color: #999;
            line-height: 1.2;
        }

        .org-section-label {
            text-align: center;
            font-size: 13px;
            color: #555;
            font-weight: 600;
            margin: 15px 0 10px 0;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #8B1538;
        }

        .org-section-label i {
            color: #8B1538;
            margin-right: 5px;
        }

        .org-connector-arrow {
            text-align: center;
            color: #ccc;
            font-size: 18px;
            margin: 5px 0;
        }

        .org-reports-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 8px;
            width: 100%;
        }
        
        @media print, (min-width: 1px) {
            .org-reports-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                width: 100%;
            }
        }
        
        @media (max-width: 992px) {
            .org-reports-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .org-reports-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .org-reports-grid .org-node-vertical {
            max-width: 100%;
            margin: 0;
            min-width: 0;
        }
    </style>

    <!-- Manager Chain (Upward) -->
    @if(!empty($managerChain) && count($managerChain) > 0)
        <div class="org-section-label">
            <i class="fas fa-arrow-up"></i> Reports To
        </div>
        @foreach($managerChain as $manager)
            @php
                $initials = implode('', array_map(function($n) { return substr($n, 0, 1); }, explode(' ', $manager['name'])));
                $initials = strtoupper(substr($initials, 0, 2));
            @endphp
            <div class="org-node-vertical" onclick="viewEmployeeProfile('{{ $manager['profile_url'] }}')">
                <div class="org-node-header-vertical">
                    <div class="org-node-avatar-vertical">
                        @if(!empty($manager['profile_pic']))
                            <img src="{{ asset(config('constants.FILE_STORAGE_PATH_URL') . config('constants.UPLOAD_FOLDER') . $manager['profile_pic']) }}" alt="{{ $manager['name'] }}">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="org-node-info-vertical">
                        <div class="org-node-name-vertical">{{ $manager['name'] }}</div>
                        <div class="org-node-title-vertical">{{ $manager['title'] }}</div>
                        @if(!empty($manager['team']))
                            <div class="org-node-team-vertical">{{ $manager['team'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @if(!$loop->last)
                <div class="org-connector-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
            @endif
        @endforeach
        <div class="org-connector-arrow">
            <i class="fas fa-arrow-down"></i>
        </div>
    @endif
    
    <!-- Current Employee -->
    <div class="org-section-label">
        <i class="fas fa-user"></i> Current Employee
    </div>
    @php
        $initials = implode('', array_map(function($n) { return substr($n, 0, 1); }, explode(' ', $currentEmployee['name'])));
        $initials = strtoupper(substr($initials, 0, 2));
    @endphp
    <div class="org-node-vertical current-employee" onclick="viewEmployeeProfile('{{ $currentEmployee['profile_url'] }}')">
        <div class="org-node-header-vertical">
            <div class="org-node-avatar-vertical">
                @if(!empty($currentEmployee['profile_pic']))
                    <img src="{{ asset(config('constants.FILE_STORAGE_PATH_URL') . config('constants.UPLOAD_FOLDER') . $currentEmployee['profile_pic']) }}" alt="{{ $currentEmployee['name'] }}">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="org-node-info-vertical">
                <div class="org-node-name-vertical">{{ $currentEmployee['name'] }}</div>
                <div class="org-node-title-vertical">{{ $currentEmployee['title'] }}</div>
                @if(!empty($currentEmployee['team']))
                    <div class="org-node-team-vertical">{{ $currentEmployee['team'] }}</div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Direct Reports (Downward) -->
    @if(!empty($directReports) && count($directReports) > 0)
        <div class="org-connector-arrow">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="org-section-label">
            <i class="fas fa-users"></i> Direct Reports ({{ count($directReports) }})
        </div>
        <div class="org-reports-grid">
            @foreach($directReports as $report)
                @php
                    $initials = implode('', array_map(function($n) { return substr($n, 0, 1); }, explode(' ', $report['name'])));
                    $initials = strtoupper(substr($initials, 0, 2));
                @endphp
                <div class="org-node-vertical" onclick="viewEmployeeProfile('{{ $report['profile_url'] }}')">
                    <div class="org-node-header-vertical">
                        <div class="org-node-avatar-vertical">
                            @if(!empty($report['profile_pic']))
                                <img src="{{ asset(config('constants.FILE_STORAGE_PATH_URL') . config('constants.UPLOAD_FOLDER') . $report['profile_pic']) }}" alt="{{ $report['name'] }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <div class="org-node-info-vertical">
                            <div class="org-node-name-vertical">{{ $report['name'] }}</div>
                            <div class="org-node-title-vertical">{{ $report['title'] }}</div>
                            @if(!empty($report['team']))
                                <div class="org-node-team-vertical">{{ $report['team'] }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    @if(empty($managerChain) && empty($directReports))
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> This employee has no manager assigned and no direct reports.
        </div>
    @endif
</div>

<script>
    function viewEmployeeProfile(url) {
        $('#org-chart-modal').modal('hide');
        setTimeout(function() {
            window.location.href = url;
        }, 300);
    }
</script>
