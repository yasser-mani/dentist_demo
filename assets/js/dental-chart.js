/**
 * Dental Chart SVG Rendering & Interaction
 */

(function () {
  // Vector shapes for FDI dental groups
  const toothSVGPaths = {
    // Molar (3 roots / wide crown)
    molar: `
      <path class="tooth-body" d="M8,12 C8,6 13,3 20,3 C27,3 32,6 32,12 C34,18 33,26 31,34 C30,38 28,42 26,45 C24,47 23,40 22,34 C21,28 19,28 18,34 C17,40 16,47 14,45 C12,42 10,38 9,34 C7,26 6,18 8,12 Z" />
      <path class="tooth-detail" d="M14,11 C18,13 22,13 26,11 M20,13 L20,22" />
    `,
    // Premolar (2 roots / medium crown)
    premolar: `
      <path class="tooth-body" d="M10,12 C10,6 14,3 20,3 C26,3 30,6 30,12 C32,18 31,26 29,35 C28,40 26,46 23,45 C21,44 21,35 20,30 C19,35 19,44 17,45 C14,46 12,40 11,35 C9,26 8,18 10,12 Z" />
      <path class="tooth-detail" d="M15,11 C18,12 22,12 25,11 M20,12 L20,20" />
    `,
    // Canine (Single root / pointed crown)
    canine: `
      <path class="tooth-body" d="M12,14 C12,8 15,3 20,3 C25,3 28,8 28,14 C29,20 27,28 24,37 C22,43 21,47 20,47 C19,47 18,43 16,37 C13,28 11,20 12,14 Z" />
      <path class="tooth-detail" d="M16,13 C18,15 22,15 24,13" />
    `,
    // Incisor (Single thin root / flat crown)
    incisor: `
      <path class="tooth-body" d="M11,10 C11,5 14,3 20,3 C26,3 29,5 29,10 C29,16 27,26 24,36 C22,42 21,46 20,46 C19,46 18,42 16,36 C13,26 11,16 11,10 Z" />
      <path class="tooth-detail" d="M14,9 L26,9" />
    `
  };

  // Determine tooth group by FDI number
  function getToothType(number) {
    const lastDigit = number % 10;
    if (lastDigit === 1 || lastDigit === 2) return 'incisor';
    if (lastDigit === 3) return 'canine';
    if (lastDigit === 4 || lastDigit === 5) return 'premolar';
    return 'molar';
  }

  // FDI Quadrants
  const upperTeeth = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28];
  const lowerTeeth = [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38];

  let chartData = {};

  async function initChart() {
    const container = document.getElementById('dentalChart');
    if (!container) return;

    try {
      const res = await App.fetch(`api/dental_chart.php?patient_id=${window.PATIENT_ID}`);
      const list = res?.data || res || [];
      
      chartData = {};
      if (Array.isArray(list)) {
        list.forEach(item => {
          chartData[item.tooth_number] = item;
        });
      }

      renderChart(container);
    } catch (err) {
      console.error('Failed to load dental chart data:', err);
      renderChart(container);
    }
  }

  function renderChart(container) {
    let html = '<div class="dental-arch-container">';
    
    // Upper Arch
    html += '<div class="dental-arch upper-arch">';
    upperTeeth.forEach(num => { html += renderToothSVG(num, false); });
    html += '</div>';

    // Lower Arch
    html += '<div class="dental-arch lower-arch">';
    lowerTeeth.forEach(num => { html += renderToothSVG(num, true); });
    html += '</div>';

    html += '</div>';
    container.innerHTML = html;

    bindToothEvents();
  }

  function renderToothSVG(number, isFlipped) {
    const type = getToothType(number);
    const toothInfo = chartData[number] || {};
    const state = toothInfo.state || 'healthy';
    const flipClass = isFlipped ? 'flipped' : '';

    return `
      <div class="tooth-item state-${state}" data-tooth="${number}">
        <div class="tooth-number">${number}</div>
        <div class="tooth-svg-wrapper ${flipClass}">
          <svg viewBox="0 0 40 50" class="tooth-svg">
            ${toothSVGPaths[type]}
          </svg>
        </div>
      </div>
    `;
  }

  function bindToothEvents() {
    document.querySelectorAll('.tooth-item').forEach(el => {
      el.addEventListener('click', function () {
        const num = this.getAttribute('data-tooth');
        openToothPanel(num);
      });
    });
  }

  function openToothPanel(num) {
    const panel = document.getElementById('toothPanel');
    const title = document.getElementById('toothPanelTitle');
    if (!panel || !title) return;

    const data = chartData[num] || {};
    title.textContent = `Dent ${num}`;
    
    document.getElementById('toothState').value = data.state || 'healthy';
    document.getElementById('toothTreatment').value = data.treatment || '';
    document.getElementById('toothNotes').value = data.notes || '';
    document.getElementById('toothDate').value = data.date || new Date().toISOString().split('T')[0];

    panel.style.display = 'block';
    panel.setAttribute('data-active-tooth', num);
  }

  // Save tooth state handler
  document.getElementById('saveToothBtn')?.addEventListener('click', async function () {
    const panel = document.getElementById('toothPanel');
    const num = panel.getAttribute('data-active-tooth');
    if (!num) return;

    const payload = {
      patient_id: window.PATIENT_ID,
      tooth_number: parseInt(num, 10),
      state: document.getElementById('toothState').value,
      treatment: document.getElementById('toothTreatment').value,
      notes: document.getElementById('toothNotes').value,
      date: document.getElementById('toothDate').value
    };

    try {
      await App.fetch('api/dental_chart.php', {
        method: 'POST',
        body: JSON.stringify(payload)
      });
      
      chartData[num] = payload;
      initChart();
      panel.style.display = 'none';
      if (App.toast) App.toast('Carte dentaire mise à jour');
    } catch (err) {
      if (App.toast) App.toast(err.message, 'error');
    }
  });

  document.getElementById('closePanelBtn')?.addEventListener('click', () => {
    document.getElementById('toothPanel').style.display = 'none';
  });

  // Load chart on page load
  initChart();
})();