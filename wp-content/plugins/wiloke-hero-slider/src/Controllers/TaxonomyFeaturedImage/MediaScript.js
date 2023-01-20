jQuery(document).ready(function ($) {
    let $body = $('body');
    function taxonomy_media_upload(button_class) {
        var custom_media = true,
            original_attachment = wp.media.editor.send.attachment;
        $body.on('click', button_class, function (e) {
            var button_id = '#' + $(this).attr('id');
            var send_attachment = wp.media.editor.send.attachment;
            var button = $(button_id);
            custom_media = true;
            wp.media.editor.send.attachment = function (props, attachment) {
                if (custom_media) {
                    $('#wiloke-image-id').val(attachment.id);
                    $('#wiloke-image-wrapper').html('<img class="custom_media_image" src=""' +
                        ' style="margin:0;padding:0;max-height:150px;float:none;" />');
                    $('#wiloke-image-wrapper .custom_media_image').attr('src', attachment.url).css('display', 'block');
                } else {
                    return original_attachment.apply(button_id, [props, attachment]);
                }
            }
            wp.media.editor.open(button);
            return false;
        });
    }

    taxonomy_media_upload('#wiloke-taxonomy-media-button');
    $body.on('click', '#wiloke-taxonomy-media-remove', function () {
        $('#wiloke-image-id').val('');
        $('#wiloke-image-wrapper').html('');
    });
});