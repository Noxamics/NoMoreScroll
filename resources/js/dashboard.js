/**
 * public/js/dashboard.js
 * Activa Admin — Dashboard Charts (v2)
 * Sections: 1-Donut, 2-ScoreTrend, 3-GroupedBar, 4-Submissions, 5-Histogram
 * Depends on: Chart.js >= 4.4
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Design tokens (sesuai palette Activa) ─────────────── */
    const C = {
        navy  : '#1E3A5F',
        teal  : '#0D9488',
        amber : '#D97706',
        red   : '#E05252',
        ice   : '#F0F9FF',
        text  : '#4A6180',
        grid  : 'rgba(30,58,95,0.06)',
        white : '#ffffff',
    };

    /* ── Shared defaults ───────────────────────────────────── */
    const TOOLTIP = {
        backgroundColor : C.white,
        borderColor     : '#E2EAF2',
        borderWidth     : 1,
        titleColor      : C.navy,
        bodyColor       : C.text,
        padding         : 10,
        cornerRadius    : 8,
    };

    const AXIS = (opts = {}) => ({
        grid  : { color: C.grid },
        ticks : { color: C.text, font: { family: 'DM Sans', size: 11 } },
        ...opts,
    });

    /* ── Read data bridge ──────────────────────────────────── */
    const el = document.getElementById('chart-data');
    if (!el) return;

    const riskDist         = JSON.parse(el.dataset.riskDist         || '[]');
    const scoreTrend       = JSON.parse(el.dataset.scoreTrend       || '{}');
    const groupedBar       = JSON.parse(el.dataset.groupedBar       || '{}');
    const dailySubmissions = JSON.parse(el.dataset.dailySubmissions || '{}');
    const scoreHistogram   = JSON.parse(el.dataset.scoreHistogram   || '{}');

    /* ═══════════════════════════════════════════════════════
       1. DONUT — Distribusi Risk Level
    ═══════════════════════════════════════════════════════ */
    const donutEl = document.getElementById('donutChart');
    if (donutEl && riskDist.length > 0) {
        new Chart(donutEl, {
            type : 'doughnut',
            data : {
                labels   : riskDist.map(d => d.label),
                datasets : [{
                    data            : riskDist.map(d => d.pct),
                    backgroundColor : [C.teal, C.amber, C.red],
                    borderColor     : C.white,
                    borderWidth     : 3,
                    hoverOffset     : 8,
                }],
            },
            options : {
                responsive : true,
                cutout     : '72%',
                plugins    : {
                    legend  : { display: false },
                    tooltip : {
                        ...TOOLTIP,
                        callbacks : {
                            label : ctx => ` ${ctx.label}: ${ctx.parsed}%`,
                        },
                    },
                },
            },
        });
    }

    /* ═══════════════════════════════════════════════════════
       2. SCORE TREND LINE CHART — dengan tab Harian/Mingguan/Bulanan
    ═══════════════════════════════════════════════════════ */
    const trendEl = document.getElementById('scoreTrendChart');
    let scoreTrendChart = null;

    function buildScoreTrendDataset(data, labels) {
        return {
            labels,
            datasets : [{
                label           : 'Avg. Skor Ketergantungan',
                data,
                borderColor     : C.navy,
                backgroundColor : 'rgba(30,58,95,0.08)',
                fill            : true,
                tension         : 0.45,
                pointRadius     : 4,
                pointBackgroundColor : C.navy,
                pointBorderColor     : C.white,
                pointBorderWidth     : 2,
                borderWidth     : 2.5,
            }],
        };
    }

    function renderScoreTrend(period) {
        if (!trendEl || !scoreTrend[period]) return;
        const { labels, data } = scoreTrend[period];

        if (scoreTrendChart) {
            scoreTrendChart.data = buildScoreTrendDataset(data, labels);
            scoreTrendChart.update('active');
        } else {
            scoreTrendChart = new Chart(trendEl, {
                type    : 'line',
                data    : buildScoreTrendDataset(data, labels),
                options : {
                    responsive  : true,
                    interaction : { mode: 'index', intersect: false },
                    plugins : {
                        legend  : { display: false },
                        tooltip : {
                            ...TOOLTIP,
                            callbacks : {
                                label : ctx => ` Skor: ${ctx.parsed.y}`,
                            },
                        },
                        annotation: {},
                    },
                    scales : {
                        x : AXIS(),
                        y : AXIS({
                            min   : 30,
                            max   : 80,
                            ticks : {
                                ...AXIS().ticks,
                                callback : v => v,
                            },
                        }),
                    },
                },
            });
        }
    }

    renderScoreTrend('daily');

    // Tab switcher
    document.getElementById('scoreTrendTabs')?.addEventListener('click', e => {
        const btn = e.target.closest('.tab-btn');
        if (!btn) return;
        document.querySelectorAll('#scoreTrendTabs .tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderScoreTrend(btn.dataset.period);
    });

    /* ═══════════════════════════════════════════════════════
       3. GROUPED BAR — Perbandingan Kategori
    ═══════════════════════════════════════════════════════ */
    const groupedEl = document.getElementById('groupedBarChart');
    let groupedBarChart = null;

    function buildGroupedDataset(groupKey) {
        const g = groupedBar[groupKey];
        if (!g) return null;
        return {
            labels   : g.labels,
            datasets : [
                {
                    label           : 'Rendah',
                    data            : g.rendah,
                    backgroundColor : C.teal,
                    borderRadius    : 4,
                    borderSkipped   : false,
                },
                {
                    label           : 'Sedang',
                    data            : g.sedang,
                    backgroundColor : C.amber,
                    borderRadius    : 4,
                    borderSkipped   : false,
                },
                {
                    label           : 'Tinggi',
                    data            : g.tinggi,
                    backgroundColor : C.red,
                    borderRadius    : 4,
                    borderSkipped   : false,
                },
            ],
        };
    }

    function renderGroupedBar(groupKey) {
        if (!groupedEl) return;
        const chartData = buildGroupedDataset(groupKey);
        if (!chartData) return;

        if (groupedBarChart) {
            groupedBarChart.data = chartData;
            groupedBarChart.update('active');
        } else {
            groupedBarChart = new Chart(groupedEl, {
                type    : 'bar',
                data    : chartData,
                options : {
                    responsive  : true,
                    interaction : { mode: 'index', intersect: false },
                    plugins : {
                        legend : {
                            position : 'top',
                            labels   : {
                                color    : C.text,
                                font     : { family: 'DM Sans', size: 11 },
                                boxWidth : 12,
                                padding  : 16,
                            },
                        },
                        tooltip : {
                            ...TOOLTIP,
                            callbacks : {
                                label : ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}%`,
                            },
                        },
                    },
                    scales : {
                        x : AXIS(),
                        y : AXIS({
                            min   : 0,
                            max   : 70,
                            ticks : {
                                ...AXIS().ticks,
                                callback : v => v + '%',
                            },
                        }),
                    },
                },
            });
        }
    }

    renderGroupedBar('byRole');

    document.getElementById('groupedBarTabs')?.addEventListener('click', e => {
        const btn = e.target.closest('.tab-btn');
        if (!btn) return;
        document.querySelectorAll('#groupedBarTabs .tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderGroupedBar(btn.dataset.group);
    });

    /* ═══════════════════════════════════════════════════════
       4. SUBMISSIONS LINE CHART — Kuesioner per Hari
    ═══════════════════════════════════════════════════════ */
    const submEl = document.getElementById('submissionsChart');
    if (submEl && dailySubmissions.labels) {
        new Chart(submEl, {
            type : 'line',
            data : {
                labels   : dailySubmissions.labels,
                datasets : [{
                    label           : 'Kuesioner Terisi',
                    data            : dailySubmissions.data,
                    borderColor     : C.teal,
                    backgroundColor : 'rgba(13,148,136,0.09)',
                    fill            : true,
                    tension         : 0.4,
                    pointRadius     : 5,
                    pointBackgroundColor : C.teal,
                    pointBorderColor     : C.white,
                    pointBorderWidth     : 2,
                    borderWidth     : 2.5,
                }],
            },
            options : {
                responsive  : true,
                interaction : { mode: 'index', intersect: false },
                plugins : {
                    legend  : { display: false },
                    tooltip : {
                        ...TOOLTIP,
                        callbacks : {
                            label : ctx => ` ${ctx.parsed.y} kuesioner`,
                        },
                    },
                },
                scales : {
                    x : AXIS(),
                    y : AXIS({
                        beginAtZero : true,
                        ticks       : {
                            ...AXIS().ticks,
                            callback : v => v + ' user',
                        },
                    }),
                },
            },
        });
    }

    /* ═══════════════════════════════════════════════════════
       5. HISTOGRAM — Distribusi Skor
       Warna bar sesuai threshold: teal(0-39), amber(40-69), red(70-100)
    ═══════════════════════════════════════════════════════ */
    const histEl = document.getElementById('histogramChart');
    if (histEl && scoreHistogram.labels) {
        // Tentukan warna tiap bin sesuai range
        const binColors = scoreHistogram.labels.map(label => {
            const start = parseInt(label.split('–')[0].replace('–', '').trim());
            if (start < 40)  return C.teal;
            if (start < 70)  return C.amber;
            return C.red;
        });

        new Chart(histEl, {
            type : 'bar',
            data : {
                labels   : scoreHistogram.labels,
                datasets : [{
                    label           : 'Jumlah User',
                    data            : scoreHistogram.data,
                    backgroundColor : binColors,
                    borderColor     : binColors.map(c => c + 'cc'),
                    borderWidth     : 1,
                    borderRadius    : 4,
                    borderSkipped   : false,
                    categoryPercentage : 0.9,
                    barPercentage      : 0.85,
                }],
            },
            options : {
                responsive  : true,
                interaction : { mode: 'index', intersect: false },
                plugins : {
                    legend  : { display: false },
                    tooltip : {
                        ...TOOLTIP,
                        callbacks : {
                            label : ctx => ` ${ctx.parsed.y} user`,
                        },
                    },
                },
                scales : {
                    x : AXIS(),
                    y : AXIS({
                        beginAtZero : true,
                        ticks       : {
                            ...AXIS().ticks,
                            callback : v => v + ' user',
                        },
                    }),
                },
            },
        });
    }

});