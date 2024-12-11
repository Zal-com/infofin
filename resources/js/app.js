import './bootstrap';

import.meta.glob([
    '../fonts/**',
]);

document.addEventListener('DOMContentLoaded', function () {
    const trixEditor = document.querySelector('limited-trix');
    const maxChars = 500;

    trixEditor.addEventListener('trix-change', function () {
        let content = trixEditor.innerText;
        const editor = trixEditor.editor;

        if (content.length > maxChars) {
            const truncatedContent = content.substring(0, maxChars);
            editor.setSelectedRange([0, content.length]);
            editor.insertString(truncatedContent);
        }
    });
});




