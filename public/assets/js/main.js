// KJM PO System - Main JS

document.addEventListener('DOMContentLoaded', function () {
  // Sidebar toggle
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const mobileBtn = document.getElementById('mobileMenuBtn');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => sidebar.classList.toggle('collapsed'));
  }
  if (mobileBtn && sidebar) {
    mobileBtn.addEventListener('click', () => sidebar.classList.toggle('mobile-open'));
    document.addEventListener('click', (e) => {
      if (!sidebar.contains(e.target) && !mobileBtn.contains(e.target)) {
        sidebar.classList.remove('mobile-open');
      }
    });
  }

  // Sidebar scroll position persistence
  const sidebarNav = sidebar ? sidebar.querySelector('.sidebar-nav') : null;
  if (sidebarNav) {
    // Restore saved scroll position
    const savedScroll = sessionStorage.getItem('sidebarScrollPos');
    if (savedScroll !== null) {
      sidebarNav.scrollTop = parseInt(savedScroll, 10);
    } else {
      // If no saved position, scroll active item into view
      const activeItem = sidebarNav.querySelector('.nav-item.active');
      if (activeItem) {
        activeItem.scrollIntoView({ block: 'center', behavior: 'instant' });
      }
    }

    // Save scroll position before navigating away
    window.addEventListener('beforeunload', function () {
      sessionStorage.setItem('sidebarScrollPos', sidebarNav.scrollTop);
    });
  }

  // Auto-dismiss alerts
  document.querySelectorAll('.alert').forEach(a => {
    setTimeout(() => a.style.opacity = '0', 4000);
  });
});

// Toast notification
function showToast(msg, type = 'success') {
  const icons = { success: 'fa-circle-check', danger: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info' };
  const colors = { success: '#10b981', danger: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<i class="fas ${icons[type]}" style="color:${colors[type]}"></i><span>${msg}</span>`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
}

// Modal helpers
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('show');
});

// Format currency
function formatRp(n) {
  if (!n) return 'Rp 0';
  return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function parseIDR(str) {
  if (!str) return 0;
  let clean = String(str).replace(/[^0-9]/g, '');
  return parseInt(clean, 10) || 0;
}

function calcRow(row) {
  const qtyVal = row.querySelector('.col-qty')?.value || '0';
  const qty = parseInt(qtyVal.replace(/[^0-9]/g, ''), 10) || 0;
  const harga = parseIDR(row.querySelector('.col-harga')?.value || '0');
  const disc = parseFloat(row.querySelector('.col-disc')?.value) || 0;
  
  const subtotal = qty * harga * (1 - disc / 100);
  const el = row.querySelector('.col-subtotal');
  if (el) el.textContent = formatRp(Math.round(subtotal));
  const inp = row.querySelector('.col-subtotal-val');
  if (inp) inp.value = Math.round(subtotal);
  calcSummary();
}

function calcSummary() {
  let sub = 0;
  document.querySelectorAll('.col-subtotal-val').forEach(el => {
    sub += parseInt(el.value, 10) || 0;
  });
  const discPct = parseFloat(document.getElementById('disc_persen')?.value) || 0;
  const ppnPct  = parseFloat(document.getElementById('ppn_persen')?.value)  || 0;
  
  const discNom = Math.round(sub * discPct / 100);
  const afterDisc = sub - discNom;
  const ppnNom = Math.round(afterDisc * ppnPct / 100);
  const total = afterDisc + ppnNom;
  
  setText('val_subtotal', formatRp(sub));
  setText('val_diskon', formatRp(discNom));
  setText('val_ppn', formatRp(ppnNom));
  setText('val_total', formatRp(total));
  setVal('subtotal_hidden', sub);
  setVal('diskon_nominal', discNom);
  setVal('ppn_nominal', ppnNom);
  setVal('total_hidden', total);
}

function setText(id, val) { const el = document.getElementById(id); if (el) el.textContent = val; }
function setVal(id, val) { const el = document.getElementById(id); if (el) el.value = val; }

// Item row event delegation
document.addEventListener('input', function (e) {
  const row = e.target.closest('.item-row');
  if (row && (e.target.classList.contains('col-qty') || e.target.classList.contains('col-harga') || e.target.classList.contains('col-disc'))) {
    calcRow(row);
  }
  if (e.target.id === 'disc_persen' || e.target.id === 'ppn_persen') calcSummary();
});

// Remove row
document.addEventListener('click', function (e) {
  if (e.target.closest('.btn-remove-row')) {
    const tbody = e.target.closest('tbody');
    const rows = tbody.querySelectorAll('.item-row');
    if (rows.length > 1) { e.target.closest('.item-row').remove(); calcSummary(); }
    else showToast('Minimal 1 item!', 'warning');
  }
});

// Print PDF
function printDoc() {
  window.print();
}

// Confirm delete
function confirmDelete(url, name) {
  if (confirm(`Yakin ingin menghapus "${name}"? Aksi ini tidak bisa dibatalkan.`)) {
    window.location.href = url;
  }
}

// Status color class
function statusClass(s) {
  const map = {
    draft:'badge-draft', terkirim:'badge-terkirim', disetujui:'badge-disetujui',
    ditolak:'badge-ditolak', expired:'badge-expired', diproses:'badge-diproses',
    selesai:'badge-selesai', dibatalkan:'badge-dibatalkan', dikirim:'badge-dikirim',
    diterima:'badge-diterima', dibayar:'badge-dibayar', jatuh_tempo:'badge-jatuh_tempo'
  };
  return 'badge ' + (map[s] || 'badge-draft');
}

// Global Print in Place via Iframe
function printInPlace(url) {
  let frame = document.getElementById('globalPrintFrame');
  if (!frame) {
    frame = document.createElement('iframe');
    frame.id = 'globalPrintFrame';
    frame.style.display = 'none';
    document.body.appendChild(frame);
  }
  showToast('Memuat dokumen untuk dicetak...', 'info');
  frame.src = url;
}

// Intercept all PDF links (href contains pdf.php) to print in place
document.addEventListener('click', function(e) {
  const printLink = e.target.closest('a[href*="pdf.php"]');
  if (printLink) {
    e.preventDefault();
    printInPlace(printLink.href);
  }
});

// Theme toggle logic (Dark and Light modes)
document.addEventListener('DOMContentLoaded', function () {
  const themeToggle = document.getElementById('themeToggle');
  const themeToggleIcon = document.getElementById('themeToggleIcon');

  function updateThemeUI(theme) {
    if (theme === 'light') {
      document.documentElement.classList.add('light-theme');
      if (themeToggleIcon) {
        themeToggleIcon.className = 'fas fa-moon';
      }
    } else {
      document.documentElement.classList.remove('light-theme');
      if (themeToggleIcon) {
        themeToggleIcon.className = 'fas fa-sun';
      }
    }
  }

  // Initialize UI based on current class list
  const currentTheme = document.documentElement.classList.contains('light-theme') ? 'light' : 'dark';
  updateThemeUI(currentTheme);

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const isLight = document.documentElement.classList.toggle('light-theme');
      const newTheme = isLight ? 'light' : 'dark';
      localStorage.setItem('theme', newTheme);
      updateThemeUI(newTheme);
      showToast(`Mode ${newTheme === 'light' ? 'Terang (Light)' : 'Gelap (Dark)'} diaktifkan`, 'info');
    });
  }
});
