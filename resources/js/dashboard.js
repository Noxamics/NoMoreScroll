/**
 * public/js/dashboard.js
 * Activa Admin — Dashboard Charts
 * Depends on: Chart.js >= 4.4
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Shared chart defaults ─────────────────────────────── */
    const TOOLTIP = {
        backgroundColor : '#fff',
        borderColor     : '#E2EAF2',
        borderWidth     : 1,
        titleColor      : '#0F1F35',
        bodyColor       : '#4A6180',
        padding         : 10,
    };

    const GRID_AXIS = {
        grid  : { color: 'rgba(30,58,95,0.05)' },
        ticks : { color: '#8BA3BE', font: { family: 'DM Sans', size: 10 } },
    };

    /* ── Read data injected by Blade ───────────────────────── */
    const el    = document.getElementById('chart-data');
    const trend = JSON.parse(el.dataset.trend);
    const dist  = JSON.parse(el.dataset.dist);

    /* ── Trend line chart ──────────────────────────────────── */
    const trendEl = document.getElementById('trendChart');

    if (trendEl) {
        new Chart(trendEl, {
            type : 'line',
            data : {
                labels   : trend.labels,
                datasets : [
                    {
                        label           : 'Focus Score',
                        data            : trend.focus,
                        borderColor     : '#0D9488',
                        backgroundColor : 'rgba(13,148,136,0.07)',
                        fill            : true,
                        tension         : 0.4,
                        pointRadius     : 3,
                        pointBackgroundColor : '#0D9488',
                        borderWidth     : 2.5,
                    },
                    {
                        label           : 'Screen Time (j)',
                        data            : trend.screen,
                        borderColor     : '#D97706',
                        backgroundColor : 'rgba(217,119,6,0.05)',
                        fill            : true,
                        tension         : 0.4,
                        pointRadius     : 3,
                        pointBackgroundColor : '#D97706',
                        borderWidth     : 2,
                        borderDash      : [4, 4],
                        yAxisID         : 'y1',
                    },
                ],
            },
            options : {
                responsive  : true,
                interaction : { mode: 'index', intersect: false },
                plugins : {
                    legend  : {
                        labels : {
                            color    : '#4A6180',
                            font     : { family: 'DM Sans', size: 11 },
                            boxWidth : 12,
                        },
                    },
                    tooltip : TOOLTIP,
                },
                scales : {
                    x  : GRID_AXIS,
                    y  : {
                        ...GRID_AXIS,
                        min : 40,
                        max : 100,
                    },
                    y1 : {
                        position : 'right',
                        grid     : { drawOnChartArea: false },
                        ticks    : { color: '#8BA3BE', font: { family: 'DM Sans', size: 10 } },
                        min      : 4,
                        max      : 12,
                    },
                },
            },
        });
    }

    /* ── Donut / risk-distribution chart ───────────────────── */
    const donutEl = document.getElementById('donutChart');

    if (donutEl && dist.length > 0) {
        new Chart(donutEl, {
            type : 'doughnut',
            data : {
                labels   : dist.map(d => d.label),
                datasets : [{
                    data            : dist.map(d => d.pct),
                    backgroundColor : ['#0D9488', '#D97706', '#E05252'],
                    borderColor     : '#fff',
                    borderWidth     : 3,
                    hoverOffset     : 6,
                }],
            },
            options : {
                responsive : true,
                cutout     : '72%',
                plugins    : {
                    legend  : { display: false },
                    tooltip : TOOLTIP,
                },
            },
        });
    }

});