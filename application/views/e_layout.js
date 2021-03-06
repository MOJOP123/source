


//Define file upload variables:
var upload_control = $(".inputfile");
var $input = $('.drag-box').find('input[type="file"]'),
    $label = $('.drag-box').find('label'),
    showFiles = function (files) {
        $label.text(files.length > 1 ? ($input.attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
    };

$(document).ready(function () {

    //Source Loader:
    var portfolio_count = parseInt($('#new_11029').attr('current-count'));
    if(portfolio_count>0 && portfolio_count<parseInt(js_e___6404[13005]['m__message'])){
        e_sort_portfolio_load();
    }

    set_autosize($('.texttype__lg.text__6197_'+e_focus_id));

    $("#input__6197, #e__title").keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            e.preventDefault();
        }
    }).click(function(event) {
        event.preventDefault();
    });



    //Update Profile counters to account for sources that member may not be able to see due to missing permissions...
    $('.en-type-counter-11030').text($('#list-in-11030 .en-item').not(".hidden").length);


    //Lookout for textinput updates
    x_set_start_text();





    $('#new_11030').focus(function() {
        $('#new_11030 .pad_expand').removeClass('hidden');
    }).focusout(function() {
        $('#new_11030 .pad_expand').addClass('hidden');
    });

    $('#new_11029').focus(function() {
        $('#new_11029 .pad_expand').removeClass('hidden');
    }).focusout(function() {
        $('#new_11029 .pad_expand').addClass('hidden');
    });



    //Load search for mass update function:
    load_editor();

    //Keep an eye for icon change:
    $('#e__icon').keyup(function() {
        update_demo_icon();
    });

    //Lookout for idea transaction related changes:
    $('#x__status').change(function () {
        if (parseInt($('#x__status').find(":selected").val()) == 6173 /* DELETED */ ) {
            //About to delete? Notify them:
            $('.notify_unx_e').removeClass('hidden');
        } else {
            $('.notify_unx_e').addClass('hidden');
        }
    });

    $('#e__type').change(function () {

        if (parseInt($('#e__type').find(":selected").val()) == 6178 /* Member Deleted */) {

            //Notify Member:
            $('.notify_e_delete').removeClass('hidden');
            $('.e_delete_stats').html('<span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span>');

            //About to delete... Fetch total transactions:
            $.post("/e/e_count_deletion", { e__id: parseInt($('#modal13571 .modal_e__id').val()) }, function (data) {

                if(data.status){
                    $('.e_delete_stats').html('<b>'+data.e_x_count+'</b>');
                    $('#e_x_count').val(data.e_x_count); //This would require a confirmation upon saving...
                }

            });

        } else {

            $('.notify_e_delete').addClass('hidden');
            $('.e_delete_stats').html('');
            $('#e_x_count').val(0);

        }
    });


    //SEARCH
    e_load_search(11030);
    e_load_search(11029);


    //UPLOAD
    $('.drag-box').find('input[type="file"]').change(function () {
        e_upload_file(droppedFiles, 'file');
    });

    //Should we auto start?
    if (isAdvancedUpload) {

        $('.drag-box').addClass('has-advanced-upload');
        var droppedFiles = false;

        $('.drag-box').on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
        })
            .on('dragover dragenter', function () {
                $('.e_has_link').addClass('dynamic_saving');
            })
            .on('dragleave dragend drop', function () {
                $('.e_has_link').removeClass('dynamic_saving');
            })
            .on('drop', function (e) {
                droppedFiles = e.originalEvent.dataTransfer.files;
                e.preventDefault();
                e_upload_file(droppedFiles, 'drop');
            });

    }

    x_type_preview_load();

});



function x_reset_all(){
    //Confirm First:
    var r = confirm("DANGER WARNING!!! You are about to delete your ENTIRE read history. This action cannot be undone and you will lose all your read coins.");
    if (r == true) {
        $('.x_reset_all').html('<span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span><b class="css__title">REMOVING ALL...</b>');

        //Redirect:
        window.location = '/x/x_clear_coins';
    } else {
        return false;
    }
}


function e_load_search(x__type) {

    //Search Enabled?
    if(!parseInt(js_e___6404[12678]['m__message'])){
        return false;
    }

    //Valid Source Creation Type?
    if(!js_n___14687.includes(x__type)){
        alert('Invalid Source Creation Type: ' + x__type);
        return false;
    }


    var element_focus = '#new_'+x__type;

    //Load Search:
    $(element_focus + ' .add-input').focus(function() {

        $(element_focus + ' .algolia_pad_search' ).removeClass('hidden');

    }).focusout(function() {

        $(element_focus + ' .algolia_pad_search' ).addClass('hidden');

    }).keypress(function (e) {

        var code = (e.keyCode ? e.keyCode : e.which);
        if ((code == 13) || (e.ctrlKey && code == 13)) {
            if(superpower_js_13422){
                e__add(x__type, 0);
            }
            return true;
        }

    }).on('autocomplete:selected', function (event, suggestion, dataset) {

        e__add(x__type, suggestion.s__id);

    }).autocomplete({hint: false, minLength: 1, keyboardShortcuts: [js_e___14687[x__type]['m__message']]}, [{
        source: function (q, cb) {
            algolia_index.search(q, {
                filters: 's__type=12274' + ( superpower_js_13422 ? '' : ' AND ( _tags:alg_e_13897 ) ' ), /* Nonfiction Content */
                hitsPerPage: ( validURL(q) ? 1 : 21 ),
            }, function (error, content) {
                if (error) {
                    cb([]);
                    return;
                }
                cb(content.hits, content);
            });
        },
        templates: {
            suggestion: function (suggestion) {
                return view_s_js(suggestion);
            },
            header: function (data) {
                if (!data.isEmpty) {
                    if(superpower_js_13422){
                        return '<a href="javascript:void(0);" onclick="e__add('+x__type+',0)" class="suggestion">' + '<span class="icon-block"><i class="fas fa-plus-circle add-plus source"></i></span>' + '<b class="source css__title">Create "' + data.query.toUpperCase() + '"</b>' + '</a>';
                    } else {
                        return '';
                    }
                }
            },
            empty: function (data) {
                return '<a href="javascript:void(0);" onclick="e__add('+x__type+',0)" class="suggestion css__title"><span class="icon-block"><i class="fas fa-plus-circle add-plus source"></i></span><b class="source">' + data.query.toUpperCase() + '</b></a>';
            },
        }
    }]);
}






//Adds OR transactions sources to sources
function e__add(x__type, e_existing_id) {

    //if e_existing_id>0 it means we're adding an existing source, in which case e_new_string should be null
    //If e_existing_id=0 it means we are creating a new source and then adding it, in which case e_new_string is required

    var input = $('#new_'+x__type+' .add-input');
    var list_id = 'list-in-'+x__type;

    var e_new_string = null;
    if (e_existing_id == 0) {
        e_new_string = input.val();
        if (e_new_string.length < 1) {
            alert('Missing source name or URL, try again');
            input.focus();
            return false;
        }
    }

    //Add via Ajax:
    $.post("/e/e__add", {

        x__type: x__type,
        e__id: e_focus_id,
        e_existing_id: e_existing_id,
        e_new_string: e_new_string,

    }, function (data) {

        if (data.status) {

            //Raw input to make it ready for next URL:
            input.focus();

            //Add new object to list:
            add_to_list(list_id, '.en-item', data.e_new_echo, js_n___14686.includes(x__type));

            //Allow inline editing if enabled:
            x_set_start_text();

            e_sort_portfolio_load();

        } else {
            //We had an error:
            alert(data.message);
        }

    });
}


function e_filter_status(x__type, new_val) {
    //Delete active class:
    $('.e_filter_status_'+x__type).removeClass('active');
    //We do have a filter:
    e_focus_filter = parseInt(new_val);
    $('.en_status_'+x__type+'_' + new_val).addClass('active');
    e_load_page(x__type,0, 1);
}





function e_load_page(x__type, page, load_new_filter) {

    if (load_new_filter) {
        //Replace load more with spinner:
        var append_div = $('#new_'+x__type).html();
        //The padding-bottom would delete the scrolling effect on the left side!
        $('#list-in-'+x__type).html('<span class="load-more" style="padding-bottom:500px;"><span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span></span>').hide().fadeIn();
    } else {
        //Replace load more with spinner:
        $('.load-more').html('<span class="load-more"><span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span></span>').hide().fadeIn();
    }

    $.post("/e/e_load_page", {
        x__type: x__type,
        page: page,
        parent_e__id: e_focus_id,
        e_focus_filter: e_focus_filter,
    }, function (data) {

        //Appending to existing content:
        $('.load-more').remove();

        if (load_new_filter) {
            $('#list-in-'+x__type).html(data + '<div id="new_'+x__type+'" class="list-group-item no-side-padding itemsource grey-input">' + append_div + '</div>').hide().fadeIn();
            //Reset search engine:
            e_load_search(x__type);

        } else {
            //Update UI to confirm with member:
            $(data).insertBefore('#new_'+x__type);
        }

        lazy_load();

        x_set_start_text();

        //Tooltips:
        $('[data-toggle="tooltip"]').tooltip();
    });

}




function e_x_form_lock(){
    $('#x__message').prop("disabled", true);

    $('.btn-save').addClass('grey').attr('href', '#').html('<span class="icon-block">i class="far fa-yin-yang fa-spin"></i></span>Uploading');

}

function e_x_form_unlock(result){

    //What was the result?
    if (!result.status) {
        alert(result.message);
    }

    //Unlock either way:
    $('#x__message').prop("disabled", false);

    $('.btn-save').removeClass('grey').attr('href', 'javascript:e_modify_save();').html('Save');

    //Tooltips:
    $('[data-toggle="tooltip"]').tooltip();

    //Replace the upload form to reset:
    upload_control.replaceWith( upload_control = upload_control.clone( true ) );
}


function e_upload_file(droppedFiles, uploadType) {

    //Prevent multiple concurrent uploads:
    if ($('.drag-box').hasClass('dynamic_saving')) {
        return false;
    }

    var current_value = $('#x__message').val();
    if(current_value.length > 0){
        //There is something in the input field, notify the member:
        var r = confirm("Current transaction content [" + current_value + "] will be deleted. Continue?");
        if (r == false) {
            return false;
        }
    }


    if (isAdvancedUpload) {

        //Lock message:
        e_x_form_lock();

        var ajaxData = new FormData($('.drag-box').get(0));
        if (droppedFiles) {
            $.each(droppedFiles, function (i, file) {
                var thename = $input.attr('name');
                if (typeof thename == typeof undefined || thename == false) {
                    var thename = 'drop';
                }
                ajaxData.append(uploadType, file);
            });
        }

        ajaxData.append('upload_type', uploadType);

        $.ajax({
            url: '/e/e_upload_file',
            type: 'post',
            data: ajaxData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            complete: function () {
                $('.drag-box').removeClass('dynamic_saving');
            },
            success: function (data) {

                if(data.status){

                    //Add URL to input:
                    $('#x__message').val( data.cdn_url );

                    //Also update type:
                    x_type_preview();
                }

                //Unlock form:
                e_x_form_unlock(data);

            },
            error: function (data) {
                var result = [];
                result.status = 0;
                result.message = data.responseText;
                e_x_form_unlock(result);
            }
        });
    } else {
        // ajax for legacy browsers
    }
}


function e_sort_save() {

    var new_x__spectrums = [];
    var sort_rank = 0;

    $("#list-in-11029 .en-item").each(function () {
        //Fetch variables for this idea:
        var e__id = parseInt($(this).attr('e__id'));
        var x__id = parseInt($(this).attr('x__id'));

        sort_rank++;

        //Store in DB:
        new_x__spectrums[sort_rank] = x__id;
    });

    //It might be zero for lists that have jsut been emptied
    if (sort_rank > 0) {
        //Update backend:
        $.post("/e/e_sort_save", {e__id: e_focus_id, new_x__spectrums: new_x__spectrums}, function (data) {
            //Update UI to confirm with member:
            if (!data.status) {
                //There was some sort of an error returned!
                alert(data.message);
            }
        });
    }
}

function e_sort_reset(){
    var r = confirm("Reset all Portfolio Source orders & sort alphabetically?");
    if (r == true) {
        $('.sort_reset').html('<i class="far fa-yin-yang fa-spin"></i>');

        //Update via call:
        $.post("/e/e_sort_reset", {
            e__id: e_focus_id
        }, function (data) {

            if (!data.status) {

                //Ooops there was an error!
                alert(data.message);

            } else {

                //Refresh page:
                window.location = '/@' + e_focus_id;

            }
        });
    }
}

function e_sort_portfolio_load() {

    var element_key = null;
    var theobject = document.getElementById("list-in-11029");
    if (!theobject) {
        //due to duplicate ideas belonging in this idea:
        return false;
    }

    //Show sort icon:
    $('.sort_e, .sort_reset').removeClass('hidden');

    var sort = Sortable.create(theobject, {
        animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
        draggable: ".en-item", // Specifies which items inside the element should be sortable
        handle: ".sort_e", // Restricts sort start click/touch to the specified element
        onUpdate: function (evt/**Event*/) {
            e_sort_save();
        }
    });
}
