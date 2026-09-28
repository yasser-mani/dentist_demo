/**
 * Patients page logic
 */

(function() {
  
  let allPatients = [];
  
  // Load patients
  async function loadPatients(search = '', statusId = '') {
    try {
      let url = 'api/patients.php?';
      if (search) url += `search=${encodeURIComponent(search)}&`;
      if (statusId) url += `status=${statusId}&`;
      
      const res = await App.fetch(url);
      allPatients = res.data;
      renderPatients();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }
  
  // Render table
  function renderPatients() {
    const tbody = document.querySelector('#patientsTable tbody');
    
    if (allPatients.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Aucun patient trouvé.</td></tr>';
      return;
    }
    
    tbody.innerHTML = allPatients.map(p => `
      <tr>
        <td><strong>${App.escapeHtml(p.full_name)}</strong></td>
        <td>${App.escapeHtml(p.phone)}</td>
        <td>${p.insurance ? App.escapeHtml(p.insurance) : '<span class="text-muted">—</span>'}</td>
        <td>${App.renderBadge(p.status_label, p.badge_color)}</td>
        <td>${p.next_appointment ? App.formatDate(p.next_appointment, true) : '<span class="text-muted">—</span>'}</td>
        <td class="text-right">
          <button class="btn btn-sm btn-secondary" onclick="editPatient(${p.id})">Modifier</button>
          <a href="patient.php?id=${p.id}" class="btn btn-sm btn-primary">Profil</a>
        </td>
      </tr>
    `).join('');
  }
  
  // Search
  const searchInput = document.getElementById('searchPatients');
  const filterStatus = document.getElementById('filterStatus');
  
  searchInput.addEventListener('input', App.debounce(() => {
    loadPatients(searchInput.value, filterStatus.value);
  }, 300));
  
  filterStatus.addEventListener('change', () => {
    loadPatients(searchInput.value, filterStatus.value);
  });
  
  // Add patient modal
  document.getElementById('btnAddPatient').addEventListener('click', () => {
    const bodyHTML = `
      <div class="form-group">
        <label>Nom complet *</label>
        <input type="text" id="patientName" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Téléphone *</label>
        <input type="tel" id="patientPhone" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Assurance</label>
        <input type="text" id="patientInsurance" class="form-control">
      </div>
      <div class="form-group">
        <label>Statut</label>
        <select id="patientStatus" class="form-control">
          <option value="1">Nouveau rendez-vous</option>
          <option value="2">En traitement</option>
        </select>
      </div>
    `;
    
    const footerHTML = `
      <button class="btn btn-secondary" onclick="App.closeModal()">Annuler</button>
      <button class="btn btn-primary" id="btnSavePatient">Enregistrer</button>
    `;
    
    App.openModal('Nouveau patient', bodyHTML, footerHTML);
    
    document.getElementById('btnSavePatient').addEventListener('click', savePatient);
  });
  
  // Save new patient
  async function savePatient() {
    const name = document.getElementById('patientName').value.trim();
    const phone = document.getElementById('patientPhone').value.trim();
    const insurance = document.getElementById('patientInsurance').value.trim();
    const statusId = document.getElementById('patientStatus').value;
    
    if (!name || !phone) {
      App.toast('Veuillez remplir tous les champs requis', 'error');
      return;
    }
    
    try {
      const res = await App.fetch('api/patients.php', {
        method: 'POST',
        body: JSON.stringify({ full_name: name, phone, insurance, status_id: statusId })
      });
      
      App.toast(res.message, 'success');
      App.closeModal();
      loadPatients(searchInput.value, filterStatus.value);
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }
  
  // Edit patient modal
  window.editPatient = async function(id) {
    try {
      const res = await App.fetch(`api/patients.php?id=${id}`);
      const p = res.data;
      
      const bodyHTML = `
        <div class="form-group">
          <label>Nom complet *</label>
          <input type="text" id="editName" class="form-control" value="${App.escapeHtml(p.full_name)}">
        </div>
        <div class="form-group">
          <label>Téléphone *</label>
          <input type="tel" id="editPhone" class="form-control" value="${App.escapeHtml(p.phone)}">
        </div>
        <div class="form-group">
          <label>Assurance</label>
          <input type="text" id="editInsurance" class="form-control" value="${App.escapeHtml(p.insurance || '')}">
        </div>
        <div class="form-group">
          <label>Statut</label>
          <select id="editStatus" class="form-control">
            <option value="1" ${p.status_id == 1 ? 'selected' : ''}>Nouveau rendez-vous</option>
            <option value="2" ${p.status_id == 2 ? 'selected' : ''}>En traitement</option>
          </select>
        </div>
      `;
      
      const footerHTML = `
        <button class="btn btn-secondary" onclick="App.closeModal()">Annuler</button>
        <button class="btn btn-primary" id="btnUpdatePatient">Enregistrer</button>
      `;
      
      App.openModal('Modifier le patient', bodyHTML, footerHTML);
      
      document.getElementById('btnUpdatePatient').addEventListener('click', async () => {
        const name = document.getElementById('editName').value.trim();
        const phone = document.getElementById('editPhone').value.trim();
        const insurance = document.getElementById('editInsurance').value.trim();
        const statusId = document.getElementById('editStatus').value;
        
        if (!name || !phone) {
          App.toast('Veuillez remplir tous les champs requis', 'error');
          return;
        }
        
        try {
          const upRes = await App.fetch('api/patients.php', {
            method: 'PUT',
            body: JSON.stringify({ id, full_name: name, phone, insurance, status_id: statusId })
          });
          
          App.toast(upRes.message, 'success');
          App.closeModal();
          loadPatients(searchInput.value, filterStatus.value);
        } catch (error) {
          App.toast(error.message, 'error');
        }
      });
      
    } catch (error) {
      App.toast(error.message, 'error');
    }
  };
  
  // Initial load
  loadPatients();
  
})();