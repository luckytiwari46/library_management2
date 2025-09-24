        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            $('.alert').delay(5000).fadeOut();
            
            // Form validation
            $('form').on('submit', function(e) {
                let isValid = true;
                const requiredFields = $(this).find('[required]');
                
                requiredFields.each(function() {
                    if ($(this).val().trim() === '') {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    $('<div class="alert alert-danger mt-3">Please fill in all required fields.</div>')
                        .insertAfter($(this))
                        .delay(5000).fadeOut();
                }
            });
            
            // Remove validation classes on input
            $('input, select, textarea').on('input change', function() {
                $(this).removeClass('is-invalid');
            });
        });
    </script>
</body>
</html>
