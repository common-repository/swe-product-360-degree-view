 jQuery( function( $ ) {
    $(document).ready(function(){
        if ($('.swe360-image').length > 5){
            $('.swe360-delete-all').bind('click', deleteAll);
            
        }
        updateOptions();
        $('#swe360_columns').bind('input', columnsChanged);
        $('#swe360_multi_rows').bind('click', multiSpinCheck);
    });
    // Product gallery file uploads
    var swe360_gallery_frame;
    var $swe360_data = $( '#swe360_data' );
    var $swe360_images    = $( '#swe360_images_container' ).find( 'ul.swe360_images' );

    jQuery( '.add_swe360_images' ).on( 'click', 'a', function( event ) {
        var $el = $( this );

        swe360_dataObject = JSON.parse($swe360_data.val());

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( swe360_gallery_frame ) {
            swe360_gallery_frame.open();
            return;
        }

        // Create the media frame.
        swe360_gallery_frame = wp.media.frames.product_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data( 'choose' ),
            button: {
                text: $el.data( 'update' )
            },
            states: [
                new wp.media.controller.Library({
                    title: $el.data( 'choose' ),
                    filterable: 'all',
                    multiple: true
                })
            ]
        });

        // When an image is selected, run a callback.
        swe360_gallery_frame.on( 'select', function() {
            var selection = swe360_gallery_frame.state().get( 'selection' );
            var attachment_ids = swe360_dataObject.images_ids;

            //attachment_ids = attachment_ids.length == 0? [] : attachment_ids[0].split(',');

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids.push(attachment.id);
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $swe360_images.append( '<li class="swe360-image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                }
            });

            if ($('.swe360-image').length > 5 && $('.swe360-delete-all').length == 0){
                $('#swe360_images_container').append('<a class="button button-primary button-large swe360-delete-all">Delete all images</a>');
                $('.swe360-delete-all').bind('click', deleteAll);
            }
                disableChecbox();
                swe360_dataObject.images_ids = attachment_ids
                if(!swe360_dataObject.options.set_columns){
                    swe360_dataObject.options.columns = attachment_ids.length;
                    $('#swe360_columns').val(attachment_ids.length);
                }
                $swe360_data.val( JSON.stringify(swe360_dataObject) );
        });

        // Finally, open the modal.
        swe360_gallery_frame.open();
    });


    // delete an item
    $( '#swe360_images_container' ).on( 'click', 'a.delete', function() {
        $( this ).closest( 'li.swe360-image' ).remove();

        if ($('.swe360-image').length <= 5 && $('.swe360-image').length > 0){
            $('.swe360-delete-all').unbind('click');
            $('.swe360-delete-all').remove();
        }

        var attachment_ids = [];

        $( '#swe360_images_container' ).find( 'ul li.swe360-image' ).css( 'cursor', 'default' ).each( function() {
            attachment_ids.push(jQuery( this ).attr( 'data-attachment_id' ) );
        });

        disableChecbox();
        swe360_dataObject = JSON.parse($swe360_data.val());
        if( $('.swe360-image').length == 0 ){
            swe360_dataObject.options.set_columns = false;    
        }

        swe360_dataObject.images_ids = attachment_ids;
        if(!swe360_dataObject.options.set_columns){
            $('#swe360_columns').val(swe360_dataObject.images_ids.length);
            swe360_dataObject.options.columns = attachment_ids.length;   
        }
        $swe360_data.val( JSON.stringify(swe360_dataObject) );

        // remove any lingering tooltips
        $( '#tiptip_holder' ).removeAttr( 'style' );
        $( '#tiptip_arrow' ).removeAttr( 'style' );

        return false;
    });

    // Remove all images
    function deleteAll() {
        $('ul.swe360_images').empty();
        $('.swe360-delete-all').unbind('click');
        $('.swe360-delete-all').remove();
        var attachment_ids = [];

        swe360_dataObject = JSON.parse($swe360_data.val());
        swe360_dataObject.images_ids = attachment_ids;
        swe360_dataObject.options.columns = attachment_ids.length;
        swe360_dataObject.options.set_columns = false;
        $swe360_data.val( JSON.stringify(swe360_dataObject) );
        $('#swe360_multi_rows').attr('checked', false);
        $('#swe360_columns').attr("disabled", true);
        $('#swe360_columns').val('0');
        disableChecbox();

    }


    function multiSpinCheck(){
        swe360_dataObject = JSON.parse($swe360_data.val());

        if($('#swe360_multi_rows').is(":checked")){
            $('#swe360_columns').attr("disabled", false);
        }else{
            $('#swe360_columns').attr("disabled", true);
            $('#swe360_columns').val(swe360_dataObject.images_ids.length);
            swe360_dataObject.options.columns = $('#swe360_columns').val();
            swe360_dataObject.options.set_columns = false;
            $swe360_data.val( JSON.stringify(swe360_dataObject) );

        }

        swe360_dataObject.options.checked = $('#swe360_multi_rows').is(":checked");
        $swe360_data.val( JSON.stringify(swe360_dataObject) );
    }

    function updateOptions () {
        swe360_dataObject = JSON.parse($swe360_data.val());
        disableChecbox();
        $('#swe360_multi_rows').attr('checked', swe360_dataObject.options.checked);
        $('#swe360_columns').attr('disabled', !swe360_dataObject.options.checked);
        $('#swe360_columns').val(swe360_dataObject.options.columns);
    }


    function columnsChanged(){
        swe360_dataObject = JSON.parse($swe360_data.val());
        swe360_dataObject.options.set_columns = true;
        swe360_dataObject.options.columns = $('#swe360_columns').val();
        $swe360_data.val( JSON.stringify(swe360_dataObject) );
    }

    function disableChecbox(){
        if($('.swe360-image').length == 0){
            $('#swe360_multi_rows').attr('disabled', true);
            $('#swe360_multi_rows').attr('checked', false);
            $('#swe360_columns').attr('disabled', true);
            $('#swe360_columns').val('0');
        }else{
            $('#swe360_multi_rows').attr('disabled', false);
        }
    }

});