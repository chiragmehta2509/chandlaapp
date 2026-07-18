const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else {
            if (file.endsWith('.php')) {
                results.push(file);
            }
        }
    });
    return results;
}

const files = walk(path.join(__dirname, 'app'));

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let originalContent = content;

    // config key replacements
    content = content.replace(/landing\.host_duo_pack_inr/g, 'packs.ledger_duo.amount_inr');
    content = content.replace(/landing\.premium_bundle_inr/g, 'packs.premium_bundle.amount_inr');
    content = content.replace(/landing\.guest_pay_single_inr/g, 'packs.guest_pay_single.amount_inr');
    content = content.replace(/landing\.celebration_pack_inr/g, 'packs.celebration.amount_inr');

    // Label replacements
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

    // Also replace hardcoded static string labels in controllers mapping directly if they exist
    // "Complete host pack (₹700)" -> "Premium Host Plan (₹" . config('packs.premium_bundle.amount_inr') . ")" etc.
    content = content.replace(/'Premium Host Plan \(₹700\)'/g, "'Premium Host Plan (₹' . config('packs.premium_bundle.amount_inr', 700) . ')'");
    content = content.replace(/'Host Plus Plan \(₹500\)'/g, "'Host Plus Plan (₹' . config('packs.ledger_duo.amount_inr', 500) . ')'");
    content = content.replace(/'Family Plan \(₹600\)'/g, "'Family Plan (₹' . config('packs.family.amount_inr', 600) . ')'");
    content = content.replace(/'Guest Contribution single event \(₹400\)'/g, "'Guest Contribution credit (₹' . config('packs.guest_pay_single.amount_inr', 400) . ')'");
    content = content.replace(/'Celebration Plan \(₹300\)'/g, "'Celebration Plan (₹' . config('packs.celebration.amount_inr', 300) . ')'");

    if (content !== originalContent) {
        fs.writeFileSync(file, content, 'utf8');
        console.log(`Updated ${file}`);
    }
});
