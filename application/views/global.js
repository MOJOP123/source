

function load_fullstory(){
    window['_fs_debug'] = false;
    window['_fs_host'] = 'fullstory.com';
    window['_fs_script'] = 'edge.fullstory.com/s/fs.js';
    window['_fs_org'] = 'QMKCQ';
    window['_fs_namespace'] = 'FS';
    (function(m,n,e,t,l,o,g,y){
        if (e in m) {if(m.console && m.console.log) { m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].');} return;}
        g=m[e]=function(a,b,s){g.q?g.q.push([a,b,s]):g._api(a,b,s);};g.q=[];
        o=n.createElement(t);o.async=1;o.crossOrigin='anonymous';o.src='https://'+_fs_script;
        y=n.getElementsByTagName(t)[0];y.parentNode.insertBefore(o,y);
        g.identify=function(i,v,s){g(l,{uid:i},s);if(v)g(l,v,s)};g.setUserVars=function(v,s){g(l,v,s)};g.event=function(i,v,s){g('event',{n:i,p:v},s)};
        g.anonymize=function(){g.identify(!!0)};
        g.shutdown=function(){g("rec",!1)};g.restart=function(){g("rec",!0)};
        g.log = function(a,b){g("log",[a,b])};
        g.consent=function(a){g("consent",!arguments.length||a)};
        g.identifyAccount=function(i,v){o='account';v=v||{};v.acctId=i;g(o,v)};
        g.clearUserCookie=function(){};
        g._w={};y='XMLHttpRequest';g._w[y]=m[y];y='fetch';g._w[y]=m[y];
        if(m[y])m[y]=function(){return g._w[y].apply(this,arguments)};
        g._v="1.2.0";
    })(window,document,window['_fs_namespace'],'script','user');

    if(js_pl_id>0){
        //https://help.fullstory.com/hc/en-us/articles/360020623294-FS-setUserVars-Recording-custom-user-data
        FS.identify(js_pl_id, {
            displayName: js_pl_name,
            uid: js_pl_id,
            profileURL: base_url+'/@'+js_pl_id
        });
    }
}



function mass_action_ui(){
    $('.mass_action_item').addClass('hidden');
    $('#mass_id_' + $('#set_mass_action').val() ).removeClass('hidden');
}

function htmlentitiesjs(rawStr){
    return rawStr.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
        return '&#'+i.charCodeAt(0)+';';
    });
}

function load_editor(){


    $('#set_mass_action').change(function () {
        mass_action_ui();
    });

    if(parseInt(js_e___6404[12678]['m__message'])){
        $('.e_text_search').on('autocomplete:selected', function (event, suggestion, dataset) {

            $(this).val('@' + suggestion.s__id + ' ' + suggestion.s__title);

        }).autocomplete({hint: false, minLength: 2}, [{

            source: function (q, cb) {
                algolia_index.search(q, {
                    filters: 's__type=12274',
                    hitsPerPage: 8,
                }, function (error, content) {
                    if (error) {
                        cb([]);
                        return;
                    }
                    cb(content.hits, content);
                });
            },
            displayKey: function (suggestion) {
                return '@' + suggestion.s__id + ' ' + suggestion.s__title;
            },
            templates: {
                suggestion: function (suggestion) {
                    return view_search_result(suggestion);
                },
                empty: function (data) {
                    return '<div class="not-found montserrat"><i class="fas fa-exclamation-circle"></i> No Sources Found</div>';
                },
            }
        }]);

        $('.i_text_search').on('autocomplete:selected', function (event, suggestion, dataset) {

            $(this).val('#' + suggestion.s__id + ' ' + suggestion.s__title);

        }).autocomplete({hint: false, minLength: 2}, [{

            source: function (q, cb) {
                algolia_index.search(q, {
                    filters: 's__type=12273',
                    hitsPerPage: 8,
                }, function (error, content) {
                    if (error) {
                        cb([]);
                        return;
                    }
                    cb(content.hits, content);
                });
            },
            displayKey: function (suggestion) {
                return '#' + suggestion.s__id + ' ' + suggestion.s__title;
            },
            templates: {
                suggestion: function (suggestion) {
                    return view_search_result(suggestion);
                },
                empty: function (data) {
                    return '<div class="not-found montserrat"><i class="fas fa-exclamation-circle"></i> No Ideas Found</div>';
                },
            }
        }]);

    }
}


function js_extract_icon_color(e__icon){

    //NOTE: Has a twin PHP function

    if(e__icon.includes('discover')){
        return ' discover ';
    } else if(e__icon.includes( 'idea')){
        return ' idea ';
    } else if(e__icon.includes('source') || !e__icon.length){
        return ' source ';
    } else {
        return '';
    }
}

function view_search_result(algolia_object){

    var title = htmlentitiesjs( algolia_object._highlightResult && algolia_object._highlightResult.s__title.value ? algolia_object._highlightResult.s__title.value : algolia_object.s__title );

    return '<span class="icon-block">'+ algolia_object.s__icon +'</span><span class="montserrat '+ (algolia_object.s__type==12274 ? js_extract_icon_color(algolia_object.s__icon) : '' ) +'">' + title + '</span>';

}



function js_view_shuffle_message(e__id){
    var messages = js_e___12687[e__id]['m__message'].split("\n");
    if(messages.length == 1){
        //Return message:
        return messages[0];
    } else {
        //Choose Random:
        return messages[Math.floor(Math.random()*messages.length)];
    }
}


function loadtab(x__type, tab_data_id){

    //Hide all tabs:
    $('.tab-group-'+x__type).addClass('hidden');
    $('.tab-nav-'+x__type).removeClass('active');

    //Show this tab:
    $('.tab-group-'+x__type+'.tab-data-'+tab_data_id).removeClass('hidden');
    $('.tab-nav-'+x__type+'.tab-head-'+tab_data_id).addClass('active');

    //Focus on potential input field if any:
    $('.input_note_'+tab_data_id).focus();

}

function lazy_load(){
    //Lazyload photos:
    var lazyLoadInstance = new LazyLoad({
        elements_selector: "img.lazyimage"
    });
}



var algolia_index = false;
$(document).ready(function () {


    //For the S shortcut to load search:
    $("#mench_search").focus(function() {
        if(!search_on){
            toggle_search();
        }
    });


    lazy_load();

    if(js_pl_id > 1){
        //For any logged in user except shervin:
        load_fullstory();
    }

    $('#topnav li a').click(function (e) {

        e.preventDefault();
        var hash = $(this).attr('href').replace('#', '');

        if (hash.length > 0 && $('#tab' + hash).length && !$('#tab' + hash).hasClass("hidden")) {
            //Adjust Header:
            $('#topnav>li').removeClass('active');
            $('#nav_' + hash).addClass('active');
            //Adjust Tab:
            $('.tab-pane').removeClass('active');
            $('#tab' + hash).addClass('active');
        }
    });


    //Load Algolia on Focus:
    $(".algolia_search").focus(function () {
        if(!algolia_index && parseInt(js_e___6404[12678]['m__message'])){
            //Loadup Algolia once:
            client = algoliasearch('49OCX1ZXLJ', 'ca3cf5f541daee514976bc49f8399716');
            algolia_index = client.initIndex('alg_index');
        }
    });


    //General ESC cancel
    $(document).keyup(function (e) {
        //Watch for action keys:
        if (e.keyCode === 27 && search_on) { //ESC
            toggle_search();
        }
    });


    //Load tooltips:
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });


    //Prevent search submit:
    $('#searchFrontForm').on('submit', function(e) {
        e.preventDefault();
        return false;
    });


    if(parseInt(js_e___6404[12678]['m__message'])){

        $("#mench_search").on('autocomplete:selected', function (event, suggestion, dataset) {

            $('#mench_search').prop("disabled", true).val('Loading...');

            window.location = suggestion.s__url;

        }).autocomplete({minLength: 1, autoselect: true, keyboardShortcuts: ['s']}, [
            {
                source: function (q, cb) {

                    //Users can filter search with first word:
                    var search_only_e = $("#mench_search").val().charAt(0) == '@';
                    var search_only_in = $("#mench_search").val().charAt(0) == '#';

                    //Do not search if specific command ONLY:
                    if (( search_only_in || search_only_e ) && !isNaN($("#mench_search").val().substr(1)) ) {

                        cb([]);
                        return;

                    } else {

                        //Now determine the filters we need to apply:
                        var search_filters = '';

                        if(search_only_e || search_only_in){
                            search_filters += ' s__type='+( search_only_in ? 12273 : 12274 );
                        }

                        if(js_pl_id > 0){

                            //For Users:
                            if(!js_session_superpowers_activated.includes(12701)){
                                //Can view limited sources:
                                if(search_filters.length>0){
                                    search_filters += ' AND ';
                                }
                                search_filters += ' ( _tags:is_featured OR _tags:alg_e_' + js_pl_id + ' ) ';
                            }

                        } else {

                            //Guest can search ideas only by default as they start typing;
                            if(search_filters.length>0){
                                search_filters += ' AND ';
                            }
                            search_filters += ' _tags:is_featured ';

                        }

                        //Append filters:
                        algolia_index.search(q, {
                            hitsPerPage: 34,
                            filters:search_filters,
                        }, function (error, content) {
                            if (error) {
                                cb([]);
                                return;
                            }
                            cb(content.hits, content);
                        });
                    }
                },
                displayKey: function(suggestion) {
                    return ""
                },
                templates: {
                    suggestion: function (suggestion) {
                        return view_search_result(suggestion);
                    },
                    header: function (data) {
                        if(validURL(data.query)){

                            return e_fetch_canonical(data.query, false);

                        } else if($("#mench_search").val().charAt(0)=='#' || $("#mench_search").val().charAt(0)=='@'){

                            //See what follows the @/# sign to determine if we should create OR redirect:
                            var search_body = $("#mench_search").val().substr(1);
                            if(!isNaN(search_body)){
                                //Valid Integer, Give option to go there:
                                return '<a href="' + ( $("#mench_search").val().charAt(0)=='#' ? '/i/i_go/' : '/@' ) + search_body + '" class="suggestion montserrat"><span class="icon-block"><i class="far fa-level-up rotate90" style="margin: 0 5px;"></i></span>Go to ' + data.query
                            }

                        }
                    },
                    footer: function (data) {
                        return '<div class="suggestion" style="text-align: right;">Search Powered by Algolia<span class="icon-block"><i class="fab fa-algolia" style="margin: 0 5px;"></i></span></div>';
                    },
                    empty: function (data) {
                        if(validURL(data.query)){
                            return e_fetch_canonical(data.query, true);
                        } else if($("#mench_search").val().charAt(0)=='#'){
                            if(isNaN($("#mench_search").val().substr(1))){
                                return '<div class="not-found montserrat"><span class="icon-block-xs"><i class="fas fa-exclamation-circle"></i></span>No IDEA found</div>';
                            }
                        } else if($("#mench_search").val().charAt(0)=='@'){
                            if(isNaN($("#mench_search").val().substr(1))) {
                                return '<div class="not-found montserrat"><span class="icon-block-xs"><i class="fas fa-exclamation-circle"></i></span>No SOURCE found</div>';
                            }
                        } else {
                            return '<div class="not-found suggestion montserrat"><span class="icon-block"><i class="fas fa-exclamation-circle"></i></span>No results found</div>';
                        }
                    },
                }
            }
        ]);
    }
});



function x_type_preview_load(){

    //Watchout for content change
    var textInput = document.getElementById('x__message');

    //Init a timeout variable to be used below
    var timeout = null;

    //Listen for keystroke events
    textInput.onkeyup = function (e) {

        // Clear the timeout if it has previously been set.
        // This will prevent the previous step from executing
        // if it has been less than <MILLISECONDS>
        clearTimeout(timeout);

        // Make a new timeout set to go off in 800ms
        timeout = setTimeout(function () {
            //update type:
            x_type_preview();
        }, 610);
    };

}



function remove_10673(x__id, note_type_id) {

    var r = confirm("Remove this source?");
    if (r == true) {
        $.post("/e/remove_10673", {

            x__id: x__id,

        }, function (data) {
            if (data.status) {

                i_note_counter(note_type_id, -1);
                $(".tr_" + x__id).fadeOut();
                setTimeout(function () {
                    $(".tr_" + x__id).remove();
                }, 610);

            } else {
                //We had an error:
                alert(data.message);
            }
        });
    }

}

function x_type_preview() {

    //Shows the transaction type based on the transaction message
    $('#x__type_preview').html('<span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span>');

    //Fetch Idea Data to load modify widget:
    $.post("/x/x_type_preview", {
        x__message: $('#x__message').val(),
        x__id: $('#modal13571 .modal_x__id').val(),
    }, function (data) {

        if(data.status){

            $('#x__type_preview').html(data.x__type_preview);
            $('#x__message_preview').html(data.x__message_preview);
            lazy_load();
            $('[data-toggle="tooltip"]').tooltip();

        } else {

            //Show Error:
            $('#x__type_preview').html('<b class="discover">' + data.message+'</b>');

        }

    });

}




//For the drag and drop file uploader:
var isAdvancedUpload = function () {
    var div = document.createElement('div');
    return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
}();

//Main navigation
var search_on = false;
function toggle_search(){

    $('.left_nav').addClass('hidden');
    $('.search_icon').toggleClass('hidden');

    if(search_on){

        //Switch to Menu:
        search_on = false; //Reverse
        $('.mench_nav').removeClass('hidden');

    } else {

        //Turn Search On:
        search_on = true; //Reverse
        $('.search_nav').removeClass('hidden');
        $('#searchFrontForm input').focus();

    }
}


function x_save(i__id){
    $('.toggle_saved').toggleClass('hidden');
    $.post("/x/x_save", {
        i__id:i__id,
    }, function (data) {
        if (!data.status) {
            alert(data.message);
            $('.toggle_saved').toggleClass('hidden');
        }
    });
}


function e_fetch_canonical(query_string, not_found){

    //Do a call to PHP to fetch canonical URL and see if that exists:
    $.post("/e/e_fetch_canonical", { search_url:query_string }, function (searchdata) {
        if(searchdata.status && searchdata.url_previously_existed){
            //URL was detected via PHP, update the search results:
            $('.add-e-suggest').remove();
            $('.not-found').html('<a href="/@'+searchdata.algolia_object.s__id+'" class="suggestion montserrat">' + view_search_result(searchdata.algolia_object)+'</a>');
        }
    });

    //We did not find the URL:
    return ( not_found ? '<div class="not-found montserrat"><i class="fas fa-exclamation-circle"></i> URL not found</div>' : '');
}


function validURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[\@\=-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
}


function add_to_list(sort_list_id, sort_handler, html_content) {

    //See if we previously have a list in place?
    if ($("#" + sort_list_id + " " + sort_handler).length > 0) {
        //yes we do! add this:
        $("#" + sort_list_id + " " + sort_handler + ":last").after(html_content);
    } else {
        //Raw list, add before input filed:
        $("#" + sort_list_id).prepend(html_content);
    }

    lazy_load();
}

jQuery.fn.extend({
    insertAtCaret: function (myValue) {
        return this.each(function (i) {
            if (document.selection) {
                //For browsers like Internet Explorer
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            } else if (this.selectionStart || this.selectionStart == '0') {
                //For browsers like Firefox and Webkit based
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        })
    }
});




function i_load_search(element_focus, is_i_previous, shortcut, is_add_mode) {

    $(element_focus + ' .add-input').focus(function() {
        $(element_focus + ' .algolia_pad_search').removeClass('hidden');
    }).focusout(function() {
        $(element_focus + ' .algolia_pad_search').addClass('hidden');
    });

    //Idea Search
    $(element_focus + ' .add-input').keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if ((code == 13) || (e.ctrlKey && code == 13)) {
            if(is_add_mode=='x_in') {
                return i_add($(element_focus + ' .add-input').attr('i-id'), is_i_previous, 0);
            } else if(is_add_mode=='x_my_in') {
                return i_create();
            }
            e.preventDefault();
        }
    });

    if(!parseInt(js_e___6404[12678]['m__message'])){
        //Previously loaded:
        return false;
    }

    //Not yet loaded, continue with loading it:
    $(element_focus + ' .add-input').on('autocomplete:selected', function (event, suggestion, dataset) {

        if(is_add_mode=='x_in'){
            i_add($(element_focus + ' .add-input').attr('i-id'), is_i_previous, suggestion.s__id);
        } else {
            //Go to idea:
            window.location = suggestion.s__url;
            return true;
        }
    }).autocomplete({hint: false, minLength: 1, keyboardShortcuts: [( is_i_previous ? 'q' : 'a' )]}, [{

        source: function (q, cb) {

            if($(element_focus).val().charAt(0)=='#'){
                cb([]);
                return;
            } else {
                algolia_index.search(q, {

                    filters: ' s__type=12273 ' + ( js_session_superpowers_activated.includes(12701) ? '' : ' AND ( _tags:is_featured ' + ( js_pl_id > 0 ? 'OR _tags:alg_e_' + js_pl_id : '' ) + ') ' ),
                    hitsPerPage:21,

                }, function (error, content) {
                    if (error) {
                        cb([]);
                        return;
                    }
                    cb(content.hits, content);
                });
            }

        },
        displayKey: function (suggestion) {
            return ""
        },
        templates: {
            suggestion: function (suggestion) {
                return view_search_result(suggestion);
            },
            header: function (data) {
                if (is_add_mode=='x_in' && !($(element_focus).val().charAt(0)=='#') && !data.isEmpty) {
                    return '<a href="javascript:void(0);" onclick="i_add(' + parseInt($(element_focus + ' .add-input').attr('i-id')) + ','+is_i_previous+',0)" class="suggestion montserrat"><span class="icon-block"><i class="fas fa-plus-circle idea add-plus"></i></span><b>' + data.query + '</b></a>';
                } else if(is_add_mode=='x_my_in'){
                    return '<a href="javascript:void(0);" onclick="i_create()" class="suggestion montserrat"><span class="icon-block"><i class="fas fa-plus-circle idea add-plus"></i></span><b>' + data.query + '</b></a>';
                }
            },
            empty: function (data) {
                if(is_add_mode=='x_in'){
                    if($(element_focus).val().charAt(0)=='#'){
                        return '<a href="javascript:void(0)" onclick="i_add(' + parseInt($(element_focus + ' .add-input').attr('i-id')) + ','+is_i_previous+',0)" class="suggestion montserrat"><span class="icon-block"><i class="fas fa-x"></i></span>Transaction to <b>' + data.query + '</b></a>';
                    } else {
                        return '<a href="javascript:void(0)" onclick="i_add(' + parseInt($(element_focus + ' .add-input').attr('i-id')) + ','+is_i_previous+',0)" class="suggestion montserrat"><span class="icon-block"><i class="fas fa-plus-circle idea add-plus"></i></span><b>' + data.query + '</b></a>';
                    }
                }
            },
        }
    }]);

}

function gif_modal(x__type){
    $('#modal14073').modal('show');
    $('#modal_x__type').val(x__type);
    $('#gif_results').html('');
    $('#gif_query').val('');
    setTimeout(function () {
        $('#gif_query').focus();
    }, 610);
}

Math.fmod = function (a,b) { return Number((a - (Math.floor(a / b) * b)).toPrecision(8)); };

var current_q = '';
function gif_search(){

    var q = encodeURI($('#gif_query').val());
    if(q==current_q){
        return false;
    }

    var x__type = $('#modal_x__type').val();
    current_q = q;
    $('#gif_results').html('<span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span>').hide().fadeIn();
    $.get({
        url: "https://api.giphy.com/v1/gifs/search?q="+current_q+"&api_key=7kQlJD3Q1puRjBoKomL4wSx5Qi2XOS8F&limit=50&offset=0",
        success: function(result) {
            var data = result.data;
            var output = "";
            var counter = 0;
            for (var index in data){
                counter++;
                var gifObject = data[index];
                console.log(gifObject);
                output += "<div class=\"gif-col col-4\"><a href=\"javascript:void(0);\" onclick=\"gif_add("+x__type+",'"+gifObject.id+"','"+gifObject.title+"')\"><img src='/img/mench.png' alt='GIF' class='lazyimage' data-src='https://media"+parseInt(Math.fmod(counter, 5))+".giphy.com/media/"+gifObject.id+"/giphy.gif' /></a></div>";
                if(!Math.fmod(counter, 3)){
                    output += "</div><div class=\"row\">";
                }
            }

            //Did we find anything?
            if(output.length){
                output = "<div style=\"margin:5px 0;\">Tap the GIF you want to add:</div><div class=\"row\">"+output+"</div>";
            } else {
                //No results found:
                output = "<div style=\"margin:5px 0;\">No GIFs found</div>";
            }
            $("#gif_results").html(output);
            lazy_load();
        },
        error: function(error) {
            console.log(error);
        }
    });

}

function gif_add(x__type, giphy_id, giphy_title){
    $('#modal14073').modal('hide');
    $('#x__message' + x__type).val('https://media.giphy.com/media/'+giphy_id+'/giphy.gif?e__title='+encodeURI(giphy_title));
    i_note_text(x__type);
}


function x_set_text_start(){
    $('.x_set_text').keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            x_set_text(this);
            e.preventDefault();
        }
    }).change(function() {
        x_set_text(this);
    });
}

function view_input_text_count(cache_e__id, s__id) {

    //Count text area characters:

    //Update count:
    var len = $('.text__'+cache_e__id+'_'+s__id).val().length;
    if (len > js_e___6404[cache_e__id]['m__message']) {
        $('#current_count_'+cache_e__id+'_'+s__id).addClass('overload').text(len);
    } else {
        $('#current_count_'+cache_e__id+'_'+s__id).removeClass('overload').text(len);
    }

    //Only show counter if getting close to limit:
    if(len > ( js_e___6404[cache_e__id]['m__message'] * js_e___6404[12088]['m__message'] )){
        $('.title_counter_'+cache_e__id+'_'+s__id).removeClass('hidden');
    } else {
        $('.title_counter_'+cache_e__id+'_'+s__id).addClass('hidden');
    }

}

function update_text_name(cache_e__id, e__id, e__title){
    if(cache_e__id==6197){
        e__title = e__title.toUpperCase();
    }
    $(".text__"+cache_e__id+"_" + e__id).val(e__title).text(e__title).attr('old-value', e__title);
}

function x_set_text(this_handler){

    var modify_data = {
        s__id: parseInt($(this_handler).attr('s__id')),
        cache_e__id: parseInt($(this_handler).attr('cache_e__id')),
        field_value: $(this_handler).val().trim()
    };

    //See if anything changes:
    if( $(this_handler).attr('old-value') == modify_data['field_value'] ){
        //Nothing changed:
        return false;
    }

    //Grey background to indicate saving...
    var handler = '.text__'+modify_data['cache_e__id']+'_'+modify_data['s__id'];
    $(handler).addClass('dynamic_saving');

    $.post("/x/x_set_text", modify_data, function (data) {

        if (!data.status) {

            //Reset to original value:
            $(handler).val(data.original_val);

            //Show error:
            alert(data.message);

        } else {

            //If Updating Text, Updating Corresponding Fields:
            update_text_name(modify_data['cache_e__id'], modify_data['s__id'], modify_data['field_value']);

        }

        setTimeout(function () {
            //Restore background:
            $(handler).removeClass('dynamic_saving');
        }, 233);

    });
}



/*
*
* IDEA NOTES
*
* */
function i_note_activate(){
    //Loop through all new idea inboxes:
    $(".new-note").each(function () {

        var note_type_id = parseInt($(this).attr('note_type_id'));

        //Initiate @ search for all idea text areas:
        i_note_e_search($(this));

        autosize($(this));

        //Activate sorting:
        i_note_sort_load(note_type_id);

        var showFiles = function (files) {
            if(typeof files[0] !== 'undefined'){
                $('.box' + note_type_id).find('label').text(files.length > 1 ? ($('.box' + note_type_id).find('input[type="file"]').attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
            }
        };

        $('.box' + note_type_id).find('input[type="file"]').on('drop', function (e) {
            droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped
            showFiles(droppedFiles);
        });

        $('.box' + note_type_id).find('input[type="file"]').on('change', function (e) {
            showFiles(e.target.files);
        });

        //Watch for message creation:
        $('#x__message' + note_type_id).keydown(function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
                i_note_text(note_type_id);
            }
        });

        //Watchout for file uplods:
        $('.box' + note_type_id).find('input[type="file"]').change(function () {
            i_note_file(droppedFiles, 'file', note_type_id);
        });


        //Should we auto start?
        if (isAdvancedUpload) {

            $('.box' + note_type_id).addClass('has-advanced-upload');
            var droppedFiles = false;

            $('.box' + note_type_id).on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
            })
                .on('dragover dragenter', function () {
                    $('.add_notes_' + note_type_id).addClass('is-working');
                })
                .on('dragleave dragend drop', function () {
                    $('.add_notes_' + note_type_id).removeClass('is-working');
                })
                .on('drop', function (e) {
                    droppedFiles = e.originalEvent.dataTransfer.files;
                    e.preventDefault();
                    i_note_file(droppedFiles, 'drop', note_type_id);
                });
        }

    });
}

function i_note_counter(note_type_id, adjustment_count){
    var current_count = parseInt( $('.en-type-counter-'+note_type_id).text().length ? $('.en-type-counter-'+note_type_id).text() : 0 );
    var new_count = current_count + adjustment_count;
    $('.en-type-counter-'+note_type_id).text(new_count);
}

function i_note_count_new(note_type_id) {

    //Update count:
    var len = $('#x__message' + note_type_id).val().length;
    if (len > js_e___6404[4485]['m__message']) {
        $('#charNum' + note_type_id).addClass('overload').text(len);
    } else {
        $('#charNum' + note_type_id).removeClass('overload').text(len);
    }

    //Only show counter if getting close to limit:
    if(len > ( js_e___6404[4485]['m__message'] * js_e___6404[12088]['m__message'] )){
        $('#ideaNoteNewCount' + note_type_id).removeClass('hidden');
    } else {
        $('#ideaNoteNewCount' + note_type_id).addClass('hidden');
    }

}

function count_13574(x__id) {
    //See if this is a valid text message editing:
    if (!($('#charEditingNum' + x__id).length)) {
        return false;
    }
    //Update count:
    var len = $('#message_body_' + x__id).val().length;
    if (len > js_e___6404[4485]['m__message']) {
        $('#charEditingNum' + x__id).addClass('overload').text(len);
    } else {
        $('#charEditingNum' + x__id).removeClass('overload').text(len);
    }

    //Only show counter if getting close to limit:
    if(len > ( js_e___6404[4485]['m__message'] * js_e___6404[12088]['m__message'] )){
        $('#NoteCounter' + x__id).removeClass('hidden');
    } else {
        $('#NoteCounter' + x__id).addClass('hidden');
    }
}

function i_note_e_search(obj) {

    if(parseInt(js_e___6404[12678]['m__message'])){
        obj.textcomplete([
            {
                match: /(^|\s)@(\w*(?:\s*\w*))$/,
                search: function (query, callback) {
                    algolia_index.search(query, {
                        hitsPerPage: 8,
                        filters: 's__type=12274',
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
                template: function (suggestion) {
                    return '<div style="padding: 3px 0;">' + view_search_result(suggestion) + '</div>';
                },
                replace: function (suggestion) {
                    return ' @' + suggestion.s__id + ' ';
                }
            },
        ]);
    }
}

function i_note_sort_apply(note_type_id) {

    var new_x__sorts = [];
    var sort_rank = 0;
    var this_x__id = 0;

    $(".msg_e_type_" + note_type_id).each(function () {
        this_x__id = parseInt($(this).attr('x__id'));
        if (this_x__id > 0) {
            sort_rank++;
            new_x__sorts[sort_rank] = this_x__id;
        }
    });

    //Update backend if any:
    if(sort_rank > 0){
        $.post("/i/i_note_sort", {new_x__sorts: new_x__sorts}, function (data) {
            //Only show message if there was an error:
            if (!data.status) {
                //Show error:
                alert(data.message);
            }
        });
    }
}

function i_note_sort_load(note_type_id) {

    var inner_content = null;

    var sort_msg = Sortable.create( document.getElementById("i_notes_list_" + note_type_id) , {
        animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
        handle: ".i_note_sorting", // Restricts sort start click/touch to the specified element
        draggable: ".note_sortable", // Specifies which items inside the element should be sortable
        onUpdate: function (evt/**Event*/) {
            //Apply new sort:
            i_note_sort_apply(note_type_id);
        },
        //The next two functions resolve a Bug with sorting iframes like YouTube embeds while also making the UI more informative
        onChoose: function (evt/**Event*/) {
            //See if this is a YouTube or Vimeo iFrame that needs to be temporarily deleted:
            var x__id = $(evt.item).attr('x__id');
            if ($('#ul-nav-' + x__id).find('.video-sorting').length !== 0) {
                inner_content = $('#msgbody_' + x__id).html();
                $('#msgbody_' + x__id).css('height', $('#msgbody_' + x__id).height()).html('SORT VIDEO UP/DOWN');
            } else {
                inner_content = null;
            }
        },
        onEnd: function (evt/**Event*/) {
            if (inner_content) {
                var x__id = $(evt.item).attr('x__id');
                $('#msgbody_' + x__id).html(inner_content);
            }
        }
    });

}

function load_i_note_editor(x__id) {

    //Start editing:
    $("#ul-nav-" + x__id).addClass('in-editing');
    $("#ul-nav-" + x__id + " .edit-off").addClass('hidden');
    $("#ul-nav-" + x__id + " .edit-on").removeClass('hidden');
    $("#ul-nav-" + x__id + ">div").css('width', '100%');

    //Set focus to end of text:
    var textinput = $("#ul-nav-" + x__id + " textarea");
    var data = textinput.val();
    textinput.focus().val('').val(data);
    autosize(textinput); //Adjust height


    //Initiate search:
    i_note_e_search(textinput);

    //Try to initiate the editor, which only applies to text messages:
    count_13574(x__id);

}

function cancel_13574(x__id) {
    //Revert editing:
    $("#ul-nav-" + x__id).removeClass('in-editing');
    $("#ul-nav-" + x__id + " .edit-off").removeClass('hidden');
    $("#ul-nav-" + x__id + " .edit-on").addClass('hidden');
    $("#ul-nav-" + x__id + ">div").css('width', 'inherit');
}

function save_13574(x__id, note_type_id) {

    //Revert View:
    cancel_13574(x__id);

    //Clear Message:
    $("#ul-nav-" + x__id + " .edit-updates").html('');

    var modify_data = {
        x__id: parseInt(x__id),
        i__id: parseInt(focus_i__id),
        x__message: $("#ul-nav-" + x__id + " textarea").val(),
    };

    //Update message:
    $.post("/i/save_13574", modify_data, function (data) {

        if (data.status) {

            //Update text message:
            $("#ul-nav-" + x__id + " .text_message").html(data.message);

            lazy_load();

        } else {

            //ERROR
            $("#ul-nav-" + x__id + " .edit-updates").html('<b class="discover montserrat"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</b>');

        }

        //Tooltips:
        $('[data-toggle="tooltip"]').tooltip();

    });

}


function remove_13579(x__id, note_type_id){
    //REMOVE NOTE
    $.post("/i/remove_13579", { x__id: parseInt(x__id) }, function (data) {
        if (data.status) {

            i_note_counter(note_type_id, -1);

            $("#ul-nav-" + x__id).fadeOut();

            setTimeout(function () {
                $("#ul-nav-" + x__id).remove();
            }, 610);

        } else {

            alert(data.message);

        }
    });
}

function i_note_start_adding(note_type_id) {

    $('.save_notes_' + note_type_id).html('<i class="far fa-yin-yang fa-spin"></i>').attr('href', '#');
    $('.add_notes_' + note_type_id).addClass('is-working');
    $('.no_notes_' + note_type_id).remove();
    $('#x__message' + note_type_id).prop("disabled", true);
    $('.remove_loading').hide();
}


function i_note_end_adding(result, note_type_id) {

    //Update UI to unlock:
    $('.save_notes_' + note_type_id).html('<i class="fas fa-plus"></i>').attr('href', 'javascript:i_note_text('+note_type_id+');');
    $('.add_notes_' + note_type_id).removeClass('is-working');
    $('#x__message' + note_type_id).prop("disabled", false).focus();
    $('.remove_loading').fadeIn();

    //What was the result?
    if (result.status) {

        //Append data:
        $(result.message).insertBefore( ".add_notes_" + note_type_id );

        //Tooltips:
        $('[data-toggle="tooltip"]').tooltip();

        //Load Images:
        lazy_load();

        //Hide any errors:
        $(".note_error_"+note_type_id).html('');

    } else {

        $(".note_error_"+note_type_id).html('<span class="icon-block"><i class="fas fa-exclamation-circle discover"></i></span>'+result.message);

    }
}

function i_note_file(droppedFiles, uploadType, note_type_id) {

    //Prevent multiple concurrent uploads:
    if ($('.box' + note_type_id).hasClass('is-uploading')) {
        return false;
    }

    if (isAdvancedUpload) {

        //Lock message:
        i_note_start_adding(note_type_id);

        var ajaxData = new FormData($('.box' + note_type_id).get(0));
        if (droppedFiles) {
            $.each(droppedFiles, function (i, file) {
                var thename = $('.box' + note_type_id).find('input[type="file"]').attr('name');
                if (typeof thename == typeof undefined || thename == false) {
                    var thename = 'drop';
                }
                ajaxData.append(uploadType, file);
            });
        }

        ajaxData.append('upload_type', uploadType);
        ajaxData.append('i__id', focus_i__id);
        ajaxData.append('note_type_id', note_type_id);

        $.ajax({
            url: '/i/i_note_file',
            type: $('.box' + note_type_id).attr('method'),
            data: ajaxData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            complete: function () {
                $('.box' + note_type_id).removeClass('is-uploading');
            },
            success: function (data) {

                i_note_counter(note_type_id, +1);
                i_note_end_adding(data, note_type_id);

                //Adjust icon again:
                $('.file_label_' + note_type_id).html('<span class="icon-block">'+js_e___11035[13572]['m__icon']+'</span>');

            },
            error: function (data) {
                var result = [];
                result.status = 0;
                result.message = data.responseText;
                i_note_end_adding(result, note_type_id);
            }
        });
    } else {
        // ajax for legacy browsers
    }
}

function i_note_text(note_type_id) {

    //Lock message:
    i_note_start_adding(note_type_id);

    //Update backend:
    $.post("/i/i_note_text", {

        i__id: focus_i__id, //Synonymous
        x__message: $('#x__message' + note_type_id).val(),
        note_type_id: note_type_id,

    }, function (data) {

        //Raw Inputs Fields if success:
        if (data.status) {

            //Reset input field:
            $('#x__message' + note_type_id).val("");
            autosize.update($('#x__message' + note_type_id));

            i_note_count_new(note_type_id);
            i_note_counter(note_type_id, +1);

        }

        //Unlock field:
        i_note_end_adding(data, note_type_id);

    });

}


function x_sort_load(x__type){
    //Load sorter:
    var sort = Sortable.create(document.getElementById('list_'+x__type), {
        animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
        draggable: "#list_"+x__type+" .cover_sort", // Specifies which items inside the element should be sortable
        handle: "#list_"+x__type+" .x_sort", // Restricts sort start click/touch to the specified element
        onUpdate: function (evt/**Event*/) {
            x_sort(x__type);
        }
    });
}


function x_sort(x__type) {

    var sort_rank = 0;
    var new_x_order = [];
    $("#list_"+x__type+" .cover_sort").each(function () {
        var x_id = parseInt($(this).attr('x__id'));
        if(x_id > 0){
            sort_rank++;
            new_x_order[sort_rank] = x_id;
        }
    });

    //Update order:
    if(sort_rank > 0){
        $.post("/x/x_sort", { new_x_order:new_x_order, x__type:x__type }, function (data) {
            //Update UI to confirm with user:
            if (!data.status) {
                //There was some sort of an error returned!
                alert(data.message);
            }
        });
    }

}