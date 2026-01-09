// index.js
const express = require('express');
const path = require('path');
const app = express();
const db = require('./database'); // your database.js

app.use(express.json());

// Serve static files from 'public' folder
app.use(express.static(path.join(__dirname, 'public')));

// ------------------- TEST ROUTE -------------------
app.get('/', (req, res) => {
  res.send('Nurse Shift Payroll API is running 🚀');
});

// ------------------- GET ROUTES -------------------

// Get all nurses
app.get('/nurses', (req, res) => {
  db.query('SELECT * FROM nurses', (err, results) => {
    if (err) return res.status(500).json({ error: err });
    res.json(results);
  });
});

// Get all shifts
app.get('/shifts', (req, res) => {
  db.query('SELECT * FROM shifts', (err, results) => {
    if (err) return res.status(500).json({ error: err });
    res.json(results);
  });
});

// Get all attendance
app.get('/attendance', (req, res) => {
  db.query('SELECT * FROM attendance', (err, results) => {
    if (err) return res.status(500).json({ error: err });
    res.json(results);
  });
});

// Get all patients
app.get('/patients', (req, res) => {
  db.query('SELECT * FROM patients', (err, results) => {
    if (err) return res.status(500).json({ error: err });
    res.json(results);
  });
});

// Get all payroll
app.get('/payroll', (req, res) => {
  db.query('SELECT * FROM payroll', (err, results) => {
    if (err) return res.status(500).json({ error: err });
    res.json(results);
  });
});

// Get all handover reports
app.get('/handover_reports', (req, res) => {
  db.query('SELECT * FROM handover_reports', (err, results) => {
    if (err) return res.status(500).json({ error: err });
    res.json(results);
  });
});



// ------------------- START SERVER -------------------
const PORT = 3000;
app.listen(PORT, () => {
  console.log(`Server started on http://localhost:${PORT}`);
});
