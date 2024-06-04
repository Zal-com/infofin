import './bootstrap';
import Editor from '@toast-ui/editor'
//import 'codemirror/lib/codemirror.css';
import '@toast-ui/editor/dist/toastui-editor.css';


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const editor_long = new Editor({
    el: document.querySelector('#editor_long'),
    height: '400px',
    initialEditType: 'markdown',
    placeholder: 'Write something cool!',
})
const editor_short = new Editor({
    el: document.querySelector('#editor_short'),
    height: '400px',
    initialEditType: 'markdown',
    placeholder: 'Write something cool!',
})

