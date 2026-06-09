const { exec } = require('child_process');
const path = require('path');
const fs = require('fs');
const os = require('os');

const scriptPath = path.join(__dirname, 'agent.js');
const startupDir = path.join(os.homedir(), 'AppData', 'Roaming', 'Microsoft', 'Windows', 'Start Menu', 'Programs', 'Startup');
const batPath = path.join(startupDir, 'diegospizza-print-agent.bat');
const batContent = `@echo off\r\nstart /B node "${scriptPath}"\r\n`;

console.log('Instalando Diego\\'s Pizza Print Agent...\n');

try {
    fs.mkdirSync(startupDir, { recursive: true });
    fs.writeFileSync(batPath, batContent);
    console.log('✓ Agregado al inicio de Windows');
} catch (e) {
    console.log('! No se pudo agregar al inicio de Windows:', e.message);
}

console.log('\nPara iniciar el agente manualmente:');
console.log('  node ' + scriptPath);
console.log('\nO ejecuta: npm start');
console.log('\nHecho!');
