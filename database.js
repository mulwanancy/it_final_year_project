const mysql = require('mysql2');

// Create a connection to MySQL
const connection = mysql.createConnection({
  host: 'localhost',        // XAMPP MySQL runs on localhost
  user: 'root',             // default XAMPP MySQL user
  password: '',             // use your MySQL password if set
  database: 'nurseshift_db' // your database name
});

// Connect to MySQL
connection.connect((err) => {
  if (err) {
    console.error('Database connection failed: ' + err.stack);
    return;
  }
  console.log('Connected to nurseshift_db.');
});

// Export the connection to use in other files
module.exports = connection;
