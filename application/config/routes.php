<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['translate_uri_dashes']      = FALSE;

//PLAY
$route['404_override']              = 'play/play_404';
$route['signin/(:num)']             = "play/signin/$1";
$route['signin']                    = "play/signin";
$route['play']                      = "play/play_my";
$route['play/(:num)']               = "play/play_coin/$1";

//READ
$route['default_controller']        = "read/read_home";
$route['read']                      = "read/read_my";
$route['read/next']                 = "read/read_next";
$route['read/(:num)']               = "read/read_add/$1";
$route['oil']                       = "read/read_oil";
$route['(:num)']                    = "read/read_coin/$1";
$route['(:num)/next']               = "read/read_next/$1";

//IDEA
$route['idea']                      = "idea/idea_my";
$route['idea/create']               = "idea/idea_create";
$route['idea/stats']                = "idea/idea_stats";
$route['idea/(:num)']               = "idea/idea_coin/$1";
