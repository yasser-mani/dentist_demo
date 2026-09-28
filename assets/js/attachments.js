/**
 * Attachments module
 */

(function() {
  
  const patientId = window.PATIENT_ID;
  let attachments = [];
  
  async function loadAttachments() {
    try {
      const res = await App.fetch(`api/attachments.php?patient_id=${patientId}`);
      attachments = res.data;
      renderAttachments();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  }
  
  function renderAttachments() {
    const container = document.getElementById('attachmentsList');
    
    if (attachments.length === 0) {
      container.innerHTML = '<div class="attachment-empty">Aucune pièce jointe.</div>';
      return;
    }
    
    container.innerHTML = attachments.map(att => {
      const isImage = ['jpg','jpeg','png'].includes(att.file_type);
      const iconHTML = isImage
        ? `<img src="api/attachments.php?action=view&id=${att.id}" alt="">`
        : `<svg width="24" height="24" fill="#6b7280" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>`;
      
      return `
        <div class="attachment-item">
          <div class="attachment-icon">${iconHTML}</div>
          <div class="attachment-info">
            <div class="attachment-name">${App.escapeHtml(att.original_name)}</div>
            <div class="attachment-meta">${att.file_type.toUpperCase()} • ${formatFileSize(att.file_size)}</div>
          </div>
          <div class="attachment-actions">
            ${isImage || att.file_type === 'pdf' ? `<button onclick="viewAttachment(${att.id})" title="Voir"><svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/></svg></button>` : ''}
            <button onclick="downloadAttachment(${att.id}, '${App.escapeHtml(att.original_name)}')" title="Télécharger"><svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/></svg></button>
            <button onclick="deleteAttachment(${att.id})" title="Supprimer"><svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/></svg></button>
          </div>
        </div>
      `;
    }).join('');
  }
  
  function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' o';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
    return (bytes / 1048576).toFixed(1) + ' Mo';
  }
  
  // Upload
  document.getElementById('uploadFile').addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('patient_id', patientId);
    
    try {
      const response = await fetch('api/attachments.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (!response.ok) {
        throw new Error(result.error || 'Erreur lors du téléchargement');
      }
      
      App.toast(result.message, 'success');
      loadAttachments();
      
      // Reset input
      e.target.value = '';
      
    } catch (error) {
      App.toast(error.message, 'error');
    }
  });
  
  // View
  window.viewAttachment = function(id) {
    window.open(`api/attachments.php?action=view&id=${id}`, '_blank');
  };
  
  // Download
  window.downloadAttachment = function(id, filename) {
    const a = document.createElement('a');
    a.href = `api/attachments.php?action=view&id=${id}&download=1`;
    a.download = filename;
    a.click();
  };
  
  // Delete
  window.deleteAttachment = async function(id) {
    if (!confirm('Supprimer cette pièce jointe ?')) return;
    
    try {
      await fetch('api/attachments.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
      }).then(r => r.json());
      
      App.toast('Pièce jointe supprimée', 'success');
      loadAttachments();
    } catch (error) {
      App.toast(error.message, 'error');
    }
  };
  
  // Init
  loadAttachments();
  
})();