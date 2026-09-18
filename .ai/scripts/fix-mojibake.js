const fs = require('fs');
const path = require('path');

const files = [
    "AGENTS.md", "brain.md", "CLAUDE.md", "engine.md", "glossary.md", 
    "index.md", "keys.md", "log.md", "MEMORY.md", "PROJECTS.md", 
    "ROLE.md", "ULTRA-THINKING.md", "VISION.md", "WORKFLOW.md"
];

const replacements = {
    "Ã§": "ç",
    "Ã‡": "Ç",
    "ÄŸ": "ğ",
    "Ä°": "İ",
    "Ä±": "ı",
    "Ã¶": "ö",
    "Ã–": "Ö",
    "ÅŸ": "ş",
    "Åž": "Ş",
    "Ã¼": "ü",
    "Ãœ": "Ü",
    "Ã¢": "â"
};

for (const file of files) {
    const fullPath = path.join('c:\\www\\coremusic.net\\.ai', file);
    if (fs.existsSync(fullPath)) {
        let content = fs.readFileSync(fullPath, 'utf8');
        let original = content;
        
        for (const [key, value] of Object.entries(replacements)) {
            content = content.split(key).join(value);
        }
        
        if (content !== original) {
            fs.writeFileSync(fullPath, content, 'utf8');
            console.log(`Fixed encoding for ${file}`);
        }
    }
}
