const fs = require('fs');
const path = require('path');

const files = [
    "AGENTS.md", "brain.md", "CLAUDE.md", "engine.md", "glossary.md", 
    "index.md", "keys.md", "log.md", "MEMORY.md", "PROJECTS.md", 
    "ROLE.md", "ULTRA-THINKING.md", "VISION.md", "WORKFLOW.md"
];

for (const file of files) {
    const fullPath = path.join('c:\\www\\coremusic.net\\.ai', file);
    if (fs.existsSync(fullPath)) {
        let content = fs.readFileSync(fullPath, 'utf8');
        let original = content;
        
        if (!content.includes('reference_doc: Freelancer Technical Documentation v1.0')) {
            content = content.replace('---\n', '---\nreference_doc: Freelancer Technical Documentation v1.0\n');
        }
        
        if (content !== original) {
            fs.writeFileSync(fullPath, content, 'utf8');
            console.log(`Added PDF reference to ${file}`);
        }
    }
}
