/**
 * Dashboard page logic
 */

(async function() {
  
  // Load stats
  async function loadStats() {
    try {
      const res = await App.fetch('api/stats.php');
      // Defensive payload extraction
      const stats = (res && res.data) ? res.data : (res || {});
      
      const statsRow = document.getElementById('statsRow');
      if (!statsRow) return;

      statsRow.innerHTML = `
        <div class="stat-card">
          <div class="stat-icon" style="background: #dbeafe;">
            <svg width="24" height="24" fill="#2563eb" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
          </div>
          <div class="stat-content">
            <div class="stat-label">Total patients</div>
            <div class="stat-value">${stats.total_patients ?? 0}</div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon" style="background: #dcfce7;">
            <svg width="24" height="24" fill="#16a34a" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/></svg>
          </div>
          <div class="stat-content">
            <div class="stat-label">Rendez-vous aujourd'hui</div>
            <div class="stat-value">${stats.today_appointments ?? 0}</div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon" style="background: #fef3c7;">
            <svg width="24" height="24" fill="#f59e0b" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
          </div>
          <div class="stat-content">
            <div class="stat-label">En traitement</div>
            <div class="stat-value">${stats.in_treatment ?? 0}</div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon" style="background: #e0e7ff;">
            <svg width="24" height="24" fill="#4f46e5" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/></svg>
          </div>
          <div class="stat-content">
            <div class="stat-label">Nouveaux patients (30j)</div>
            <div class="stat-value">${stats.new_patients ?? 0}</div>
          </div>
        </div>
      `;
    } catch (error) {
      if (window.App && App.toast) App.toast(error.message, 'error');
    }
  }
  
  // Load today's appointments
  async function loadTodayAppointments() {
    try {
      const res = await App.fetch('api/appointments.php?filter=today');
      
      // Safely check all possible array locations
      let appointments = [];
      if (res && Array.isArray(res.data)) {
        appointments = res.data;
      } else if (Array.isArray(res)) {
        appointments = res;
      }
      
      const container = document.getElementById('todayAppointments');
      if (!container) return;
      
      if (!appointments || appointments.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>Aucun rendez-vous prévu aujourd\'hui.</p></div>';
        return;
      }
      
      container.innerHTML = appointments.map(appt => `
        <div class="appointment-item">
          <div class="appointment-info">
            <div class="appointment-patient">${App.escapeHtml(appt.patient_name || '')}</div>
            <div class="appointment-time">${App.formatDate(appt.appointment_date, true)}</div>
            ${appt.reason ? `<div class="appointment-reason">${App.escapeHtml(appt.reason)}</div>` : ''}
          </div>
          <a href="patient.php?id=${appt.patient_id}" class="btn btn-sm btn-secondary">Voir profil</a>
        </div>
      `).join('');
      
    } catch (error) {
      if (window.App && App.toast) App.toast(error.message, 'error');
    }
  }
  
  // Load recent patients
  async function loadRecentPatients() {
    try {
      const res = await App.fetch('api/patients.php?limit=10');
      
      // Safely check all possible array locations
      let patients = [];
      if (res && Array.isArray(res.data)) {
        patients = res.data;
      } else if (Array.isArray(res)) {
        patients = res;
      }
      
      const tbody = document.querySelector('#recentPatientsTable tbody');
      if (!tbody) return;
      
      if (!patients || patients.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Aucun patient enregistré.</td></tr>';
        return;
      }
      
      tbody.innerHTML = patients.map(p => `
        <tr>
          <td><strong>${App.escapeHtml(p.full_name || '')}</strong></td>
          <td>${App.escapeHtml(p.phone || '')}</td>
          <td>${App.renderBadge(p.status_label || '', p.badge_color || '')}</td>
          <td>${App.formatDate(p.created_at)}</td>
          <td class="text-right">
            <a href="patient.php?id=${p.id}" class="btn btn-sm btn-primary">Ouvrir</a>
          </td>
        </tr>
      `).join('');
      
    } catch (error) {
      if (window.App && App.toast) App.toast(error.message, 'error');
    }
  }
  
  // Init
  await loadStats();
  await loadTodayAppointments();
  await loadRecentPatients();
  
})();