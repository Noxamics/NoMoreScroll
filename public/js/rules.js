// ───────────────────────────────────────────────────
// public/js/rules.js
// Rule Management (Add / Edit Modal)
// ───────────────────────────────────────────────────

(function () {
    const modal = document.getElementById("modal");
    const form = document.getElementById("rule-form");

    // ================================
    // OPEN CREATE (Tambah Rule)
    // ================================
    window.openAdd = function () {
        if (!modal || !form) return;

        // Reset form
        form.reset();

        // Set mode CREATE
        document.getElementById("m-title").textContent = "Tambah Rule Baru";
        document.getElementById("m-method").value = "POST";
        document.getElementById("m-id").value = "";

        form.action = window.ROUTES.store;

        showModal();
    };

    // Alias (biar fleksibel kalau di Blade pakai nama lain)
    window.openCreateModal = function () {
        openAdd();
    };

    // ================================
    // OPEN EDIT
    // ================================
    window.openEdit = function (btn) {
        if (!modal || !form || !btn) return;

        const rule = {
            id: btn.dataset.id,
            name: btn.dataset.name,
            variable: btn.dataset.variable,
            operator: btn.dataset.operator,
            value: btn.dataset.value,
            recommendation: btn.dataset.recommendation,
            priority: btn.dataset.priority,
        };

        console.log("📝 Edit rule:", rule);

        // Set mode EDIT
        document.getElementById("m-title").textContent = "Edit Rule";
        document.getElementById("m-method").value = "PATCH";
        document.getElementById("m-id").value = rule.id;

        document.getElementById("m-name").value = rule.name || "";
        document.getElementById("m-var").value = rule.variable || "sleep_hours";
        document.getElementById("m-op").value = rule.operator || "<";
        document.getElementById("m-val").value = rule.value || "";
        document.getElementById("m-then").value = rule.recommendation || "";
        document.getElementById("m-pri").value = rule.priority || "high";

        form.action = window.ROUTES.update.replace(":id", rule.id);

        showModal();
    };

    // ================================
    // SHOW MODAL
    // ================================
    function showModal() {
        modal.classList.add("modal-show");
        document.body.style.overflow = "hidden";

        // Fokus ke input pertama
        setTimeout(() => {
            const input = document.getElementById("m-name");
            if (input) input.focus();
        }, 100);
    }

    // ================================
    // CLOSE MODAL
    // ================================
    window.closeModal = function () {
        modal.classList.remove("modal-show");
        document.body.style.overflow = "auto";
    };

    // ================================
    // CLOSE ON ESC
    // ================================
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    // ================================
    // CLOSE CLICK OUTSIDE
    // ================================
    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });
})();
