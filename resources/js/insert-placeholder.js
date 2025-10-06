document.addEventListener("DOMContentLoaded", () => {
    window.addEventListener("insert-placeholder", (event) => {
        let placeholder = event.detail;
        let editorEl = document.querySelector('[data-tiptap-root]'); // RichEditor root

        if (editorEl && editorEl.__tiptapEditor) {
            editorEl.__tiptapEditor.commands.insertContent(placeholder);
        }
    });
});
