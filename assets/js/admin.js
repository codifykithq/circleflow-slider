(function($){'use strict';
$(function(){
    $('.ckcs-color-picker').wpColorPicker();

    $('.ckcs-tab').on('click',function(){
        const tab=$(this).data('tab');
        $('.ckcs-tab').removeClass('is-active');
        $(this).addClass('is-active');
        $('.ckcs-panel').removeClass('is-active');
        $('.ckcs-panel[data-panel="'+tab+'"]').addClass('is-active');
    });

    const $container=$('#ckcs-slides-container');
    let nextIndex=0;
    $container.find('.ckcs-slide-item').each(function(){
        nextIndex=Math.max(nextIndex,parseInt($(this).attr('data-index'),10)+1||0);
    });

    function reindex(){
        $container.find('.ckcs-slide-item').each(function(i){
            const $item=$(this);
            $item.attr('data-index',i);
            $item.find('.ckcs-slide-number').text(i+1);
            $item.find('[name^="slides["]').each(function(){
                this.name=this.name.replace(/^slides\[[^\]]+\]/,'slides['+i+']');
            });
        });
        const count=$container.find('.ckcs-slide-item').length;
        $('#ckcs-slide-count').text(count);
        $('#ckcs-empty-state').toggle(count===0);
        nextIndex=count;
    }

    function addSlide(){
        const tpl=$('#ckcs-slide-template').html().replaceAll('__INDEX__',String(nextIndex++));
        $container.append(tpl);
        $('#ckcs-empty-state').hide();
        reindex();
        const $new=$container.find('.ckcs-slide-item').last();
        $('html,body').animate({scrollTop:Math.max(0,$new.offset().top-90)},260);
        $new.addClass('is-new');
        setTimeout(function(){$new.removeClass('is-new')},900);
    }

    $('.ckcs-add-slide').on('click',addSlide);

    $container.sortable({
        items:'.ckcs-slide-item',
        handle:'.ckcs-drag',
        placeholder:'ckcs-slide-placeholder',
        tolerance:'pointer',
        forcePlaceholderSize:true,
        start:function(e,ui){ui.item.addClass('is-sorting')},
        stop:function(e,ui){ui.item.removeClass('is-sorting');reindex()}
    });

    $(document).on('click','.ckcs-duplicate-slide',function(){
        const $source=$(this).closest('.ckcs-slide-item');
        const $copy=$source.clone(false,false);
        $copy.find('.ckcs-slide-number').text('');
        $source.after($copy);
        reindex();
        $copy.addClass('is-new');
        setTimeout(function(){$copy.removeClass('is-new')},900);
    });

    $(document).on('click','.ckcs-remove-slide',function(){
        const $item=$(this).closest('.ckcs-slide-item');
        const label=$item.find('input[type="text"]').first().val();
        const question=label ? 'Remove “'+label+'” from this slider?' : 'Remove this slide?';
        if(window.confirm(question)){
            $item.slideUp(160,function(){$(this).remove();reindex()});
        }
    });

    $(document).on('click','.ckcs-upload-image',function(e){
        e.preventDefault();
        const $btn=$(this),$item=$btn.closest('.ckcs-slide-item'),target=$btn.data('target');
        const frame=wp.media({title:target==='background'?'Select background image':'Select center image',button:{text:'Use this image'},multiple:false});
        frame.on('select',function(){
            const a=frame.state().get('selection').first().toJSON();
            const $id=target==='background'?$item.find('.ckcs-bg-image-id'):$item.find('.ckcs-image-id');
            $id.val(a.id);
            $btn.find('.ckcs-media-preview').addClass('has-image').css('background-image','url("'+a.url+'")');
            $btn.find('.ckcs-media-text').text(target==='background'?'Replace background':'Replace image');
            if(target==='background'&&!$item.find('.ckcs-clear-bg').length){
                $btn.after('<button type="button" class="button-link ckcs-clear-bg">Clear separate background</button>');
            }
        });
        frame.open();
    });

    $(document).on('click','.ckcs-clear-bg',function(){
        $(this).siblings('.ckcs-bg-image-id').val('');
        const $btn=$(this).siblings('.ckcs-media-button');
        $btn.find('.ckcs-media-preview').removeClass('has-image').css('background-image','');
        $btn.find('.ckcs-media-text').text('Use center image');
        $(this).remove();
    });

    $(document).on('click','.ckcs-save-slider',function(){
        const $publish=$('#publish');
        if($publish.length){
            $publish.trigger('click');
        }else{
            $('#post').trigger('submit');
        }
    });

    $(document).on('click','.ckcs-copy-shortcode',function(){
        const $b=$(this),text=$b.data('copy');
        navigator.clipboard.writeText(text).then(function(){
            $b.addClass('is-copied').find('span').text('Copied');
            setTimeout(function(){$b.removeClass('is-copied').find('span').text('Copy')},1600);
        });
    });

    reindex();
});
})(jQuery);
