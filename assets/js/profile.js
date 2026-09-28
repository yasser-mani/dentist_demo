/**
 * Patient profile page – coordinator
 */

(async function() {
  
  const patientId = window.PATIENT_ID;
  let currentPatient = null;
  
  // Load patient info
  async function loadPatient() {
    try {
      const res = await App.fetch(`api/patients.php?id=${patientId}`);
      currentPatient = res.data;
      
      // Update header
      document.querySelector('.profile-title').textContent = currentPatient.full_name;
      
      // Render info
      renderInfo();
      
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }
  
  function renderInfo() {
    const card = document.getElementById('patientInfoCard');
    const bodyHTML = `
      <div class="info-grid">
        <div class="info-item">
          <label>Nom complet</label>
          <div class="value">${App.escapeHtml(currentPatient.full_name)}</div>
        </div>
        <div class="info-item">
          <label>Téléphone</label>
          <div class="value">${App.escapeHtml(currentPatient.phone)}</div>
        </div>
        <div class="info-item">
          <label>Assurance</label>
          <div class="value">${currentPatient.insurance ? App.escapeHtml(currentPatient.insurance) : '<span class="text-muted">Aucune</span>'}</div>
        </div>
        <div class="info-item">
          <label>Statut</label>
          <div class="value">${App.renderBadge(currentPatient.status_label, currentPatient.badge_color)}</div>
        </div>
        <div class="info-item">
          <label>Prochain RDV</label>
          <div class="value">${currentPatient.next_appointment ? App.formatDate(currentPatient.next_appointment, true) : '<span class="text-muted">—</span>'}</div>
        </div>
        <div class="info-item">
          <label>Patient depuis</label>
          <div class="value">${App.formatDate(currentPatient.created_at)}</div>
        </div>
      </div>
    `;
    
    card.querySelector('.card-body').innerHTML = bodyHTML;
  }
  
  // Edit info
  document.getElementById('btnEditInfo').addEventListener('click', () => {
    const bodyHTML = `
      <div class="form-group">
        <label>Nom complet *</label>
        <input type="text" id="editName" class="form-control" value="${App.escapeHtml(currentPatient.full_name)}">
      </div>
      <div class="form-group">
        <label>Téléphone *</label>
        <input type="tel" id="editPhone" class="form-control" value="${App.escapeHtml(currentPatient.phone)}">
      </div>
      <div class="form-group">
        <label>Assurance</label>
        <input type="text" id="editInsurance" class="form-control" value="${App.escapeHtml(currentPatient.insurance || '')}">
      </div>
      <div class="form-group">
        <label>Statut</label>
        <select id="editStatus" class="form-control">
          <option value="1" ${currentPatient.status_id == 1 ? 'selected' : ''}>Nouveau rendez-vous</option>
          <option value="2" ${currentPatient.status_id == 2 ? 'selected' : ''}>En traitement</option>
        </select>
      </div>
    `;
    
    const footerHTML = `
      <button class="btn btn-secondary" onclick="App.closeModal()">Annuler</button>
      <button class="btn btn-primary" id="btnSaveInfo">Enregistrer</button>
    `;
    
    App.openModal('Modifier les informations', bodyHTML, footerHTML);
    
    document.getElementById('btnSaveInfo').addEventListener('click', async () => {
      const name = document.getElementById('editName').value.trim();
      const phone = document.getElementById('editPhone').value.trim();
      const insurance = document.getElementById('editInsurance').value.trim();
      const statusId = document.getElementById('editStatus').value;
      
      if (!name || !phone) {
        App.toast('Veuillez remplir tous les champs requis', 'error');
        return;
      }
      
      try {
        await App.fetch('api/patients.php', {
          method: 'PUT',
          body: JSON.stringify({
            id: patientId,
            full_name: name,
            phone,
            insurance,
            status_id: statusId
          })
        });
        
        App.toast('Informations mises à jour', 'success');
        App.closeModal();
        loadPatient();
      } catch (error) {
        App.toast(error.message, 'error');
      }
    });
  });
  
  // Init
  await loadPatient();
  
  // Expose for other modules
  window.ProfileData = {
    patientId,
    currentPatient: () => currentPatient
  };
  
})();