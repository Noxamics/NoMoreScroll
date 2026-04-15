// ───────────────────────────────────────────────────
// public/js/rules.js
// Rule management modal handler
// ───────────────────────────────────────────────────

function openAdd() {
  // Reset form untuk tambah baru
  const form = document.getElementById('rule-form');
  form.reset();
  
  document.getElementById('m-title').textContent = 'Tambah Rule Baru';
  document.getElementById('m-method').value = 'POST';
  document.getElementById('m-id').value = '';
  document.getElementById('rule-form').action = ROUTES.store;
  
  showModal();
}

function openEdit(rule) {
  // Set form untuk edit
  document.getElementById('m-title').textContent = 'Edit Rule';
  document.getElementById('m-method').value = 'PATCH';
  document.getElementById('m-id').value = rule._id;
  
  document.getElementById('m-name').value = rule.name;
  document.getElementById('m-var').value = rule.variable;
  document.getElementById('m-op').value = rule.operator;
  document.getElementById('m-val').value = rule.value;
  document.getElementById('m-then').value = rule.recommendation;
  document.getElementById('m-pri').value = rule.priority;
  
  document.getElementById('rule-form').action = ROUTES.update.replace(':id', rule._id);
  
  showModal();
}

function showModal() {
  document.getElementById('modal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

// Tutup modal saat tekan Escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeModal();
  }
});
