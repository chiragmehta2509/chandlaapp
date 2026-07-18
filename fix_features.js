const fs = require('fs');

let content = fs.readFileSync('resources/views/public/home.blade.php', 'utf8');

// Section wrapper
content = content.replace(/<section id="features" class="relative overflow-hidden bg-slate-100 text-slate-900">/, '<section id="features" class="relative overflow-hidden bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-white">');

// Title
content = content.replace(/<h2 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">Built for real Indian events<\/h2>/, '<h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white tracking-tight">Built for real Indian events</h2>');

// Subtitle
content = content.replace(/<p class="text-slate-600 mt-3 text-base leading-relaxed">Ledger, guests, social-ready invites, and pre‑wedding graphics — in one calm dashboard.<\/p>/, '<p class="text-slate-600 dark:text-slate-300 mt-3 text-base leading-relaxed">Ledger, guests, social-ready invites, and pre‑wedding graphics — in one calm dashboard.</p>');

// Cards
content = content.replace(/<div class="group relative flex flex-col h-full rounded-2xl border border-slate-200\/90 bg-white\/95 /g, '<div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 ');

// Card Title
content = content.replace(/<h3 class="font-bold text-lg text-slate-900 mb-2 tracking-tight">/g, '<h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">');

// Card Paragraph
content = content.replace(/<p class="text-slate-600 text-sm leading-relaxed flex-1">/g, '<p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">');

fs.writeFileSync('resources/views/public/home.blade.php', content);
console.log('Fixed features section!');
