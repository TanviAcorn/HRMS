<div class="form-group">
    <label>Letter Content</label>
    <div id="editor" class="editor" style="min-height: 500px; border: 1px solid #ddd; padding: 10px;">
        <p>Dear [Employee Name],</p>
        <p>Content goes here...</p>
        <p>Yours sincerely,</p>
        <p>For {{ config('constants.COMPANY_NAME') }}</p>
    </div>
    <textarea name="html" id="letter-html" style="display:none;"></textarea>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.getElementById('editor');
        editor.contentEditable = 'true';
        
        // Save content to hidden textarea on form submit
        const form = document.querySelector('form');
        if (form) {
            form.onsubmit = function() {
                document.getElementById('letter-html').value = editor.innerHTML;
                return true;
            };
        }
    });
</script>
