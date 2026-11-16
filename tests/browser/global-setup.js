// Before each run: start a fresh contrast report (see expectAccessible in specs/helpers.js).
const fs = require('fs');
const path = require('path');

module.exports = () => {
  fs.rmSync(path.join(__dirname, 'reports', 'contrast.txt'), { force: true });
};
