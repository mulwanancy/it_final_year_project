fetch('/attendance')
  .then(res => res.json())
  .then(data => {
    const tbody = document.querySelector('#attendanceTable tbody');
    tbody.innerHTML = '';
    data.forEach(att => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${att.attendance_id}</td>
        <td>${att.nurse_id}</td>
        <td>${att.shift_id}</td>
        <td>${new Date(att.shift_date).toLocaleDateString()}</td>
        <td>${att.status}</td>
      `;
      tbody.appendChild(row);
    });
  })
  .catch(err => console.error('Error fetching attendance:', err));
