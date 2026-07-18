const fs = require('fs');
const path = require('path');

const filePath = path.join('c:', 'Users', 'Chirag', 'Desktop', 'New folder', 'ChandlaBook', 'resources', 'views', 'public', 'home.blade.php');
let content = fs.readFileSync(filePath, 'utf8');

const replacements = [
    // Body and Backgrounds
    { regex: /(?<!dark:)\bbg-slate-950\b/g, replace: 'bg-slate-50 dark:bg-slate-950' },
    { regex: /(?<!dark:)\bbg-slate-900\b/g, replace: 'bg-white dark:bg-slate-900' },
    { regex: /(?<!dark:)\bbg-gradient-to-br from-indigo-950 via-purple-900 to-slate-950\b/g, replace: 'bg-gradient-to-br from-indigo-50 via-purple-50 to-slate-50 dark:from-indigo-950 dark:via-purple-900 dark:to-slate-950' },
    { regex: /(?<!dark:)\bbg-slate-950\/80\b/g, replace: 'bg-slate-50/90 dark:bg-slate-950/80' },
    { regex: /(?<!dark:)\bbg-slate-950\/50\b/g, replace: 'bg-slate-100/50 dark:bg-slate-950/50' },
    { regex: /(?<!dark:)\bbg-slate-900\/80\b/g, replace: 'bg-white/80 dark:bg-slate-900/80' },
    { regex: /(?<!dark:)\bbg-white\/5\b/g, replace: 'bg-slate-100 dark:bg-white/5' },
    { regex: /(?<!dark:)\bbg-white\/10\b/g, replace: 'bg-slate-200 dark:bg-white/10' },

    // Text
    { regex: /(?<!dark:)\btext-white\b/g, replace: 'text-slate-900 dark:text-white' },
    { regex: /(?<!dark:)\btext-white\/90\b/g, replace: 'text-slate-800 dark:text-white/90' },
    { regex: /(?<!dark:)\btext-white\/80\b/g, replace: 'text-slate-700 dark:text-white/80' },
    { regex: /(?<!dark:)\btext-white\/70\b/g, replace: 'text-slate-600 dark:text-white/70' },

    // Borders
    { regex: /(?<!dark:)\bborder-white\/5\b/g, replace: 'border-slate-200 dark:border-white/5' },
    { regex: /(?<!dark:)\bborder-white\/10\b/g, replace: 'border-slate-200 dark:border-white/10' },
    { regex: /(?<!dark:)\bborder-white\/20\b/g, replace: 'border-slate-300 dark:border-white/20' },
    { regex: /(?<!dark:)\bborder-white\/30\b/g, replace: 'border-slate-400 dark:border-white/30' },
];

replacements.forEach(({ regex, replace }) => {
    content = content.replace(regex, replace);
});

// Write it back
fs.writeFileSync(filePath, content, 'utf8');
console.log('Refactoring complete!');
