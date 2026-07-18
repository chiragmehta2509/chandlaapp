const fs = require('fs');
const path = require('path');

const filePath = path.join('c:', 'Users', 'Chirag', 'Desktop', 'New folder', 'ChandlaBook', 'resources', 'views', 'public', 'home.blade.php');
let content = fs.readFileSync(filePath, 'utf8');

// Fix the hover variants
content = content.replace(/hover:text-slate-900 dark:text-white/g, 'hover:text-slate-900 dark:hover:text-white');
content = content.replace(/hover:bg-white dark:bg-slate-900/g, 'hover:bg-white dark:hover:bg-slate-900');
content = content.replace(/hover:bg-slate-50 dark:bg-slate-950/g, 'hover:bg-slate-50 dark:hover:bg-slate-950');
content = content.replace(/hover:bg-slate-100 dark:bg-white\/5/g, 'hover:bg-slate-100 dark:hover:bg-white/5');
content = content.replace(/hover:bg-slate-200 dark:bg-white\/10/g, 'hover:bg-slate-200 dark:hover:bg-white/10');

// Write it back
fs.writeFileSync(filePath, content, 'utf8');
console.log('Hover variants fixed!');
