<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Play extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->output->enable_profiler(FALSE);

        date_default_timezone_set(config_var(11079));
    }


    function add_11158(){

        //A function that goes through Medium topics @11097 and fetches all the top Publishers @11158 within that topic
        $ln_owner_play_id = 1; //Shervin as Developer for logging all READS

        //Fetch URL:
        $medium_urls = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
            'ln_parent_play_id' => 1326, //Domain Names
            'ln_child_play_id' => 3311, //Medium URL
        ));


        $topic_count = 0;
        foreach ($this->READ_model->ln_fetch(array(
            'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7357')) . ')' => null, //Player Statuses Public
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
            'ln_parent_play_id' => 11097, //Medium Topic
        ), array('en_child')) as $medium_topic){

            $topic_count++;

            //Fetch this page:
            foreach ($this->READ_model->ln_fetch(array(
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
                'ln_type_play_id' => 4256, //URL
                'ln_parent_play_id' => 3311, //Medium Link
                'ln_child_play_id' => $medium_topic['en_id'],
            )) as $medium_topic_link){

                //Fetch Publishers within this topic:
                $url_content = @file_get_contents($medium_topic_link['ln_content']);

                if(!$url_content){
                    echo '<div>FAILED to fetch ['.$medium_topic_link['ln_content'].']</div>';
                    continue;
                }

                //Fetch UNIQUE author URLs:
                $unique_authors = array();
                foreach(explode('"/@', $url_content) as $index => $author_string){

                    if(!$index){
                        continue; //Do not check the first one
                    }

                    $author_url_path = one_two_explode('', '"', $author_string);

                    if(substr_count($author_url_path, '/')){
                        $author_handler = one_two_explode('', '/', $author_url_path);
                    } elseif(substr_count($author_url_path, '?')){
                        $author_handler = one_two_explode('', '?', $author_url_path);
                    } else {
                        $author_handler = $author_url_path;
                    }

                    if(!in_array($author_handler, $unique_authors)){
                        array_push($unique_authors, $author_handler);
                    }

                }

                //Now sync authors in Database:
                $newly_added = 0;

                foreach($unique_authors as $author_handler){

                    $full_url = rtrim($medium_urls[0]['ln_content'], '/') . '/@' . $author_handler;

                    $already_added = $this->READ_model->ln_fetch(array(
                        'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Link Statuses Active
                        'ln_type_play_id' => 4256, //Generic URL
                        'ln_parent_play_id' => 3311, //Medium URL
                        'ln_content' => $full_url,
                    ));

                    //Add to DB IF not already there:
                    if(!count($already_added)){

                        $newly_added++;

                        //Create new Player:
                        $added_en = $this->PLAY_model->en_verify_create($author_handler, $ln_owner_play_id, 6181, random_player_avatar());

                        //Create relevant READS:

                        $this->READ_model->ln_create(array(
                            'ln_type_play_id' => 4256, //Generic URL
                            'ln_owner_play_id' => $ln_owner_play_id,
                            'ln_parent_play_id' => 3311, //Medium URL
                            'ln_child_play_id' => $added_en['en']['en_id'],
                            'ln_content' => $full_url,
                        ));

                        $this->READ_model->ln_create(array(
                            'ln_type_play_id' => 4230, //Raw link
                            'ln_owner_play_id' => $ln_owner_play_id,
                            'ln_parent_play_id' => 1278, //People
                            'ln_child_play_id' => $added_en['en']['en_id'],
                        ));

                        $this->READ_model->ln_create(array(
                            'ln_type_play_id' => 4230, //Raw link
                            'ln_owner_play_id' => $ln_owner_play_id,
                            'ln_parent_play_id' => 11158, //Medium Publisher
                            'ln_child_play_id' => $added_en['en']['en_id'],
                        ));

                        //Medium Topic
                        $this->READ_model->ln_create(array(
                            'ln_type_play_id' => 4230, //Raw link
                            'ln_owner_play_id' => $ln_owner_play_id,
                            'ln_parent_play_id' => $medium_topic['en_id'],
                            'ln_child_play_id' => $added_en['en']['en_id'],
                        ));

                    }
                }

                //Count total authors:
                echo '<div>'.$topic_count.') Added '.$newly_added.' Authors in ['.$medium_topic_link['ln_content'].'] from the full list ['.join(', ',$unique_authors).']</div>';

            }
        }
    }


    function echo_post(){
        print_r($_POST);
    }

    function bot(){

        $url = 'https://medium.com/_/graphql';
        $topic = 'books';
        $custom_header = array(
            'medium-frontend-app: lite/master-20191021-212205-4df9cf54be',
            'medium-frontend-route: topic',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-origin',
            'apollographql-client-name: lite',
            'apollographql-client-version: master-20191021-212205-4df9cf54be',

            'accept: */*',
            'accept-encoding: gzip, deflate, br',
            'accept-language: en-GB,en-US;q=0.9,en;q=0.8',
            'content-length: 6731',
            'graphql-operation: TopicHandler',
            'content-type: application/json',
            'origin: https://medium.com',
            'referer: https://medium.com/topic/'.$topic,

            'cookie: __cfduid=db6eef3c324dc50d96ef21938b6f00edc1559329362; _ga=GA1.2.2055208362.1565325511; lightstep_session_id=7e05aed248707e2e; lightstep_guid/medium-web=93bf19db9151b98a; tz=420; pr=2; lightstep_guid/lite-web=58f426d9268501ac; _gid=GA1.2.1980711924.1571614565; optimizelyEndUserId=lo_e8082354b03e; uid=lo_e8082354b03e; sid=1:/xbfZQ7E3E7EPoIIifaIKj/DmNhQCAKcI9h6hfo+EFoV1Vicr75acNYhuyD26dd9; __cfruid=119da54070c31f79db03af372b90931f3f9aa260-1571694671; _parsely_session={%22sid%22:38%2C%22surl%22:%22https://medium.com/%22%2C%22sref%22:%22%22%2C%22sts%22:1571694672029%2C%22slts%22:1571688510366}; _parsely_visitor={%22id%22:%22pid=acaaa24a25423adbd91231b52e769418%22%2C%22session_count%22:38%2C%22last_session_ts%22:1571694672029}; sz=1652',
        );

        if(0){
            if(!isset($_POST['custom_head'])){
                $_POST['custom_head'] = join("\n", $custom_header);
            }


            echo count(explode("\n", $_POST['custom_head']));

            ?>
            <form action="" method="post">
                <input type="submit" name="GO">
                <textarea style="width: 800px; height: 500px;" name="custom_head"><?= $_POST['custom_head'] ?></textarea>
            </form>
            <?php
        }




        $data = array(
            'operationName' => 'TopicHandler',
            'variables' => array(
                'feedPagingOptions' => array(
                    'limit' => 25,
                    'to' => '1571614526950',
                ),
                'sidebarPagingOptions' => array(
                    'limit' => 5,
                ),
                'topicSlug' => $topic,
            ),
            'query' => 'query TopicHandler($topicSlug: ID!, $feedPagingOptions: PagingOptions, $sidebarPagingOptions: PagingOptions) {
  topic(slug: $topicSlug) {
    canonicalSlug
    ...TopicScreen_topic
    __typename
  }
}

fragment PostListingItemFeed_postPreview on PostPreview {
  post {
    ...PostListingItemPreview_post
    ...PostListingItemByline_post
    ...PostListingItemImage_post
    ...PostPresentationTracker_post
    __typename
  }
  __typename
}

fragment PostListingItemPreview_post on Post {
  id
  mediumUrl
  title
  previewContent {
    subtitle
    isFullContent
    __typename
  }
  isPublished
  creator {
    id
    __typename
  }
  __typename
}

fragment PostListingItemByline_post on Post {
  id
  creator {
    id
    username
    name
    __typename
  }
  isLocked
  readingTime
  ...BookmarkButton_post
  firstPublishedAt
  statusForCollection
  collection {
    id
    name
    ...collectionUrl_collection
    __typename
  }
  __typename
}

fragment BookmarkButton_post on Post {
  ...SusiClickable_post
  ...WithSetReadingList_post
  __typename
}

fragment SusiClickable_post on Post {
  id
  mediumUrl
  ...SusiContainer_post
  __typename
}

fragment SusiContainer_post on Post {
  id
  __typename
}

fragment WithSetReadingList_post on Post {
  ...ReadingList_post
  __typename
}

fragment ReadingList_post on Post {
  id
  readingList
  __typename
}

fragment collectionUrl_collection on Collection {
  id
  domain
  slug
  __typename
}

fragment PostListingItemImage_post on Post {
  id
  mediumUrl
  previewImage {
    id
    focusPercentX
    focusPercentY
    __typename
  }
  __typename
}

fragment PostPresentationTracker_post on Post {
  id
  visibility
  previewContent {
    isFullContent
    __typename
  }
  collection {
    id
    __typename
  }
  __typename
}

fragment TopicScreen_topic on Topic {
  id
  ...TopicMetadata_topic
  ...TopicLandingHeader_topic
  ...TopicFeaturedAndLatest_topic
  ...TopicLandingRelatedTopics_topic
  ...TopicLandingPopular_posts
  __typename
}

fragment TopicMetadata_topic on Topic {
  name
  description
  image {
    id
    __typename
  }
  __typename
}

fragment TopicLandingHeader_topic on Topic {
  name
  description
  visibility
  ...TopicFollowButtonSignedIn_topic
  ...TopicFollowButtonSignedOut_topic
  __typename
}

fragment TopicFollowButtonSignedIn_topic on Topic {
  slug
  isFollowing
  __typename
}

fragment TopicFollowButtonSignedOut_topic on Topic {
  id
  slug
  ...SusiClickable_topic
  __typename
}

fragment SusiClickable_topic on Topic {
  ...SusiContainer_topic
  __typename
}

fragment SusiContainer_topic on Topic {
  ...SignInContainer_topic
  ...SignUpOptions_topic
  __typename
}

fragment SignInContainer_topic on Topic {
  ...SignInOptions_topic
  __typename
}

fragment SignInOptions_topic on Topic {
  id
  name
  __typename
}

fragment SignUpOptions_topic on Topic {
  id
  name
  __typename
}

fragment TopicFeaturedAndLatest_topic on Topic {
  featuredPosts {
    postPreviews {
      post {
        id
        ...TopicLandingFeaturedStory_post
        __typename
      }
      __typename
    }
    __typename
  }
  featuredTopicWriters(limit: 1) {
    ...FeaturedWriter_featuredTopicWriter
    __typename
  }
  latestPosts(paging: $feedPagingOptions) {
    postPreviews {
      post {
        id
        __typename
      }
      ...PostListingItemFeed_postPreview
      __typename
    }
    pagingInfo {
      next {
        limit
        to
        __typename
      }
      __typename
    }
    __typename
  }
  __typename
}

fragment TopicLandingFeaturedStory_post on Post {
  ...FeaturedPostPreview_post
  ...PostListingItemPreview_post
  ...PostListingItemBylineWithAvatar_post
  ...PostListingItemImage_post
  ...PostPresentationTracker_post
  __typename
}

fragment FeaturedPostPreview_post on Post {
  id
  title
  mediumUrl
  previewContent {
    subtitle
    isFullContent
    __typename
  }
  __typename
}

fragment PostListingItemBylineWithAvatar_post on Post {
  creator {
    username
    name
    id
    imageId
    mediumMemberAt
    __typename
  }
  isLocked
  readingTime
  updatedAt
  statusForCollection
  collection {
    id
    name
    ...collectionUrl_collection
    __typename
  }
  __typename
}

fragment FeaturedWriter_featuredTopicWriter on FeaturedTopicWriter {
  user {
    id
    username
    name
    bio
    ...UserAvatar_user
    ...UserFollowButton_user
    __typename
  }
  posts {
    postPreviews {
      ...PostListingItemFeaturedWriter_postPreview
      __typename
    }
    __typename
  }
  __typename
}

fragment UserAvatar_user on User {
  username
  id
  name
  imageId
  mediumMemberAt
  __typename
}

fragment UserFollowButton_user on User {
  ...UserFollowButtonSignedIn_user
  ...UserFollowButtonSignedOut_user
  __typename
}

fragment UserFollowButtonSignedIn_user on User {
  id
  isFollowing
  __typename
}

fragment UserFollowButtonSignedOut_user on User {
  id
  ...SusiClickable_user
  __typename
}

fragment SusiClickable_user on User {
  ...SusiContainer_user
  __typename
}

fragment SusiContainer_user on User {
  ...SignInContainer_user
  ...SignUpOptions_user
  __typename
}

fragment SignInContainer_user on User {
  ...SignInOptions_user
  __typename
}

fragment SignInOptions_user on User {
  id
  name
  __typename
}

fragment SignUpOptions_user on User {
  id
  name
  __typename
}

fragment PostListingItemFeaturedWriter_postPreview on PostPreview {
  postId
  post {
    readingTime
    id
    mediumUrl
    title
    ...PostListingItemImage_post
    ...PostPresentationTracker_post
    __typename
  }
  __typename
}

fragment TopicLandingRelatedTopics_topic on Topic {
  relatedTopics {
    topic {
      name
      slug
      __typename
    }
    __typename
  }
  __typename
}

fragment TopicLandingPopular_posts on Topic {
  name
  popularPosts(paging: $sidebarPagingOptions) {
    postPreviews {
      post {
        ...PostListingItemSidebar_post
        __typename
      }
      __typename
    }
    __typename
  }
  __typename
}

fragment PostListingItemSidebar_post on Post {
  id
  mediumUrl
  title
  readingTime
  ...PostListingItemImage_post
  ...PostPresentationTracker_post
  __typename
}',
        );


        for($i=0;$i<count($custom_header);$i++){

            echo $custom_header[$i].'<br />';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($custom_header[$i]));
            $server_output = curl_exec ($ch);
            curl_close ($ch);

            echo '<div style="font-weight: bold; color:#FF0000;">'.$server_output.'</div>';
            echo '<hr />';

        }

    }


    //Lists players
    function play_coin($en_id)
    {

        $session_en = superpower_assigned();

        //Do we have any mass action to process here?
        if (superpower_assigned(10967) && isset($_POST['mass_action_en_id']) && isset($_POST['mass_value1_'.$_POST['mass_action_en_id']]) && isset($_POST['mass_value2_'.$_POST['mass_action_en_id']])) {

            //Process mass action:
            $process_mass_action = $this->PLAY_model->en_mass_update($en_id, intval($_POST['mass_action_en_id']), $_POST['mass_value1_'.$_POST['mass_action_en_id']], $_POST['mass_value2_'.$_POST['mass_action_en_id']], $session_en['en_id']);

            //Pass-on results to UI:
            $message = '<div class="alert '.( $process_mass_action['status'] ? 'alert-success' : 'alert-danger' ).'" role="alert">'.$process_mass_action['message'].'</div>';

        } else {

            //No mass action, just viewing...
            //Update session count and log link:
            $message = null; //No mass-action message to be appended...

            $new_order = ( $this->session->userdata('session_page_count') + 1 );
            $this->session->set_userdata('session_page_count', $new_order);
            $this->READ_model->ln_create(array(
                'ln_owner_play_id' => $session_en['en_id'],
                'ln_type_play_id' => 4994, //Trainer Opened Player
                'ln_child_play_id' => $en_id,
                'ln_order' => $new_order,
            ));

        }

        //Validate player ID and fetch data:
        $ens = $this->PLAY_model->en_fetch(array(
            'en_id' => $en_id,
        ));

        if (count($ens) < 1) {
            return redirect_message('/play', '<div class="alert alert-danger" role="alert">Invalid Player ID</div>');
        }

        //Load views:
        $this->load->view('header', array(
            'title' => $ens[0]['en_name'] . ' | PLAY',
            'flash_message' => $message, //Possible mass-action message for UI:
        ));
        $this->load->view('play/play_coin', array(
            'player' => $ens[0],
            'session_en' => $session_en,
        ));
        $this->load->view('footer');

    }

    function php_info(){
        echo phpinfo();
    }

    function my_session()
    {
        echo_json($this->session->all_userdata());
    }


    function load_leaderboard(){

        //Fetch top users per each direction
        $session_en = superpower_assigned();
        $load_max = config_var(11064);
        $show_max = config_var(11986);
        $en_all_2738 = $this->config->item('en_all_2738'); //MENCH
        $en_all_4463 = $this->config->item('en_all_4463'); //GLOSSARY

        //Create FILTERS:
        $filters_idea = array(
            'in_status_play_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Idea Statuses Public
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_12273')) . ')' => null, //IDEA COIN
        );
        $filters_read = array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //READ COIN
        );

        $start_date = null;
        /*
        if(1){ //Weekly

            //Week always starts on Monday:
            if(date('D') === 'Mon'){
                //Today is Monday:
                $start_date = date("Y-m-d");
            } else {
                $start_date = date("Y-m-d", strtotime('previous monday'));
            }
            $filters_idea['ln_timestamp >='] = $start_date.' 00:00:00'; //From beginning of the day
        }
        */

        //Fetch leaderboard:
        $idea_player_coins = $this->READ_model->ln_fetch($filters_idea, array('en_parent','in_child'), $load_max, 0, array('total_coins' => 'DESC'), 'COUNT(ln_id) as total_coins, en_name, en_icon, en_id', 'en_id, en_name, en_icon');


        echo '<table id="leaderboard" class="table table-sm table-striped tablepadded">';

        //Start with top Players:
        foreach ($idea_player_coins as $count=>$ln) {

            if($count==$show_max){

                echo '<tr class="see_more_who"><td colspan="3"><span class="icon-block"><i class="far fa-search-plus play"></i></span><a href="javascript:void(0);" onclick="$(\'.see_more_who\').toggleClass(\'hidden\')"><b class="montserrat play" style="text-decoration: none !important;">TOP '.$load_max.'</b></a></td></tr>';

                echo '<tr class="see_more_who"></tr>';

            }

            $first_name = one_two_explode('',' ',$ln['en_name']);

            //COUNT this PLAYERS total READ COINS:
            $read_coins = $this->READ_model->ln_fetch(array(
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //READ COIN
                'ln_owner_play_id' => $ln['en_id'],
            ), array(), 1, 0, array(), 'COUNT(ln_id) as total_coins');

            echo '<tr class="'.( $count<$show_max ? '' : 'see_more_who hidden').'">';


            //PLAY
            echo '<td class="play MENCHcolumn1"><span class="icon-block icon_en_'.$ln['en_id'].'">'.echo_en_icon($ln['en_icon']).'</span>'.'<a href="/play/'.$ln['en_id'].'" class="montserrat play en_name_first_'.$ln['en_id'].'">'.$first_name.'</a>'.echo_rank($count+1).'</td>';


            //IDEA
            echo '<td class="idea MENCHcolumn2">'.

                ( $session_en

                    ? '<a href="/oii?ln_status_play_id='.join(',', $this->config->item('en_ids_7359')).'&ln_type_play_id='.join(',', $this->config->item('en_ids_12273')).'&ln_parent_play_id='.$ln['en_id'].( $start_date ? '&start_range='.$start_date : '' ).'" class="montserrat idea"><span class="icon-block">'.$en_all_2738[4535]['m_icon'].'</span>'.echo_number($ln['total_coins']).'</a>'

                    : '<span class="montserrat idea"><span class="icon-block">'.$en_all_2738[4535]['m_icon'].'</span>'.echo_number($ln['total_coins']).'</span>'

                )

                . '</td>';


            //READ
            echo '<td class="read MENCHcolumn3">'.( $session_en ? '<a href="/oii?ln_status_play_id='.join(',', $this->config->item('en_ids_7359')).'&ln_type_play_id='.join(',', $this->config->item('en_ids_6255')).'&ln_owner_play_id='.$ln['en_id'].( $start_date ? '&start_range='.$start_date : $start_date ).'" class="montserrat read"><span class="icon-block">'.$en_all_2738[6205]['m_icon'].'</span>'.echo_number($read_coins[0]['total_coins']).'</a>' : '<span class="montserrat read"><span class="icon-block">'.$en_all_2738[6205]['m_icon'].'</span>'.echo_number($read_coins[0]['total_coins']).'</span>' ).'</td>';

            echo '</tr>';


        }

        //Then show top readers to hit the target $load_max
        //TODO retire this once we hit 100+ Players, which means no more need for readers
        $players_found = count($idea_player_coins);
        $show_readers = $load_max - $players_found;
        if($show_readers > 0){

            $read_coins = $this->READ_model->ln_fetch($filters_read, array('en_owner'), $show_readers, 0, array('total_coins' => 'DESC'), 'COUNT(ln_id) as total_coins, en_name, en_icon, en_id', 'en_id, en_name, en_icon');

            foreach ($read_coins as $count=>$ln) {

                $count += $players_found;

                if($count==$show_max){

                    echo '<tr class="see_more_who"><td colspan="3"><span class="icon-block"><i class="far fa-search-plus play"></i></span><a href="javascript:void(0);" onclick="$(\'.see_more_who\').toggleClass(\'hidden\')"><b class="montserrat play" style="text-decoration: none !important;">TOP '.$load_max.'</b></a></td></tr>';

                    echo '<tr class="see_more_who"></tr>';

                }

                $first_name = one_two_explode('',' ',$ln['en_name']);

                echo '<tr class="'.( $count<$show_max ? '' : 'see_more_who hidden').'">';

                //PLAY
                echo '<td class="play MENCHcolumn1"><span class="icon-block">'.echo_en_icon($ln['en_icon']).'</span><a href="/play/'.$ln['en_id'].'" class="montserrat play">'.$first_name.'</a>'.echo_rank($count+1).'</td>';

                //IDEA
                echo '<td class="idea MENCHcolumn2"></td>';

                //READ
                echo '<td class="read MENCHcolumn3">'.( $session_en ? '<a href="/oii?ln_status_play_id='.join(',', $this->config->item('en_ids_7359')).'&ln_type_play_id='.join(',', $this->config->item('en_ids_6255')).'&ln_owner_play_id='.$ln['en_id'].( $start_date ? '&start_range='.$start_date : $start_date ).'" class="montserrat read"><span class="icon-block">'.$en_all_2738[6205]['m_icon'].'</span>'.echo_number($ln['total_coins']).'</a>' : '<span class="montserrat read"><span class="icon-block">'.$en_all_2738[6205]['m_icon'].'</span>'.echo_number($ln['total_coins']).'</span>' ).'</td>';

                echo '</tr>';

            }
        }



        echo '</table>';

    }

    function stats(){
        $this->load->view('header', array(
            'title' => 'Play Stats',
        ));
        $this->load->view('play/play_stats');
        $this->load->view('footer');
    }

    function sign($in_id = 0){

        //Check to see if they are already logged in?
        $session_en = superpower_assigned();
        if (isset($session_en['en_id'])) {
            //Lead trainer and above, go to console:
            if($in_id > 0){
                return redirect_message('/' . $in_id);
            } else {
                return redirect_message('/play');
            }
        }

        $en_all_11035 = $this->config->item('en_all_11035'); //MENCH PLAYER NAVIGATION
        $this->load->view('header', array(
            'hide_header' => 1,
            'title' => $en_all_11035[4269]['m_name'],
        ));
        $this->load->view('play/play_sign', array(
            'referrer_in_id' => intval($in_id),
            'contrainer_class_append' => intval($in_id),
        ));
        $this->load->view('footer');

    }


    function en_add_source_paste_url()
    {

        /*
         *
         * Validates the input URL to be added as a new source player
         *
         * */

        $session_en = superpower_assigned();
        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Expired Session or Missing Superpower',
                'url_player' => array(),
            ));
        }

        //All seems good, fetch URL:
        $url_player = $this->PLAY_model->en_sync_url($_POST['input_url']);

        if (!$url_player['status']) {
            //Oooopsi, we had some error:
            return echo_json(array(
                'status' => 0,
                'message' => $url_player['message'],
            ));
        }

        //Return results:
        return echo_json(array(
            'status' => 1,
            'player_domain_ui' => '<span class="en_mini_ui_icon">' . (isset($url_player['en_domain']['en_icon']) && strlen($url_player['en_domain']['en_icon']) > 0 ? $url_player['en_domain']['en_icon'] : detect_fav_icon($url_player['url_clean_domain'], true)) . '</span> ' . (isset($url_player['en_domain']['en_name']) ? $url_player['en_domain']['en_name'] . ' <a href="/play/' . $url_player['en_domain']['en_id'] . '" class="underdot" data-toggle="tooltip" title="Click to open domain player in a new windows" data-placement="top" target="_blank">@' . $url_player['en_domain']['en_id'] . '</a>' : $url_player['url_domain_name'] . ' [<span data-toggle="tooltip" title="Domain player not yet added" data-placement="top">New</span>]'),
            'js_url_player' => $url_player,
        ));

    }


    function en_ln_type_preview()
    {

        if (!isset($_POST['ln_content']) || !isset($_POST['ln_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing inputs',
            ));
        }

        //Will Contain every possible Player Link Connector:
        $en_all_4592 = $this->config->item('en_all_4592');

        //See what this is:
        $detected_ln_type = ln_detect_type($_POST['ln_content']);

        if (!$detected_ln_type['status'] && isset($detected_ln_type['url_already_existed']) && $detected_ln_type['url_already_existed']) {

            //See if this is duplicate to either link:
            $en_lns = $this->READ_model->ln_fetch(array(
                'ln_id' => $_POST['ln_id'],
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4537')) . ')' => null, //Player URL Links
            ));

            //Are they both different?
            if (count($en_lns) < 1 || ($en_lns[0]['ln_parent_play_id'] != $detected_ln_type['en_url']['en_id'] && $en_lns[0]['ln_child_play_id'] != $detected_ln_type['en_url']['en_id'])) {
                //return error:
                return echo_json($detected_ln_type);
            }

        }

        return echo_json(array(
            'status' => 1,
            'html_ui' => '<a href="/play/' . $detected_ln_type['ln_type_play_id'] . '" style="font-weight: bold;" data-toggle="tooltip" data-placement="top" title="' . $en_all_4592[$detected_ln_type['ln_type_play_id']]['m_desc'] . '">' . $en_all_4592[$detected_ln_type['ln_type_play_id']]['m_icon'] . ' ' . $en_all_4592[$detected_ln_type['ln_type_play_id']]['m_name'] . '</a>',
            'en_link_preview' => echo_url_type_4537($_POST['ln_content'], $detected_ln_type['ln_type_play_id']),
        ));
    }


    function en_save_file_upload()
    {

        //Authenticate Trainer:
        $session_en = superpower_assigned(10967);
        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Expired Session or Missing Superpower',
            ));
        } elseif (!isset($_POST['upload_type']) || !in_array($_POST['upload_type'], array('file', 'drop'))) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Unknown upload type.',
            ));
        } elseif (!isset($_FILES[$_POST['upload_type']]['tmp_name']) || strlen($_FILES[$_POST['upload_type']]['tmp_name']) == 0 || intval($_FILES[$_POST['upload_type']]['size']) == 0) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Unknown error while trying to save file.',
            ));
        } elseif ($_FILES[$_POST['upload_type']]['size'] > (config_var(11063) * 1024 * 1024)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'File is larger than ' . config_var(11063) . ' MB.',
            ));
        }


        //Attempt to save file locally:
        $file_parts = explode('.', $_FILES[$_POST['upload_type']]["name"]);
        $temp_local = "application/cache/temp_files/" . md5($file_parts[0] . $_FILES[$_POST['upload_type']]["type"] . $_FILES[$_POST['upload_type']]["size"]) . '.' . $file_parts[(count($file_parts) - 1)];
        move_uploaded_file($_FILES[$_POST['upload_type']]['tmp_name'], $temp_local);


        //Attempt to store in Mench Cloud on Amazon S3:
        if (isset($_FILES[$_POST['upload_type']]['type']) && strlen($_FILES[$_POST['upload_type']]['type']) > 0) {
            $mime = $_FILES[$_POST['upload_type']]['type'];
        } else {
            $mime = mime_content_type($temp_local);
        }

        //Return the CDN uploader results:
        return echo_json(upload_to_cdn($temp_local, 0, $_FILES[$_POST['upload_type']], true));

    }


    function en_load_next_page()
    {

        $items_per_page = config_var(11064);
        $parent_en_id = intval($_POST['parent_en_id']);
        $en_focus_filter = intval($_POST['en_focus_filter']);
        $page = intval($_POST['page']);
        $filters = array(
            'ln_parent_play_id' => $parent_en_id,
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
            'en_status_play_id IN (' . join(',', ( $en_focus_filter<0 /* Remove Filters*/ ? $this->config->item('en_ids_7358') /* Player Statuses Active */ : array($en_focus_filter) /* This specific filter*/ )) . ')' => null,
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Link Statuses Active
        );

        //Fetch & display next batch of children:
        $child_players = $this->READ_model->ln_fetch($filters, array('en_child'), $items_per_page, ($page * $items_per_page), array(
            'ln_order' => 'ASC',
            'en_name' => 'ASC'
        ));

        foreach ($child_players as $en) {
            echo echo_en($en,false);
        }

        //Count total children:
        $child_players_count = $this->READ_model->ln_fetch($filters, array('en_child'), 0, 0, array(), 'COUNT(ln_id) as totals');

        //Do we need another load more button?
        if ($child_players_count[0]['totals'] > (($page * $items_per_page) + count($child_players))) {
            echo echo_en_load_more(($page + 1), $items_per_page, $child_players_count[0]['totals']);
        }

    }


    function en_add_or_link()
    {

        //Auth user and check required variables:
        $session_en = superpower_assigned(10967);

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Expired Session or Missing Superpower',
            ));
        } elseif (intval($_POST['en_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Parent Player',
            ));
        } elseif (!isset($_POST['is_parent'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing Player Link Direction',
            ));
        } elseif (!isset($_POST['en_existing_id']) || !isset($_POST['en_new_string']) || (intval($_POST['en_existing_id']) < 1 && strlen($_POST['en_new_string']) < 1)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Either New Player ID or Name is required',
            ));
        }

        //Validate parent player:
        $current_en = $this->PLAY_model->en_fetch(array(
            'en_id' => $_POST['en_id'],
        ));
        if (count($current_en) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid parent player ID',
            ));
        }


        //Set some variables:
        $_POST['is_parent'] = intval($_POST['is_parent']);
        $_POST['en_existing_id'] = intval($_POST['en_existing_id']);
        $linking_to_existing_u = false;
        $is_url_input = false;
        $ur1 = array();

        //Are we linking to an existing player?
        if (intval($_POST['en_existing_id']) > 0) {

            //Validate this existing player:
            $ens = $this->PLAY_model->en_fetch(array(
                'en_id' => $_POST['en_existing_id'],
                'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7358')) . ')' => null, //Player Statuses Active
            ));

            if (count($ens) < 1) {
                return echo_json(array(
                    'status' => 0,
                    'message' => 'Invalid active player',
                ));
            }

            //All good, assign:
            $player_new = $ens[0];
            $linking_to_existing_u = true;

        } else {

            //We are creating a new player OR adding a URL...

            //Is this a URL?
            if (filter_var($_POST['en_new_string'], FILTER_VALIDATE_URL)) {

                //Digest URL to see what type it is and if we have any errors:
                $url_player = $this->PLAY_model->en_sync_url($_POST['en_new_string']);
                if (!$url_player['status']) {
                    return echo_json($url_player);
                }

                //Is this a root domain? Add to domains if so:
                if($url_player['url_is_root']){

                    //Link to domains parent:
                    $player_new = array('en_id' => 1326);

                    //Update domain to stay synced:
                    $_POST['en_new_string'] = $url_player['url_clean_domain'];

                } else {

                    //Let's first find/add the domain:
                    $domain_player = $this->PLAY_model->en_sync_domain($_POST['en_new_string'], $session_en['en_id']);

                    //Link to this player:
                    $player_new = $domain_player['en_domain'];
                }

            } else {

                //Create player:
                $added_en = $this->PLAY_model->en_verify_create($_POST['en_new_string'], $session_en['en_id']);
                if(!$added_en['status']){
                    //We had an error, return it:
                    return echo_json($added_en);
                } else {
                    //Assign new player:
                    $player_new = $added_en['en'];
                }

            }

        }


        //We need to check to ensure this is not a duplicate link if linking to an existing player:
        $ur2 = array();

        if (!$is_url_input) {

            //Add links only if not already added by the URL function:
            if ($_POST['is_parent']) {

                $ln_child_play_id = $current_en[0]['en_id'];
                $ln_parent_play_id = $player_new['en_id'];

            } else {

                $ln_child_play_id = $player_new['en_id'];
                $ln_parent_play_id = $current_en[0]['en_id'];

            }


            if (isset($url_player['url_is_root']) && $url_player['url_is_root']) {

                $ln_type_play_id = 4256; //Generic URL (Domains always are generic)
                $ln_content = $url_player['cleaned_url'];

            } elseif (isset($domain_player['en_domain'])) {

                $ln_type_play_id = $url_player['ln_type_play_id'];
                $ln_content = $url_player['cleaned_url'];

            } else {

                $ln_type_play_id = 4230; //Raw
                $ln_content = null;

            }

            // Link to new OR existing player:
            $ur2 = $this->READ_model->ln_create(array(
                'ln_owner_play_id' => $session_en['en_id'],
                'ln_type_play_id' => $ln_type_play_id,
                'ln_content' => $ln_content,
                'ln_child_play_id' => $ln_child_play_id,
                'ln_parent_play_id' => $ln_parent_play_id,
            ));
        }

        //Fetch latest version:
        $ens_latest = $this->PLAY_model->en_fetch(array(
            'en_id' => $player_new['en_id'],
            'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7358')) . ')' => null, //Player Statuses Active
        ));
        if(!count($ens_latest)){
            return echo_json(array(
                'status' => 0,
                'message' => 'Failed to create/fetch new player',
            ));
        }

        //Return newly added or linked player:
        return echo_json(array(
            'status' => 1,
            'en_new_status' => $ens_latest[0]['en_status_play_id'],
            'en_new_echo' => echo_en(array_merge($ens_latest[0], $ur2), $_POST['is_parent']),
        ));

    }

    function en_count_to_be_removed_links()
    {

        if (!isset($_POST['en_id']) || intval($_POST['en_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Player ID',
            ));
        }

        //Simply counts the links for a given player:
        $all_en_links = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Link Statuses Active
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
            '(ln_child_play_id = ' . $_POST['en_id'] . ' OR ln_parent_play_id = ' . $_POST['en_id'] . ')' => null,
        ), array(), 0);

        return echo_json(array(
            'status' => 1,
            'message' => 'Success',
            'en_link_count' => count($all_en_links),
        ));

    }



    function toggle_superpower($superpower_en_id){

        //Toggles the advance session variable for the trainer on/off for logged-in trainers:
        $session_en = superpower_assigned();
        $superpower_en_id = intval($superpower_en_id);
        $en_all_10957 = $this->config->item('en_all_10957');

        if(!$session_en){

            return echo_json(array(
                'status' => 0,
                'message' => 'Expired Session or Missing Superpower',
            ));

        } elseif(!superpower_assigned($superpower_en_id)){

            //Access not authorized:
            return echo_json(array(
                'status' => 0,
                'message' => 'You are not assigned to the superpowers of '.$en_all_10957[$superpower_en_id]['m_icon'],
            ));

        }

        //Figure out new toggle state:
        $session_data = $this->session->all_userdata();

        if(in_array($superpower_en_id, $session_data['session_superpowers_activated'])){
            //Already there, turn it off:
            $session_data['session_superpowers_activated'] = array_diff($session_data['session_superpowers_activated'], array($superpower_en_id));
            $toggled_setting = 'DEACTIVATED';
        } else {
            //Not there, turn it on:
            array_push($session_data['session_superpowers_activated'], $superpower_en_id);
            $toggled_setting = 'ACTIVATED';
        }


        //Update Session:
        $this->session->set_userdata($session_data);


        //Log Link:
        $this->READ_model->ln_create(array(
            'ln_owner_play_id' => $session_en['en_id'],
            'ln_type_play_id' => 5007, //TOGGLE SUPERPOWER
            'ln_parent_play_id' => $superpower_en_id,
            'ln_content' => 'SUPERPOWER '.$toggled_setting, //To be used when trainer logs in again
        ));

        //Return to JS function:
        return echo_json(array(
            'status' => 1,
            'message' => 'Success',
        ));
    }




    function en_modify_save()
    {

        //Auth user and check required variables:
        $session_en = superpower_assigned(10967);
        $success_message = 'Saved'; //Default, might change based on what we do...

        //Fetch current data:
        $ens = $this->PLAY_model->en_fetch(array(
            'en_id' => intval($_POST['en_id']),
        ));

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Expired Session or Missing Superpower',
            ));
        } elseif (!isset($_POST['en_id']) || intval($_POST['en_id']) < 1 || !(count($ens) == 1)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid ID',
            ));
        } elseif (!isset($_POST['en_focus_id']) || intval($_POST['en_focus_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Focus ID',
            ));
        } elseif (!isset($_POST['en_name']) || strlen($_POST['en_name']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing name',
            ));
        } elseif (!isset($_POST['en_status_play_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing status',
            ));
        } elseif (!isset($_POST['ln_id']) || !isset($_POST['ln_content']) || !isset($_POST['ln_status_play_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing player link data',
            ));
        } elseif (strlen($_POST['en_name']) > config_var(11072)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Name is longer than the allowed ' . config_var(11072) . ' characters.',
            ));
        } elseif (strlen($_POST['en_name']) < config_var(12232)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Name is shorter than the minimum ' . config_var(12232) . ' characters.',
            ));
        } elseif(!isset($_POST['en_icon']) || !is_valid_icon($_POST['en_icon'])){
            //Check if valid icon:
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid icon: '. is_valid_icon(null, true),
            ));
        }

        $remove_redirect_url = null;
        $remove_from_ui = 0;
        $js_ln_type_play_id = 0; //Detect link type based on content

        //Prepare data to be updated:
        $en_update = array(
            'en_name' => trim($_POST['en_name']),
            'en_icon' => trim($_POST['en_icon']),
            'en_status_play_id' => intval($_POST['en_status_play_id']),
        );

        //Is this being removed?
        if (!in_array($en_update['en_status_play_id'], $this->config->item('en_ids_7358') /* Player Statuses Active */) && !($en_update['en_status_play_id'] == $ens[0]['en_status_play_id'])) {


            //Make sure player is not referenced in key DB reference fields:
            $en_count_references = en_count_references($_POST['en_id']);
            if(count($en_count_references) > 0){

                $en_all_6194 = $this->config->item('en_all_6194');

                //Construct the message:
                $error_message = 'Cannot be removed because player is referenced as ';
                foreach($en_count_references as $en_id=>$en_count){
                    $error_message .= $en_all_6194[$en_id]['m_name'].' '.echo_number($en_count).' times ';
                }

                return echo_json(array(
                    'status' => 0,
                    'message' => $error_message,
                ));
            }



            //Count player references in Idea Notes:
            $messages = $this->READ_model->ln_fetch(array(
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Link Statuses Active
                'in_status_play_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //Idea Statuses Active
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4485')) . ')' => null, //All Idea Notes
                'ln_parent_play_id' => $_POST['en_id'],
            ), array('in_child'), 0, 0, array('ln_order' => 'ASC'));

            //Assume no merge:
            $merged_ens = array();

            //See if we have merger player:
            if (strlen($_POST['en_merge']) > 0) {

                //Yes, validate this player:

                //Validate the input for updating linked Idea:
                $merger_en_id = 0;
                if (substr($_POST['en_merge'], 0, 1) == '@') {
                    $parts = explode(' ', $_POST['en_merge']);
                    $merger_en_id = intval(str_replace('@', '', $parts[0]));
                }

                if ($merger_en_id < 1) {

                    return echo_json(array(
                        'status' => 0,
                        'message' => 'Unrecognized merger player [' . $_POST['en_merge'] . ']',
                    ));

                } elseif ($merger_en_id == $_POST['en_id']) {

                    return echo_json(array(
                        'status' => 0,
                        'message' => 'Cannot merge player into itself',
                    ));

                } else {

                    //Finally validate merger player:
                    $merged_ens = $this->PLAY_model->en_fetch(array(
                        'en_id' => $merger_en_id,
                        'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7358')) . ')' => null, //Player Statuses Active
                    ));
                    if (count($merged_ens) == 0) {
                        return echo_json(array(
                            'status' => 0,
                            'message' => 'Could not find player @' . $merger_en_id,
                        ));
                    }

                }

            } elseif(count($messages) > 0){

                //Cannot delete this player until Idea references are removed:
                return echo_json(array(
                    'status' => 0,
                    'message' => 'You can remove player after removing all its idea note references',
                ));

            }

            //Remove/merge player links:
            $_POST['ln_id'] = 0; //Do not consider the link as the player is being Removed
            $remove_from_ui = 1; //Removing player
            $merger_en_id = (count($merged_ens) > 0 ? $merged_ens[0]['en_id'] : 0);
            $links_adjusted = $this->PLAY_model->en_unlink($_POST['en_id'], $session_en['en_id'], $merger_en_id);

            //Show appropriate message based on action:
            if ($merger_en_id > 0) {

                if($_POST['en_id'] == $_POST['en_focus_id'] || $merged_ens[0]['en_id'] == $_POST['en_focus_id']){
                    //Player is being Removed and merged into another player:
                    $remove_redirect_url = '/play/' . $merged_ens[0]['en_id'];
                }

                $success_message = 'Player removed and merged its ' . $links_adjusted . ' links here';

            } else {

                if($_POST['en_id'] == $_POST['en_focus_id']){
                    //Fetch parents to redirect to:
                    $en__parents = $this->READ_model->ln_fetch(array(
                        'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
                        'ln_child_play_id' => $_POST['en_id'],
                        'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Link Statuses Active
                        'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7358')) . ')' => null, //Player Statuses Active
                    ), array('en_parent'), 1);

                    $remove_redirect_url = '/play/' . ( count($en__parents) ? $en__parents[0]['en_id'] : $session_en['en_id'] );
                }

                //Display proper message:
                $success_message = 'Player and its ' . $links_adjusted . ' links removed successfully';

            }

        }


        if (intval($_POST['ln_id']) > 0) { //DO we have a link to update?

            //Yes, first validate player link:
            $en_lns = $this->READ_model->ln_fetch(array(
                'ln_id' => $_POST['ln_id'],
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Link Statuses Active
            ));
            if (count($en_lns) < 1) {
                return echo_json(array(
                    'status' => 0,
                    'message' => 'Invalid Player READ ID',
                ));
            }


            //Status change?
            if($en_lns[0]['ln_status_play_id']!=$_POST['ln_status_play_id']){

                if (in_array($_POST['ln_status_play_id'], $this->config->item('en_ids_7360') /* Link Statuses Active */)) {
                    $ln_status_play_id = 10656; //Player Link Iterated Status
                } else {
                    $remove_from_ui = 1;
                    $ln_status_play_id = 10673; //Player Link Unlinked
                }

                $this->READ_model->ln_update($_POST['ln_id'], array(
                    'ln_status_play_id' => intval($_POST['ln_status_play_id']),
                ), $session_en['en_id'], $ln_status_play_id);
            }


            //Link content change?
            if ($en_lns[0]['ln_content'] == $_POST['ln_content']) {

                //Link content has not changed:
                $js_ln_type_play_id = $en_lns[0]['ln_type_play_id'];
                $ln_content = $en_lns[0]['ln_content'];

            } else {

                //Link content has changed:
                $detected_ln_type = ln_detect_type($_POST['ln_content']);

                if (!$detected_ln_type['status']) {

                    return echo_json($detected_ln_type);

                } elseif (in_array($detected_ln_type['ln_type_play_id'], $this->config->item('en_ids_4537'))) {

                    //This is a URL, validate modification:

                    if ($detected_ln_type['url_is_root']) {

                        if ($en_lns[0]['ln_parent_play_id'] == 1326) {

                            //Override with the clean domain for consistency:
                            $_POST['ln_content'] = $detected_ln_type['url_clean_domain'];

                        } else {

                            //Domains can only be added to the domain player:
                            return echo_json(array(
                                'status' => 0,
                                'message' => 'Domain URLs must link to <b>@1326 Domains</b> as their parent player',
                            ));

                        }

                    } else {

                        if ($en_lns[0]['ln_parent_play_id'] == 1326) {

                            return echo_json(array(
                                'status' => 0,
                                'message' => 'Only domain URLs can be linked to Domain player.',
                            ));

                        } elseif ($detected_ln_type['en_domain']) {
                            //We do have the domain mapped! Is this connected to the domain player as its parent?
                            if ($detected_ln_type['en_domain']['en_id'] != $en_lns[0]['ln_parent_play_id']) {
                                return echo_json(array(
                                    'status' => 0,
                                    'message' => 'Must link to <b>@' . $detected_ln_type['en_domain']['en_id'] . ' ' . $detected_ln_type['en_domain']['en_name'] . '</b> as their parent player',
                                ));
                            }
                        } else {
                            //We don't have the domain mapped, this is for sure not allowed:
                            return echo_json(array(
                                'status' => 0,
                                'message' => 'Requires a new parent player for <b>' . $detected_ln_type['url_tld'] . '</b>. Add by pasting URL into the [Add @Player] input field.',
                            ));
                        }

                    }

                }

                //Update variables:
                $ln_content = $_POST['ln_content'];
                $js_ln_type_play_id = $detected_ln_type['ln_type_play_id'];


                $this->READ_model->ln_update($_POST['ln_id'], array(
                    'ln_content' => $ln_content,
                ), $session_en['en_id'], 10657 /* Player Link Iterated Content */);


                //Also, did the link type change based on the content change?
                if($js_ln_type_play_id!=$en_lns[0]['ln_type_play_id']){
                    $this->READ_model->ln_update($_POST['ln_id'], array(
                        'ln_type_play_id' => $js_ln_type_play_id,
                    ), $session_en['en_id'], 10659 /* Player Link Iterated Type */);
                }
            }
        }

        //Now update the DB:
        $this->PLAY_model->en_update(intval($_POST['en_id']), $en_update, true, $session_en['en_id']);


        //Reset user session data if this data belongs to the logged-in user:
        if ($_POST['en_id'] == $session_en['en_id']) {
            //Re-activate Session with new data:
            $this->PLAY_model->en_activate_session($session_en, true);
        }


        if ($remove_redirect_url) {
            //Page will be refresh, set flash message to be shown after restart:
            $this->session->set_flashdata('flash_message', '<div class="alert alert-success" role="alert">' . $success_message . '</div>');
        }

        //Start return array:
        $return_array = array(
            'status' => 1,
            'message' => '<i class="fas fa-check"></i> ' . $success_message,
            'remove_from_ui' => $remove_from_ui,
            'remove_redirect_url' => $remove_redirect_url,
            'js_ln_type_play_id' => intval($js_ln_type_play_id),
        );

        if (intval($_POST['ln_id']) > 0) {

            //Fetch player link:
            $lns = $this->READ_model->ln_fetch(array(
                'ln_id' => $_POST['ln_id'],
            ), array('en_owner'));

            //Prep last updated:
            $return_array['ln_content'] = echo_ln_urls($ln_content, $js_ln_type_play_id);
            $return_array['ln_content_final'] = $ln_content; //In case content was updated

        }

        //Show success:
        return echo_json($return_array);

    }




    function en_review_metadata($en_id){
        //Fetch Idea:
        $ens = $this->PLAY_model->en_fetch(array(
            'en_id' => $en_id,
        ));
        if(count($ens) > 0){
            echo_json(unserialize($ens[0]['en_metadata']));
        } else {
            echo 'Player @'.$en_id.' not found!';
        }
    }

    function en_fetch_canonical_url(){

        //Auth user and check required variables:
        $session_en = superpower_assigned();

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Expired Session or Missing Superpower',
            ));
        } elseif (!isset($_POST['search_url']) || !filter_var($_POST['search_url'], FILTER_VALIDATE_URL)) {
            //This string was incorrectly detected as a URL by JS, return not found:
            return echo_json(array(
                'status' => 1,
                'url_already_existed' => 0,
            ));
        }

        //Fetch URL:
        $url_player = $this->PLAY_model->en_sync_url($_POST['search_url']);

        if($url_player['url_already_existed']){
            return echo_json(array(
                'status' => 1,
                'url_already_existed' => 1,
                'algolia_object' => update_algolia('en', $url_player['en_url']['en_id'], 1),
            ));
        } else {
            return echo_json(array(
                'status' => 1,
                'url_already_existed' => 0,
            ));
        }
    }


    function singin_check_psid($psid){

        if (!isset($_GET['sr']) || !parse_signed_request($_GET['sr'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Failed to authenticate your origin',
            ));
        }

        //Messenger Webview, authenticate PSID:
        $session_en = $this->PLAY_model->en_messenger_auth($psid);

        //Make sure we found them:
        if ($session_en) {

            //Activate Session:
            $this->PLAY_model->en_activate_session($session_en, false, 1);

            //Set message before refreshing:
            $this->session->set_flashdata('flash_message', '<div class="alert alert-success" role="alert">Signed-in from Messenger</div>');

            return echo_json(array(
                'status' => 1,
                'message' => 'Missing user ID',
            ));

        } else {

            return echo_json(array(
                'status' => 0,
                'message' => 'Failed to authenticate PSID',
            ));

        }

    }



    function singin_check_password(){

        if (!isset($_POST['login_en_id']) || intval($_POST['login_en_id'])<1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing user ID',
            ));
        } elseif (!isset($_POST['input_password']) || strlen($_POST['input_password']) < config_var(11066)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Password',
            ));
        } elseif (!isset($_POST['referrer_url'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing referrer URL',
            ));
        } elseif (!isset($_POST['referrer_in_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing idea referrer',
            ));
        }



        //Validaye user ID
        $ens = $this->PLAY_model->en_fetch(array(
            'en_id' => $_POST['login_en_id'],
        ));
        if (!in_array($ens[0]['en_status_play_id'], $this->config->item('en_ids_7357') /* Player Statuses Public */)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Your account player is not public. Contact us to adjust your account.',
            ));
        }

        //Authenticate password:
        $user_passwords = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id' => 4255, //Text
            'ln_parent_play_id' => 3286, //Password
            'ln_child_play_id' => $ens[0]['en_id'],
        ));
        if (count($user_passwords) == 0) {
            //They do not have a password assigned yet!
            return echo_json(array(
                'status' => 0,
                'message' => 'An active login password has not been assigned to your account yet. You can assign a new password using the Forgot Password Button.',
            ));
        } elseif (!in_array($user_passwords[0]['ln_status_play_id'], $this->config->item('en_ids_7359') /* Link Statuses Public */)) {
            //They do not have a password assigned yet!
            return echo_json(array(
                'status' => 0,
                'message' => 'Password link is not public. Contact us to adjust your account.',
            ));
        } elseif ($user_passwords[0]['ln_content'] != hash('sha256', $this->config->item('cred_password_salt') . $_POST['input_password'] . $ens[0]['en_id'])) {
            //Bad password
            return echo_json(array(
                'status' => 0,
                'message' => 'Incorrect password',
            ));
        }


        //All good...

        //Was there a Idea to read?
        if(intval($_POST['referrer_in_id']) > 0){
            //Add this Idea to their READING LIST:
            $this->READ_model->read_add($ens[0]['en_id'], $_POST['referrer_in_id']);
        }


        //Assign session & log link:
        $this->PLAY_model->en_activate_session($ens[0]);


        if (intval($_POST['referrer_in_id']) > 0) {
            $login_url = '/'.$_POST['referrer_in_id'];
        } elseif (isset($_POST['referrer_url']) && strlen($_POST['referrer_url']) > 0) {
            $login_url = urldecode($_POST['referrer_url']);
        } else {
            $login_url = '/';
        }

        return echo_json(array(
            'status' => 1,
            'login_url' => $login_url,
        ));

    }



    function sign_reset_password_apply()
    {

        //This function updates the user's new password as requested via a password reset:
        if (!isset($_POST['ln_id']) || intval($_POST['ln_id']) < 1 || !isset($_POST['input_email']) || strlen($_POST['input_email']) < 1 || !isset($_POST['input_password'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing core data',
            ));
        } elseif (strlen($_POST['input_password']) < config_var(11066)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Must be longer than '.config_var(11066).' characters',
            ));
        } else {

            //Validate READ ID and matching email:
            $validate_links = $this->READ_model->ln_fetch(array(
                'ln_id' => $_POST['ln_id'],
                'ln_content' => $_POST['input_email'],
                'ln_type_play_id' => 7563, //User Signin Magic Link Email
            )); //The user making the request
            if(count($validate_links) < 1){
                //Probably already completed the reset password:
                return echo_json(array(
                    'status' => 0,
                    'message' => 'Reset password link not found',
                ));
            }

            //Validate user:
            $ens = $this->PLAY_model->en_fetch(array(
                'en_id' => $validate_links[0]['ln_owner_play_id'],
            ));
            if(count($ens) < 1){
                return echo_json(array(
                    'status' => 0,
                    'message' => 'User not found',
                ));
            }


            //Generate the password hash:
            $password_hash = hash('sha256', $this->config->item('cred_password_salt') . $_POST['input_password']. $ens[0]['en_id']);


            //Fetch their passwords to authenticate login:
            $user_passwords = $this->READ_model->ln_fetch(array(
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
                'ln_parent_play_id' => 3286, //Mench Sign In Password
                'ln_child_play_id' => $ens[0]['en_id'],
            ));

            if (count($user_passwords) > 0) {

                $detected_ln_type = ln_detect_type($password_hash);
                if (!$detected_ln_type['status']) {
                    return echo_json($detected_ln_type);
                }

                //Update existing password:
                $this->READ_model->ln_update($user_passwords[0]['ln_id'], array(
                    'ln_content' => $password_hash,
                    'ln_type_play_id' => $detected_ln_type['ln_type_play_id'],
                ), $ens[0]['en_id'], 7578 /* User Iterated Password */);

            } else {

                //Create new password link:
                $this->READ_model->ln_create(array(
                    'ln_type_play_id' => 4255, //Text link
                    'ln_content' => $password_hash,
                    'ln_parent_play_id' => 3286, //Mench Password
                    'ln_owner_play_id' => $ens[0]['en_id'],
                    'ln_child_play_id' => $ens[0]['en_id'],
                ));

            }


            //Log password reset:
            $this->READ_model->ln_create(array(
                'ln_owner_play_id' => $ens[0]['en_id'],
                'ln_type_play_id' => 7578, //User Iterated Password
                'ln_content' => $password_hash, //A copy of their password set at this time
            ));


            //Log them in:
            $ens[0] = $this->PLAY_model->en_activate_session($ens[0]);

            //Their next Idea in line:
            return echo_json(array(
                'status' => 1,
                'login_url' => '/read/next',
            ));


        }
    }


    function sign_create_account(){

        if (!isset($_POST['referrer_in_id']) || !isset($_POST['referrer_url'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing core data',
            ));
        } elseif (!isset($_POST['input_email']) || !filter_var($_POST['input_email'], FILTER_VALIDATE_EMAIL)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Email',
            ));
        } elseif (!isset($_POST['input_name']) || strlen($_POST['input_name'])<1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing name',
                'focus_input_field' => 'input_name',
            ));
        }

        //Prep inputs & validate further:
        $_POST['input_email'] =  trim(strtolower($_POST['input_email']));
        $_POST['input_name'] = trim($_POST['input_name']);
        $name_parts = explode(' ', trim($_POST['input_name']));
        if (strlen($_POST['input_name']) < config_var(12232)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Name must longer than '.config_var(12232).' characters',
                'focus_input_field' => 'input_name',
            ));
        } elseif (strlen($_POST['input_name']) > config_var(11072)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Name must be less than '.config_var(11072).' characters',
                'focus_input_field' => 'input_name',
            ));

        /*
        } elseif (!isset($name_parts[1])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'There must be a space between your your first and last name',
                'focus_input_field' => 'input_name',
            ));
        } elseif (strlen($name_parts[1])<2) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Last name must be 2 characters or longer',
                'focus_input_field' => 'input_name',
            ));
        } elseif (strlen($name_parts[0])<2) {
            return echo_json(array(
                'status' => 0,
                'message' => 'First name must be 2 characters or longer',
                'focus_input_field' => 'input_name',
            ));
        */

        } elseif (!isset($_POST['new_password']) || strlen($_POST['new_password'])<1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing password',
                'focus_input_field' => 'new_password',
            ));
        } elseif (strlen($_POST['new_password']) < config_var(11066)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'New password must be '.config_var(11066).' characters or longer',
                'focus_input_field' => 'new_password',
            ));
        }



        //All good, create new player:
        $user_en = $this->PLAY_model->en_verify_create(trim($_POST['input_name']), 0, 6181, random_player_avatar());
        if(!$user_en['status']){
            //We had an error, return it:
            return echo_json($user_en);
        }


        //Create user links:
        $this->READ_model->ln_create(array(
            'ln_type_play_id' => 4230, //Raw link
            'ln_parent_play_id' => 4430, //Mench User
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_child_play_id' => $user_en['en']['en_id'],
        ));

        /*
        $this->READ_model->ln_create(array(
            'ln_type_play_id' => 4230, //Raw link
            'ln_parent_play_id' => 11010, //FREE ACCOUNT
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_child_play_id' => $user_en['en']['en_id'],
        ));
        */

        $this->READ_model->ln_create(array(
            'ln_type_play_id' => 4230, //Raw link
            'ln_parent_play_id' => 1278, //People
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_child_play_id' => $user_en['en']['en_id'],
        ));

        $this->READ_model->ln_create(array(
            'ln_type_play_id' => 4230, //Raw link
            'ln_parent_play_id' => 12221, //Notify on EMAIL
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_child_play_id' => $user_en['en']['en_id'],
        ));

        $this->READ_model->ln_create(array(
            'ln_type_play_id' => 4230, //Raw link
            'ln_parent_play_id' => 3504, //English Language (Since everything is in English so far)
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_child_play_id' => $user_en['en']['en_id'],
        ));
        $this->READ_model->ln_create(array(
            'ln_type_play_id' => 4255, //Text link
            'ln_content' => trim(strtolower($_POST['input_email'])),
            'ln_parent_play_id' => 3288, //Mench Email
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_child_play_id' => $user_en['en']['en_id'],
        ));
        $this->READ_model->ln_create(array(
            'ln_type_play_id' => 4255, //Text link
            'ln_content' => strtolower(hash('sha256', $this->config->item('cred_password_salt') . $_POST['new_password'] . $user_en['en']['en_id'])),
            'ln_parent_play_id' => 3286, //Mench Password
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_child_play_id' => $user_en['en']['en_id'],
        ));


        //Fetch referral Idea, if any:
        if(intval($_POST['referrer_in_id']) > 0){

            //Fetch the Idea:
            $referrer_ins = $this->IDEA_model->in_fetch(array(
                'in_status_play_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Idea Statuses Public
                'in_id' => $_POST['referrer_in_id'],
            ));

            if(count($referrer_ins) > 0){
                //Add this Idea to their READING LIST:
                $this->READ_model->read_add($user_en['en']['en_id'], $_POST['referrer_in_id']);
            } else {
                //Cannot be added, likely because its not published:
                $_POST['referrer_in_id'] = 0;
            }

        } else {
            $referrer_ins = array();
        }


        ##Email Subject
        $subject =  ( count($referrer_ins) > 0 ? echo_in_title($referrer_ins[0]['in_title'], true).' with ' : 'Welcome to ' ) . 'MENCH';

        ##Email Body
        $html_message = '<div>Hi '.$name_parts[0].' 👋</div><br />';

        $html_message .= '<div>'.( count($referrer_ins) > 0 ? echo_in_title($referrer_ins[0]['in_title'], true) : 'Get started' ).':</div><br />';
        $actionplan_url = $this->config->item('base_url') . ( count($referrer_ins) > 0 ? 'actionplan/'.$referrer_ins[0]['in_id'] : '' );
        $html_message .= '<div><a href="'.$actionplan_url.'" target="_blank">' . $actionplan_url . '</a></div><br />';

        $html_message .= '<div>Connect on Messenger:</div><br />';
        $messenger_url = 'https://m.me/menchideas' . ( count($referrer_ins) > 0 ? '?ref=' . $referrer_ins[0]['in_id'] : '' ) ;
        $html_message .= '<div><a href="'.$messenger_url.'" target="_blank">' . $messenger_url . '</a></div>';
        $html_message .= '<br /><br />';
        $html_message .= '<div>Cheers,</div><br />';
        $html_message .= '<div>MENCH</div>';

        //Send Welcome Email:
        $email_log = $this->READ_model->dispatch_emails(array($_POST['input_email']), $subject, $html_message);

        //Log User Signin Joined Mench
        $invite_link = $this->READ_model->ln_create(array(
            'ln_type_play_id' => 7562, //User Signin Joined Mench
            'ln_owner_play_id' => $user_en['en']['en_id'],
            'ln_parent_idea_id' => intval($_POST['referrer_in_id']),
            'ln_metadata' => array(
                'email_log' => $email_log,
            ),
        ));

        //Assign session & log login link:
        $this->PLAY_model->en_activate_session($user_en['en']);


        if (strlen($_POST['referrer_url']) > 0) {
            $login_url = urldecode($_POST['referrer_url']);
        } elseif(intval($_POST['referrer_in_id']) > 0) {
            $login_url = '/'.$_POST['referrer_in_id'];
        } else {
            //Go to home page and let them continue from there:
            $login_url = '/play';
        }

        return echo_json(array(
            'status' => 1,
            'login_url' => $login_url,
        ));



    }

    function magicemail(){


        if (!isset($_POST['input_email']) || !filter_var($_POST['input_email'], FILTER_VALIDATE_EMAIL)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Email',
            ));
        } elseif (!isset($_POST['referrer_in_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing core data',
            ));
        }

        //Cleanup/validate email:
        $_POST['input_email'] =  trim(strtolower($_POST['input_email']));
        $user_emails = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_content' => $_POST['input_email'],
            'ln_type_play_id' => 4255, //Linked Players Text (Email is text)
            'ln_parent_play_id' => 3288, //Mench Email
        ), array('en_child'));
        if(count($user_emails) < 1){
            return echo_json(array(
                'status' => 0,
                'message' => 'Email not associated with a registered account',
            ));
        }

        //Log email search attempt:
        $reset_link = $this->READ_model->ln_create(array(
            'ln_type_play_id' => 7563, //User Signin Magic Link Email
            'ln_content' => $_POST['input_email'],
            'ln_owner_play_id' => $user_emails[0]['en_id'], //User making request
            'ln_parent_idea_id' => intval($_POST['referrer_in_id']),
        ));

        //This is a new email, send invitation to join:

        ##Email Subject
        $en_all_11035 = $this->config->item('en_all_11035'); //MENCH PLAYER NAVIGATION
        $subject = 'MENCH '.$en_all_11035[11068]['m_name'];

        ##Email Body
        $html_message = '<div>Hi '.one_two_explode('',' ',$user_emails[0]['en_name']).' 👋</div><br /><br />';

        $magic_link_expiry_hours = (config_var(11065)/3600);
        $html_message .= '<div>Login within the next '.$magic_link_expiry_hours.' hour'.echo__s($magic_link_expiry_hours).':</div>';
        $magic_url = 'https://mench.com/play/magic/' . $reset_link['ln_id'] . '?email='.$_POST['input_email'];
        $html_message .= '<div><a href="'.$magic_url.'" target="_blank">' . $magic_url . '</a></div>';

        $html_message .= '<br /><br />';
        $html_message .= '<div>Cheers,</div>';
        $html_message .= '<div>MENCH</div>';

        //Send email:
        $this->READ_model->dispatch_emails(array($_POST['input_email']), $subject, $html_message);

        //Return success
        return echo_json(array(
            'status' => 1,
        ));
    }

    function magic($ln_id){

        //Validate email:
        if(superpower_assigned(10939)){
            return redirect_message('/idea');
        } elseif(superpower_assigned()){
            return redirect_message('/read/next');
        } elseif(!isset($_GET['email']) || !filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)){
            //Missing email input:
            return redirect_message('/sign', '<div class="alert alert-danger" role="alert">Missing Email</div>');
        }

        //Validate READ ID and matching email:
        $validate_links = $this->READ_model->ln_fetch(array(
            'ln_id' => $ln_id,
            'ln_content' => $_GET['email'],
            'ln_type_play_id' => 7563, //User Signin Magic Link Email
        )); //The user making the request
        if(count($validate_links) < 1){
            //Probably already completed the reset password:
            return redirect_message('/sign?input_email='.$_GET['email'], '<div class="alert alert-danger" role="alert">Invalid data source</div>');
        } elseif(strtotime($validate_links[0]['ln_timestamp']) + config_var(11065) < time()){
            //Probably already completed the reset password:
            return redirect_message('/sign?input_email='.$_GET['email'], '<div class="alert alert-danger" role="alert">Magic link has expired. Try again.</div>');
        }

        //Fetch player:
        $ens = $this->PLAY_model->en_fetch(array(
            'en_id' => $validate_links[0]['ln_owner_play_id'],
        ));
        if(count($ens) < 1){
            return redirect_message('/sign?input_email='.$_GET['email'], '<div class="alert alert-danger" role="alert">User not found</div>');
        }

        //Log them in:
        $ens[0] = $this->PLAY_model->en_activate_session($ens[0]);

        //Take them to their account:
        return redirect_message( '/play?open_en_id=3286' , '<div class="alert alert-info" role="alert"><i class="fas fa-check-circle"></i> Successfully signed in. You can set a new password below.</div>');

    }

    function singin_check_email(){

        if (!isset($_POST['input_email']) || !filter_var($_POST['input_email'], FILTER_VALIDATE_EMAIL)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Email',
            ));
        } elseif (!isset($_POST['referrer_in_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing core data',
            ));
        }


        //Cleanup input email:
        $_POST['input_email'] =  trim(strtolower($_POST['input_email']));


        if(intval($_POST['referrer_in_id']) > 0){
            //Fetch the idea:
            $referrer_ins = $this->IDEA_model->in_fetch(array(
                'in_status_play_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Idea Statuses Public
                'in_id' => $_POST['referrer_in_id'],
            ));
        } else {
            $referrer_ins = array();
        }


        //Search for email to see if it exists...
        $user_emails = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_content' => $_POST['input_email'],
            'ln_type_play_id' => 4255, //Linked Players Text (Email is text)
            'ln_parent_play_id' => 3288, //Mench Email
        ), array('en_child'));

        if(count($user_emails) > 0){

            return echo_json(array(
                'status' => 1,
                'email_existed_already' => 1,
                'login_en_id' => $user_emails[0]['en_id'],
                'clean_input_email' => $_POST['input_email'],
            ));

        } else {

            return echo_json(array(
                'status' => 1,
                'email_existed_already' => 0,
                'login_en_id' => 0,
                'clean_input_email' => $_POST['input_email'],
            ));

        }
    }



    function play_404(){
        $this->load->view('header', array(
            'title' => 'Page not found',
        ));
        $this->load->view('play/play_404');
        $this->load->view('footer');
    }



    function account_update_radio()
    {
        /*
         *
         * Saves the radio selection of some account fields
         * that are displayed using echo_radio_players()
         *
         * */

        $session_en = superpower_assigned();

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Session expired',
            ));
        } elseif (!isset($_POST['parent_en_id']) || intval($_POST['parent_en_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing parent player',
            ));
        } elseif (!isset($_POST['selected_en_id']) || intval($_POST['selected_en_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing selected player',
            ));
        } elseif (!isset($_POST['enable_mulitiselect']) || !isset($_POST['was_already_selected'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing multi-select setting',
            ));
        }


        if(!$_POST['enable_mulitiselect'] || $_POST['was_already_selected']){
            //Since this is not a multi-select we want to remove all existing options...

            //Fetch all possible answers based on parent player:
            $filters = array(
                'ln_parent_play_id' => $_POST['parent_en_id'],
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
                'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7357')) . ')' => null, //Player Statuses Public
            );

            if($_POST['enable_mulitiselect'] && $_POST['was_already_selected']){
                //Just remove this single item, not the other ones:
                $filters['ln_child_play_id'] = $_POST['selected_en_id'];
            }

            //List all possible answers:
            $possible_answers = array();
            foreach($this->READ_model->ln_fetch($filters, array('en_child'), 0, 0) as $answer_en){
                array_push($possible_answers, $answer_en['en_id']);
            }

            //Remove selected options for this trainer:
            foreach($this->READ_model->ln_fetch(array(
                'ln_parent_play_id IN (' . join(',', $possible_answers) . ')' => null,
                'ln_child_play_id' => $session_en['en_id'],
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            )) as $remove_en){
                //Should usually remove a single option:
                $this->READ_model->ln_update($remove_en['ln_id'], array(
                    'ln_status_play_id' => 6173, //Link Removed
                ), $session_en['en_id'], 6224 /* User Account Updated */);
            }

        }

        //Add new option if not already there:
        if(!$_POST['enable_mulitiselect'] || !$_POST['was_already_selected']){
            $this->READ_model->ln_create(array(
                'ln_parent_play_id' => $_POST['selected_en_id'],
                'ln_child_play_id' => $session_en['en_id'],
                'ln_owner_play_id' => $session_en['en_id'],
                'ln_type_play_id' => 4230, //Raw
            ));
        }


        //Log Account iteration link type:
        $_POST['account_update_function'] = 'account_update_radio'; //Add this variable to indicate which My Account function created this link
        $this->READ_model->ln_create(array(
            'ln_owner_play_id' => $session_en['en_id'],
            'ln_type_play_id' => 6224, //My Account Iterated
            'ln_content' => 'My Account '.( $_POST['enable_mulitiselect'] ? 'Multi-Select Radio Field ' : 'Single-Select Radio Field ' ).( $_POST['was_already_selected'] ? 'Removed' : 'Added' ),
            'ln_metadata' => $_POST,
            'ln_parent_play_id' => $_POST['parent_en_id'],
            'ln_child_play_id' => $_POST['selected_en_id'],
        ));

        //All good:
        return echo_json(array(
            'status' => 1,
            'message' => 'Updated', //Note: NOT shown in UI
        ));
    }


    function play_home()
    {

        //Authenticate user:
        $session_en = superpower_assigned(null);

        //Log My Account View:
        if($session_en){
            $this->READ_model->ln_create(array(
                'ln_type_play_id' => 4282, //Opened My Account
                'ln_owner_play_id' => $session_en['en_id'],
            ));
        }

        $en_all_2738 = $this->config->item('en_all_2738'); //MENCH
        $this->load->view('header', array(
            'title' => $en_all_2738[4536]['m_name'],
        ));
        $this->load->view('play/play_home', array(
            'session_en' => $session_en,
        ));
        $this->load->view('footer');

    }


    function signout()
    {
        //Destroys Session
        $this->session->sess_destroy();
        header('Location: /');
    }


    function account_update_avatar_icon()
    {

        $session_en = superpower_assigned();

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Session expired',
            ));
        } elseif (!isset($_POST['type_css'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing type_css',
            ));
        } elseif (!isset($_POST['icon_css'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing icon_css',
            ));
        }

        //Validate:
        $icon_new_css = $_POST['type_css'].' '.$_POST['icon_css'].' play';
        $validated = false;
        foreach ($this->config->item('en_all_12279') as $en_id => $m) {
            if(substr_count($m['m_icon'], $icon_new_css) == 1){
                $validated = true;
                break;
            }
        }
        if(!$validated){
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid icon',
            ));
        }


        //Update icon:
        $new_avatar = '<i class="'.$icon_new_css.'"></i>';
        $this->PLAY_model->en_update($session_en['en_id'], array(
            'en_icon' => $new_avatar,
        ), true, $session_en['en_id']);


        //Update Session:
        $session_en['en_icon'] = $new_avatar;
        $this->PLAY_model->en_activate_session($session_en, true);


        return echo_json(array(
            'status' => 1,
            'message' => 'Name updated',
            'new_avatar' => $new_avatar,
        ));
    }


    function account_update_name()
    {

        $session_en = superpower_assigned();

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Session expired',
            ));
        } elseif (!isset($_POST['en_name']) || strlen($_POST['en_name']) < config_var(12232)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Name must be at-least '.config_var(12232).' characters long',
            ));
        } elseif (strlen($_POST['en_name']) > config_var(11072)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Name is longer than the allowed ' . config_var(11072) . ' characters.',
            ));
        }

        //Cleanup:
        $_POST['en_name'] = trim($_POST['en_name']);

        //Update name:
        $this->PLAY_model->en_update($session_en['en_id'], array(
            'en_name' => $_POST['en_name'],
        ), true, $session_en['en_id']);


        //Update Session:
        $session_en['en_name'] = $_POST['en_name'];
        $this->PLAY_model->en_activate_session($session_en, true);


        return echo_json(array(
            'status' => 1,
            'message' => 'Name updated',
            'first__name' => one_two_explode('',' ', $_POST['en_name']),
        ));
    }


    function account_update_email()
    {

        $session_en = superpower_assigned();

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Session expired',
            ));
        } elseif (!isset($_POST['en_email']) || (strlen($_POST['en_email']) > 0 && !filter_var($_POST['en_email'], FILTER_VALIDATE_EMAIL))) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Email',
            ));
        }


        if (strlen($_POST['en_email']) > 0) {

            //Cleanup:
            $_POST['en_email'] = trim(strtolower($_POST['en_email']));

            //Check to make sure not duplicate:
            $duplicates = $this->READ_model->ln_fetch(array(
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Link Statuses Active
                'ln_type_play_id' => 4255, //Emails are of type Text
                'ln_parent_play_id' => 3288, //Mench Email
                'ln_child_play_id !=' => $session_en['en_id'],
                'LOWER(ln_content)' => $_POST['en_email'],
            ));
            if (count($duplicates) > 0) {
                //This is a duplicate, disallow:
                return echo_json(array(
                    'status' => 0,
                    'message' => 'Email already in-use. Use another email or contact support for assistance.',
                ));
            }
        }


        //Fetch existing email:
        $user_emails = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_child_play_id' => $session_en['en_id'],
            'ln_type_play_id' => 4255, //Emails are of type Text
            'ln_parent_play_id' => 3288, //Mench Email
        ));
        if (count($user_emails) > 0) {

            if (strlen($_POST['en_email']) == 0) {

                //Remove email:
                $this->READ_model->ln_update($user_emails[0]['ln_id'], array(
                    'ln_status_play_id' => 6173, //Link Removed
                ), $session_en['en_id'], 6224 /* User Account Updated */);

                $return = array(
                    'status' => 1,
                    'message' => 'Email removed',
                );

            } elseif ($user_emails[0]['ln_content'] != $_POST['en_email']) {

                //Update if not duplicate:
                $this->READ_model->ln_update($user_emails[0]['ln_id'], array(
                    'ln_content' => $_POST['en_email'],
                ), $session_en['en_id'], 6224 /* User Account Updated */);

                $return = array(
                    'status' => 1,
                    'message' => 'Email updated',
                );

            } else {

                $return = array(
                    'status' => 0,
                    'message' => 'Email unchanged',
                );

            }

        } elseif (strlen($_POST['en_email']) > 0) {

            //Create new link:
            $this->READ_model->ln_create(array(
                'ln_owner_play_id' => $session_en['en_id'],
                'ln_child_play_id' => $session_en['en_id'],
                'ln_type_play_id' => 4255, //Emails are of type Text
                'ln_parent_play_id' => 3288, //Mench Email
                'ln_content' => $_POST['en_email'],
            ), true);

            $return = array(
                'status' => 1,
                'message' => 'Email added',
            );

        } else {

            $return = array(
                'status' => 0,
                'message' => 'Email unchanged',
            );

        }


        if($return['status']){
            //Log Account iteration link type:
            $_POST['account_update_function'] = 'account_update_email'; //Add this variable to indicate which My Account function created this link
            $this->READ_model->ln_create(array(
                'ln_owner_play_id' => $session_en['en_id'],
                'ln_type_play_id' => 6224, //My Account Iterated
                'ln_content' => 'My Account '.$return['message']. ( strlen($_POST['en_email']) > 0 ? ': '.$_POST['en_email'] : ''),
                'ln_metadata' => $_POST,
            ));
        }


        //Return results:
        return echo_json($return);


    }


    function account_update_password()
    {

        $session_en = superpower_assigned();

        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Session Expired',
            ));
        } elseif (!isset($_POST['input_password']) || strlen($_POST['input_password']) < config_var(11066)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'New password must be '.config_var(11066).' characters or more',
            ));
        }

        //Fetch existing password:
        $user_passwords = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id' => 4255, //Passwords are of type Text
            'ln_parent_play_id' => 3286, //Password
            'ln_child_play_id' => $session_en['en_id'],
        ));

        $hashed_password = strtolower(hash('sha256', $this->config->item('cred_password_salt') . $_POST['input_password'] . $session_en['en_id']));


        if (count($user_passwords) > 0) {

            if ($hashed_password == $user_passwords[0]['ln_content']) {

                $return = array(
                    'status' => 0,
                    'message' => 'Password Unchanged',
                );

            } else {

                //Update password:
                $this->READ_model->ln_update($user_passwords[0]['ln_id'], array(
                    'ln_content' => $hashed_password,
                ), $session_en['en_id'], 7578 /* User Iterated Password  */);

                $return = array(
                    'status' => 1,
                    'message' => 'Password Updated',
                );

            }

        } else {

            //Create new link:
            $this->READ_model->ln_create(array(
                'ln_type_play_id' => 4255, //Passwords are of type Text
                'ln_parent_play_id' => 3286, //Password
                'ln_owner_play_id' => $session_en['en_id'],
                'ln_child_play_id' => $session_en['en_id'],
                'ln_content' => $hashed_password,
            ), true);

            $return = array(
                'status' => 1,
                'message' => 'Password Added',
            );

        }


        //Log Account iteration link type:
        if($return['status']){
            $_POST['account_update_function'] = 'account_update_password'; //Add this variable to indicate which My Account function created this link
            $this->READ_model->ln_create(array(
                'ln_owner_play_id' => $session_en['en_id'],
                'ln_type_play_id' => 6224, //My Account Iterated
                'ln_content' => 'My Account '.$return['message'],
                'ln_metadata' => $_POST,
            ));
        }


        //Return results:
        return echo_json($return);

    }



    function update_coin_counter(){

        $session_en = superpower_assigned();
        if (!$session_en) {
            return echo_json(array(
                'play_count' => 0,
                'play_raw_count' => 0,
                'idea_count' => 0,
                'idea_raw_count' => 0,
                'read_count' => 0,
                'read_raw_count' => 0
            ));
        }

        $play_coin_count = 1;
        if(superpower_assigned(10986)){
            $play_coins = $this->READ_model->ln_fetch(array(
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_12274')) . ')' => null, //PLAY COIN
                'ln_owner_play_id' => $session_en['en_id'],
            ), array(), 0, 0, array(), 'COUNT(ln_id) as total_coins');
            $play_coin_count = $play_coins[0]['total_coins'];
        }

        $idea_coins = $this->READ_model->ln_fetch(array(
            'in_status_play_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Idea Statuses Public
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_12273')) . ')' => null, //IDEA COIN
            'ln_parent_play_id' => $session_en['en_id'],
        ), array('in_child'), 0, 0, array(), 'COUNT(ln_id) as total_coins');

        $read_coins = $this->READ_model->ln_fetch(array(
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //READ COIN
            'ln_owner_play_id' => $session_en['en_id'],
        ), array(), 0, 0, array(), 'COUNT(ln_id) as total_coins');

        return echo_json(array(
            'play_count' => echo_number($play_coin_count),
            'play_raw_count' => $play_coin_count,
            'idea_count' => echo_number($idea_coins[0]['total_coins']),
            'idea_raw_count' => $idea_coins[0]['total_coins'],
            'read_count' => echo_number($read_coins[0]['total_coins']),
            'read_raw_count' => $read_coins[0]['total_coins']
        ));

    }




    function platform_cache(){

        /*
         *
         * This function prepares a PHP-friendly text to be copied to platform_cache.php
         * (which is auto loaded) to provide a cache image of some players in
         * the tree for faster application processing.
         *
         * */

        //First first all players that have Cache in PHP Config @4527 as their parent:
        $config_ens = $this->READ_model->ln_fetch(array(
            'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7357')) . ')' => null, //Player Statuses Public
            'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
            'ln_parent_play_id' => 4527,
        ), array('en_child'), 0);

        echo htmlentities('<?php').'<br /><br />';
        echo 'defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');'.'<br /><br />';

        echo '/*<br />
 * Keep a cache of certain parts of the Idea tree for faster processing<br />
 * So we don\'t have to make DB calls to figure them out every time!<br />
 * See here for all players cached: https://mench.com/play/4527<br />
 *<br />
 * ATTENTION: Also search for "en_ids_" and "en_all_" when trying to manage these throughout the code base<br />
 *<br />
 */<br /><br />';
        echo '//Generated '.date("Y-m-d H:i:s").' PST<br />';


        foreach($config_ens as $en){

            //Now fetch all its children:
            $children = $this->READ_model->ln_fetch(array(
                'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
                'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7357')) . ')' => null, //Player Statuses Public
                'ln_parent_play_id' => $en['ln_child_play_id'],
                'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
            ), array('en_child'), 0, 0, array('ln_order' => 'ASC', 'en_name' => 'ASC'));


            //Find common base, if any:
            $common_prefix = common_prefix($children, 'en_name');

            //Generate raw IDs:
            $child_ids = array();
            foreach($children as $child){
                array_push($child_ids , $child['en_id']);
            }

            echo '<br />//'.$en['en_name'].':<br />';
            echo '$config[\'en_ids_'.$en['ln_child_play_id'].'\'] = array('.join(',',$child_ids).');<br />';
            echo '$config[\'en_all_'.$en['ln_child_play_id'].'\'] = array(<br />';
            foreach($children as $child){

                //Do we have an omit command?
                if(strlen($common_prefix) > 0){
                    $child['en_name'] = trim(substr($child['en_name'], strlen($common_prefix)));
                }

                //Fetch all parents for this child:
                $child_parent_ids = array(); //To be populated soon
                $child_parents = $this->READ_model->ln_fetch(array(
                    'ln_status_play_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
                    'en_status_play_id IN (' . join(',', $this->config->item('en_ids_7357')) . ')' => null, //Player Statuses Public
                    'ln_child_play_id' => $child['en_id'],
                    'ln_type_play_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //Player-to-Player Links
                ), array('en_parent'), 0);
                foreach($child_parents as $cp_en){
                    array_push($child_parent_ids, intval($cp_en['en_id']));
                }

                echo '&nbsp;&nbsp;&nbsp;&nbsp; '.$child['en_id'].' => array(<br />';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'m_icon\' => \''.htmlentities($child['en_icon']).'\',<br />';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'m_name\' => \''.str_replace('\'','\\\'',$child['en_name']).'\',<br />';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'m_desc\' => \''.str_replace('\'','\\\'',$child['ln_content']).'\',<br />';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'m_parents\' => array('.join(',',$child_parent_ids).'),<br />';
                echo '&nbsp;&nbsp;&nbsp;&nbsp; ),<br />';

            }
            echo ');<br />';
        }
    }




    function play_admin($action = null, $command1 = null, $command2 = null)
    {

        boost_power();

        //Validate trainer:
        $session_en = superpower_assigned(10985, true);

        //Load tools:
        $this->load->view('header', array(
            'title' => 'Moderation Tools',
        ));

        $this->load->view('play/play_admin' , array(
            'action' => $action,
            'command1' => $command1,
            'command2' => $command2,
            'session_en' => $session_en,
        ));

        $this->load->view('footer');

    }


}