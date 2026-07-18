const fs = require('fs');
const path = require('path');

const filePath = path.join('c:', 'Users', 'Chirag', 'Desktop', 'New folder', 'ChandlaBook', 'resources', 'views', 'public', 'home.blade.php');
let content = fs.readFileSync(filePath, 'utf8');

// The original file used text-white on emerald-600, indigo-600, violet-600, etc.
// My previous script blindly changed all text-white to text-slate-900 dark:text-white.
// We need to revert text-slate-900 dark:text-white back to text-white for buttons and banners that have dark backgrounds in both modes.

// Find bg-emerald-600
content = content.replace(/bg-emerald-600(.*?)text-slate-900 dark:text-white/g, 'bg-emerald-600$1text-white');
// Find bg-indigo-600
content = content.replace(/bg-indigo-600(.*?)text-slate-900 dark:text-white/g, 'bg-indigo-600$1text-white');
// Find from-indigo-600 (gradients)
content = content.replace(/from-indigo-600(.*?)text-slate-900 dark:text-white/g, 'from-indigo-600$1text-white');
content = content.replace(/from-indigo-500(.*?)text-slate-900 dark:text-white/g, 'from-indigo-500$1text-white');
content = content.replace(/from-emerald-500(.*?)text-slate-900 dark:text-white/g, 'from-emerald-500$1text-white');
content = content.replace(/from-violet-500(.*?)text-slate-900 dark:text-white/g, 'from-violet-500$1text-white');
content = content.replace(/from-amber-500(.*?)text-slate-900 dark:text-white/g, 'from-amber-500$1text-white');
content = content.replace(/from-rose-500(.*?)text-slate-900 dark:text-white/g, 'from-rose-500$1text-white');
content = content.replace(/from-fuchsia-500(.*?)text-slate-900 dark:text-white/g, 'from-fuchsia-500$1text-white');
content = content.replace(/from-slate-600(.*?)text-slate-900 dark:text-white/g, 'from-slate-600$1text-white');
content = content.replace(/bg-teal-600(.*?)text-slate-900 dark:text-white/g, 'bg-teal-600$1text-white');

fs.writeFileSync(filePath, content, 'utf8');
console.log('Restored text-white on dark backgrounds!');
