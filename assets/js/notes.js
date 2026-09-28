/**
 * Patient notes module
 */

(function() {
  
  const patientId = window.PATIENT_ID;
  let notes = [];
  
  async function loadNotes() {
    try {
      const res = await App.fetch(`api/notes.php?patient_id=${patientId}`);
      notes = res.data;
      renderNotes();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }
  
  function renderNotes() {
    const container = document.getElementById('notesList');
    
    if (notes.length === 0) {
      container.innerHTML = '<div class="note-empty">Aucune note.</div>';
      return;
    }
    
    container.innerHTML = notes.map(note => `
      <div class="note-item" data-id="${note.id}">
        <div class="note-header">
          <div class="note-date">${App.formatDate(note.created_at, true)}</div>
          <div class="note-actions">
            <button onclick="editNote(${note.id})" title="Modifier">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
            </button>
            <button onclick="deleteNote(${note.id})" title="Supprimer">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/></svg>
            </button>
          </div>
        </div>
        <div class="note-content">${App.escapeHtml(note.content)}</div>
      </div>
    `).join('');
  }
  
  // Add note
  document.getElementById('btnAddNote').addEventListener('click', () => {
    const bodyHTML = `
      <div class="form-group">
        <label>Contenu *</label>
        <textarea id="noteContent" class="form-control" rows="5" placeholder="Entrez votre note ici..." required></textarea>
      </div>
    `;
    
    const footerHTML = `
      <button class="btn btn-secondary" onclick="App.closeModal()">Annuler</button>
      <button class="btn btn-primary" id="btnSaveNote">Enregistrer</button>
    `;
    
    App.openModal('Ajouter une note', bodyHTML, footerHTML);
    
    document.getElementById('btnSaveNote').addEventListener('click', async () => {
      const content = document.getElementById('noteContent').value.trim();
      if (!content) {
        App.toast('Le contenu est requis', 'error');
        return;
      }
      
      try {
        await App.fetch('api/notes.php', {
          method: 'POST',
          body: JSON.stringify({ patient_id: patientId, content })
        });
        
        App.toast('Note ajoutée', 'success');
        App.closeModal();
        loadNotes();
      } catch (error) {
        App.toast(error.message, 'error');
      }
    });
  });
  
  // Edit note
  window.editNote = function(id) {
    const note = notes.find(n => n.id === id);
    if (!note) return;
    
    const bodyHTML = `
      <div class="form-group">
        <label>Contenu *</label>
        <textarea id="editNoteContent" class="form-control" rows="5" required>${App.escapeHtml(note.content)}</textarea>
      </div>
    `;
    
    const footerHTML = `
      <button class="btn btn-secondary" onclick="App.closeModal()">Annuler</button>
      <button class="btn btn-primary" id="btnUpdateNote">Enregistrer</button>
    `;
    
    App.openModal('Modifier la note', bodyHTML, footerHTML);
    
    document.getElementById('btnUpdateNote').addEventListener('click', async () => {
      const content = document.getElementById('editNoteContent').value.trim();
      if (!content) {
        App.toast('Le contenu est requis', 'error');
        return;
      }
      
      try {
        await App.fetch('api/notes.php', {
          method: 'PUT',
          body: JSON.stringify({ id, content })
        });
        
        App.toast('Note modifiée', 'success');
        App.closeModal();
        loadNotes();
      } catch (error) {
        App.toast(error.message, 'error');
      }
    });
  };
  
  // Delete note
  window.deleteNote = async function(id) {
    if (!confirm('Supprimer cette note ?')) return;
    
    try {
      await fetch('api/notes.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
      });
      
      const res = await fetch('api/notes.php', { method: 'DELETE', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `id=${id}` }).then(r => r.json());
      
      // Simpler way:
      await App.fetch('api/notes.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
      });
      
      App.toast('Note supprimée', 'success');
      loadNotes();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  };
  
  // Init
  loadNotes();
  
})();