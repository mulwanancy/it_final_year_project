fetch('/handover_reports')
  .then(res => res.json())
  .then(data => {
    const tbody = document.querySelector('#handoverTable tbody');
    tbody.innerHTML = '';
    data.forEach(r => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${r.report_id}</td>
        <td>${r.shift_id}</td>
        <td>${r.nurse_id}</td>
        <td>${new Date(r.report_date).toLocaleString()}</td>
        <td>${r.notes}</td>
      `;
      tbody.appendChild(row);
    });
  })
  .catch(err => console.error('Error fetching handover reports:', err));
