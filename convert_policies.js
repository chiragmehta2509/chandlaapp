const fs = require('fs');

function parseMarkdownToBlade(inputFile, outputFile, title, description, slug) {
    const md = fs.readFileSync(inputFile, 'utf-8');
    const lines = md.split('\n');

    let html = `
@php
    $seoTitle = '${title} — Chandla Book';
    $seoDesc = '${description}';
    $seoCanonical = url('/${slug}');
    $seoRobots = 'index, follow';
@endphp
@extends('layouts.public-guest')

@section('content')
<div class="max-w-4xl mx-auto pt-10 pb-20">
    <div class="bg-slate-900/40 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl p-8 sm:p-14">
        <header class="border-b border-white/10 pb-10 mb-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight mb-4">${title}</h1>
`;

    let inList = false;
    let sectionOpen = false;
    let contentBody = `        <div class="space-y-12 text-base sm:text-lg text-white/80">\n`;

    for (let i = 0; i < lines.length; i++) {
        let line = lines[i].trim();
        if (line === '') continue;

        if (line.startsWith('# ')) {
            // Handled by header
            continue;
        }

        if (line.startsWith('Effective Date:') || line.startsWith('Last Updated:')) {
            html += `            <p class="text-sm font-semibold tracking-[0.2em] uppercase text-indigo-300/80">${line}</p>\n`;
            continue;
        }

        if (line.startsWith('## ')) {
            if (inList) {
                contentBody += `            </ul>\n`;
                inList = false;
            }
            if (sectionOpen) {
                contentBody += `        </section>\n`;
            }
            
            let headingText = line.replace('## ', '');
            let headingMatch = headingText.match(/^(\\d+)\\.\\s*(.*)/);
            let numHtml = '';
            if (headingMatch) {
                numHtml = `<span class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-500/30 border border-indigo-400/50 text-indigo-100 flex items-center justify-center text-sm font-bold shadow-sm">${headingMatch[1]}</span>`;
                headingText = headingMatch[2];
            } else {
                numHtml = `<span class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-500/30 border border-indigo-400/50 text-indigo-100 flex items-center justify-center text-sm font-bold shadow-sm"><i class="fas fa-file-contract"></i></span>`;
            }

            contentBody += `        <section>\n`;
            contentBody += `            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">${numHtml} <span>${headingText}</span></h2>\n`;
            sectionOpen = true;
            continue;
        }

        if (line.startsWith('### ')) {
            if (inList) {
                contentBody += `            </ul>\n`;
                inList = false;
            }
            contentBody += `            <h3 class="text-xl font-semibold text-indigo-200 mt-8 mb-4">${line.replace('### ', '')}</h3>\n`;
            continue;
        }

        if (line.startsWith('* ')) {
            if (!inList) {
                contentBody += `            <ul class="list-none space-y-3 mb-6 pl-4">\n`;
                inList = true;
            }
            contentBody += `                <li class="flex items-start gap-3"><i class="fas fa-check text-emerald-400 mt-1.5 text-sm"></i> <span class="leading-relaxed text-white/80">${line.replace('* ', '')}</span></li>\n`;
            continue;
        }

        // Normal paragraph
        if (inList) {
            contentBody += `            </ul>\n`;
            inList = false;
        }

        // Basic link parsing
        line = line.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-indigo-300 font-medium hover:text-indigo-200 hover:underline decoration-indigo-400/50 underline-offset-4 transition-all">$1</a>');

        contentBody += `            <p class="leading-relaxed text-white/80 mb-6">${line}</p>\n`;
    }

    if (inList) contentBody += `            </ul>\n`;
    if (sectionOpen) contentBody += `        </section>\n`;

    contentBody += `        </div>\n`;
    
    html += `        </header>\n${contentBody}    </div>\n</div>\n@endsection\n`;

    fs.writeFileSync(outputFile, html);
}

parseMarkdownToBlade(
    'c:/Users/Chirag/Desktop/New folder/ChandlaBook/PRIVACY POLICY (1).md',
    'c:/Users/Chirag/Desktop/New folder/ChandlaBook/resources/views/public/privacy.blade.php',
    'Privacy Policy',
    'Privacy policy for Chandla Book.',
    'privacy'
);

parseMarkdownToBlade(
    'c:/Users/Chirag/Desktop/New folder/ChandlaBook/TERMS & CONDITIONS (1).md',
    'c:/Users/Chirag/Desktop/New folder/ChandlaBook/resources/views/public/terms.blade.php',
    'Terms & Conditions',
    'Terms and Conditions for Chandla Book.',
    'terms'
);

parseMarkdownToBlade(
    'c:/Users/Chirag/Desktop/New folder/ChandlaBook/REFUND & CANCELLATION POLICY (1).md',
    'c:/Users/Chirag/Desktop/New folder/ChandlaBook/resources/views/public/refund.blade.php',
    'Refund & Cancellation Policy',
    'Refund & Cancellation policy for Chandla Book.',
    'refund'
);
