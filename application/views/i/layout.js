/*
*
* Functions related to modifying ideas
* and managing IDEA NOTES.
*
* */


var match_search_loaded = 0; //Keeps track of when we load the match search

$(document).ready(function () {

    i_note_activate();

    //Load search for mass update function:
    load_editor();

    //Lookout for textinput updates
    x_set_start_text();


    //Look for power editor updates:
    $('.x_set_class_text').keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            x_set_text(this);
            e.preventDefault();
        }
    }).change(function() {
        x_set_text(this);
    });


    $('.power_editor').keydown(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (e.ctrlKey && code== 13) {
            alert($(this).attr('note_type_id'));
            i_note_power_edit($(this).attr('note_type_id'));
        }
    });

    //Put focus on messages if no message:
    if(!$('#i_notes_list_4231 .note_sortable').length){
        $('.input_note_4231').focus();
    }


    autosize($('.text__4736_'+focus_i__id));

    //Activate Source-Only Inputs:
    $(".e-only").each(function () {
        e_e_only_search($(this).attr('note_type_id'));
    });


    //Load top/bottom idea searches:
    i_load_search(".previous_i",1, 'q', 'x_in');
    i_load_search(".next_i",0, 'w', 'x_in');

    //Load Sortable:
    i_sort_load(focus_i__id);

});


function i_note_power_edit(note_type_id){

    var input_textarea = '.input_note_'+note_type_id;
    $(input_textarea).addClass('dynamic_saving').prop("disabled", true);
    $('.save_notes_' + note_type_id).html('<i class="far fa-yin-yang fa-spin"></i>').attr('href', '#');

    $.post("/i/i_note_power_edit", {
        i__id: focus_i__id,
        note_type_id: note_type_id,
        field_value: $(input_textarea).val().trim()
    }, function (data) {

        $(input_textarea).removeClass('dynamic_saving').prop("disabled", false);
        $('.save_notes_' + note_type_id).attr('href', 'javascript:i_note_power_edit('+note_type_id+');');

        //Update raw text input:
        $(input_textarea).val(data.input_clean.trim());
        autosize($(input_textarea));

        if (!data.status) {

            $('.save_notes_' + note_type_id).html(js_e___11035[14422]['m__title']);

            //Show Errors:
            $(".note_error_"+note_type_id).html('<span class="icon-block"><i class="fas fa-exclamation-circle discover"></i></span> Message not saved because:<br />'+data.message);

        } else {

            //Show update success icon:
            $('.save_notes_' + note_type_id).html(js_e___11035[14424]['m__icon']);

            //Reset errors:
            $(".note_error_"+note_type_id).html('');

            //Update READ:
            $('.editor_read_'+note_type_id).html(data.message);

            //Tooltips:
            $('[data-toggle="tooltip"]').tooltip();

            //Load Images:
            lazy_load();

            setTimeout(function () {
                $('.save_notes_' + note_type_id).html(js_e___11035[14422]['m__title']);
            }, 987);

        }
    });
}



function e_only_add(e_existing_id, note_type_id) {


    //if e_existing_id>0 it means we're adding an existing source, in which case e_new_string should be null
    //If e_existing_id=0 it means we are creating a new source and then adding it, in which case e_new_string is required

    var e_new_string = null;
    var input = $('.e-i-'+note_type_id+' .add-input');
    var list_id = 'add-e-'+note_type_id;

    if (e_existing_id == 0) {

        e_new_string = input.val();
        if (e_new_string.length < 1) {
            alert('Missing source name or URL, try again');
            input.focus();
            return false;
        }
    }

    //Add via Ajax:
    input.prop('disabled', true);
    $.post("/e/e_only_add", {

        i__id: focus_i__id,
        note_type_id: note_type_id,
        e_existing_id: e_existing_id,
        e_new_string: e_new_string,

    }, function (data) {

        //Release lock:
        input.prop('disabled', false);

        if (data.status) {

            i_note_counter(note_type_id, +1);

            //Raw input to make it discovers for next URL:
            input.focus();

            //Add new object to list:
            add_to_list(list_id, '.en-item', data.e_new_echo);

            //Tooltips:
            $('[data-toggle="tooltip"]').tooltip();

        } else {
            //We had an error:
            alert(data.message);
        }

    });

    return true;

}

function e_e_only_search(note_type_id) {

    if(!js_pl_id){
        return false;
    }

    var element_focus = ".e-i-"+note_type_id;

    var base_creator_url = '/e/create/'+focus_i__id+'/?content_title=';

    $(element_focus + ' .add-input').focus(function() {
        $(element_focus + ' .algolia_pad_search' ).removeClass('hidden');
    }).focusout(function() {
        $(element_focus + ' .algolia_pad_search' ).addClass('hidden');
    }).keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if ((code == 13) || (e.ctrlKey && code == 13)) {
            return e_only_add(0, note_type_id);
        }
    });

    if(parseInt(js_e___6404[12678]['m__message'])){

        $(element_focus + ' .add-input').on('autocomplete:selected', function (event, suggestion, dataset) {

            e_only_add(suggestion.s__id, note_type_id);

        }).autocomplete({hint: false, minLength: 1}, [{

            source: function (q, cb) {
                algolia_index.search(q, {
                    filters: 's__type=12274',
                    hitsPerPage: 21,
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
                    //If clicked, would trigger the autocomplete:selected above which will trigger the e__add() function
                    return view_s_js(suggestion);
                },
                header: function (data) {
                    if (!data.isEmpty) {
                        return '<a href="javascript:void(0);" onclick="e_only_add(0, '+note_type_id+');" class="suggestion montserrat"><span class="icon-block"><i class="fas fa-plus-circle add-plus source"></i></span><b class="source">Create Source "' + data.query.toUpperCase() + '"</b></a>';
                    }
                },
                empty: function (data) {
                    return '<a href="javascript:void(0);" onclick="e_only_add(0, '+note_type_id+');" class="suggestion montserrat"><span class="icon-block"><i class="fas fa-plus-circle add-plus source"></i></span><b class="source">' + data.query.toUpperCase() + '</b></a>';
                },
            }
        }]);
    }
}


function i_sort_save(i__id) {

    var new_x__spectrums = [];
    var sort_rank = 0;

    $("#list-in-" + focus_i__id + "-0 .cover_sort").each(function () {
        //Fetch variables for this idea:
        var i__id = parseInt($(this).attr('i-id'));
        var x__id = parseInt($(this).attr('x__id'));

        sort_rank++;

        //Store in DB:
        new_x__spectrums[sort_rank] = x__id;
    });

    //It might be zero for lists that have jsut been emptied
    if (sort_rank > 0 && i__id) {
        //Update backend:
        $.post("/i/i_sort_save", {i__id: i__id, new_x__spectrums: new_x__spectrums}, function (data) {
            //Update UI to confirm with user:
            if (!data.status) {
                //There was some sort of an error returned!
                alert(data.message);
            }
        });
    }
}

function i_sort_load(i__id) {

    var element_key = null;
    var theobject = document.getElementById("list-in-" + focus_i__id + "-0");
    if (!theobject) {
        //due to duplicate ideas belonging in this idea:
        return false;
    }

    var sort = Sortable.create(theobject, {
        animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
        draggable: ".cover_sort", // Specifies which items inside the element should be sortable
        handle: ".x_sort", // Restricts sort start click/touch to the specified element
        onUpdate: function (evt/**Event*/) {
            i_sort_save(i__id);
        }
    });
}

function i_add(i_x_id, is_parent, i_x_child_id) {

    /*
     *
     * Either creates an IDEA transaction between i_x_id & i_x_child_id
     * OR will create a new idea based on input text and then transaction it
     * to i_x_id (In this case i_x_child_id=0)
     *
     * */


    var sort_handler = ".cover_sort";
    var sort_list_id = "list-in-" + focus_i__id + '-' + is_parent;
    var input_field = $('#addi-c-' + i_x_id + '-' + is_parent);
    var i__title = input_field.val();


    if( i__title.charAt(0)=='#'){
        if(isNaN(i__title.substr(1))){
            alert('Use numbers only. Example: #1234');
            return false;
        } else {
            //Update the references:
            i_x_child_id = parseInt(i__title.substr(1));
            i__title = i_x_child_id; //As if we were just adding
        }
    }



    //We either need the idea name (to create a new idea) or the i_x_child_id>0 to create an IDEA transaction:
    if (!i_x_child_id && i__title.length < 1) {
        alert('Enter something');
        input_field.focus();
        return false;
    }


    //Set processing status:
    add_to_list(sort_list_id, sort_handler, '<div id="tempLoader" class="list-group-item montserrat no-side-padding"><span class="icon-block"><i class="fas fa-yin-yang fa-spin idea"></i></span>' + js_view_shuffle_message(12695) +  '</div>');


    //Update backend:
    $.post("/i/i_add", {
        i_x_id: i_x_id,
        is_parent:is_parent,
        i__title: i__title,
        i_x_child_id: i_x_child_id
    }, function (data) {

        //Delete loader:
        $("#tempLoader").remove();

        if (data.status) {

            if(!is_parent){
                //Only children have a counter:
                i_note_counter(12273, +1);
            }

            //Add new
            add_to_list(sort_list_id, sort_handler, data.next_i_html);

            //Reload sorting to enable sorting for the newly added idea:
            i_sort_load(i_x_id);

            //Lookout for textinput updates
            x_set_start_text();

            //Tooltips:
            $('[data-toggle="tooltip"]').tooltip();

        } else {
            //Show errors:
            alert(data.message);
        }

    });

    //Return false to prevent <form> submission:
    return false;

}


function i_set_dropdown(element_id, new_e__id, i__id, x__id, show_full_name){

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

    var current_selected = parseInt($('.dropi_'+element_id+'_'+i__id+'_'+x__id+'.active').attr('new-en-id'));
    new_e__id = parseInt(new_e__id);
    if(current_selected == new_e__id){
        //Nothing changed:
        return false;
    }

    //Changing Idea Status?
    if(element_id==4737){

        var is_i_active = (new_e__id in js_e___7356);
        var is_i_public = (new_e__id in js_e___7355);


        //Deleting?
        if(!is_i_active){
            //Seems to be deleting, confirm:
            var r = confirm("Are you sure you want to delete this idea and unlink it from all other ideas?");
            if (r == false) {
                return false;
            }
        }


        //Discoveries Setting:
        if(is_i_public){

            //Enable Discoveries:
            $('.i-x').removeClass('hidden');

        } else {

            //Disable Discoveries:
            $('.i-x').addClass('hidden');

        }

    }



    //Is Status Public?



    //Show Loading...
    var data_object = eval('js_e___'+element_id);
    $('.dropd_'+element_id+'_'+i__id+'_'+x__id+' .btn').html('<span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span><b class="montserrat">'+ ( show_full_name ? '<span class="show-max">SAVING...</span>' : '' ) +'</b>');

    $.post("/i/i_set_dropdown", {

        i__id: i__id,
        x__id: x__id,
        focus_i__id:focus_i__id,
        element_id: element_id,
        new_e__id: new_e__id

    }, function (data) {
        if (data.status) {

            //Update on page:
            $('.dropd_'+element_id+'_'+i__id+'_'+x__id+' .btn').html('<span class="icon-block">'+data_object[new_e__id]['m__icon']+'</span>' + ( show_full_name ? data_object[new_e__id]['m__title'] : '' ));

            $('.dropd_'+element_id+'_'+i__id+'_'+x__id+' .dropi_' + element_id +'_'+i__id+ '_' + x__id).removeClass('active');
            $('.dropd_'+element_id+'_'+i__id+'_'+x__id+' .optiond_' + new_e__id+'_'+i__id+ '_' + x__id).addClass('active');

            $('.dropd_'+element_id+'_'+i__id+'_'+x__id).attr('selected-val' , new_e__id);

            //Update micro icons, if any: (Idea status has it)
            $('.this_i__icon_'+i__id+'>span').html(data.new_i__icon);

            if( data.deletion_redirect && data.deletion_redirect.length > 0 ){
                //Go to main idea page:
                window.location = data.deletion_redirect;
            } else if( data.delete_element && data.delete_element.length > 0 ){
                //Go to main idea page:
                setTimeout(function () {
                    //Restore background:
                    $( data.delete_element ).fadeOut();

                    setTimeout(function () {
                        //Restore background:
                        $( data.delete_element ).remove();
                    }, 55);

                }, 377);
            }

            if(element_id==4486){
                $('.cover_x_'+x__id+' .x_marks').addClass('hidden');
                $('.cover_x_'+x__id+' .account_' + new_e__id).removeClass('hidden');
            }

        } else {

            //Reset to default:
            $('.dropd_'+element_id+'_'+i__id+'_'+x__id+' .btn').html('<span class="icon-block">'+data_object[current_selected]['m__icon']+'</span>' + ( show_full_name ? data_object[current_selected]['m__title'] : '' ));

            //Show error:
            alert(data.message);

        }
    });
}
