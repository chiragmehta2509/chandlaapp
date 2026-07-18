const fs = require('fs');
const path = require('path');

const homePath = path.join('c:', 'Users', 'Chirag', 'Desktop', 'New folder', 'ChandlaBook', 'resources', 'views', 'public', 'home.blade.php');
let homeContent = fs.readFileSync(homePath, 'utf8');

// Trusted By Marquee fixes
homeContent = homeContent.replace(/from-slate-900/g, 'from-white dark:from-slate-900');
homeContent = homeContent.replace(/background: rgba\(255,255,255,0\.05\);/g, '');
homeContent = homeContent.replace(/border: 1px solid rgba\(255,255,255,0\.10\);/g, '');
homeContent = homeContent.replace(/color: #e2e8f0;/g, '');
homeContent = homeContent.replace(/background: rgba\(255,255,255,0\.10\);/g, '');
homeContent = homeContent.replace(/class="cb-marquee-logo"/g, 'class="cb-marquee-logo bg-slate-100 border border-slate-200 hover:bg-slate-200 dark:bg-white/5 dark:border-white/10 dark:hover:bg-white/10"');
homeContent = homeContent.replace(/class="cb-marquee-logo-name"/g, 'class="cb-marquee-logo-name text-slate-600 dark:text-slate-200"');

// How it works fixes
homeContent = homeContent.replace(/<div class="p-6 rounded-2xl border bg-slate-50">/g, '<div class="p-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">');
homeContent = homeContent.replace(/class="text-sm text-slate-500"/g, 'class="text-sm text-slate-500 dark:text-slate-400"');
homeContent = homeContent.replace(/class="font-semibold mb-2"/g, 'class="font-semibold mb-2 text-slate-900 dark:text-white"');
homeContent = homeContent.replace(/class="text-slate-600"/g, 'class="text-slate-600 dark:text-slate-300"');
homeContent = homeContent.replace(/<h2 class="text-3xl font-bold mb-6">How it works<\/h2>/g, '<h2 class="text-3xl font-bold mb-6 text-slate-900 dark:text-white">How it works</h2>');

// Refer & earn fixes
homeContent = homeContent.replace(/id="refer" class="bg-white text-slate-900 scroll-mt-20"/g, 'id="refer" class="bg-white dark:bg-slate-950 text-slate-900 dark:text-white scroll-mt-20"');
homeContent = homeContent.replace(/class="text-3xl font-bold mb-2"/g, 'class="text-3xl font-bold mb-2 text-slate-900 dark:text-white"');
homeContent = homeContent.replace(/border-2 border-indigo-200 bg-indigo-50\/50/g, 'border-2 border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/20');
homeContent = homeContent.replace(/class="text-xl font-bold text-slate-900"/g, 'class="text-xl font-bold text-slate-900 dark:text-white"');
homeContent = homeContent.replace(/class="text-slate-700 leading-relaxed/g, 'class="text-slate-700 dark:text-slate-300 leading-relaxed');
homeContent = homeContent.replace(/text-slate-800 text-sm list-none/g, 'text-slate-800 dark:text-slate-300 text-sm list-none');
homeContent = homeContent.replace(/text-slate-500 mt-4/g, 'text-slate-500 dark:text-slate-400 mt-4');
homeContent = homeContent.replace(/bg-white\/60 p-5 border border-dashed border-indigo-300/g, 'bg-white/60 dark:bg-slate-900/60 p-5 border border-dashed border-indigo-300 dark:border-indigo-700');
homeContent = homeContent.replace(/text-indigo-900/g, 'text-indigo-900 dark:text-indigo-300');
homeContent = homeContent.replace(/text-indigo-600 font-bold uppercase/g, 'text-indigo-600 dark:text-indigo-400 font-bold uppercase');

fs.writeFileSync(homePath, homeContent, 'utf8');

const faqPath = path.join('c:', 'Users', 'Chirag', 'Desktop', 'New folder', 'ChandlaBook', 'resources', 'views', 'partials', 'faq-section.blade.php');
let faqContent = fs.readFileSync(faqPath, 'utf8');

// FAQ Fixes
faqContent = faqContent.replace(/class="bg-slate-50 text-slate-900 scroll-mt-20/g, 'class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white scroll-mt-20');
faqContent = faqContent.replace(/border-slate-200\/80/g, 'border-slate-200/80 dark:border-slate-700/80');
faqContent = faqContent.replace(/class="bg-white border border-slate-200 rounded-xl p-5"/g, 'class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5"');
faqContent = faqContent.replace(/text-slate-900/g, 'text-slate-900 dark:text-white');
faqContent = faqContent.replace(/text-slate-600/g, 'text-slate-600 dark:text-slate-300');
faqContent = faqContent.replace(/text-indigo-600/g, 'text-indigo-600 dark:text-indigo-400');

fs.writeFileSync(faqPath, faqContent, 'utf8');

console.log('Final sections refactored!');
