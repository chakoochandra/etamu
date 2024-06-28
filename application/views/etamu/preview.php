<div class="p-2 col">
    <div class="card">
        <div class="card-body d-flex flex-column" id="container-preview">
        </div>
    </div>
    <script>
        $(document).ready(function($) {
            if (!$("#container-preview .container-camera").length) {
                $("#container-preview").append($(".container-camera"));
                $(".container-camera").show();

                //kembalikan hidden input ke dalam form
                $('.container-camera input[type="hidden"]').each(function() {
                    $(this).appendTo('form');
                });
            }
        });
    </script>