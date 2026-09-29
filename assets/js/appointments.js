/**
 * Appointments page logic
 */

(function() {

  let allPatients = [];

  function toLocalDatetimeValue(d) {
    const pad = n => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }

  async function loadUpcoming() {
    try {
      const res = await App.fetch('api/appointments.php?filter=upcoming');
      const appointments = res.data;

      const tbody = document.querySelector('#upcomingTable tbody');

      if (appointments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Aucun rendez-vous à venir.</td></tr>';
        return;
      }

      tbody.innerHTML = appointments.map(appt => `
        <tr>
          <td><a href="patient.php?id=${appt.patient_id}"><strong>${App.escapeHtml(appt.patient_name)}</strong></a></td>
          <td>${App.formatDate(appt.appointment_date, true)}</td>
          <td>${appt.reason ? App.escapeHtml(appt.reason) : '<span class="text-muted">—</span>'}</td>
          <td>${App.renderBadge(appt.status === 'scheduled' ? 'Prévu' : appt.status, 'blue')}</td>
          <td class="text-right">
            <button class="btn btn-sm btn-success" onclick="markCompleted(${appt.id})">Terminé</button>
            <button class="btn btn-sm btn-danger" onclick="markCancelled(${appt.id})">Annuler</button>
          </td>
        </tr>
      `).join('');
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }

  async function loadPast() {
    try {
      const res = await App.fetch('api/appointments.php?filter=past');
      const appointments = res.data;

      const tbody = document.querySelector('#pastTable tbody');

      if (appointments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Aucun rendez-vous passé.</td></tr>';
        return;
      }

      tbody.innerHTML = appointments.map(appt => {
        const badge = appt.status === 'completed' ? App.renderBadge('Terminé', 'green') :
                      appt.status === 'cancelled' ? App.renderBadge('Annulé', 'red') :
                      App.renderBadge(appt.status, 'gray');

        return `
          <tr>
            <td><a href="patient.php?id=${appt.patient_id}"><strong>${App.escapeHtml(appt.patient_name)}</strong></a></td>
            <td>${App.formatDate(appt.appointment_date, true)}</td>
            <td>${appt.reason ? App.escapeHtml(appt.reason) : '<span class="text-muted">—</span>'}</td>
            <td>${badge}</td>
          </tr>
        `;
      }).join('');
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }

  window.markCompleted = async function(id) {
    try {
      await App.fetch('api/appointments.php', {
        method: 'PATCH',
        body: JSON.stringify({ id, status: 'completed' })
      });
      App.toast('Rendez-vous marqué comme terminé', 'success');
      loadUpcoming();
      loadPast();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  };

  window.markCancelled = async function(id) {
    try {
      await App.fetch('api/appointments.php', {
        method: 'PATCH',
        body: JSON.stringify({ id, status: 'cancelled' })
      });
      App.toast('Rendez-vous annulé', 'success');
      loadUpcoming();
      loadPast();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  };

  // Add appointment
  document.getElementById('btnAddAppointment').addEventListener('click', async () => {
    // Load patients for dropdown
    try {
      const res = await App.fetch('api/patients.php?limit=200');
      allPatients = res.data;

      const bodyHTML = `
        <div class="form-group">
          <label>Patient *</label>
          <select id="apptPatient" class="form-control" required>
            <option value="">Sélectionnez un patient</option>
            ${allPatients.map(p => `<option value="${p.id}">${App.escapeHtml(p.full_name)}</option>`).join('')}
          </select>
        </div>
        <div class="form-group">
          <label>Date et heure *</label>
          <input type="datetime-local" id="apptDate" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Motif</label>
          <input type="text" id="apptReason" class="form-control" placeholder="Ex: Contrôle annuel">
        </div>
      `;

      const footerHTML = `
        <button class="btn btn-secondary" onclick="App.closeModal()">Annuler</button>
        <button class="btn btn-primary" id="btnSaveAppt">Enregistrer</button>
      `;

      App.openModal('Nouveau rendez-vous', bodyHTML, footerHTML);

      // Set default date to tomorrow 9am, in local time
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      tomorrow.setHours(9, 0, 0, 0);
      document.getElementById('apptDate').value = toLocalDatetimeValue(tomorrow);

      document.getElementById('btnSaveAppt').addEventListener('click', saveAppointment);

    } catch (error) {
      App.toast(error.message, 'error');
    }
  });

  async function saveAppointment() {
    const patientId = document.getElementById('apptPatient').value;
    const date = document.getElementById('apptDate').value;
    const reason = document.getElementById('apptReason').value.trim();

    if (!patientId || !date) {
      App.toast('Veuillez remplir tous les champs requis', 'error');
      return;
    }

    try {
      const res = await App.fetch('api/appointments.php', {
        method: 'POST',
        body: JSON.stringify({
          patient_id: patientId,
          appointment_date: date.replace('T', ' ') + ':00',
          reason
        })
      });

      App.toast(res.message, 'success');
      App.closeModal();
      loadUpcoming();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }

  // Init
  loadUpcoming();
  loadPast();

})();