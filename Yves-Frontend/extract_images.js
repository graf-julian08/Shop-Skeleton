const fs = require('fs');
const path = require('path');

const svgFilePath = '/Users/juliangraf/Desktop/Yves-Frontend/Example/BY PRODUCTION - Homepage.svg';
const outputDir = '/Users/juliangraf/Desktop/Yves-Frontend/public/assets';

if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

fs.readFile(svgFilePath, 'utf8', (err, data) => {
    if (err) {
        console.error('Error reading SVG file:', err);
        return;
    }

    // RegEx to find image tags with base64 data
    // Looking for id="..." and xlink:href="data:image/png;base64,..."
    const imageRegex = /<image\s+id="([^"]+)"[^>]*xlink:href="data:image\/png;base64,([^"]+)"/g;

    let match;
    let count = 0;

    console.log('Scanning for images...');

    while ((match = imageRegex.exec(data)) !== null) {
        const imageId = match[1];
        const base64Data = match[2];
        const filename = `${imageId}.png`;
        const filePath = path.join(outputDir, filename);

        try {
            fs.writeFileSync(filePath, Buffer.from(base64Data, 'base64'));
            console.log(`Saved: ${filename}`);
            count++;
        } catch (writeErr) {
            console.error(`Error writing ${filename}:`, writeErr);
        }
    }

    console.log(`Extraction complete. ${count} images extracted.`);
});
