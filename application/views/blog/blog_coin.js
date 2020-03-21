/*
*
* Functions related to modifying blogs
* and managing blog notes.
*
* */

function in_update_text(this_handler){

    var handler = '.text__'+$(this_handler).attr('cache_en_id')+'_'+$(this_handler).attr('in_ln__id');
    var new_value = $(this_handler).val().trim();

    //See if anything changes:
    if( $(this_handler).attr('old-value') == new_value ){
        //Nothing changed:
        return false;
    }

    //Grey background to indicate saving...
    $(handler).addClass('dynamic_saving');

    $.post("/blog/in_update_text", {

        in_ln__id: $(this_handler).attr('in_ln__id'),
        cache_en_id: $(this_handler).attr('cache_en_id'),
        field_value: new_value

    }, function (data) {

        if (!data.status) {

            //Reset to original value:
            $(handler).val(data.original_val);

            //Show error:
            alert('Note: ' + data.message);

        } else {
            //Update value:
            $(this_handler).attr('old-value', new_value)
        }

        setTimeout(function () {
            //Restore background:
            $(handler).removeClass('dynamic_saving');
        }, 233);

    });
}

var match_search_loaded = 0; //Keeps track of when we load the match search

function in_update_text_start(){
    $('.in_update_text').keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            in_update_text(this);
            e.preventDefault();
        }
    }).change(function() {
        in_update_text(this);
    });
}

$(document).ready(function () {

    //Lookout for textinput updates
    in_update_text_start();

    //Put focus on messages if no message:
    if(!$('#in_notes_list_4231 .notes_sortable').length){
        $('#ln_content4231').focus();
    }

    autosize($('.text__4736_'+in_loaded_id));

    $('#expand_blogs .expand_all').click(function (e) {
        $(".list-is-children .blogs_sortable").each(function () {
            ms_toggle($(this).attr('in-link-id'), 1);
        });
    });

    //Load top/bottom blog searches:
    in_load_search(".blogadder-level-2-parent",1, 'q', 'link_blog');
    in_load_search(".blogadder-level-2-child",0, 'w', 'link_blog');

    //Expand selections:
    prep_search_pad();

    //Load Sortable:
    in_sort_load(in_loaded_id);

    //Watch the expand/close all buttons:
    $('#expand_blogs .expand_all').click(function (e) {
        $(".list-is-children .blogs_sortable").each(function () {
            ms_toggle($(this).attr('in-link-id'), 1);
        });
    });
    $('#expand_blogs .close_all').click(function (e) {
        $(".list-is-children .blogs_sortable").each(function () {
            ms_toggle($(this).attr('in-link-id'), 0);
        });
    });


    //Loop through all new note inboxes:
    $(".new-note").each(function () {

        var focus_ln_type_play_id = parseInt($(this).attr('note-type-id'));

        //Initiate @ search for all note text areas:
        in_message_inline_en_search($(this));

        //Watch for focus:
        $(this).focus(function() {
            $( '#notes_control_'+focus_ln_type_play_id ).removeClass('hidden');
        }).keyup(function() {
            $( '#notes_control_'+focus_ln_type_play_id ).removeClass('hidden');
        });

        autosize($(this));

        //Activate sorting:
        in_notes_sort_load(focus_ln_type_play_id);

        var showFiles = function (files) {
            if(typeof files[0] !== 'undefined'){
                $('.box' + focus_ln_type_play_id).find('label').text(files.length > 1 ? ($('.box' + focus_ln_type_play_id).find('input[type="file"]').attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
            }
        };

        $('.box' + focus_ln_type_play_id).find('input[type="file"]').on('drop', function (e) {
            droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped
            showFiles(droppedFiles);
        });

        $('.box' + focus_ln_type_play_id).find('input[type="file"]').on('change', function (e) {
            showFiles(e.target.files);
        });

        //Watch for message creation:
        $('#ln_content' + focus_ln_type_play_id).keydown(function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
                in_note_add(focus_ln_type_play_id);
            }
        });

        //Watchout for file uplods:
        $('.box' + focus_ln_type_play_id).find('input[type="file"]').change(function () {
            in_note_create_upload(droppedFiles, 'file', focus_ln_type_play_id);
        });


        //Should we auto start?
        if (isAdvancedUpload) {

            $('.box' + focus_ln_type_play_id).addClass('has-advanced-upload');
            var droppedFiles = false;

            $('.box' + focus_ln_type_play_id).on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
            })
                .on('dragover dragenter', function () {
                    $('.add_note_' + focus_ln_type_play_id).addClass('is-working');
                })
                .on('dragleave dragend drop', function () {
                    $('.add_note_' + focus_ln_type_play_id).removeClass('is-working');
                })
                .on('drop', function (e) {
                    droppedFiles = e.originalEvent.dataTransfer.files;
                    e.preventDefault();
                    in_note_create_upload(droppedFiles, 'drop', focus_ln_type_play_id);
                });
        }

    });

});

function read_preview(){
    if(parseInt($('.dropi_4737_'+in_loaded_id+'_0.active').attr('new-en-id')) in js_en_all_7355){
        //Blog is public, go to preview:
        window.location = '/' + in_loaded_id;
    } else {
        //Inform them that they cannot read yet:
        alert('Publish blog before reading it.');
    }
}

function in_update_dropdown(element_id, new_en_id, in_id, ln_id, show_full_name){

    /*
    *
    * WARNING:
    *
    * element_id Must be listed as children of:
    *
    * MEMORY CACHE @4527
    * JS MEMORY CACHE @11054
    *
    *
    * */

    var current_selected = parseInt($('.dropi_'+element_id+'_'+in_id+'_'+ln_id+'.active').attr('new-en-id'));
    new_en_id = parseInt(new_en_id);
    if(current_selected == new_en_id){
        //Nothing changed:
        return false;
    }

    //Are we deleting a status?
    var is_blog_delete = (element_id==4737 && !(new_en_id in js_en_all_7356));
    if(is_blog_delete){
        //Seems to be deleting, confirm:
        var r = confirm("Archive this blog AND remove all its links to other blogs?");
        if (r == false) {
            return false;
        }
    }



    //Show Loading...
    var data_object = eval('js_en_all_'+element_id);
    $('.dropd_'+element_id+'_'+in_id+'_'+ln_id+' .btn').html('<span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span><b class="montserrat">'+ ( show_full_name ? 'SAVING...' : '' ) +'</b>');

    $.post("/blog/in_update_dropdown", {

        in_id: in_id,
        ln_id: ln_id,
        in_loaded_id:in_loaded_id,
        element_id: element_id,
        new_en_id: new_en_id

    }, function (data) {
        if (data.status) {

            //Update on page:
            $('.dropd_'+element_id+'_'+in_id+'_'+ln_id+' .btn').html('<span class="icon-block">'+data_object[new_en_id]['m_icon']+'</span>' + ( show_full_name ? data_object[new_en_id]['m_name'] : '' ));
            $('.dropd_'+element_id+'_'+in_id+'_'+ln_id+' .dropi_' + element_id +'_'+in_id+ '_' + ln_id).removeClass('active');
            $('.dropd_'+element_id+'_'+in_id+'_'+ln_id+' .optiond_' + new_en_id+'_'+in_id+ '_' + ln_id).addClass('active');

            if( data.deletion_redirect && data.deletion_redirect.length > 0 ){
                //Go to main blog page:
                window.location = data.deletion_redirect;
            } else if( data.remove_element && data.remove_element.length > 0 ){
                //Go to main blog page:
                setTimeout(function () {
                    //Restore background:
                    $( data.remove_element ).fadeOut();

                    setTimeout(function () {
                        //Restore background:
                        $( data.remove_element ).remove();
                    }, 55);

                }, 377);
            }

            if(element_id==4486){
                $('.in__tr_'+ln_id+' .link_marks').addClass('hidden');
                $('.in__tr_'+ln_id+' .settings_' + new_en_id).removeClass('hidden');
            }

        } else {

            //Reset to default:
            $('.dropd_'+element_id+'_'+in_id+'_'+ln_id+' .btn').html('<span class="icon-block">'+data_object[current_selected]['m_icon']+'</span>' + ( show_full_name ? data_object[current_selected]['m_name'] : '' ));

            //Show error:
            alert('Note: ' + data.message);

        }
    });
}



function in_unlink(in_id, ln_id){
    var r = confirm("Unlink ["+$('.in_ln__id_'+in_id).val()+"]?");
    if (r == true) {

        //Fetch Blog Data to load modify widget:
        $.post("/blog/in_unlink", {
            in_id: in_id,
            ln_id: ln_id,
        }, function (data) {
            if (data.status) {
                in_ui_remove(in_id,ln_id);
            }
        });
    }
}

function in_ui_remove(in_id,ln_id){

    //Fetch parent blog before removing element from DOM:
    var parent_in_id = parseInt($('.blog_line_' + in_id).attr('parent-blog-id'));

    //Remove from UI:
    $('.in__tr_' + ln_id).html('<span style="color:#000000;"><i class="fas fa-trash-alt"></i></span>');

    //Hide the editor & saving results:
    $('.in__tr_' + ln_id).fadeOut();

    //Disappear in a while:
    setTimeout(function () {

        //Hide the editor & saving results:
        $('.in__tr_' + ln_id).remove();

        //Hide editing box:
        $('#modifybox').addClass('hidden');

        //Re-sort sibling blogs:
        in_sort_save(parent_in_id);

    }, 610);

}






/*
*
* BLOG NOTES
*
* */

function in_note_insert_string(focus_ln_type_play_id, add_string) {
    $('#ln_content' + focus_ln_type_play_id).insertAtCaret(add_string);
    in_new_note_count(focus_ln_type_play_id);
}


//Count text area characters:
function in_new_note_count(focus_ln_type_play_id) {

    //Update count:
    var len = $('#ln_content' + focus_ln_type_play_id).val().length;
    if (len > js_en_all_6404[11073]['m_desc']) {
        $('#charNum' + focus_ln_type_play_id).addClass('overload').text(len);
    } else {
        $('#charNum' + focus_ln_type_play_id).removeClass('overload').text(len);
    }

    //Only show counter if getting close to limit:
    if(len > ( js_en_all_6404[11073]['m_desc'] * js_en_all_6404[12088]['m_desc'] )){
        $('#blogNoteNewCount' + focus_ln_type_play_id).removeClass('hidden');
    } else {
        $('#blogNoteNewCount' + focus_ln_type_play_id).addClass('hidden');
    }

}



//Count text area characters:
function in_title_count() {

    //Update count:
    var len = $('.text__4736_'+in_loaded_id).val().length;
    if (len > js_en_all_6404[11071]['m_desc']) {
        $('#charTitleNum').addClass('overload').text(len);
    } else {
        $('#charTitleNum').removeClass('overload').text(len);
    }

    //Only show counter if getting close to limit:
    if(len > ( js_en_all_6404[11071]['m_desc'] * js_en_all_6404[12088]['m_desc'] )){
        $('.title_counter').removeClass('hidden');
    } else {
        $('.title_counter').addClass('hidden');
    }
}

function in_edit_note_count(ln_id) {
    //See if this is a valid text message editing:
    if (!($('#charEditingNum' + ln_id).length)) {
        return false;
    }
    //Update count:
    var len = $('#message_body_' + ln_id).val().length;
    if (len > js_en_all_6404[11073]['m_desc']) {
        $('#charEditingNum' + ln_id).addClass('overload').text(len);
    } else {
        $('#charEditingNum' + ln_id).removeClass('overload').text(len);
    }

    //Only show counter if getting close to limit:
    if(len > ( js_en_all_6404[11073]['m_desc'] * js_en_all_6404[12088]['m_desc'] )){
        $('#blogNoteCount' + ln_id).removeClass('hidden');
    } else {
        $('#blogNoteCount' + ln_id).addClass('hidden');
    }
}


function in_message_inline_en_search(obj) {

    obj.textcomplete([
        {
            match: /(^|\s)@(\w*(?:\s*\w*))$/,
            search: function (query, callback) {
                algolia_index.search(query, {
                    hitsPerPage: 5,
                    filters: 'alg_obj_is_in=0',
                })
                    .then(function searchSuccess(content) {
                        if (content.query === query) {
                            callback(content.hits);
                        }
                    })
                    .catch(function searchFailure(err) {
                        console.error(err);
                    });
            },
            template: function (hit) {
                // Returns the highlighted version of the name attribute
                return '<span class="inline34">@' + hit.alg_obj_id + '</span> ' + hit._highlightResult.alg_obj_name.value;
            },
            replace: function (hit) {
                return ' @' + hit.alg_obj_id + ' ';
            }
        },
        {
            match: /(^|\s)#(\w*(?:\s*\w*))$/,
            search: function (query, callback) {
                algolia_index.search(query, {
                    hitsPerPage: 5,
                    filters: 'alg_obj_is_in=1',
                })
                    .then(function searchSuccess(content) {
                        if (content.query === query) {
                            callback(content.hits);
                        }
                    })
                    .catch(function searchFailure(err) {
                        console.error(err);
                    });
            },
            template: function (hit) {
                // Returns the highlighted version of the name attribute
                return '<span class="inline34">#' + hit.alg_obj_id + '</span> ' + hit._highlightResult.alg_obj_name.value;
            },
            replace: function (hit) {
                return ' #' + hit.alg_obj_id + ' ';
            }
        },
    ]);
}



function in_notes_sort_apply(focus_ln_type_play_id) {

    var new_ln_orders = [];
    var sort_rank = 0;
    var this_ln_id = 0;

    $(".msg_en_type_" + focus_ln_type_play_id).each(function () {
        this_ln_id = parseInt($(this).attr('tr-id'));
        if (this_ln_id > 0) {
            sort_rank++;
            new_ln_orders[sort_rank] = this_ln_id;
        }
    });

    //Update backend if any:
    if(sort_rank > 0){
        $.post("/blog/in_notes_sort", {new_ln_orders: new_ln_orders}, function (data) {
            //Only show message if there was an error:
            if (!data.status) {
                //Show error:
                alert('Note: ' + data.message);
            }
        });
    }
}

function in_notes_sort_load(focus_ln_type_play_id) {

    var inner_content = null;

    var sort_msg = Sortable.create( document.getElementById("in_notes_list_" + focus_ln_type_play_id) , {
        animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
        handle: ".blog_note_sorting", // Restricts sort start click/touch to the specified element
        draggable: ".notes_sortable", // Specifies which items inside the element should be sortable
        onUpdate: function (evt/**Event*/) {
            //Apply new sort:
            in_notes_sort_apply(focus_ln_type_play_id);
        },
        //The next two functions resolve a Bug with sorting iframes like YouTube embeds while also making the UI more informative
        onChoose: function (evt/**Event*/) {
            //See if this is a YouTube or Vimeo iFrame that needs to be temporarily removed:
            var ln_id = $(evt.item).attr('tr-id');
            if ($('#ul-nav-' + ln_id).find('.video-sorting').length !== 0) {
                inner_content = $('#msgbody_' + ln_id).html();
                $('#msgbody_' + ln_id).css('height', $('#msgbody_' + ln_id).height()).html('<i class="fas fa-sort"></i> Drag up/down to sort video');
            } else {
                inner_content = null;
            }
        },
        onEnd: function (evt/**Event*/) {
            if (inner_content) {
                var ln_id = $(evt.item).attr('tr-id');
                $('#msgbody_' + ln_id).html(inner_content);
            }
        }
    });

}

function in_note_modify_start(ln_id) {

    //Start editing:
    $("#ul-nav-" + ln_id).addClass('in-editing');
    $("#ul-nav-" + ln_id + " .edit-off").addClass('hidden');
    $("#ul-nav-" + ln_id + " .edit-on").removeClass('hidden');
    $("#ul-nav-" + ln_id + ">div").css('width', '100%');

    //Set focus to end of text:
    var textinput = $("#ul-nav-" + ln_id + " textarea");
    var data = textinput.val();
    textinput.focus().val('').val(data);
    autosize(textinput); //Adjust height

    //Initiate search:
    in_message_inline_en_search(textinput);

    //Try to initiate the editor, which only applies to text messages:
    in_edit_note_count(ln_id);

}

function in_note_modify_cancel(ln_id) {
    //Revert editing:
    $("#ul-nav-" + ln_id).removeClass('in-editing');
    $("#ul-nav-" + ln_id + " .edit-off").removeClass('hidden');
    $("#ul-nav-" + ln_id + " .edit-on").addClass('hidden');
    $("#ul-nav-" + ln_id + ">div").css('width', 'inherit');
}

function in_note_modify_save(ln_id, focus_ln_type_play_id) {

    //Show loader:
    $("#ul-nav-" + ln_id + " .edit-updates").html('<div><i class="far fa-yin-yang fa-spin"></i></div>');

    //Revert View:
    in_note_modify_cancel(ln_id);


    var modify_data = {
        ln_id: parseInt(ln_id),
        message_ln_status_play_id: parseInt($("#message_status_" + ln_id).val()),
        in_id: parseInt(in_loaded_id),
        ln_content: $("#ul-nav-" + ln_id + " textarea").val(),
    };

    //Update message:
    $.post("/blog/in_note_modify_save", modify_data, function (data) {

        if (data.status) {

            //Did we remove this message?
            if(data.remove_from_ui){

                //Yes, message was removed, adjust accordingly:
                $("#ul-nav-" + ln_id).html('<div>' + data.message + '</div>');

                //Disapper in a while:
                setTimeout(function ()
                {
                    $("#ul-nav-" + ln_id).fadeOut();

                    setTimeout(function () {

                        //Remove first:
                        $("#ul-nav-" + ln_id).remove();

                        //Adjust sort for this message type:
                        in_notes_sort_apply(focus_ln_type_play_id);

                    }, 610);
                }, 610);

            } else {

                //Nope, message was just edited...

                //Update text message:
                $("#ul-nav-" + ln_id + " .text_message").html(data.message);

                //Update message status:
                $("#ul-nav-" + ln_id + " .message_status").html(data.message_new_status_icon);

                //Show success here
                $("#ul-nav-" + ln_id + " .edit-updates").html('<b>' + data.success_icon + '</b>');

            }

        } else {
            //Oops, some sort of an error, lets
            $("#ul-nav-" + ln_id + " .edit-updates").html('<b style="color:#FF0000 !important; line-height: 110% !important;"><i class="fad fa-exclamation-triangle"></i> ' + data.message + '</b>');
        }

        //Tooltips:
        $('[data-toggle="tooltip"]').tooltip();

        //Disapper in a while:
        setTimeout(function () {
            $("#ul-nav-" + ln_id + " .edit-updates>b").fadeOut();
        }, 4181);
    });
}



function in_message_form_lock(focus_ln_type_play_id) {
    $('.save_note_' + focus_ln_type_play_id).html('<span class="icon-block-lg"><i class="far fa-yin-yang fa-spin"></i></span>').attr('href', '#');
    $('.add_note_' + focus_ln_type_play_id).addClass('is-working');
    $('#ln_content' + focus_ln_type_play_id).prop("disabled", true);
    $('.remove_loading').hide();
}


function in_message_form_unlock(result, focus_ln_type_play_id) {

    //Update UI to unlock:
    $('.save_note_' + focus_ln_type_play_id).html('SAVE').attr('href', 'javascript:in_note_add('+focus_ln_type_play_id+');');
    $('.add_note_' + focus_ln_type_play_id).removeClass('is-working');
    $("#ln_content" + focus_ln_type_play_id).prop("disabled", false).focus();
    $('.remove_loading').fadeIn();
    $( '#notes_control_'+focus_ln_type_play_id ).addClass('hidden');

    //What was the result?
    if (result.status) {

        //Append data:
        $(result.message).insertBefore( ".add_note_" + focus_ln_type_play_id );

        //Tooltips:
        $('[data-toggle="tooltip"]').tooltip();

        //Hide any errors:
        setTimeout(function () {
            $(".note_error_"+focus_ln_type_play_id).fadeOut();
        }, 4181);

    } else {

        $(".note_error_"+focus_ln_type_play_id).html('<span class="read">Note: '+result.message+'</span>');

    }
}

function in_note_create_upload(droppedFiles, uploadType, focus_ln_type_play_id) {

    //Prevent multiple concurrent uploads:
    if ($('.box' + focus_ln_type_play_id).hasClass('is-uploading')) {
        return false;
    }

    if (isAdvancedUpload) {

        //Lock message:
        in_message_form_lock(focus_ln_type_play_id);

        var ajaxData = new FormData($('.box' + focus_ln_type_play_id).get(0));
        if (droppedFiles) {
            $.each(droppedFiles, function (i, file) {
                var thename = $('.box' + focus_ln_type_play_id).find('input[type="file"]').attr('name');
                if (typeof thename == typeof undefined || thename == false) {
                    var thename = 'drop';
                }
                ajaxData.append(uploadType, file);
            });
        }

        ajaxData.append('upload_type', uploadType);
        ajaxData.append('in_id', in_loaded_id);
        ajaxData.append('focus_ln_type_play_id', focus_ln_type_play_id);

        $.ajax({
            url: '/blog/in_note_create_upload',
            type: $('.box' + focus_ln_type_play_id).attr('method'),
            data: ajaxData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            complete: function () {
                $('.box' + focus_ln_type_play_id).removeClass('is-uploading');
            },
            success: function (data) {

                in_message_form_unlock(data, focus_ln_type_play_id);

                //Adjust icon again:
                $('.file_label_' + focus_ln_type_play_id).html('<span class="icon-block"><i class="far fa-paperclip"></i></span>');

            },
            error: function (data) {
                var result = [];
                result.status = 0;
                result.message = data.responseText;
                in_message_form_unlock(result, focus_ln_type_play_id);
            }
        });
    } else {
        // ajax for legacy browsers
    }
}

function in_note_add(focus_ln_type_play_id) {

    //Lock message:
    in_message_form_lock(focus_ln_type_play_id);

    //Update backend:
    $.post("/blog/in_note_create_text", {

        in_id: in_loaded_id, //Synonymous
        ln_content: $('#ln_content' + focus_ln_type_play_id).val(),
        focus_ln_type_play_id: focus_ln_type_play_id,

    }, function (data) {

        //Raw Inputs Fields if success:
        if (data.status) {

            //Reset input field:
            $("#ln_content" + focus_ln_type_play_id).val("");
            in_new_note_count(focus_ln_type_play_id);

        }

        //Unlock field:
        in_message_form_unlock(data, focus_ln_type_play_id);

    });

}






























function prep_search_pad(){

    //All level 2s:
    $('.blogadder-level-2-parent').focus(function() {
        $('.in_pad_top' ).removeClass('hidden');
    }).focusout(function() {
        $('.in_pad_top' ).addClass('hidden');
    });

    $('.blogadder-level-2-child').focus(function() {
        $('.in_pad_bottom' ).removeClass('hidden');
    }).focusout(function() {
        $('.in_pad_bottom' ).addClass('hidden');
    });

}

function in_sort_save(in_id) {

    var new_ln_orders = [];
    var sort_rank = 0;

    $("#list-in-" + in_loaded_id + "-0 .blogs_sortable").each(function () {
        //Fetch variables for this blog:
        var in_id = parseInt($(this).attr('blog-id'));
        var ln_id = parseInt($(this).attr('in-link-id'));

        sort_rank++;

        //Store in DB:
        new_ln_orders[sort_rank] = ln_id;
    });

    //It might be zero for lists that have jsut been emptied
    if (sort_rank > 0 && in_id) {
        //Update backend:
        $.post("/blog/in_sort_save", {in_id: in_id, new_ln_orders: new_ln_orders}, function (data) {
            //Update UI to confirm with user:
            if (!data.status) {
                //There was some sort of an error returned!
                alert('Note: ' + data.message);
            }
        });
    }
}


function in_sort_load(in_id) {


    var element_key = null;
    var theobject = document.getElementById("list-in-" + in_loaded_id + "-0");
    if (!theobject) {
        //due to duplicate blogs belonging in this tree:
        return false;
    }

    var sort = Sortable.create(theobject, {
        animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
        draggable: ".blogs_sortable", // Specifies which items inside the element should be sortable
        handle: ".blog-sort-handle", // Restricts sort start click/touch to the specified element
        onUpdate: function (evt/**Event*/) {
            in_sort_save(in_id);
        }
    });
}



function in_link_or_create(in_linked_id, is_parent, in_link_child_id) {

    /*
     *
     * Either creates an BLOG link between in_linked_id & in_link_child_id
     * OR will create a new blog based on input text and then link it
     * to in_linked_id (In this case in_link_child_id=0)
     *
     * */


    var sort_handler = ".blogs_sortable";
    var sort_list_id = "list-in-" + in_loaded_id + '-' + is_parent;
    var input_field = $('#addblog-c-' + in_linked_id + '-' + is_parent);
    var blog_name = input_field.val();


    if( blog_name.charAt(0)=='#'){
        if(isNaN(blog_name.substr(1))){
            alert('Note: Use numbers only. Example: #1234');
            return false;
        } else {
            //Update the references:
            in_link_child_id = parseInt(blog_name.substr(1));
            blog_name = in_link_child_id; //As if we were just linking
        }
    }




    //We either need the blog name (to create a new blog) or the in_link_child_id>0 to create an BLOG link:
    if (!in_link_child_id && blog_name.length < 1) {
        alert('Note: Enter something');
        input_field.focus();
        return false;
    }

    //Set processing status:
    add_to_list(sort_list_id, sort_handler, '<div id="tempLoader" class="list-group-item itemblog montserrat"><span class="icon-block"><i class="fas fa-yin-yang fa-spin blog"></i></span>Adding... </div>');

    //Update backend:
    $.post("/blog/in_link_or_create", {
        in_linked_id: in_linked_id,
        is_parent:is_parent,
        in_title: blog_name,
        in_link_child_id: in_link_child_id
    }, function (data) {

        //Remove loader:
        $("#tempLoader").remove();

        if (data.status) {

            //Add new
            add_to_list(sort_list_id, sort_handler, data.in_child_html);

            //Reload sorting to enable sorting for the newly added blog:
            in_sort_load(in_linked_id);

            //Lookout for textinput updates
            in_update_text_start();

            //Expand selections:
            prep_search_pad();

            //Tooltips:
            $('[data-toggle="tooltip"]').tooltip();

        } else {
            //Show errors:
            alert('Note: ' + data.message);
        }

    });

    //Return false to prevent <form> submission:
    return false;

}