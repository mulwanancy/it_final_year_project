// nurses.js
fetch('/nurses')
  .then(response => response.json())
  .then(data => {
    const tableBody = document.querySelector('#nurses-table tbody');
    data.forEach(nurse => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${nurse.id}</td>
        <td>${nurse.nurse_number}</td>
        <td>${nurse.first_name}</td>
        <td>${nurse.last_name}</td>
        <td>${nurse.contact}</td>
        <td>${nurse.email}</td>
        <td>${new Date(nurse.hire_date).toLocaleDateString()}</td>
        <td>${nurse.basic_salary}</td>
        <td>${nurse.status}</td>
        <td>${nurse.department}</td>
      `;
      tableBody.appendChild(row);
    });
  })
  .catch(err => console.error(err));
