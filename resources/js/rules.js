/**
 * public/js/rules.js
 * Activa Admin — Rule Recommendation CRUD modals
 *
 * Requires window.ROUTES to be set before this script loads.
 * Add this snippet in rules.blade.php just before @push('scripts'):
 *
 *   <script>
 *     window.ROUTES = {
 *       store:  "{{ route('admin.rules.store') }}",
 *       update: "/admin/rules/:id",   // :id replaced at runtime
 *     };
 *   </script>
 *
 * Perubahan dari versi sebelumnya:
 *   - openEdit() sekarang dipanggil dari data-rule attribute di blade:
 *       onclick="openEdit(JSON.parse(this.dataset.rule))"
 *     sehingga tidak ada json_encode() langsung di onclick="" yang
 *     menyebabkan JS false-positive di VS Code.
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Helpers ──────────────────────────────────────────── */
    function openModal() { document.getElementById('modal').classList.add('show'); }

    window.closeModal = function () {
        document.getElementById('modal').classList.remove('show');
    };

    /* ── Open ADD form ────────────────────────────────────── */
    window.openAdd = function () {
        document.getElementById('m-title').textContent  = 'Tambah Rule Baru';
        document.getElementById('m-method').value       = 'POST';
        document.getElementById('rule-form').action     = window.ROUTES.store;

        document.getElementById('m-name').value = '';
        document.getElementById('m-val').value  = '';
        document.getElementById('m-then').value = '';
        document.getElementById('m-var').value  = 'sleep_hours';
        document.getElementById('m-op').value   = '<';
        document.getElementById('m-pri').value  = 'medium';

        openModal();
    };

    /* ── Open EDIT form ───────────────────────────────────── */
    window.openEdit = function (rule) {
        document.getElementById('m-title').textContent  = 'Edit Rule — ' + rule.name;
        document.getElementById('m-method').value       = 'PUT';
        document.getElementById('rule-form').action     = window.ROUTES.update.replace(':id', rule.id);

        document.getElementById('m-name').value = rule.name;
        document.getElementById('m-var').value  = rule.variable;
        document.getElementById('m-op').value   = rule.operator;
        document.getElementById('m-val').value  = rule.value;
        document.getElementById('m-then').value = rule.recommendation;
        document.getElementById('m-pri').value  = rule.priority;

        openModal();
    };

});