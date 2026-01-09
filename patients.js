// public/js/patients.js

fetch('/patients')
  .then(response => response.json())
  .then(data => {
    const tableBody = document.getElementById('patients-table');
    data.forEach(patient => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${patient.patient_id}</td>
        <td>${patient.first_name}</td>
        <td>${patient.last_name}</td>
        <td>${patient.bed_number || '-'}</td>
        <td>${patient.condition || '-'}</td>
      `;
      tableBody.appendChild(row);
    });
  })
  .catch(error => {
    console.error('Error fetching patients:', error);
  });
