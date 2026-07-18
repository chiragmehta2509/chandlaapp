const fs = require('fs');
const path = require('path');

const filesToUpdate = [
    'resources/views/public/home.blade.php',
    'resources/views/partials/faq-section.blade.php',
    'resources/views/public/partials/jsonld.blade.php',
    'resources/views/client/packs/thanks.blade.php',
    'resources/views/client/chandlas/free-limit-pdf.blade.php',
    'resources/views/client/chandlas/pdf.blade.php',
    'resources/views/client/family-members/index.blade.php'
];

filesToUpdate.forEach(file => {
    const filePath = path.join(__dirname, file);
    if (!fs.existsSync(filePath)) {
        console.log(`Skipping ${file}, not found.`);
        return;
    }
    
    let content = fs.readFileSync(filePath, 'utf8');
    
    // config key replacements
    content = content.replace(/landing\.host_duo_pack_inr/g, 'packs.ledger_duo.amount_inr');
    content = content.replace(/landing\.premium_bundle_inr/g, 'packs.premium_bundle.amount_inr');
    content = content.replace(/landing\.guest_pay_single_inr/g, 'packs.guest_pay_single.amount_inr');
    content = content.replace(/landing\.celebration_pack_inr/g, 'packs.celebration.amount_inr');

    // Label replacements (case sensitive / exact matching to avoid breaking code)
    // Complete host
    content = content.replace(/Complete host pack/gi, 'Premium Host Plan');
    content = content.replace(/Complete host/gi, 'Premium Host Plan');
    
    // Host Duo
    content = content.replace(/Host Duo pack/gi, 'Host Plus Plan');
    content = content.replace(/Host Duo/gi, 'Host Plus Plan');
    
    // Guest pay
    content = content.replace(/Guest pay pack/gi, 'Guest Contribution');
    content = content.replace(/Guest pay/gi, 'Guest Contribution');
    
    // Celebration
    content = content.replace(/Celebration pack/gi, 'Celebration Plan');
    
    // Family pack
    content = content.replace(/Family pack/gi, 'Family Plan');
    
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated ${file}`);
});
