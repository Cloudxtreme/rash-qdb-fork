<?php

class NHQDBTemplate extends BaseTemplate {

    public $resizehooks = 'onkeyup="resizeTextarea(this)" onmouseup="resizeTextarea(this)" onblur="resizeTextarea(this)"';

function printheader($title, $topleft='nhqdb', $topright='#NetHack Quote Database')
{
ob_start();
header('Content-type: text/html; charset=utf-8');
// begin editing after this line ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
 <title><?=$title?></title>
 <meta name="robots" content="noarchive,nofollow">
 <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
 <link rel="icon" type="image/png" href="./templates/nhqdb/favicon.png">
 <link rel="alternate" type="application/rss+xml" href="?rss" title="RSS">
 <style type="text/css" media="all">
  @import "./templates/nhqdb/style.css";
 </style>
<script src="./templates/nhqdb/util.js" type="text/javascript"></script>
<script src="qdb.js" type="text/javascript"></script>
</head>
<body>
 <div id="site_all">
  <div id="site_nav">
   <div id="site_nav_upper">
      <div id="site_nav_upper_qms-long"><?=$topright?>
    </div>
      <div id="site_nav_upper_qms"><?=$topleft?>
    </div>&nbsp;
<?php

  if(!isset($_SESSION['logged_in'])){
      print '<a href="?'.urlargs('admin').'" id="site_nav_admin">'.lang('menu_admin').'</a>';
  } else {
      print sprintf(lang('logged_in_as'), htmlspecialchars($_SESSION['user']));
  }
	print '</div>';
	print $this->get_menu();
	print '</div>';
}


// printfooter()
// Bottom of the document!
//
function printfooter($dbstats=null)
{
    print $this->get_messages(); /* in case the page itself didn't already get the messages */
?>
  <p>
  <div id="site_admin_nav">
   <div id="site_admin_nav_upper">
    <div id="site_admin_nav_upper_linkbar"><?=$this->get_menu(1);?>
    </div>
   </div>
   <div id="site_admin_nav_lower">
    <div id="site_admin_nav_lower_infobar">
     <span id="site_admin_nav_lower_infobar_pending">
<?=lang('pending_quotes').": ".$dbstats['pending_quotes'].";\n";?>
     </span>
     <span id="site_admin_nav_lower_infobar_approved">
<?=lang('approved_quotes').": ".$dbstats['approved_quotes']."\n";?>
     </span>
    </div>
   </div>
  </div>
 </div>
</body>
</html>
<?php

}

function edit_news_page($news)
{
    $str = '<div id="editnews_all">';
    $str .= '<h1 id="editnews_title">'.lang('editnews_title').'</h1>';
    $str .= $this->get_messages();
    $str .= $news;
    $str .= '</div>';
    return $str;
}


function main_page($news)
{
    $str = $this->get_messages() . '<div id="home_all"><div id="news">'.$news;
    if ($news) {
	$str .= '<div class="show_all_news"><a href="?'.urlargs('news').'">'.lang('news_show_all').'</a></div>';
    }
    $str .= '</div>';
    $str .= '<div id="home_greeting">'.lang('home_greeting').'</div></div>';
    return $str;
}



function add_quote_page($quotetxt='', $added_quote_html='', $wasadded=null)
{
    global $CAPTCHA;
    $str = '<div id="add_all">';

    $str .= '<h1 id="add_title">'.lang('add_title').'</h1>';

    $str .= $this->get_messages();

    $str .= $added_quote_html;

    $str .= '<form action="?'.urlargs('add','submit').'" method="post">
     <textarea cols="80" rows="5" name="rash_quote" id="add_quote" '.$this->resizehooks.'>'.($wasadded ? '' : $quotetxt).'</textarea><br />';
    $str .= $CAPTCHA->get_CAPTCHA('add_quote');
    $str .= '
        <input type="submit" value="'.lang('preview_quote_btn').'" id="add_preview" name="preview" />
        <input type="submit" value="'.lang('add_quote_btn').'" id="add_submit" name="submit" />
     <input type="reset" value="'.lang('add_reset_btn').'" id="add_reset" />
    </form>';

    $str .= '<script type="text/javascript">setFocus("add_quote"); document.write("<input type=\'button\' onclick=\'javascript:mangle_quote(\"add_quote\")\' value=\''.lang('remove_timestamps_btn').'\'>");</script>';

    $str .= '</div>';
    return $str;
}



function edit_quote_page($action, $quoteid, $quotetxt, $edited_quote_html='')
{
    $str = '<div id="editquote_all">';

    $str .= '<h1 id="editquote_title">'.lang('editquote_title').'</h1>';

    $str .= $this->get_messages();

    $str .= $edited_quote_html;

    $str .= '<form action="?'.urlargs($action,'submit', $quoteid).'" method="post">
     <textarea cols="80" rows="5" name="rash_quote" id="edit_quote" '.$this->resizehooks.'>'.$quotetxt.'</textarea><br />
     <input type="submit" value="'.lang('edit_quote_btn').'" id="edit_submit" />
     <input type="reset" value="'.lang('edit_reset_btn').'" id="edit_reset" />
    </form>';

    $str .= '<script type="text/javascript">setFocus("edit_quote"); document.write("<input type=\'button\' onclick=\'javascript:mangle_quote(\"edit_quote\")\' value=\''.lang('remove_timestamps_btn').'\'>");</script>';

    $str .= '</div>';

    return $str;
}

function search_quotes_page($fetched, $searchstr)
{
    $str = '<div class="search_all">';

    if (!$fetched) {
	$str .= '<h1 id="search_title">'.lang('search_title').'</h1>';
    }

    $str .= $this->get_messages();

    $str .= '<form method="post" action="?'.urlargs('search','fetch').'">';
    if ($fetched) { $str .= '<input type="submit" name="submit" value="'.lang('search_btn').'" id="search_submit-button">&nbsp;'; }
    $str .= '<input type="text" name="search" size="28" id="search_query-box" value="'.$searchstr.'">&nbsp;';
    if (!$fetched) { $str .= '<input type="submit" name="submit" value="'.lang('search_btn').'" id="search_submit-button">&nbsp;<br />'; }
    $str .= lang('search_sort').': <select name="sortby" size="1" id="search_sortby-dropdown">';
    $str .= '<option selected>'.lang('search_opt_rating');
    $str .= '<option>'.lang('search_opt_id');
    $str .= '</select>';

    $str .= '<script type="text/javascript">setFocus("search_query-box");</script>';

    $str .= '&nbsp;';

    $str .= lang('search_howmany').': <select name="number" size="1" id="search_limit-dropdown">
     <option selected>10
     <option>25
     <option>50
     <option>75
     <option>100
    </select>';

    $str .= '</form>';

    $str .= '</div>';

    return $str;
}


function flag_queue_page($inner_html)
{
    $str = '<h1 id="admin_flag_title">'.lang('flag_quote_adminpage_title').'</h1>';

    $str .= $this->get_messages();

    $str .= '<form action="?'.urlargs('flag_queue','judgement').'" method="post">
<table width="100%" class="admin_queue">';

    $str .= $inner_html;

    $str .= '</table>
<input type="submit" value="'.lang('flag_quote_adminpage_submit_btn').'" />
<input type="reset" value="Reset" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="'.lang('flag_quote_adminpage_unflag_all_btn').'" name="unflag_all">
<input type="submit" value="'.lang('flag_quote_adminpage_delete_all_btn').'" name="delete_all">
'.lang('flag_quote_adminpage_verify').'<input type="checkbox" name="do_all">
</form>';

    return $str;
}

function add_news_page($previewnews, $newstxt)
{
    $str = '  <div id="admin_add-news_all">
   <h1 id="admin_add-news_title">'.lang('add_news_title').'</h1>' . $this->get_messages();
    if (isset($previewnews)) $str .= '<div class="admin_preview_news">'.$previewnews.'</div>';
    $str .= '<p>'.lang('add_news_help').'
   <form method="post" action="?'.urlargs('add_news','submit').'">
	<textarea cols="80" rows="5" name="news" id="add_news_news" '.$this->resizehooks.'>'.$newstxt.'</textarea><br />
        <input type="submit" value="'.lang('preview_news_btn').'" id="add_preview" name="preview" />
        <input type="submit" value="'.lang('add_news_btn').'" id="add_news" name="submit" />
   </form>
  </div>
';
    return $str;
}

    function register_user_page()
    {
	global $CAPTCHA;
	$str = '  <div id="register-user_all">
   <h1 id="register-user_title">'.lang('register_user_title').'</h1>' . $this->get_messages() . '
   <form method="post" action="?'.urlargs('register','update').'">
   <table>
   <tr><td>'.lang('register_user_username_label').'</td><td><input type="text" name="username" id="register-user_username" /></td></tr>
   <tr><td>'.lang('register_user_password_label').'</td><td><input type="password" name="password" /></td></tr>
   <tr><td>'.lang('register_user_verifypassword_label').'</td><td><input type="password" name="verifypassword" /></td></tr>
   <tr><td></td><td><input type="submit" value="'.lang('register_user_btn').'" id="register-user_submit" /></td></tr>
   </table>' . $CAPTCHA->get_CAPTCHA('register_user') . '
   </form>
  </div>
';
	return $str;
    }

function add_user_page()
{
	return '  <div id="admin_add-user_all">
   <h1 id="admin_add-user_title">'.lang('add_user_title').'</h1>
   ' . $this->get_messages() . '
   <form method="post" action="?'.urlargs('add_user','update').'">
   <table>
   <tr><td>'.lang('add_user_username_label').'</td><td><input type="text" name="username" id="admin_add-user_username" /></td></tr>
   <tr><td>'.lang('add_user_randomsalt_label').'</td><td><input type="text" name="salt" value="'.str_rand(8,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789').'" id="admin_add-user_salt" /></td></tr>
   <tr><td>'.lang('add_user_password_label').'</td><td><input type="text" name="password" /></td></tr>
   <tr><td>'.lang('add_user_level_label').'</td><td>'.user_level_select().'</td></tr>
   <tr><td></td><td><input type="submit" value="'.lang('add_user_btn').'" id="admin_add-user_submit" /></td></tr>
   </table>
   </form>
  </div>
';
}

function change_password_page()
{
	return '  <h1 id="admin_change-pw_title">'.lang('change_password_title').'</h1>
        ' . $this->get_messages() . '
  <form action="?'.urlargs('change_pw','update',$_SESSION['userid']).'" method="post">
  <table>
  <tr><td>'.lang('change_password_oldpass').'</td><td><input type="password" name="old_password"></td></tr>
  <tr><td>'.lang('change_password_newpass').'</td><td><input type="password" name="new_password"></td></tr>
  <tr><td>'.lang('change_password_verify').'</td><td><input type="password" name="verify_password"></td></tr>
  <tr><td></td><td><input type="submit" value="'.lang('change_password_submit_btn').'"></td></tr>
  </table>
  </form>';
}



function edit_user_page_table($innerhtml)
{
	$str = '  <h1 id="admin_users_title">'.lang('users_list_title').'</h1>' . $this->get_messages() . '
  <form action="?'.urlargs('users','delete').'" method="post">
   <table class="users">
    <tr>
     <th>'.lang('users_list_id').'</th>
     <th>'.lang('users_list_username').'</th>
     <th>'.lang('users_list_pwhash').'</th>
     <th>'.lang('users_list_level').'</th>
     <th>'.lang('users_list_delete').'</th>
    </tr>
';

    $str .= $innerhtml;

    $str .= '  </table>
  <input type="submit" value="'.lang('users_list_submit_btn').'" />&nbsp;'.lang('users_list_verify').' <input type="checkbox" name="verify" value="1" />
 </form>
';

    return $str;
}

    function user_login_page()
    {
    return '<h1 id="login_title">'.lang('login_title').'</h1>'.
	$this->get_messages() . '<div id="admin_all"><p>'.lang('user_login_greeting').'</p>
    <form action="?'.urlargs('login','login').'" method="post">
    <table>
    <tr><td>'.lang('login_username').'</td><td><input type="text" name="rash_username" size="8" id="user_login_username-box" /></td></tr>
    <tr><td>'.lang('login_password').'</td><td><input type="password" name="rash_password" size="8" id="user_login_password-box" /></td></tr>
    <tr><td><label for="remember_login_input">'.lang('login_remember').'</label></td><td><input type="checkbox" name="remember_login" id="remember_login_input"></td></tr>
    <tr><td></td><td><input type="submit" value="'.lang('login_submit_btn').'" id="user_login_submit-button" /></td></tr>
    </table>
    </form></div>';
    }

function admin_login_page()
{
    return '<h1 id="login_title">'.lang('login_title').'</h1>'.
	$this->get_messages() . '<div id="admin_all"><p>'.lang('admin_login_greeting').'</p>
    <form action="?'.urlargs('admin','login').'" method="post">
    <table>
    <tr><td>'.lang('login_username').'</td><td><input type="text" name="rash_username" size="8" id="admin_login_username-box" /></td></tr>
    <tr><td>'.lang('login_password').'</td><td><input type="password" name="rash_password" size="8" id="admin_login_password-box" /></td></tr>
    <tr><td><label for="remember_login_input">'.lang('login_remember').'</label></td><td><input type="checkbox" name="remember_login" id="remember_login_input"></td></tr>
    <tr><td></td><td><input type="submit" value="'.lang('login_submit_btn').'" id="admin_login_submit-button" /></td></tr>
    </table>
    </form></div>';
}


function quote_queue_page($innerhtml)
{
    $str = '<h1 id="admin_queue_title">'.lang('quote_queue_admin_title').'</h1>';

    $str .= $this->get_messages();

    $str .= '  <form action="?'.urlargs('adminqueue','judgement').'" method="post">
   <table width="100%" cellspacing="0" class="admin_queue">';

    $str .= $innerhtml;

    $str .= '   </table>
   <input type="submit" value="'.lang('quote_queue_submit_btn').'" />
   <input type="reset" value="'.lang('quote_queue_reset_btn').'" />
  </form>
';

    return $str;
}


function quote_iter($quoteid, $rating, $quotetxt, $canflag, $canvote, $date=null)
{
    $str = '<div class="quote_whole" id="q'.$quoteid.'">
    <div class="quote_separator">&nbsp;</div>
    <div class="quote_option-bar">
     <a href="?'.urlargs($quoteid).'" class="quote_number">#'.$quoteid.'</a>'
	.' '.$this->quote_upvote_button($quoteid, $canvote, TRUE)
	.' '.'<span class="quote_rating">(<span id="quote_rating_'.$quoteid.'">'.$rating.'</span>)</span>'
	.' '.$this->quote_downvote_button($quoteid, $canvote, TRUE)
	.' '.$this->quote_flag_button($quoteid, $canflag)
	.' '.edit_quote_button($quoteid);

    if (isset($date)) {
	$str .= "     <span class=\"quote_date\">" . $date . "</span>\n";
    }

    $str .= '
    </div>
    <div class="quote_quote">
     '.$quotetxt.'
    </div>
   </div>
';
    return $str;
}




}


$TEMPLATE = new NHQDBTemplate;