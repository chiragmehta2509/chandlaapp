const fs = require('fs');
const path = require('path');

const faviconCode = `    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">`;

function processDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDir(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            if (content.includes('<head>') && !content.includes('<link rel="icon"')) {
                // Ignore email templates
                if (fullPath.includes('emails') || fullPath.includes('vendor\\mail') || fullPath.includes('vendor/mail')) continue;
                
                content = content.replace(/<head>/, '<head>\n' + faviconCode);
                fs.writeFileSync(fullPath, content);
                console.log('Added favicon to: ' + fullPath);
            }
        }
    }
}

processDir(path.join(__dirname, 'resources', 'views'));
console.log('Done!');
