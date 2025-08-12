<?php

require_once __DIR__ . '/../../config/config.php';
?>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Send Newsletter</h2>

        <div class="mt-8 mb-10 p-6 bg-white rounded-lg shadow">
            <form id="send-newsletter-form" method="POST">
                <div class="mb-6">
                    <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Email Subject:</label>
                    <input type="text" id="subject" name="subject" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="newsletter-body" class="block text-gray-700 text-sm font-bold mb-2">Newsletter Body:</label>
                    <textarea id="newsletter-body" name="body" class="w-full"></textarea>
                </div>

                <div class="flex items-center">
                    <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Send to All Subscribers
                    </button>
                </div>
            </form>
            <div id="newsletter-status" class="mt-4"></div>
        </div>
    </div>
</div>



<script>
    const newsletterForm = document.getElementById('send-newsletter-form');
    const newsletterStatus = document.getElementById('newsletter-status');
    let editor;
    ClassicEditor
        .create(document.querySelector('#newsletter-body'), {
                       toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo', 'insertTable' ],
        })
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });

    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const subject = document.getElementById('subject').value;
        const body = editor.getData();
        if (!subject || !body) {
            newsletterStatus.innerHTML = `<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">⚠️ Please fill in both the subject and the body.</div>`;
            return;
        }

        const formData = new FormData();
        formData.append('subject', subject);
        formData.append('body', body);

        newsletterStatus.innerHTML = '<p class="text-blue-600">Sending emails, please wait...</p>';

        fetch('/admin/util/send_newsletter_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                newsletterStatus.innerHTML = `<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">✅ ${data.message}</div>`;
                newsletterForm.reset();
                editor.setData('');            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            newsletterStatus.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">❌ Error: ${error.message}</div>`;
        });
    });
</script>
    