@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ $pageTitle }}</h1>
    </div>

    <div class="container-fluid pt-3">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomIn()">
                                    <i class="fas fa-search-plus"></i> Zoom In
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomOut()">
                                    <i class="fas fa-search-minus"></i> Zoom Out
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetZoom()">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="expandAll()">
                                    <i class="fas fa-expand-alt"></i> Expand All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                                    <i class="fas fa-compress-alt"></i> Collapse All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="org-chart-container" style="width: 100%; height: 700px; overflow: auto; border: 1px solid #ddd; background: #f9f9f9;">
                            <div id="org-chart" style="transform-origin: top left;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .org-chart-node {
        background: white;
        border: 2px solid #8B1538;
        border-radius: 8px;
        padding: 15px;
        margin: 10px;
        min-width: 200px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .org-chart-node:hover {
        box-shadow: 0 4px 16px rgba(139, 21, 56, 0.3);
        transform: translateY(-2px);
    }

    .org-chart-node.has-children::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 20px;
        background: #8B1538;
    }

    .org-chart-node-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .org-chart-node-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #8B1538;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .org-chart-node-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .org-chart-node-info {
        flex: 1;
    }

    .org-chart-node-name {
        font-weight: 600;
        color: #8B1538;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .org-chart-node-title {
        font-size: 12px;
        color: #666;
        margin-bottom: 2px;
    }

    .org-chart-node-team {
        font-size: 11px;
        color: #999;
    }

    .org-chart-node-expand {
        text-align: center;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #eee;
    }

    .org-chart-node-expand button {
        background: #8B1538;
        color: white;
        border: none;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
    }

    .org-chart-node-expand button:hover {
        background: #6d1029;
    }

    .org-chart-children {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        position: relative;
        padding-top: 30px;
    }

    .org-chart-children::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 30px;
        background: #8B1538;
    }

    .org-chart-children > div {
        position: relative;
    }

    .org-chart-children > div::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 30px;
        background: #8B1538;
    }

    .org-chart-level {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .org-chart-branch {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 10px;
    }

    .collapsed .org-chart-children {
        display: none;
    }

    .loading-spinner {
        text-align: center;
        padding: 50px;
        font-size: 18px;
        color: #8B1538;
    }
</style>

<script>
    let currentZoom = 1;
    let chartData = null;

    $(document).ready(function() {
        loadOrganizationChart();
    });

    function loadOrganizationChart() {
        $('#org-chart').html('<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading Organization Chart...</div>');

        $.ajax({
            url: '{{ url("organization-chart/get-chart-data") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status_code == 1 && response.data) {
                    chartData = response.data;
                    renderChart(chartData);
                } else {
                    $('#org-chart').html('<div class="alert alert-warning">No organization data available.</div>');
                }
            },
            error: function() {
                $('#org-chart').html('<div class="alert alert-danger">Failed to load organization chart.</div>');
            }
        });
    }

    function renderChart(data) {
        $('#org-chart').html('');
        
        if (!data || data.length === 0) {
            $('#org-chart').html('<div class="alert alert-info">No employees found in the organization.</div>');
            return;
        }

        const chartHtml = '<div class="org-chart-level">' + data.map(node => renderNode(node)).join('') + '</div>';
        $('#org-chart').html(chartHtml);
    }

    function renderNode(node) {
        const hasChildren = node.children && node.children.length > 0;
        const initials = getInitials(node.name);
        const avatarHtml = node.profile_pic 
            ? `<img src="${node.profile_pic}" alt="${node.name}">`
            : initials;

        let html = `
            <div class="org-chart-branch">
                <div class="org-chart-node ${hasChildren ? 'has-children' : ''}" data-id="${node.id}" onclick="viewProfile('${node.profile_url}')">
                    <div class="org-chart-node-header">
                        <div class="org-chart-node-avatar">${avatarHtml}</div>
                        <div class="org-chart-node-info">
                            <div class="org-chart-node-name">${node.name}</div>
                            <div class="org-chart-node-title">${node.title}</div>
                            ${node.team ? `<div class="org-chart-node-team">${node.team}</div>` : ''}
                        </div>
                    </div>
                    ${hasChildren ? `
                        <div class="org-chart-node-expand">
                            <button onclick="event.stopPropagation(); toggleNode(this, ${node.id})">
                                <i class="fas fa-chevron-down"></i> ${node.children.length} Report${node.children.length > 1 ? 's' : ''}
                            </button>
                        </div>
                    ` : ''}
                </div>
        `;

        if (hasChildren) {
            html += `
                <div class="org-chart-children collapsed">
                    ${node.children.map(child => renderNode(child)).join('')}
                </div>
            `;
        }

        html += '</div>';
        return html;
    }

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }

    function toggleNode(button, nodeId) {
        const node = $(button).closest('.org-chart-branch');
        const children = node.find('> .org-chart-children').first();
        
        children.toggleClass('collapsed');
        
        const icon = $(button).find('i');
        if (children.hasClass('collapsed')) {
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    }

    function expandAll() {
        $('.org-chart-children').removeClass('collapsed');
        $('.org-chart-node-expand i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }

    function collapseAll() {
        $('.org-chart-children').addClass('collapsed');
        $('.org-chart-node-expand i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }

    function zoomIn() {
        currentZoom += 0.1;
        applyZoom();
    }

    function zoomOut() {
        if (currentZoom > 0.3) {
            currentZoom -= 0.1;
            applyZoom();
        }
    }

    function resetZoom() {
        currentZoom = 1;
        applyZoom();
    }

    function applyZoom() {
        $('#org-chart').css('transform', `scale(${currentZoom})`);
    }

    function viewProfile(url) {
        window.open(url, '_blank');
    }
</script>
@endsection
