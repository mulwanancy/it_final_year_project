fetch('/shifts')
  .then(res => res.json())
  .then(data => {
    const tbody = document.querySelector('#shiftsTable tbody');
    tbody.innerHTML = '';
    data.forEach(shift => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${shift.shift_id}</td>
        <td>${shift.shift_name}</td>
        <td>${shift.start_time}</td>
        <td>${shift.end_time}</td>
      `;
      tbody.appendChild(row);
    });
  })
  .catch(err => console.error('Error fetching shifts:', err));
