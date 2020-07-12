


$(document).ready(function () {

    check_i_e__status();

    //Watch for Idea status change:
    $("#x__type").change(function () {
        check_i_e__status();
    });

    //Load first page of links:
    x_load(link_filters, link_joined_by, 1);

});


function check_i_e__status(){
    //Checks to see if the Idea/Miner status filter should be visible
    //Would only make visible if Link type is Created Idea/Miner

    //Hide both in/en status:
    $(".filter-statuses").addClass('hidden');

    //Show only if creating new in/en Link type:
    if($("#x__type").val()==4250){
        $(".filter-in-status").removeClass('hidden');
    } else if($("#x__type").val()==4251){
        $(".filter-en-status").removeClass('hidden');
    }
}


function x_load(link_filters, link_joined_by, page_num){

    //Show spinner:
    $('#link_page_'+page_num).html('<div class="montserrat"><span class="icon-block"><i class="far fa-yin-yang fa-spin"></i></span>' + js_view_platform_message(12694) +  '</div>').hide().fadeIn();

    //Load report based on input fields:
    $.post("/x/x_load", {
        link_filters: link_filters,
        link_joined_by: link_joined_by,
        x__message_search:x__message_search,
        x__message_replace:x__message_replace,
        page_num: page_num,
    }, function (data) {
        if (!data.status) {
            //Show Error:
            $('#link_page_'+page_num).html('<span class="discover">'+ data.message +'</span>');
        } else {
            //Load Report:
            $('#link_page_'+page_num).html(data.message);
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

}
