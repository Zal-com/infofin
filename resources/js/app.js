import './bootstrap';
import sound from '../../storage/app/public/sounds/boing.mp3'

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

document.addEventListener("DOMContentLoaded", function () {
    const path = document.getElementById("easter");

    const audio = new Audio(sound);

    path.addEventListener("click", () => {
        console.log(audio)
        audio.currentTime = 0; // remet à zéro si déjà en cours
        audio.play().catch(console.error);
    });
});




