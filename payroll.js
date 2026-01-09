// public/js/payroll.js

// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.querySelector('#payrollTable tbody');

  fetch('/payroll')
    .then(response => response.json())
    .then(data => {
      data.forEach(payroll => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${payroll.payroll_id}</td>
          <td>${payroll.nurse_id}</td>
          <td>${new Date(payroll.period_start).toLocaleDateString()}</td>
          <td>${new Date(payroll.period_end).toLocaleDateString()}</td>
          <td>${payroll.basic_salary}</td>
          <td>${payroll.total_salary}</td>
        `;
        tableBody.appendChild(row);
      });
    })
    .catch(err => console.error('Error fetching payroll:', err));
});
